<?php
/*
*******************************************************************
requireAuth.php
Require a logged-in admin session for SWM /admin pages.
Included automatically from AdminSettings.php unless ADMIN_AUTH_SKIP is set.
*******************************************************************
*/

if (session_status() !== PHP_SESSION_ACTIVE) {
	$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
	session_set_cookie_params([
		'lifetime' => 0,
		'path' => '/',
		'secure' => $secure,
		'httponly' => true,
		'samesite' => 'Lax',
	]);
	session_start();
}

if (defined('ADMIN_INCLUDE_DIR')) {
	include_once ADMIN_INCLUDE_DIR . '/adminRememberMe.php';
	adminRememberRestore();
}

if (!empty($_SESSION['adminUserId'])) {
	return;
}

$loginUrl = (defined('ADMIN_DIR') ? ADMIN_DIR : '.') . '/login.php';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if ($requestUri !== '') {
	$loginUrl .= '?return=' . rawurlencode($requestUri);
}

// AJAX / script callers get 401 instead of an HTML login redirect.
$requestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
$accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
$script = basename($_SERVER['SCRIPT_NAME'] ?? '');
$isAjax = ($requestedWith === 'xmlhttprequest')
	|| str_starts_with($script, 'GetPotential')
	|| str_contains($accept, 'application/json');

if ($isAjax) {
	http_response_code(401);
	header('Content-Type: text/plain; charset=UTF-8');
	echo 'Unauthorized';
	exit;
}

header('Location: ' . $loginUrl);
exit;
