<?php
//This include defines the relative path to the root directory from this sub-directory
include_once ("root.inc.php");

//inlcude web site settings
include_once ($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

// recaptcha library
require_once (INCLUDE_DIR . "/recaptchalib.php");

//initialize captcha error flag
$captcha_error = FALSE;

//turn off error reporting so that we get a simple error code return
error_reporting(0);

// empty response
$response = null;
// check secret key

//echo "KEY: " . RECAPTCHA_SECRET_KEY . " SERVER: {$_SERVER["REMOTE_ADDR"]} RESPONSE: {$_POST["g-recaptcha-response"]}";
$reCaptcha = new ReCaptcha(RECAPTCHA_SECRET_KEY);

$response = $reCaptcha->verifyResponse(
	$_SERVER["REMOTE_ADDR"],
	$_POST["g-recaptcha-response"]
);

//If the captcha check failed, set the error flag
if ($response == null || !$response->success) 
{
	$captcha_error = TRUE;
}
	
//If no captcha error was detected attempt to send email	
if (!$captcha_error) {

	//Check for required fields to prevent direct calls to this page
	if(empty($_POST["name"]) || empty($_POST["email"]) || empty($_POST["message"]))
	{
		echo "error";
	}
	else
	{
		//TODO: Need to get steveweeksmusic.com receiving emails from form
		$email_to = "steve@steveweeksmusic.com";
		$email_from = "steve@steveweeksmusic.com";
		$subject = "Contact From steveweeksmusic.com website";
	
		$text = "Name: " . $_POST["name"] . "\n";
		$text .= "Email: " . $_POST["email"] . "\n";
		$text .= "Message: " . $_POST["message"];
	
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/plain; charset=utf-8" . "\r\n";
		$headers .= "From: '" . $_POST['name'] . "' <".$email_from."> " . "\r\n";
		
		$result = mail($email_to, $subject, $text, $headers);
	
		//Email Failure
		if(!$result) {
			echo "email-failure";
		}
		else
		{
			//Success
			echo "success";	
		}
	}
}
else {
	//Recaptcha Failure
	echo "recaptcha-failure";
}
?>