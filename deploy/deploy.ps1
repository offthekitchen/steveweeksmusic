# Deploy Steve Weeks Music site via FTP
# Uploads only files changed vs a git base ref, swaps production settings files,
# and renames existing remote files to *.old before overwrite.
#
# Usage:
#   .\deploy\deploy.ps1                          # dry-run of changes vs origin/main
#   .\deploy\deploy.ps1 -DryRun:$false           # actually deploy
#   .\deploy\deploy.ps1 -BaseRef HEAD~1          # changes since previous commit
#   .\deploy\deploy.ps1 -Files admin/login.php

[CmdletBinding()]
param(
	[string]$BaseRef = "origin/main",
	[string[]]$Files = @(),
	[switch]$DryRun = $true,
	[switch]$IncludeUncommitted
)

$ErrorActionPreference = "Stop"
$RepoRoot = Resolve-Path (Join-Path $PSScriptRoot "..")
Set-Location $RepoRoot

$configPath = Join-Path $PSScriptRoot "deploy.config.ps1"
$configExamplePath = Join-Path $PSScriptRoot "deploy.config.ps1.example"

if (-not (Test-Path $configPath)) {
	Write-Host "Missing deploy\deploy.config.ps1"
	Write-Host "Copy deploy.config.ps1.example to deploy.config.ps1 and fill in your FTP password (or leave blank to be prompted)."
	if (Test-Path $configExamplePath) {
		Copy-Item $configExamplePath $configPath
		Write-Host "Created deploy.config.ps1 from example. Edit it, then re-run."
	}
	exit 1
}

. $configPath

if ([string]::IsNullOrWhiteSpace($FtpHost) -or [string]::IsNullOrWhiteSpace($FtpUser) -or [string]::IsNullOrWhiteSpace($RemoteRoot)) {
	throw "deploy.config.ps1 must set `$FtpHost, `$FtpUser, and `$RemoteRoot."
}

