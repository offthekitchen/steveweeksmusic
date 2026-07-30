<?php
/*
*******************************************************************
performance.php
This PHP file defines the Performannce Page
NOTES
Date        Change
-------------------------------------------------------------
2017-06-05	Added 50 States
*******************************************************************
*/
//Don't Display Notice messages from PHP Server
error_reporting(E_ALL ^ E_NOTICE);

//This include defines the relative path to the root directory from this sub-directory
include_once("root.inc.php");

//inlcude web site settings
include_once($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once(SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//Common Functions
include(INCLUDE_DIR . "/commonFunctions.php");

//include Review Class
include_once(CLASS_DIR . "/class_Review.php");

//include Error Log Class
include_once(CLASS_DIR . "/class_ErrorLog.php");

//Page Name 
$sPageName = "Performance";
//Page Name 
$sPageTitle = "About Steve's Performance";
//Custom FB Image
$sFBImage = "FB_Performing.png";

$aBreadcrumb = array(
	0 => array('Home', 'index.php'),
	1 => array('Music', 'music.php'),
	2 => array('Performance', 'performance.php')
);


$sActiveMenuItem = MUSIC_ACTIVE;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
//Inlcude a common <HEAD> section
include(INCLUDE_DIR . "/HTMLHead.php");
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
			<aside class="col-xs-12 col-sm-10 col-sm-push-1">
				<div class="orange-frame frame-box in-front-of-theme-image">
					<div class="thanks">
						As a touring family music performer, I've really enjoyed having the opportunity to travel to
						<a href="http://www.offthekitchen.com/music/performance/memories-of-50-states/"
							target="_blank">all 50 United States</a>,
						Europe and Canada playing my tunes. It wouldn't be possible without all the <em>WONDERFUL
							FOLKS</em> who have booked me,
						encouraged me, attended my shows, and even let me crash on their couch.&nbsp;&nbsp;<em>THANK
							YOU!</em>&nbsp;&nbsp;
						If you're interested in booking me or seeing a show, <em><a
								href="<?php echo $ROOT; ?>/contact.php">CONTACT ME</a></em>.&nbsp;&nbsp;
						I'd love to get the chance to come to your town and perform.<br><br>
						<note>I'm taking a break from touring in 2025 to catch my breath and reset, so I'll only be
							performing a very small select number
							of shows.&nbsp;&nbsp;I will return to touring in 2026 and hope to see you on the road!
						</note>
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12 col-sm-6 col-lg-5">
				<div class="row">
					<div class="col-xs-12">
						<aside class="subtle-blue behind-theme-image avoid-theme-xs">
							<?php renderNextPerformance(TRUE, TRUE); ?>
						</aside>
					</div>
					<div class="col-xs-12">
						<aside class=" frame-box green-frame in-front-of-theme-image">
							<?php
							$oReviews = new Review();
							$oReviews->bPerformanceRelated = TRUE;
							$oReviews->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;
							if (!$oReviews->getReview()) {
								$error = new ErrorLog('performance.php', 'getReview()', 'Error Retrieving Reviews', $oReviews->nArtistID, 'ARTIST');
								$error->writeErrorLog();
							} else {
								renderReviewSlideshow($oReviews->aReviewRecords);
							}
							?>
						</aside>
					</div>
					<div class="col-xs-12">
						<?php
						renderScheduleButton();
						?>
					</div>
				</div>
			</main>
			<div class="col-xs-12 col-sm-6 col-lg-7 avoid-theme">
				<?php renderAboutPerformance(); ?>
			</div>
		</div>
		<div class="row">
			<?php
			include(INCLUDE_DIR . "/footer.php");
			?>
		</div>
	</div>
</BODY>

</HTML>
<style>
	note {
		font-size: 25px;
		color: #CC0000;
		font-family: 'Sue Ellen Francisco', cursive;
		font-weight: 500;
	}
</style>