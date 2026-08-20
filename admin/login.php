<?php
/*
*******************************************************************
login.php
Admin login for Steve Weeks Music /admin
*******************************************************************
*/

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

define('ADMIN_AUTH_SKIP', true);

include_once('root.inc.php');
include_once($ROOT . '/includes/websiteSettings.php');
include_once(SETTINGS_DIR . '/SteveWeeksMusicSettings.php');
include_once(ADMIN_DIR . '/includes/AdminSettings.php');
include_once(DATALAYER_DIR . '/Connection.php');
include_once(DATALAYER_DIR . '/AdminUser.php');
include_once(DATALAYER_DIR . '/AdminUserRepository.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
	$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
	session_set_cookie_params([
		'lifetime' => 0,
		'path' => '/',
		'secure' => $secure,
		'httponly' => true,
		'samesite' => 'Lax',
	]);
	// PHP's default session Cache-Control includes no-store, which stops Chrome
	// from offering to save or autofill the username and password.
	session_cache_limiter('');
	session_start();
	header('Cache-Control: private, no-cache, must-revalidate');
}

include_once ADMIN_INCLUDE_DIR . '/adminRememberMe.php';
adminRememberRestore();

if (!empty($_SESSION['adminUserId'])) {
	$dest = $_GET['return'] ?? (ADMIN_DIR . '/AdminMain.php');
	header('Location: ' . $dest);
	exit;
}

$error = '';
$username = '';
$remember = false;
$returnUrl = $_POST['return'] ?? ($_GET['return'] ?? (ADMIN_DIR . '/AdminMain.php'));

// Only allow relative return paths within this site.
if (!is_string($returnUrl) || $returnUrl === '' || preg_match('#^(https?:)?//#i', $returnUrl)) {
	$returnUrl = ADMIN_DIR . '/AdminMain.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim((string) ($_POST['username'] ?? ''));
	$password = (string) ($_POST['password'] ?? '');
	$remember = !empty($_POST['remember']);

	try {
		$repo = new \Datalayer\AdminUserRepository();
		$user = $username !== '' ? $repo->findByUsername($username) : null;

		if ($user && $user->isActive && password_verify($password, (string) $user->passwordHash)) {
			session_regenerate_id(true);
			$_SESSION['adminUserId'] = $user->id;
			$_SESSION['adminUsername'] = $user->username;
			$repo->recordLogin((int) $user->id);
			adminRememberClear();
			if ($remember) {
				adminRememberIssue((int) $user->id);
			}
			header('Location: ' . $returnUrl, true, 303);
			exit;
		}

		$error = 'Invalid username or password.';
	} catch (\Throwable $e) {
		error_log('admin login failed: ' . $e->getMessage());
		$error = 'Login is temporarily unavailable. If this is a new setup, create the ADMIN_USER table and first account.';
	}
}

$sPageName = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin Login — Steve Weeks Music</title>
	<link href="<?php echo CSS_DIR; ?>/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo CSS_DIR; ?>/SWM.css" rel="stylesheet" type="text/css">
	<style>
		body { background: #f4f4f4; }
		.login-wrap { max-width: 420px; margin: 4rem auto; background: #fff; padding: 2rem; border: 1px solid #ddd; }
		.login-wrap h1 { font-size: 1.4rem; margin-top: 0; }
		.login-error { color: #a94442; margin-bottom: 1rem; }
		.login-wrap label { display: block; margin-bottom: .25rem; }
		.login-wrap input[type="text"],
		.login-wrap input[type="password"] { width: 100%; padding: .5rem; margin-bottom: 1rem; box-sizing: border-box; }
		.login-wrap button { padding: .5rem 1.25rem; }
		.remember-me { display: flex; align-items: center; gap: .5rem; margin: 0 0 1rem; }
		.remember-me input[type="checkbox"] { width: auto; margin: 0; }
		.remember-me label { display: inline; margin: 0; font-weight: normal; }
		.setup-link { margin-top: 1.25rem; font-size: .9rem; }
	</style>
</head>
<body>
	<div class="login-wrap">
		<h1>Steve Weeks Music Admin</h1>
		<?php if ($error !== '') { ?>
			<div class="login-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
		<?php } ?>
		<form id="login" method="post" action="login.php" autocomplete="on">
			<label for="username">Username</label>
			<input id="username" name="username" type="text" required autocomplete="username"
				autocapitalize="none" autocorrect="off" spellcheck="false"
				<?php if ($username !== '') { ?>
				value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
				<?php } ?>>
			<label for="current-password">Password</label>
			<input id="current-password" name="password" type="password" required autocomplete="current-password">
			<div class="remember-me">
				<input id="remember" name="remember" type="checkbox" value="1"<?php echo $remember ? ' checked' : ''; ?>>
				<label for="remember">Stay logged in on this computer</label>
			</div>
			<input type="hidden" name="return" value="<?php echo htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8'); ?>">
			<button type="submit" name="login" value="1">Log in</button>
		</form>
		<?php
		try {
			$repo = new \Datalayer\AdminUserRepository();
			if ($repo->countAll() === 0) {
				echo '<p class="setup-link"><a href="createFirstAdmin.php">Create the first admin account</a></p>';
			}
		} catch (\Throwable $e) {
			echo '<p class="setup-link">Run the ADMIN_USER SQL script, then <a href="createFirstAdmin.php">create the first admin account</a>.</p>';
		}
		?>
	</div>
</body>
</html>
