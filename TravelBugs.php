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
	$sPageName = "Agree to Disagree";
	$sPageTitle = "Agree to Disagree";
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
			<aside class="col-xs-12 col-sm-10 col-sm-push-1">
				<div class="orange-frame frame-box in-front-of-theme-image">
					<div class="thanks">
					Below is everything you should need to work on "Agree to Disagree".  
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12">
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header yellow-box">
						<a name="Lyrics">
							Lyrics and Tab
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-4 col-md-2 col-sm-push-2 col-md-push-4">
						<div class="presskit-document">
						<A HREF="<?php echo PDF_DIR ?>/AgreeToDisagree.pdf" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/AgreeLyrics-icon.gif" BORDER=0><br><br>
						<b>Lyrics</b></A>	
						</div>
					</div>
					<div class="col-xs-6 col-sm-4 col-md-2 col-sm-push-2 col-md-push-4">
						<div class="presskit-document">
						<A HREF="<?php echo PDF_DIR ?>/AgreeToDisagreeHarmonies.pdf" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/AgreeHarmonies-icon.gif" BORDER=0><br><br>
						<b>Harmony Tabs</b></A>	
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
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/AgreeToDisagreeDemo.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>"Agree to Disagree" Demo (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/AgreeToDisagreeDemo.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>"Agree to Disagree" Demo (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/AgreeToDisagreeNoVox.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>"Agree to Disagree" no vox (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/AgreeToDisagreeNoVox.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>"Agree to Disagree" no vox (WAV)</b></A>	
						</div>
					</div>					
				</div>		
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header red-box">
						<a name="stream">
							Stream Music
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-6">
						<div class="frame-box green-frame">
							<h2 class="listen">&nbsp;&nbsp;Agree to Disagree Demo</h2>
							<audio controls
							  data-info-att="Music: Steve Weeks"
							  data-info-att-link="https://www.steveweeksmusic.com">
							  <source src="<?php echo MP3_DIR;?>/AgreeToDisagreeDemo.mp3" type="audio/mpeg" />
							  <a href="<?php echo MP3_DIR;?>/AgreeToDisagreeDemo.mp3">Agree to Disagree Demo</a>
							  An html5-capable browser is required to play this audio. 
							</audio>		
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="frame-box green-frame">
							<h2 class="listen">&nbsp;&nbsp;Agree to Disagree (no vox)</h2>
							<audio controls
							  data-info-att="Music: Steve Weeks"
							  data-info-att-link="https://www.steveweeksmusic.com">
							  <source src="<?php echo MP3_DIR;?>/AgreeToDisagreeNoVox.mp3" type="audio/mpeg" />
							  <a href="<?php echo MP3_DIR;?>/AgreeToDisagreeNoVox.mp3">Agree to Disagree (no vox)</a>
							  An html5-capable browser is required to play this audio. 
							</audio>		
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
