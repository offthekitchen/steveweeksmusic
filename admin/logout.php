<?php
/*
*******************************************************************
logout.php
End the current admin session.
*******************************************************************
*/

define('ADMIN_AUTH_SKIP', true);

include_once('root.inc.php');
include_once($ROOT . '/includes/websiteSettings.php');
include_once(SETTINGS_DIR . '/SteveWeeksMusicSettings.php');
include_once(ADMIN_DIR . '/includes/AdminSettings.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
}
session_destroy();

header('Location: ' . ADMIN_DIR . '/login.php');
exit;
