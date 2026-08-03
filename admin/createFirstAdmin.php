<?php
/*
*******************************************************************
createFirstAdmin.php
One-time setup: create the music_admin account when no users exist.
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

const FIRST_ADMIN_USERNAME = 'music_admin';

$message = '';
$error = '';
$setupAllowed = false;

try {
	$repo = new \Datalayer\AdminUserRepository();
	$setupAllowed = ($repo->countAll() === 0);
} catch (\Throwable $e) {
	$error = 'Cannot reach ADMIN_USER. Run datalayer/sql/create_admin_user.sql against musician_db first.';
	$repo = null;
}

if ($setupAllowed && $_SERVER['REQUEST_METHOD'] === 'POST') {
	$password = (string) ($_POST['password'] ?? '');
	$confirm = (string) ($_POST['confirm'] ?? '');

	if (strlen($password) < 8) {
		$error = 'Password must be at least 8 characters.';
	} elseif ($password !== $confirm) {
		$error = 'Passwords do not match.';
	} else {
		$user = new \Datalayer\AdminUser();
		$user->username = FIRST_ADMIN_USERNAME;
		$user->passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$user->isActive = true;

		if ($repo->insert($user)) {
			$message = 'Created ' . FIRST_ADMIN_USERNAME . '. You can log in now.';
			$setupAllowed = false;
		} else {
			$error = 'Failed to create the admin account.';
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Create First Admin — Steve Weeks Music</title>
	<link href="<?php echo CSS_DIR; ?>/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo CSS_DIR; ?>/SWM.css" rel="stylesheet" type="text/css">
	<style>
		body { background: #f4f4f4; }
		.wrap { max-width: 420px; margin: 4rem auto; background: #fff; padding: 2rem; border: 1px solid #ddd; }
		.wrap h1 { font-size: 1.4rem; margin-top: 0; }
		.error { color: #a94442; }
		.ok { color: #3c763d; }
		label { display: block; margin-bottom: .25rem; }
		input[type="password"] { width: 100%; padding: .5rem; margin-bottom: 1rem; box-sizing: border-box; }
		button { padding: .5rem 1.25rem; }
	</style>
</head>
<body>
	<div class="wrap">
		<h1>Create first admin</h1>
		<?php if ($error !== '') { ?>
			<p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
		<?php } ?>
		<?php if ($message !== '') { ?>
			<p class="ok"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
			<p><a href="login.php">Go to login</a></p>
		<?php } elseif ($setupAllowed) { ?>
			<p>Username will be <strong><?php echo htmlspecialchars(FIRST_ADMIN_USERNAME, ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
			<form method="post" action="createFirstAdmin.php" autocomplete="off">
				<label for="password">Password</label>
				<input id="password" name="password" type="password" required minlength="8">
				<label for="confirm">Confirm password</label>
				<input id="confirm" name="confirm" type="password" required minlength="8">
				<button type="submit">Create account</button>
			</form>
		<?php } else { ?>
			<p>An admin account already exists.</p>
			<p><a href="login.php">Go to login</a></p>
		<?php } ?>
	</div>
</body>
</html>
