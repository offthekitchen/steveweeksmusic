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
	$sPageName = "Photos and Videos";
	//Page Name 
	$sPageTitle = "Photos and Videos";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('Photos & Videos','photos.php'),
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
	<?php include(INCLUDE_DIR . "/theme-image.php"); ?>
	<div class="container-fluid">
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/header.php");
		?>		
		</div>
		<div class="row">
			<main id="main-content" class="col-xs-12">
				<div class="row">
					<div class="col-xs-12"><h1 class="page-subheader">Photos</h1></div>
					<div class="col-xs-12 col-sm-8 col-sm-push-2 col-md-6 col-md-push-3">
						<div class="frame-box in-front-of-theme-image orange-frame " >
						<?php
						   include (INCLUDE_DIR . "/photo-carousel.php");
						?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12"><h1 class="page-subheader">Videos</h1></div>
					<div class="col-xs-12 col-md-8 col-md-push-2">
						<div class="in-front-of-theme-image frame-box blue-frame" >
							<article class="video-item">
								<header>
									<h3>Change of Heart Video</h3>
								</header>
								<a href="https://youtu.be/aCJDjGh5d7g" target="_blank">
									<div class="video-play-icon"><img src="<?php echo IMG_DIR; ?>/play-video.png"></div>
									<figure class="dynamic-width">
										<img src="<?php echo IMG_DIR; ?>/change-of-heart-still.jpg">
									</figure>
								</a>
								<div class="video-text-overlay">
								What do you get when you put musician Steve Weeks and director Dave Franklyn in a room filled with cardboard, paint, tape, glue, scissors and string?&nbsp;&nbsp;Watch the new video for Steve's song "Change of Heart" and find out! 
								</div>
							</article>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-8 col-md-push-2">
						<div class="yellow-box in-front-of-theme-image" >
							<article class="video-item">
								<header>
									<h3>Bartleby Finkleton Will Not Take a Bath Video</h3>
								</header>
								<a href="https://youtu.be/HEEgHRvGxvo" target="_blank">
									<div class="video-play-icon"><img src="<?php echo IMG_DIR; ?>/play-video.png"></div>
									<figure class="dynamic-width">
										<img src="<?php echo IMG_DIR; ?>/bartleby-still.jpg">
									</figure>
								</a>
								<div class="video-text-overlay">
								Check out the video for Steve's hit song about Bartley Finkleton, a kid who will never, ever, ever, ever take a bath! 
								</div>
							</article>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-8 col-md-push-2">
						<div class="in-front-of-theme-image frame-box blue-frame" >
							<article class="video-item">
								<header>
									<h3>About Steve's Performance</h3>
								</header>
								<?php 
									renderBookingVideo();
								?>
								<div class="video-text-overlay hidden-lg">
								Need some family-friendly music at your next event?&nbsp;&nbsp;Steve has travelled all over the US and Canada entertaining kids and their grown-ups with his 
								high-energy, interactive show of original music, humor and games. 
								</div>
							</article>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-8 col-md-push-2">
						<div class="yellow-box in-front-of-theme-image" >
							<article class="video-item">
								<header>
									<h3>Steve Weeks Live @ Rifle Public Library</h3>
								</header>
								<a href="https://videoplayer.telvue.com/player/P8wyVTR2qr3_LDUHnb_mF4AFW6RckIeS/media/363905?autostart=true&showtabssearch=false&fullscreen=false" target="_blank">
									<div class="video-play-icon"><img src="<?php echo IMG_DIR; ?>/play-video.png"></div>
									<figure class="dynamic-width">
										<img src="<?php echo IMG_DIR; ?>/rifle-performance-still.png">
									</figure>
								</a>
								<div class="video-text-overlay">
								Full performance in Rifle, CO produced by Rifle Community TV
								</div>
							</article>
						</div>
					</div>
				</div>
			</main>
		</div>
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/footer.php");
		?>		
		<script>
		//******************************************
		// alternate classes on Awards and Singles
		//******************************************
		$( document ).ready(function() 
		{
			//Set alternating classes on Awards and singles
			$("#award-list aside.award-listing:odd").addClass("green-frame frame-box in-front-of-theme-image");
			$("#award-list aside.award-listing:even").addClass("purple-frame frame-box behind-theme-image avoid-theme");
		})
		</script>
		</div>
	</div>
</BODY>
</HTML>
