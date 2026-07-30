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
	$sPageName = "HFVS 2022";
	$sPageTitle = "HFVS - Steve Weeks";
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
					Below is the supporting documentation and audio files for HFVS 2022 Episode (MP3 and WAV).    
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12">
			<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header purple-box">
						<a name="Info">
							Short Description
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
					Join guest host and award-winning musician Steve Weeks in his woodshop as he works on his bike and shares a few family-friendly tunes on the Hilltown Family Variety Show.&nbsp;&nbsp;He could really use your help!
					</div>				
				</div>		
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header purple-box">
						<a name="Info">
							Setlist
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
					<pre>
                    Gustafer Yellowgold – “Feel Your Shine” [I'm From the Sun] 
                    Steve Weeks – “Songbird” 
                    The Sugar Bears - “All of My Life” [Presenting the Sugar Bears]
                    Joanie Leeds – “Anything” [All the Ladies]
                    Steve Weeks – “Agree to Disagree (feat. Joanie Leeds)”
                    Glenn Phillips – “The Easy Ones” [Swallowed by the New] 
                    Casper Babypants – “Ducky is the Name of My Bike” [Flying High!]
                    Peter Mulvey - “Lila Blue” [The Knuckleball Suite]
                    Steve Weeks – “My Family”
                    XTC – “Ballet for a Rainy Day” [Skylarking]
                    Mungo Jerry – “Pushbike Song” [Pushbike Song]
                    Wilco – “Kamera” [Yankee Hotel Foxtrot]
                    Steve Weeks – “Miss Bunnyfeet and Robot Mike” [Once I Lived Upon the Sea]

                    </pre>
					</div>				
				</div>		
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header purple-box">
						<a name="Music">
							Download Episode
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/HFVS2022.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>HFVS Episode MP3 (320KBps)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/HFVS2022.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>HFVS Episode WAV</b></A>	
						</div>
					</div>
				</div>		
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header red-box">
						<a name="Cover Art">
							STILLS
						</a>
						</header>
						<div class="row">
						<div class="col-xs-12 col-md-6" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/Woodshop1.jpg"><br>
							<b>Woodshop</b>
							</div>
							<div class="col-xs-12 col-md-6" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/Woodshop5.jpg"><br>
							<b>Woodshop</b>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-md-6" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/MyFamilyCover.png"><br>
							<b>My Family</b>
							</div>
							<div class="col-xs-12 col-md-6" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/Woodshop2.jpg"><br>
							<b>Woodshop</b>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-md-6" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/AgreeToDisagreeCover.png"><br>
							<b>Agree to Disagree</b>
							</div>
							<div class="col-xs-12 col-md-6" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/Songbird-45-cover.png"><br>
							<b>Songbird</b>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-md-6" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/Woodshop3.jpg"><br>
							<b>Woodshop</b>
							</div>

							<div class="col-xs-12 col-md-6" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/Woodshop4.jpg"><br>
							<b>Woodshop</b>
							</div>
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
