<?php
	
	error_reporting(E_ALL ^ E_NOTICE);
	
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");
	
	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<META http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name="keywords" content="Steve Weeks, Children's Music, Kids Music, Kindie Music">
<meta name="description" content="Steve Weeks Music - Admin Dashboard">
<meta name="title" content="Steve Weeks Music - Admin Dashboard">
<meta property="og:type" content="article" />
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=0">
<TITLE>Steve Weeks Music - Admin Dashboard</TITLE>
<!-- customized Boostrap - http://getbootstrap.com/customize/?id=1aadfb3bf645d83ccfc7ae41f22ed034 -->
<link rel="stylesheet" href="<?php echo $ROOT; ?>/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo $ROOT; ?>/css/bootstrap-theme.min.css">

<!-- Google Fonts -->
<link href='https://fonts.googleapis.com/css?family=Amatic+SC:400,700' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Boogaloo' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<link href="https://fonts.googleapis.com/css?family=Sue+Ellen+Francisco" rel="stylesheet">

<LINK HREF="<?php echo $ROOT; ?>/css/swm.css" type="text/css" rel="StyleSheet">
<LINK REL="SHORTCUT ICON" HREF="http://www.steveweeksmusic.com/SWM.ICO">
<link href="iPhone-Icon-Sun-60x60.png" rel="apple-touch-icon" />
<link href="iPhone-Icon-Sun-76x76.png" rel="apple-touch-icon" sizes="76x76" />
<link href="iPhone-Icon-Sun-120x120.png" rel="apple-touch-icon" sizes="120x120" />
<link href="iPhone-Icon-Sun-152x152.png" rel="apple-touch-icon" sizes="152x152" />
<!-- Adds HTML5 element support for browsers older than IE9 -->
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![end if]-->
<!-- Include jQuery library -->
<?php 
//Include compresssed version of jQuery for Production or uncompressed version for other environments
if (ENVIRONMENT == "PRODUCTION")
{
	echo "<script type=\"text/javascript\" src=\"" . JS_DIR . "/jquery-2.2.0.min.js\"></script>";
} 
else
{
	echo "<script type=\"text/javascript\" src=\"" . JS_DIR . "/jquery-2.2.0.min.js\"></script>";
}
?>

</HEAD>

<body>
TEST
</body>
