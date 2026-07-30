<!-- HTML <HEAD> SECTION (HTMLHead.php) -->
<?php 
if(empty($sFBImage))
{
	$sFBImage = "FB_Logo.png";
}

//randomly assign a theme
$nThemeNumber = mt_rand (1,2);

switch ($nThemeNumber) {
    case 1:
		$sThemeName = "giraffe";
        break;
    case "2":
		$sThemeName = "dinosaur";
	    break;
    default:
		$sThemeName = "giraffe";
}

?>
<HEAD>
<META http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name="keywords" content="Steve Weeks, <?php echo "$sPageName" ?>, Children's Music, Kids Music, Kindie Music">
<meta name="description" content="Steve Weeks Music - <?php echo "$sPageName" ?>">
<meta name="title" content="Steve Weeks Music - <?php echo "$sPageName" ?>">
<meta property="og:type" content="article" />
<meta property="og:image" content="http://www.steveweeksmusic.com/images/<?php echo $sFBImage; ?>" />
<meta property="og:title" content="Steve Weeks Music - <?php echo "$sPageName" ?>" />
<meta property="og:description" content="<?php if (isset($sPageDescription)) { echo "$sPageDescription";} else {echo "$sPageName";} ?>" />
<meta property="og:site_name" content="Steve Weeks Music" />
<meta property="og:url" content="http://<?php echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=0">
<TITLE>Steve Weeks Music - <?php echo "$sPageName" ?></TITLE>
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
<link rel="image_src" href="http://www.steveweeksmusic.com/images/<?php echo $sFBImage; ?>" />
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
<!-- END HTML <HEAD> SECTION (HTMLHead.php) -->
