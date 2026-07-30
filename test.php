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
	$sPageName = "More";
	//Page Name 
	$sPageTitle = "Find Out More...";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('More','more.php'),
		); 

	$sActiveMenuItem = HOME_ACTIVE;	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
?>

<BODY>
	<div class="container-fluid">
		<div class="row"  style="height: 150px;">
		</div>
		<div class="row">
			<main id="main-content" class="col-xs-12">
				<section class="row showcase-section">
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/about.php"; ?>">
							<div class="green-box showcase-box behind-theme-image in-front-of-theme-image-xs avoid-theme-sm avoid-theme-md" style="height: 150px;">
								<center> <font face="Arial, Helvetica, sans-serif">GREEN DARK WITH LIGHT BORDER ROUNDED</font> 
								</center>	
							</div>						
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/about.php"; ?>">
							<div class="yellow-box showcase-box behind-theme-image in-front-of-theme-image-xs avoid-theme-sm avoid-theme-md" style="height: 150px;">
								<center> <font face="Arial, Helvetica, sans-serif">YELLOW WITH DARK BORDER ROUNDED</font> 
								</center>	
							</div>						
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/about.php"; ?>">
							<div class="red-box " style="height: 150px;">
								<center> <font face="Arial, Helvetica, sans-serif" size="+5">RED WITH NO BORDER</font> 
								</center>	
							</div>						
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/about.php"; ?>">
							<div class="frame-box orange-frame in-front-of-theme-image"  style="height: 150px;">
							<div class="thanks">
								<center><font face="Arial, Helvetica, sans-serif">WHITE WITH THIN ORANGE BORDER ROUNDED</font>
								</center>	
							</div>
						</div>				
						</a>
					</div>
				</section>
			</main>
		</div>
	</div>
</BODY>
</HTML>
