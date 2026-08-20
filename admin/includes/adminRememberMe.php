<?php
/*
*******************************************************************
adminRememberMe.php
"Stay logged in" cookie for Steve Weeks Music /admin.
Stores a hashed selector/validator token in ADMIN_REMEMBER_TOKEN
so closing the browser does not require a new password entry.
*******************************************************************
*/

const ADMIN_REMEMBER_COOKIE = 'swm_admin_remember';
const ADMIN_REMEMBER_DAYS = 30;

function adminRememberIsHttps(): bool
{
	return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
}

function adminRememberCookieOptions(int $expires): array
{
	return [
		'expires' => $expires,
		'path' => '/',
		'secure' => adminRememberIsHttps(),
		'httponly' => true,
		'samesite' => 'Lax',
	];
}

function adminRememberPdo(): ?\PDO
{
	if (!defined('DATALAYER_DIR')) {
		return null;
	}
	try {
		include_once DATALAYER_DIR . '/Connection.php';
		include_once DATALAYER_DIR . '/AdminUser.php';
		include_once DATALAYER_DIR . '/AdminUserRepository.php';
		return \Datalayer\Connection::getPdo();
	} catch (\Throwable $e) {
		error_log('admin remember-me DB: ' . $e->getMessage());
		return null;
	}
}

function adminRememberEnsureTable(\PDO $db): void
{
	static $ready = false;
	if ($ready) {
		return;
	}
	$db->exec(
		'CREATE TABLE IF NOT EXISTS ADMIN_REMEMBER_TOKEN (
			TOKEN_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
			ADMIN_USER_ID INT UNSIGNED NOT NULL,
			SELECTOR CHAR(32) NOT NULL,
			VALIDATOR_HASH CHAR(64) NOT NULL,
			EXPIRES_AT DATETIME NOT NULL,
			PRIMARY KEY (TOKEN_ID),
			UNIQUE KEY UK_REMEMBER_SELECTOR (SELECTOR),
			KEY IX_REMEMBER_USER (ADMIN_USER_ID),
			KEY IX_REMEMBER_EXPIRES (EXPIRES_AT),
			CONSTRAINT FK_REMEMBER_ADMIN_USER
				FOREIGN KEY (ADMIN_USER_ID) REFERENCES ADMIN_USER (ADMIN_USER_ID)
				ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
	);
	$ready = true;
}

function adminRememberParseCookie(?string $raw = null): array
{
	$raw = $raw ?? (string) ($_COOKIE[ADMIN_REMEMBER_COOKIE] ?? '');
	if ($raw === '' || !str_contains($raw, ':')) {
		return ['', ''];
	}
	[$selector, $validator] = explode(':', $raw, 2);
	return [$selector, $validator];
}

function adminRememberClearCookie(): void
{
	setcookie(ADMIN_REMEMBER_COOKIE, '', adminRememberCookieOptions(time() - 42000));
	unset($_COOKIE[ADMIN_REMEMBER_COOKIE]);
}

function adminRememberDeleteSelector(string $selector): void
{
	if ($selector === '') {
		return;
	}
	$db = adminRememberPdo();
	if (!$db) {
		return;
	}
	try {
		adminRememberEnsureTable($db);
		$stmt = $db->prepare('DELETE FROM ADMIN_REMEMBER_TOKEN WHERE SELECTOR = :selector');
		$stmt->execute([':selector' => $selector]);
	} catch (\Throwable $e) {
		error_log('admin remember-me delete: ' . $e->getMessage());
	}
}

function adminRememberClear(): void
{
	[$selector] = adminRememberParseCookie();
	adminRememberDeleteSelector($selector);
	adminRememberClearCookie();
}

function adminRememberIssue(int $userId): void
{
	$db = adminRememberPdo();
	if (!$db || $userId < 1) {
		return;
	}
	try {
		adminRememberEnsureTable($db);
		$selector = bin2hex(random_bytes(16));
		$validator = bin2hex(random_bytes(32));
		$expires = (new DateTimeImmutable('+' . ADMIN_REMEMBER_DAYS . ' days'))->format('Y-m-d H:i:s');
		$stmt = $db->prepare(
			'INSERT INTO ADMIN_REMEMBER_TOKEN (ADMIN_USER_ID, SELECTOR, VALIDATOR_HASH, EXPIRES_AT)
			 VALUES (:userId, :selector, :validatorHash, :expiresAt)'
		);
		$stmt->execute([
			':userId' => $userId,
			':selector' => $selector,
			':validatorHash' => hash('sha256', $validator),
			':expiresAt' => $expires,
		]);
		setcookie(
			ADMIN_REMEMBER_COOKIE,
			$selector . ':' . $validator,
			adminRememberCookieOptions(time() + (ADMIN_REMEMBER_DAYS * 86400))
		);
		$_COOKIE[ADMIN_REMEMBER_COOKIE] = $selector . ':' . $validator;
	} catch (\Throwable $e) {
		error_log('admin remember-me issue: ' . $e->getMessage());
	}
}

function adminRememberRestore(): void
{
	if (!empty($_SESSION['adminUserId'])) {
		return;
	}
	[$selector, $validator] = adminRememberParseCookie();
	if ($selector === '' || $validator === '') {
		return;
	}

	$db = adminRememberPdo();
	if (!$db) {
		return;
	}

	try {
		adminRememberEnsureTable($db);
		$stmt = $db->prepare(
			'SELECT ADMIN_USER_ID, VALIDATOR_HASH
			   FROM ADMIN_REMEMBER_TOKEN
			  WHERE SELECTOR = :selector
			    AND EXPIRES_AT > NOW()'
		);
		$stmt->execute([':selector' => $selector]);
		$row = $stmt->fetch();
		if (!$row || !hash_equals((string) $row['VALIDATOR_HASH'], hash('sha256', $validator))) {
			adminRememberClear();
			return;
		}

		$userRepo = new \Datalayer\AdminUserRepository($db);
		$user = $userRepo->findById((int) $row['ADMIN_USER_ID']);
		if (!$user || !$user->isActive) {
			adminRememberClear();
			return;
		}

		session_regenerate_id(true);
		$_SESSION['adminUserId'] = $user->id;
		$_SESSION['adminUsername'] = $user->username;
		$userRepo->recordLogin((int) $user->id);

		adminRememberDeleteSelector($selector);
		adminRememberIssue((int) $user->id);
	} catch (\Throwable $e) {
		error_log('admin remember-me restore: ' . $e->getMessage());
	}
}

function adminRememberRevokeUser(int $userId): void
{
	if ($userId < 1) {
		return;
	}
	$db = adminRememberPdo();
	if (!$db) {
		return;
	}
	try {
		adminRememberEnsureTable($db);
		$stmt = $db->prepare('DELETE FROM ADMIN_REMEMBER_TOKEN WHERE ADMIN_USER_ID = :userId');
		$stmt->execute([':userId' => $userId]);
	} catch (\Throwable $e) {
		error_log('admin remember-me revoke: ' . $e->getMessage());
	}
}
