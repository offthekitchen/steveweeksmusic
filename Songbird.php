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
	$sPageName = "Songbird";
	$sPageTitle = "Steve Weeks - Songbird";
	//Custom FB Image
	$sFBImage = "FB_Songbird.png";


	$sActiveMenuItem = ABOUT_ACTIVE;	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
?>

<STYLE>
@font-face {
    font-family: GraublauWeb;
    src: url("path/GraublauWeb.otf") format("opentype");
}
</STYLE>

<BODY>
	<div class="container-fluid">
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/header.php");
		?>		
		</div>
		<div class="row">
			<aside class="col-xs-12 col-sm-10 col-sm-push-1">
				<div class="orange-frame frame-box in-front-of-theme-image">
					<div class="thanks">
					Below is the art, supporting documentation and audio files for the single "Songbird" (MP3 and WAV).    
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12">
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header red-box">
						<a name="Cover Art">
							Cover Art
						</a>
						</header>
						<div class="row">
							<div class="col-xs-12" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/SongbirdCover.png"><br>
							<b>Artwork</b>
							</div>
						</div>					
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header purple-box">
						<a name="Music">
							Download Music
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/Songbird/Songbird.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>Songbird MP3 (320KBps)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/Songbird/Songbird.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>Songbird WAV 44 16-bit</b></A>	
						</div>
					</div>
				</div>		
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header blue-box">
						<a name="contact">
							Contact Info
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-8 col-md-push-2">
						<div class="frame-box red-frame">
							<div class="contact">
								<div class="email">Email:  <a href="mailto:steve@steveweeksmusic.com">steve@steveweeksmusic.com</a></div>
								<div class="email">Phone:  719-640-9986</div><br>
							</div>
						</div>								
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
