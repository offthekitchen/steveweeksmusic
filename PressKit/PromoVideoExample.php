<?php
	//Don't Display Notice messages from PHP Server
	error_reporting(E_ALL ^ E_NOTICE);

	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//Common Functions
   	include (INCLUDE_DIR . "/commonFunctions.php");

	//Page Name 
	$sPageName = "Promo Video Example";
	$sPageTitle = "Promo Video Example";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";


	$sActiveMenuItem = ABOUT_ACTIVE;	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
?>

<BODY>
	<div class="container-fluid">
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/header.php");
		?>		
		</div>
		<div class="row">
			<main id="main-content" class="col-xs-10 col-xs-push-1 col-sm-8 col-sm-push-2">

			<?php 
			renderPromoVideo();
			/*$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
			$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
			$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
			$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
			$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
			//Detect special conditions devices
			if( $iPod || $iPhone  || $iPad){
				echo "<a href=\"" . VIDEO_DIR . "/PerformancePromotionExampleIOS.mp4\">";
				echo  "<img src=\"" . IMG_DIR . "/about-performance-with-play-icon.jpg\" width=\"100%\"/></a>";
				echo "<div>THIS IS IPHONE</div>";
			}
			else{
				echo "<video width=\"100%\" controls=\"true\"  poster=\"" . IMG_DIR . "/about-performance.jpg\">";
				echo "<source src=\"" . VIDEO_DIR . "/PerformancePromotionExample.mp4\" type=\"video/mp4\">";
				echo "</video>";
			}	*/			
			?>
			</main>
		</div>
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/footer.php");
		?>		
		</div>
	</div>
</BODY>
</HTML>
