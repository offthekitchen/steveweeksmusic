<?php
//Don't Display Notice messages from PHP Server
error_reporting(E_ALL ^ E_NOTICE);

//This include defines the relative path to the root directory from this sub-directory
include_once("root.inc.php");

//inlcude web site settings
include_once($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once(SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//Common Functions
include(INCLUDE_DIR . "/commonFunctions.php");

$sPageName = "Weeks Elliptical Photos - Request 81161";
$sPageDescription = "Weeks Elliptical Photos - Request 81161";
$sPageTitle = "Weeks Elliptical Photos - Request 81161";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
//Inlcude a common <HEAD> section
include(INCLUDE_DIR . "/HTMLHead.php");
?>
<link href="<?php echo CSS_DIR; ?>/bootstrap3_player.css" rel="stylesheet">

<BODY>
<h1><?php echo $sPageTitle; ?></h1>
	<div class="container-fluid">

		<div class="row">
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/10.png"><br>
                <i>R06 (TOP)<BR>T07 (BOTTOM)</i>
            </div>
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/11.png"><br>
                <i>Large Wheel<BR><BR></i>
            </div>          
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/12.png"><br>
                <i>R06 (TOP)<BR>T07 (MIDDLE)<BR>Large Wheel (BOTTOM)</i>
            </div>                 
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/8.png"><br>
                <i>New and Old Belt</i>
            </div>
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/9.png"><br>
                <i>New and Old Belt (inside)</i>
            </div>
			<div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/1.png"><br>
                <i>Power Adapter</i>
            </div>

			<div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/2.png"><br>
                <i>model tag</i>
            </div>
            <div class="col-xs-12"></div>
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/3.png"><br>
                <i>motor</i>
            </div>
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/4.png"><br>
                <i>control screen</i>
            </div>
            <div class="col-xs-12"></div>
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/5.png"><br>
                <i>broken belt</i>
            </div>
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/6.png"><br>
                <i>Unit from Rear</i>
            </div>
            <div class="col-xs-12"></div>
            <div class="col-xs-12 col-md-6 image">
                <img src="<?php echo IMG_DIR; ?>/elliptical/7.png"><br>
                <i>Unit from side</i>
            </div>

        </div>
	</div>
</BODY>

</HTML>
<style>
    .image {
        
    }
    img{
        max-height: 400px;
        max-width: 420px;
    }
</style>