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
	$sPageName = "Wish You Were Here";
	$sPageTitle = "Wish You Were Here";
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
					Below is the cover art, supporting documentation and songs from the "Wish You Were Here" CD (MP3 and WAV).    
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
							<div class="col-xs-12 col-lg-4" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/WishYouWereHereCover.png"><br>
							<b>FRONT COVER</b>
							</div>
							<div class="col-xs-12 col-lg-4" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/WishYouWereHereBackCover.png"><br>
							<b>BACK COVER</b>
							</div>
							<div class="col-xs-12 col-lg-4" style="text-align: center;">
							<img src="<?php echo IMG_DIR ?>/WishYouWereHereCDFace.png"><br>
							<b>CD Face</b>
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
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/WishYouWereHere.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>1. Wish You Were Here (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/WishYouWereHere.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>1. Wish You Were Here (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/ChangeOfHeart.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>2. Change of Heart (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/ChangeOfHeart.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>2. Change of Heart (WAV)</b></A>	
						</div>
					</div>		
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/OpenYourselfUp.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>3. Open Yourself Up (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/OpenYourselfUp.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>3. Open Yourself Up (WAV)</b></A>	
						</div>
					</div>		
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/TinyHouse.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>4. Tiny House (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/TinyHouse.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>4. Tiny House (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/RunOn.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>5. Run-On (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/RunOn.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>5. Run-On (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/MondayIWokeUpPurple.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>6. Monday I Woke Up Purple (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/MondayIWokeUpPurple.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>6. Monday I Woke Up Purple (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/ARaindropForMe.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>7. A Raindrop For Me (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/ARaindropForMe.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>7. A Raindrop For Me (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/GetUp.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>8. Get Up (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/GetUp.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>8. Get Up (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/BabyStartedDancing.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>9. Baby Started Dancing (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/BabyStartedDancing.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>9. Baby Started Dancing (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/SongBackwardsThe.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>10. Song Backwards The (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/SongBackwardsThe.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>10. Song Backwards The (WAV)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/ALittleSunshine.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
						<b>11. A Little Sunshine (MP3)</b></A>	
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="presskit-document">
						<A HREF="<?php echo MP3_DIR ?>/WishYouWereHere/ALittleSunshine.wav" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/wav-icon.png" BORDER=0><br><br>
						<b>11. A Little Sunshine (WAV)</b></A>	
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
