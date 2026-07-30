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

	//include Review Class
	include_once (CLASS_DIR . "/class_Review.php");

	//include Award Class
	include_once (CLASS_DIR . "/class_Award.php");

	//Page Name 
	$sPageName = "Photos";
	$sPageTitle = "Steve Weeks - Photos";
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
			//include(INCLUDE_DIR . "/header.php");
		?>		
		</div>
		<div class="row">
			<aside class="col-xs-12 col-sm-10 col-sm-push-1">
				<div class="orange-frame frame-box in-front-of-theme-image">
					<div class="thanks">
					High res photos    
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12">			
				<div class="row">
					<div class="col-xs-12 col-md-6 full-width-xs">
						<img src="<?php echo IMG_DIR ?>/performance/performance1.jpg" style="width: 100%; margin-top: 15px;">
					</div>
					<div class="col-xs-12 col-md-6 full-width-xs">
						<img src="<?php echo IMG_DIR ?>/performance/performance2.jpg" style="width: 100%; margin-top: 15px;">
					</div>
					<div class="col-xs-12 col-md-6 full-width-xs">
						<img src="<?php echo IMG_DIR ?>/performance/performance3.jpg" style="width: 100%; margin-top: 15px;">
					</div>
					<div class="col-xs-12 col-md-6 full-width-xs">
						<img src="<?php echo IMG_DIR ?>/performance/performance4.jpg" style="width: 100%; margin-top: 15px;">
					</div>
				</div>
				
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
