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
	$sPageName = "Press Kit";
	$sPageTitle = "Press Kit";
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
					I provide a 45 minute to 1 hour interactive show filled with music, humor and games for young kids and their families.  I've performed at a wide variety of venues in <a href="http://www.offthekitchen.com/music/performance/memories-of-50-states/" target="_blank">all 50 United States</a>, Europe and Canada including:<br>
					<div class="row">
					<div class="col-xs-10 col-xs-push-1 col-md-5 col-md-push-1">
					<ul>
					<li>Symphony Space, NYC, NY</li>
					<li>Jammin Java, Vienna, VA</li>
					<li>Denver Botanic Gardens, Denver CO</li>
					<li>Akureyri Backpackers, Akureyri, Iceland</li>
					</ul>
					</div>
					<div class="col-xs-10 col-xs-push-1 col-md-5 col-md-push-1">
					<ul>
					<li>Rivers and Spires Festival, Clarksville, TN</li>
					<li>World Cafe Live at the Queen, Wilmington, DE</li>
					<li>The Fox Theater, North Platte, NE</li>
					<li>The Old Nag's Head, Manchester, UK</li>
					</ul>
					</div>
					<div class="col-xs-10 col-xs-push-1 col-md-5 col-md-push-1">
					</div>
					</div>
					My music is in frequent rotation on Sirius XM satellite radio (Kids Place Live) where I've had multiple #1 hits.  I've won the children's music category of the US Songwriting Competition as well as various parenting and music awards.   
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12">
				<section class="row showcase-section">
					<div class="col-xs-12 col-sm-6 col-lg-2 col-lg-push-1">
						<a href="#images">
							<div class="showcase-box presskit-showcase behind-theme-image yellow-box">
								Videos <br class="visible-lg">& Images
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-2 col-lg-push-1">
						<a href="#documents">
							<div class="showcase-box presskit-showcase behind-theme-image purple-box">
								Documents
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-2 col-lg-push-1">
						<a href="#promo-materials">
							<div class="showcase-box presskit-showcase in-front-of-theme-image green-box">
								Promotional Materials
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-2 col-lg-push-1">
						<a href="#music">
							<div class="showcase-box presskit-showcase behind-theme-image red-box">
								Music
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-2 col-sm-push-3 col-lg-push-1">
						<a href="#press">
							<div class="showcase-box presskit-showcase behind-theme-image blue-box">
								Press & Contact Info
							</div>
						</a>
					</div>

				</section>
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header yellow-box">
						<a name="images">
							Videos & Images
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-6" style="padding-bottom: 15px;">
					<?php renderBookingVideo(); ?>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="row">
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksHiRes1.jpg"><IMG SRC="<?php echo IMG_DIR ?>/HiRes1Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>
								</div>								
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksHiRes2.jpg"><IMG SRC="<?php echo IMG_DIR ?>/HiRes2Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>
								</div>					
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksHiRes3.jpg"><IMG SRC="<?php echo IMG_DIR ?>/HiRes3Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>			
								</div>		
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksHiRes4.jpg"><IMG SRC="<?php echo IMG_DIR ?>/HiRes4Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>			
								</div>		
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/OnceILivedFrontCoverHi-Res.jpg"><IMG SRC="<?php echo IMG_DIR ?>/OnceILivedFrontCoverThumb.jpg" BORDER=0><br>
								<small>Front Cover RGB</small></A>	
								</div>
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="<?php echo IMG_DIR ?>/ChangeOfHeartStill.jpg"><IMG SRC="<?php echo IMG_DIR ?>/ChangeOfHeartStillThumb.jpg" alt="Change of Heart Still" BORDER=0 height="75px;">
								<br><small>Change of Heart <br>
							Still </small></A>	
								</div>
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksPerformance5.jpg"><IMG SRC="<?php echo IMG_DIR ?>/Performance5Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>
								</div>								
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksPerformance1.jpg"><IMG SRC="<?php echo IMG_DIR ?>/Performance1Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>
								</div>					
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksPerformance2.jpg"><IMG SRC="<?php echo IMG_DIR ?>/Performance2Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>			
								</div>		
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksPerformance3.jpg"><IMG SRC="<?php echo IMG_DIR ?>/Performance3Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>			
								</div>		
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksPerformance4.jpg"><IMG SRC="<?php echo IMG_DIR ?>/Performance4Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>	
								</div>
							</div>
							<div class="col-xs-6 col-sm-4 col-md-3">
								<div class="presskit-hi-res-photo">
								<A HREF="http://www.offthekitchen.com/images/SteveWeeksPerformance6.jpg"><IMG SRC="<?php echo IMG_DIR ?>/Performance6Thumb.jpg" BORDER=0><br>
								<small>download hi-res<br>
								photo</small></A>	
								</div>
							</div>
						</div>
					</div>			
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header purple-box">
						<a name="documents">
							Documents
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-4 col-md-2 col-md-push-1">
						<div class="presskit-document">
						<A HREF="<?php echo PDF_DIR ?>/MyFamilyPressRelease.pdf" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/MyFamilyPR-Icon.gif" BORDER=0><br>
						Press Release<br>
						My Family (Single) </A>
						</div>								
					</div>
					<div class="col-xs-6 col-sm-4 col-md-2 col-md-push-1">
						<div class="presskit-document">
						<A HREF="<?php echo PDF_DIR ?>/WishYouWereHerePressRelease.pdf" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/WYWHPR-Icon.gif" alt="CD Press Release" BORDER=0>
						<br>Press Release<br>Wish You Were Here (CD)</A>
						</div>					
					</div>
					<div class="col-xs-6 col-sm-4 col-md-2 col-md-push-1">
						<div class="presskit-document">
						<A HREF="<?php echo DOC_DIR ?>/PressRelease.docx"><IMG SRC="<?php echo IMG_DIR ?>/PR_Template-icon.gif" BORDER=0><br>
						Press Release<br>Performance (Template)</A>			
						</div>		
					</div>
					<div class="col-xs-6 col-sm-4 col-md-2 col-sm-push-2 col-md-push-1">
						<div class="presskit-document">
						<A HREF="<?php echo PDF_DIR ?>/Biography.pdf" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/Biography-icon.gif" BORDER=0><br>
						Biography</A>			
						</div>		
					</div>
					<div class="col-xs-6 col-sm-4 col-md-2 col-xs-push-3 col-sm-push-2 col-md-push-1">
						<div class="presskit-document">
						<A HREF="<?php echo PDF_DIR ?>/References.pdf" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/References-icon.gif" BORDER=0><br>
						References</A>	
						</div>
					</div>
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header green-box">
						<a name="promo-materials">
							Promotional Materials
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-6 col-md-push-3" style="padding-bottom: 15px;">

						<?php 
						$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
						$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
						$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
						$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
						$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
						//Detect special conditions devices
						if( $iPod || $iPhone  || $iPad){
							echo "<a href=\"" . VIDEO_DIR . "/PerformancePromotionExampleIOS.mp4\">";
							echo  "<img src=\"" . IMG_DIR . "/about-performance-with-play-icon.jpg\" width=\"100%\"/></a>";
							echo "<div>THIS IS IPHONE</div>";
						}
						else{
							echo "<iframe width=\"100%\" height=\"50%\" src=\"https://www.youtube.com/embed/VbAbDUvXhCA\" frameborder=\"0\" allow=\"autoplay; encrypted-media;\" allowfullscreen></iframe>";
						}					
						?>

					</div>
				</div>
				<div class="row">			
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="presskit-document">
						<A HREF="<?php echo PDF_DIR ?>/Poster2.pdf"  target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/Poster2.png" BORDER=0><br>
						Poster<br>8.5" x 11"</A>
						</div>					
					</div>	
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="presskit-document">
						<A HREF="<?php echo PDF_DIR ?>/Flyer.pdf"  target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/Flyer.gif" BORDER=0><br>
						Color Flyer<br>8.5" x 11"</A>
						</div>								
					</div>
					<div class="col-xs-12 visible-sm">
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="presskit-document">
						<A HREF="<?php echo PDF_DIR ?>/Poster.pdf"  target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/Poster.gif" BORDER=0><br>
						Poster<br>11" x 17"</A>
						</div>					
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="presskit-document">
						<A HREF="<?php echo IMG_DIR ?>/Logo1000x1000.jpg" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/LogoSmall.png" BORDER=0><br>
						Color Logo<br>1000x1000</A>			
						</div>		
					</div>
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header red-box">
						<a name="music">
							Music
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-6">
						<div class="frame-box green-frame">
							<h2 class="listen">&nbsp;&nbsp;Birdsong</h2>
							<audio controls
							  data-info-att="Music: Steve Weeks"
							  data-info-att-link="https://www.steveweeksmusic.com">
							  <source src="<?php echo MP3_DIR;?>/Birdsong.mp3" type="audio/mpeg" />
							  <a href="<?php echo MP3_DIR;?>/Birdsong.mp3">Birdsong</a>
							  An html5-capable browser is required to play this audio. 
							</audio>		
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="frame-box red-frame">
							<h2 class="listen">&nbsp;&nbsp;Change of Heart</h2>
							<audio controls
							  data-info-att="Music: Steve Weeks"
							  data-info-att-link="https://www.steveweeksmusic.com">
							  <source src="<?php echo MP3_DIR;?>/ChangeOfHeart.mp3" type="audio/mpeg" />
							  <a href="<?php echo MP3_DIR;?>/ChangeOfHeart.mp3">Change of Heart</a>
							  An html5-capable browser is required to play this audio. 
							</audio>		
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="frame-box blue-frame">
							<h2 class="listen">&nbsp;&nbsp;Get Up!</h2>
							<audio controls
							  data-info-att="Music: Steve Weeks"
							  data-info-att-link="https://www.steveweeksmusic.com">
							  <source src="<?php echo MP3_DIR;?>/GetUp.mp3" type="audio/mpeg" />
							  <a href="<?php echo MP3_DIR;?>/GetUp.mp3">Get Up!</a>
							  An html5-capable browser is required to play this audio. 
							</audio>		
					 	</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="frame-box orange-frame">
							<h2 class="listen">&nbsp;&nbsp;Once I Lived Upon the Sea</h2>
							<audio controls
							  data-info-att="Music: Steve Weeks"
							  data-info-att-link="https://www.steveweeksmusic.com">
							  <source src="<?php echo MP3_DIR;?>/Once.mp3" type="audio/mpeg" />
							  <a href="<?php echo MP3_DIR;?>/Once.mp3">Once I Lived Upon the Sea</a>
							  An html5-capable browser is required to play this audio. 
							</audio>		
					 	</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header blue-box">
						<a name="press">
							Press & Contact Info
						</a>
						</header>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="presskit-clippings">
						<A HREF="<?php echo PDF_DIR ?>/PressClippings.pdf" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/PressClippings-icon.png" BORDER=0><br>
						Press Clippings <br>
						</A>
						</div>								
					</div>
					<div class="col-xs-12 col-md-8">
						<div class="frame-box red-frame">
							<div class="contact">
								<div class="email">Booking:  <a href="mailto:booking@steveweeksmusic.com">booking@steveweeksmusic.com</a></div>
								<div class="email">General Inquiries:  <a href="mailto:steve@steveweeksmusic.com">steve@steveweeksmusic.com</a></div>
							</div>
						</div>								
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12 col-lg-6">
						<div class="frame-box green-frame">
							<blockquote>
								<span>
								...always one with a way for words, Steve Weeks is a master crafting songs that weave fantastical stories you can somehow see through your ears.
								</span>
								<cite>
								Absolutely Mindy (Program Director - Sirius XM Satellite Radio)
								</cite>
							</blockquote>						
						</div>								
					</div>
					<div class="col-xs-12 col-lg-6">
						<div class="frame-box purple-frame">
							<blockquote>
								<span>
								He is simply, a great songwriter and storyteller, with a warm voice and solid production values.  With clever jokes, word play, varied instrumentation, and evocative vocals, the music puts me in mind of Barenaked Ladies.
								</span>
								<cite>
								Bill Childs (Minnesota Parent)
								</cite>
							</blockquote>						
						</div>	
					</div>							
					<div class="col-xs-12 col-lg-6">
						<div class="frame-box orange-frame">
							<blockquote>
								<span>
								Steve's performance was truly a one of a kind delight that you DON'T want to miss! He has such a gift of engaging and captivating his entire audience with his musical and comedic talents!
								</span>
								<cite>
								Heather Halligan
								</cite>
							</blockquote>						
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
