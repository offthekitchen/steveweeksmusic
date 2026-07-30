<?php
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

//include CD Class
include_once(CLASS_DIR . "/class_CD.php");

//include Song Class
include_once(CLASS_DIR . "/class_Song.php");

//include Review Class
include_once(CLASS_DIR . "/class_Review.php");

//include Award Class
include_once(CLASS_DIR . "/class_Award.php");

//include Error Log Class
include_once(CLASS_DIR . "/class_ErrorLog.php");

$oCD = new CD();

//If a CD ID is passed, display information for that CD
if (isset($_REQUEST['cd-id']) && $_REQUEST['cd-id'] != "") {
	$oCD->nCDID = preg_replace("/[^0-9]/", "", $_REQUEST['cd-id']);
} else {
	$oCD->nCDID = DEFAULT_CD_ID;
}

$oCD->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;

if (!$oCD->getCD()) {
	$error = new ErrorLog('cd.php', 'getCD()', 'Error getting CD', $oCD->nCDID, 'CD');
	$error->writeErrorLog();
} else {
	$oSongs = new Song();
	$oSongs->nCDID = $oCD->nCDID;
	if (!$oSongs->getSong()) {
		$error = new ErrorLog('cd.php', 'getSong()', 'Error getting Songs', $oCD->nCDID, 'Song');
		$error->writeErrorLog();
	}
}

if (sizeof($oCD->aCDRecords) > 0) {


	$oThisCD = $oCD->aCDRecords[0];

	//Page Name 
	$sPageName = "{$oThisCD->sCDName}";
	//Page Name 
	$sPageSubTitle = "{$oThisCD->sCDShortName}";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0 => array('Home', 'index.php'),
		1 => array('Music', 'music.php'),
		2 => array($oThisCD->sCDName, 'cd.php?cd-id=' . $oThisCD->nCDID)
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
				<main id="main-content" class="col-xs-12">
					<article id="cd-list" class="row">
						<div class="col-xs-12 col-sm-6 col-sm-push-3 col-md-5 col-md-push-0">
							<div class="cd-cover">
								<figure class="dynamic-width in-front-of-theme-image">
									<img src="<?php echo IMG_DIR . "/{$oThisCD->sCDImage}"; ?>">
								</figure>
								<?php
								renderPurchaseButton($oThisCD->sPurchaseLink);
								renderLyricsButton(LYRICS_TYPE_CD, $oThisCD->nCDID);
								?>
							</div>
						</div>
						<div class="col-xs-12 col-md-7">
							<section class="behind-theme-image avoid-theme basic-box">
								<?php
								echo "<div>";
								echo $oThisCD->sCDDescription;
								echo "</div>";
								renderAwards(AWARD_TYPE_CD, $oThisCD->nCDID, AWARD_SIZE_SMALL);
								?>
							</section>
						</div>
					</article>
					<div id="cd-list" class="row">
						<div class="col-xs-12 col-md-7 col-md-push-5">
							<section class="in-front-of-theme-image frame-box purple-frame">
								<?php
								$oCDReviews = $oThisCD->getReviews();
								if (sizeof($oCDReviews->aReviewRecords) > 1) {
									renderReviewSlideshow($oCDReviews->aReviewRecords);
								} else {
									renderReview($oCDReviews->aReviewRecords[0]);
								}
								?>
							</section>
						</div>
						<div class="col-xs-12 col-md-5 col-md-pull-7">
							<section class="behind-theme-image avoid-theme">
								<h4>Tracks</h4>
								<ol>
									<?php
									foreach ($oSongs->aSongRecords as $oSong) {
										if (!is_numeric($oSong->nSongID)) {
											error_log('cd.php: INVALID Song ID: ' . $oSong->nSongID);
										}
										echo "<li>";
										echo "<a href=\"{$ROOT}/song.php?song-id={$oSong->nSongID}\">";
										echo "<div class=\"track-title\">{$oSong->sSongName}</div>";
										echo "</a>";
										echo "</li>";
									}
									?>
								</ol>
							</section>
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
<?php } else {
	echo "<h2>I'm Sorry.  I couldn't find that CD.<h2>";
	}
?>