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
	$sPageName = "GeoPod Norge";
	$sPageTitle = "GeoPod Norge";
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
					Below are the clips for "GeoPod Norge".  There are WAV and MP3 versions of each clip.&nbsp;&nbsp;The clips are (in order):
					<ol>
						<li>Intro with the tagline "Din Podcast om Geocaching"</li>
						<li>Intro with the tagline "Alt om Geocaching"</li>
						<li>Bumper with no tagline</li>
						<li>Bumper with the tagline "Din Podcast om Geocaching"</li>
						<li>Bumper with the tagline "Alt om Geocaching"</li>
						<li>Bumper with a guest (Steve as example)</li>
						<li>Melody Signature</li>
					</ol>
                   		
                </div>
			</aside>
			<main id="main-content" class="col-xs-12">
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
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeIntro2.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Intro "Din Podcast om Geocaching"(MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeIntro2.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Intro "Din Podcast om Geocaching" (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeIntro3.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Intro "Alt om Geocaching" (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeIntro3.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Intro "Alt om Geocaching" (WAV)</b></A>	
						</div>
					</div>					
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeBumper1.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Bumper with no tagline  (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeBumper1.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Bumper with no tagline </b></A>	
						</div>
					</div>	
                    <div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeBumper2.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Bumper "Din Podcast om Geocaching" (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeBumper2.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Bumper "Din Podcast om Geocaching" (WAV)</b></A>	
						</div>
					</div>			
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeBumper3.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Bumper  "Alt om Geocaching"  (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeBumper3.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Bumper "Alt om Geocaching" (WAV)</b></A>	
						</div>
					</div>	
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeBumper4.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Bumper Steve Weeks (WAV)</b></A>	
						</div>
					</div>			
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeBumper4.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Bumper Steve Weeks  (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeoPodNorgeSignature.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>GeoPod Norge Signature (WAV)</b></A>	
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
