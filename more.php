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
					<div class="col-xs-12 col-sm-8 col-lg-6 col-sm-push-2 col-lg-push-3">
						<div class="frame-box orange-frame in-front-of-theme-image">
							<div class="thanks">
								<div class="thanks-title">Have a look Around...</div>
								<p>
									Below are some links to <em>ODDS & ENDS</em> about my music.&nbsp;&nbsp;Feel free to browse a little and if you need something <em><a href="<?php $ROOT ?>/contact.php">JUST ASK</a></em>. 
								</p>	
							</div>
						</div>
					</div>
				</div>	
				<section class="row showcase-section">
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/about.php"; ?>">
							<div class="green-box showcase-box more-box behind-theme-image in-front-of-theme-image-xs avoid-theme-sm avoid-theme-md">
								<figure class="showcase-image float-right">
									<img src="<?php echo IMG_DIR ?>/about-thumbnail.png">
								</figure>
								<div class="more-text">
									<div class="more-title">About Steve</div>
									<div class="more-details">
										Steve's Biography & More...  
									</div>	
								</div>
							</div>						
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/lyrics.php"; ?>">
							<div class="purple-box showcase-box more-box behind-theme-image avoid-theme-xs avoid-theme-sm avoid-theme-md">
								<figure class="showcase-image float-left">
									<img src="<?php echo IMG_DIR ?>/lyrics-thumbnail.png">
								</figure>
								<div class="more-text">
									<div class="more-title">Lyrics</div>
									<div class="more-details">
										Looking for lyrics or tabs?<br>You can find them here.  
									</div>	
								</div>
							</div>						
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/awards.php"; ?>">
							<div class="yellow-box showcase-box more-box in-front-of-theme-image">
								<figure class="showcase-image float-right">
									<img src="<?php echo IMG_DIR ?>/awards-thumbnail.png">
								</figure>
								<div class="more-text">
									<div class="more-title">Awards</div>
									<div class="more-details">
										See Steve's credentials...  
									</div>	
								</div>
							</div>						
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/reviews.php"; ?>">
							<div class="blue-box showcase-box more-box behind-theme-image avoid-theme-xs avoid-theme-sm">
								<figure class="showcase-image float-left">
									<img src="<?php echo IMG_DIR ?>/reviews-thumbnail.png">
								</figure>
								<div class="more-text">
									<div class="more-title">Reviews</div>
									<div class="more-details">
										What are people saying?  
									</div>	
								</div>
							</div>		
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/news.php"; ?>">
							<div class="red-box showcase-box more-box behind-theme-image in-front-of-theme-image-xs avoid-theme-sm avoid-theme-md">
								<figure class="showcase-image float-right">
									<img src="<?php echo IMG_DIR ?>/news-thumbnail.png">
								</figure>
								<div class="more-text">
									<div class="more-title">News</div>
									<div class="more-details">
										Check out the latest news!  
									</div>	
								</div>
							</div>		
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/music.php"; ?>">
							<div class="orange-box showcase-box more-box behind-theme-image avoid-theme-xs avoid-theme-sm">
								<figure class="showcase-image float-left">
									<img src="<?php echo IMG_DIR ?>/music-thumbnail.png">
								</figure>
								<div class="more-text">
									<div class="more-title">The Music</div>
									<div class="more-details">
										Award winning CDs and singles
									</div>	
								</div>
							</div>		
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/photo-video.php"; ?>">
							<div class="green-box showcase-box more-box in-front-of-theme-image">
								<figure class="showcase-image float-right">
									<img src="<?php echo IMG_DIR ?>/photos-thumbnail.png">
								</figure>
								<div class="more-text">
									<div class="more-title">Photos & Videos</div>
									<div class="more-details">
										Music videos, performance images<br class="visible-lg"> and more 
									</div>	
								</div>
							</div>		
						</a>
					</div>
				</section>
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