function Get-ChangedFiles {
	param([string]$Base, [switch]$Uncommitted)

	$changed = New-Object System.Collections.Generic.List[string]

	try {
		git rev-parse --verify $Base 2>$null | Out-Null
		if ($LASTEXITCODE -ne 0) {
			Write-Warning "Base ref '$Base' not found locally. Trying fetch..."
			git fetch origin 2>$null | Out-Null
		}
	} catch { }

	$diffArgs = @("diff", "--name-only", "--diff-filter=ACMR", "$Base...HEAD")
	$committed = git @diffArgs 2>$null
	if ($LASTEXITCODE -ne 0) {
		$committed = git diff --name-only --diff-filter=ACMR "$Base" HEAD
	}
	foreach ($f in @($committed)) {
		if (-not [string]::IsNullOrWhiteSpace($f)) { $changed.Add($f.Trim().Replace('\', '/')) }
	}

	if ($Uncommitted) {
		$wt = git diff --name-only --diff-filter=ACMR HEAD
		$untracked = git ls-files --others --exclude-standard
		foreach ($f in @($wt) + @($untracked)) {
			if (-not [string]::IsNullOrWhiteSpace($f)) { $changed.Add($f.Trim().Replace('\', '/')) }
		}
	}

	$changed | Select-Object -Unique | Sort-Object
}

function Test-ShouldDeployFile {
	param([string]$RelativePath)

	$p = $RelativePath.Replace('\', '/')

	$excludePrefixes = @(
		'.git/',
		'deploy/',
		'includes/Production/',
		'admin/includes/Production/',
		'OldSite/',
		'Photoshop Images/',
		'Backups/',
		'mp3/',
		'video/'
	)
	$excludeExact = @(
		'.gitignore',
		'error_log'
	)
	$excludeExtensions = @('.psd', '.psb', '.md', '.old', '.new')

	foreach ($prefix in $excludePrefixes) {
		if ($p.StartsWith($prefix, [StringComparison]::OrdinalIgnoreCase)) { return $false }
	}
	foreach ($exact in $excludeExact) {
		if ($p.Equals($exact, [StringComparison]::OrdinalIgnoreCase)) { return $false }
	}
	foreach ($ext in $excludeExtensions) {
		if ($p.EndsWith($ext, [StringComparison]::OrdinalIgnoreCase)) { return $false }
	}
	return $true
}

function Get-LocalSourcePath {
	param([string]$RelativePath)

	$p = $RelativePath.Replace('\', '/')
	if ($p -eq 'includes/websiteSettings.php') {
		$prod = Join-Path $RepoRoot 'includes\Production\websiteSettings.php'
		if (Test-Path $prod) { return $prod }
	}
	if ($p -eq 'admin/includes/AdminSettings.php') {
		$prod = Join-Path $RepoRoot 'admin\includes\Production\AdminSettings.php'
		if (Test-Path $prod) { return $prod }
	}
	return (Join-Path $RepoRoot ($p -replace '/', '\'))
}

function Get-FtpPassword {
	if (-not [string]::IsNullOrWhiteSpace($FtpPassword)) {
		return $FtpPassword
	}
	if (-not [string]::IsNullOrWhiteSpace($env:SWM_FTP_PASSWORD)) {
		return $env:SWM_FTP_PASSWORD
	}
	if (-not [string]::IsNullOrWhiteSpace($env:TBM_FTP_PASSWORD)) {
		return $env:TBM_FTP_PASSWORD
	}
	$secure = Read-Host "FTP password for $FtpUser@$FtpHost" -AsSecureString
	$bstr = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure)
	try {
		return [Runtime.InteropServices.Marshal]::PtrToStringBSTR($bstr)
	} finally {
		[Runtime.InteropServices.Marshal]::ZeroFreeBSTR($bstr)
	}
}

function Get-FtpUri {
	param([string]$RemoteRelativePath)
	$root = $RemoteRoot.TrimEnd('/')
	$rel = $RemoteRelativePath.Replace('\', '/').TrimStart('/')
	return "ftp://$FtpHost$root/$rel"
}

function Invoke-FtpRequest {
	param(
		[string]$Uri,
		[string]$Method,
		[string]$Password,
		[byte[]]$Body = $null,
		[switch]$IgnoreErrors
	)

	$request = [System.Net.FtpWebRequest]::Create($Uri)
	$request.Method = $Method
	$request.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $Password)
	$request.UseBinary = $true
	$request.UsePassive = $true
	$request.KeepAlive = $false

	if ($null -ne $Body) {
		$request.ContentLength = $Body.Length
		$stream = $request.GetRequestStream()
		$stream.Write($Body, 0, $Body.Length)
		$stream.Close()
	}

	try {
		$response = $request.GetResponse()
		$response.Close()
		return $true
	} catch {
		if ($IgnoreErrors) { return $false }
		throw
	}
}

function Test-FtpFileExists {
	param([string]$RemoteRelativePath, [string]$Password)
	$uri = Get-FtpUri $RemoteRelativePath
	try {
		$request = [System.Net.FtpWebRequest]::Create($uri)
		$request.Method = [System.Net.WebRequestMethods+Ftp]::GetFileSize
		$request.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $Password)
		$request.UsePassive = $true
		$request.KeepAlive = $false
		$response = $request.GetResponse()
		$response.Close()
		return $true
	} catch {
		return $false
	}
}

function Ensure-FtpDirectory {
	param([string]$RemoteRelativeDir, [string]$Password)

	$dir = $RemoteRelativeDir.Replace('\', '/').Trim('/')
	if ([string]::IsNullOrWhiteSpace($dir)) { return }

	$parts = $dir -split '/'
	$current = ''
	foreach ($part in $parts) {
		$current = if ($current) { "$current/$part" } else { $part }
		$uri = Get-FtpUri $current
		Invoke-FtpRequest -Uri $uri -Method ([System.Net.WebRequestMethods+Ftp]::MakeDirectory) -Password $Password -IgnoreErrors | Out-Null
	}
}

function Backup-RemoteFile {
	param([string]$RemoteRelativePath, [string]$Password)

	if (-not (Test-FtpFileExists -RemoteRelativePath $RemoteRelativePath -Password $Password)) {
		Write-Host "  (no existing remote file to back up)"
		return
	}

	$normalized = $RemoteRelativePath.Replace('\', '/')
	$fileName = Split-Path $normalized -Leaf
	$backupName = "$fileName.old"
	$backupPath = if ($normalized.Contains('/')) {
		(Split-Path $normalized -Parent).Replace('\', '/') + '/' + $backupName
	} else {
		$backupName
	}

	if (Test-FtpFileExists -RemoteRelativePath $backupPath -Password $Password) {
		$oldUri = Get-FtpUri $backupPath
		Invoke-FtpRequest -Uri $oldUri -Method ([System.Net.WebRequestMethods+Ftp]::DeleteFile) -Password $Password -IgnoreErrors | Out-Null
	}

	$srcUri = Get-FtpUri $RemoteRelativePath
	$request = [System.Net.FtpWebRequest]::Create($srcUri)
	$request.Method = [System.Net.WebRequestMethods+Ftp]::Rename
	$request.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $Password)
	$request.UsePassive = $true
	$request.KeepAlive = $false
	$request.RenameTo = $backupName
	$response = $request.GetResponse()
	$response.Close()
	Write-Host "  backed up -> $backupPath"
}

function Upload-File {
	param([string]$LocalPath, [string]$RemoteRelativePath, [string]$Password)

	$bytes = [System.IO.File]::ReadAllBytes($LocalPath)
	$remoteDir = Split-Path $RemoteRelativePath.Replace('\', '/') -Parent
	if ($remoteDir -and $remoteDir -ne '.' -and $remoteDir -ne '/') {
		Ensure-FtpDirectory -RemoteRelativeDir $remoteDir -Password $Password
	}
	$uri = Get-FtpUri $RemoteRelativePath
	Invoke-FtpRequest -Uri $uri -Method ([System.Net.WebRequestMethods+Ftp]::UploadFile) -Password $Password -Body $bytes | Out-Null
}

Write-Host "Repo: $RepoRoot"
Write-Host "FTP:  $FtpUser@$FtpHost$RemoteRoot"
Write-Host "Mode: $(if ($DryRun) { 'DRY RUN (no uploads)' } else { 'LIVE DEPLOY' })"
Write-Host ""

if ($Files.Count -gt 0) {
	$candidates = $Files | ForEach-Object { $_.Trim().Replace('\', '/') }
} else {
	Write-Host "Finding files changed since $BaseRef..."
	$candidates = @(Get-ChangedFiles -Base $BaseRef -Uncommitted:$IncludeUncommitted)
}

$mapped = New-Object System.Collections.Generic.List[string]
foreach ($f in $candidates) {
	if ($f -eq 'includes/Production/websiteSettings.php') {
		$mapped.Add('includes/websiteSettings.php') | Out-Null
		continue
	}
	if ($f -eq 'admin/includes/Production/AdminSettings.php') {
		$mapped.Add('admin/includes/AdminSettings.php') | Out-Null
		continue
	}
	$mapped.Add($f) | Out-Null
}
$candidates = $mapped | Select-Object -Unique

$toDeploy = @()
foreach ($f in $candidates) {
	if (-not (Test-ShouldDeployFile $f)) {
		Write-Host "SKIP  $f"
		continue
	}
	$local = Get-LocalSourcePath $f
	if (-not (Test-Path $local)) {
		Write-Host "MISS  $f (local source not found: $local)"
		continue
	}
	$note = ''
	if ($f -eq 'admin/includes/AdminSettings.php') {
		$note = '  [using Production/AdminSettings.php]'
	}
	if ($f -eq 'includes/websiteSettings.php') {
		$note = '  [using Production/websiteSettings.php]'
	}
	Write-Host "DEPLOY $f$note"
	$toDeploy += [pscustomobject]@{ Remote = $f; Local = $local }
}

if ($toDeploy.Count -eq 0) {
	Write-Host ""
	Write-Host "Nothing to deploy."
	exit 0
}

Write-Host ""
Write-Host "$($toDeploy.Count) file(s) selected."

if ($DryRun) {
	Write-Host ""
	Write-Host "Dry run complete. Re-run with -DryRun:`$false to upload."
	exit 0
}

$password = Get-FtpPassword
$failed = New-Object System.Collections.Generic.List[string]

foreach ($item in $toDeploy) {
	Write-Host ""
	Write-Host "Uploading $($item.Remote) ..."
	try {
		Backup-RemoteFile -RemoteRelativePath $item.Remote -Password $password
		Upload-File -LocalPath $item.Local -RemoteRelativePath $item.Remote -Password $password
		Write-Host "  uploaded OK"
	} catch {
		Write-Host "  FAILED: $($_.Exception.Message)" -ForegroundColor Red
		$failed.Add($item.Remote) | Out-Null
	}
}

Write-Host ""
if ($failed.Count -gt 0) {
	Write-Host "Deploy finished with $($failed.Count) failure(s):"
	$failed | ForEach-Object { Write-Host "  - $_" }
	exit 1
}

Write-Host "Deploy finished."
