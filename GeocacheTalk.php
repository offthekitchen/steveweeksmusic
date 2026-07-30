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
	$sPageName = "Geocache Talk";
	$sPageTitle = "Geocache Talk";
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
					Below are the clips for "Geocache Talk".  There are WAV and MP3 versions of each clip.  The first is the theme song.  There is a beat break in the middle intended for you to be able to say something about the show if you want like, "Welcome to Episode 20 of Geocache Talk.  Today we'll be talking about virtual caches".  The second clip is the same as the first with no vocals and can be used as an extro to the show.  The third is a much softer version instrumental version of the song.  It's a little slower, longer and more like easy listening background music.  
					</div>
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
						<A HREF="<?php echo MP3_DIR ?>/GeocacheTalkTheme.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>"Geocache Talk" Theme (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeocacheTalkTheme.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>"Geocache Talk" Theme (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeocacheTalkExtroRockin.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>Geocache Talk" Extro - Rockin (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeocacheTalkExtroRockin.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>Geocache Talk" Extro - Rockin (WAV)</b></A>	
						</div>
					</div>					
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeocacheTalkExtroSoft.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>Geocache Talk" Extro - Easy Listening (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/GeocacheTalkExtroSoft.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>Geocache Talk" Extro - Easy Listening (WAV)</b></A>	
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
