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

//include Song Class
include_once(CLASS_DIR . "/class_Song.php");

//include Review Class
include_once(CLASS_DIR . "/class_Review.php");

//include Award Class
include_once(CLASS_DIR . "/class_Award.php");

//include CD Class
include_once(CLASS_DIR . "/class_CD.php");

//include Error Log Class
include_once(CLASS_DIR . "/class_ErrorLog.php");

$oSongs = new Song();
$oThisSong = new Song();
$oSongs->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;

$sPurchaseLink = '';
$aBreadcrumb = [];
$sSongBreadcrumbItem = [];
$sCDBreadcrumbItem = [];

//If a Song ID is passed, display information for that Song
if (isset($_REQUEST['song-id']) && $_REQUEST['song-id'] != "") {
	$oSongs->nSongID = preg_replace("/[^0-9]/", "", $_REQUEST['song-id']);
}

//If a CD ID is passed, get songs for that CD
if (isset($_REQUEST['cd-id']) && $_REQUEST['cd-id'] != "") {
	$oSongs->nCDID = preg_replace("/[^0-9]/", "", $_REQUEST['cd-id']);
}

if (!$oSongs->getSong()) {
	$error = new ErrorLog('song.php', 'getSong()', 'Error getting Song', $oSongs->nSongID, 'SONG');
	$error->writeErrorLog();
}

if (sizeof($oSongs->aSongRecords) > 0) {
	$oThisSong = $oSongs->aSongRecords[0];
	$sPageName = "{$oThisSong->sSongName}";
	$sPageDescription = "{$oThisSong->sSongDescription}";
	$sPageTitle = "Award-winning Music";
	$sPageSubTitle = "{$oThisSong->sSongName}";
	$sSongBreadcrumbItem = array($oThisSong->sSongName, 'song.php?song-id=' . $oThisSong->nSongID);

	if ($oThisSong->nCDID <> SINGLES_CD_ID) {
		$oCD = new CD();
		$oCD->nCDID = $oThisSong->nCDID;
		if ($oCD->getCD()) {
			$oThisCD = $oCD->aCDRecords[0];
			$sCDBreadcrumbItem = array($oThisCD->sCDName, 'cd.php?cd-id=' . $oThisCD->nCDID);
		}
	}
}

//Custom FB Image
$sFBImage = "FB_Performing.png";

$aBreadcrumb = array(
	0 => array('Home', 'index.php'),
	1 => array('Music', 'music.php'),
);
if ($oThisSong->nCDID <> SINGLES_CD_ID) {
	array_push($aBreadcrumb, $sCDBreadcrumbItem);
}
array_push($aBreadcrumb, $sSongBreadcrumbItem);

$sActiveMenuItem = MUSIC_ACTIVE;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
//Inlcude a common <HEAD> section
include(INCLUDE_DIR . "/HTMLHead.php");
?>
<link href="<?php echo CSS_DIR; ?>/bootstrap3_player.css" rel="stylesheet">

<BODY>
	<?php include(INCLUDE_DIR . "/theme-image.php"); ?>
	<div class="container-fluid">
		<div class="row">
			<?php
			include(INCLUDE_DIR . "/header.php");
			?>
		</div>
		<?php if (sizeof($oSongs->aSongRecords) > 0) {
			?>
			<div class="row">
				<main id="main-content" class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 col-md-6 col-md-7">
							<article class="frame-box green-frame in-front-of-theme-image song-information">
								<?php
								echo "<figure class=\"song-thumbnail float-left\">";
								if (!empty($oThisSong->sSongThumbnail)) {
									echo "<img src=\"" . IMG_DIR . "/{$oThisSong->sSongThumbnail}\">";
								} else if (!empty($oThisCD->sCDThumbnail)) {
									echo "<img src=\"" . IMG_DIR . "/{$oThisCD->sCDThumbnail}\">";
								}
								echo "</figure>";
								echo "<div>";
								echo "<div class=\"song-description\">{$oThisSong->sSongDescription}</div>";
								echo "</div>";

								?>

							</article>
						</div>
						<div class="col-xs-12 col-md-6 col-md-5 behind-theme-image avoid-theme">
							<section id="song-buttons">
								<?php
								if (!empty($oThisSong->sPurchaseLink)) {
									$sPurchaseLink = $oThisSong->sPurchaseLink;
								} else if (!empty($oThisCD->sPurchaseLink)) {
									$sPurchaseLink = $oThisCD->sPurchaseLink;
								}

								renderPurchaseButton($sPurchaseLink);
								if (!empty($oThisSong->sLyricsHTML)) {
									renderLyricsButton(LYRICS_TYPE_SONG, $oThisSong->nSongID);
								}
								?>
							</section>

						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 avoid-theme">
							<?php
							renderAwards(AWARD_TYPE_SONG, $oThisSong->nSongID, AWARD_SIZE_LARGE);
							?>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-10 col-sm-push-1 col-md-8 col-md-push-2 in-front-of-theme-image ">
							<section id="song-review" class="frame-box yellow-box">
								<?php
								$oSongReviews = $oThisSong->getReviews();
								if (sizeof($oSongReviews->aReviewRecords) > 1) {
									renderReviewSlideshow($oSongReviews->aReviewRecords);
								} else if (sizeof($oSongReviews->aReviewRecords) > 0) {
									renderReview($oSongReviews->aReviewRecords[0]);
								} else {
									$oReview = new Review();
									$oReview->bGeneral = TRUE;
									if (!$oReview->getReview()) {
										//TODO: Error
									} else {
										renderReviewSlideshow($oReview->aReviewRecords);
									}
								}
								?>
							</section>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 behind-theme-image avoid-theme-xs">
							<?php
							if (!empty($oThisCD)) {
								echo "<section class=\"from-the-cd\">";
								echo "<div>";
								echo "<div><i>From the CD</i>";
								echo "<div class=\"cd-title\"><a href=\"{$ROOT}/cd.php?cd-id={$oThisCD->nCDID}\">{$oThisCD->sCDName}</a></div>";
								echo "</div>";
								echo "</div>";
								renderAwards(AWARD_TYPE_CD, $oThisCD->nCDID, AWARD_SIZE_XSMALL);
								echo "</section>";
							}
							?>

						</div>
					</div>

					<?php
					if (!empty($oThisSong->sSampleMP3)) {
						?>
						<div class="row">
							<div class="col-xs-12">
								<h2 class="listen">Listen to clip:</h2>
								<!--<div class="mp3-player-container">-->
								<audio controls data-info-att="Music: Steve Weeks"
									data-info-att-link="https://www.steveweeksmusic.com">
									<source src="<?php echo MP3_DIR . "/{$oThisSong->sSampleMP3}"; ?>" type="audio/mpeg" />
									<a
										href="<?php echo MP3_DIR . "/{$oThisSong->sSampleMP3}"; ?>"><?php echo $oThisSong->sSongName; ?></a>
									An html5-capable browser is required to play this audio.
								</audio>
								<!--</div>-->
							</div>
						</div>
						<?php
					}
					?>

				</main>
			</div>
		<?php
		} else {
			echo "<div>NO SONG FOUND FOR ID {$oSongs->nSongID} </div>";
		}
		?>
		<div class="row">
			<?php
			include(INCLUDE_DIR . "/footer.php");
			?>
		</div>
	</div>
</BODY>

</HTML>
<script src="<?php echo JS_DIR; ?>/bootstrap3_player.js"></script>