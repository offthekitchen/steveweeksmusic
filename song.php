<?php
/*
*******************************************************************
song.php
NOTES
Date        Change
-------------------------------------------------------------
2026-10-12	Migrated to shared PDO datalayer
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

include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Song.php");
include_once(DATALAYER_DIR . "/SongRepository.php");
include_once(DATALAYER_DIR . "/CD.php");
include_once(DATALAYER_DIR . "/CDRepository.php");
include_once(DATALAYER_DIR . "/Review.php");
include_once(DATALAYER_DIR . "/ReviewRepository.php");

$songRepo = new \Datalayer\SongRepository();
$cdRepo = new \Datalayer\CDRepository();
$reviewRepo = new \Datalayer\ReviewRepository();

$aSongs = [];
$oThisSong = null;
$oThisCD = null;
$nSongID = 0;
$nCDID = 0;

$sPurchaseLink = '';
$aBreadcrumb = [];
$sSongBreadcrumbItem = [];
$sCDBreadcrumbItem = [];

$criteria = ['artistId' => STEVE_WEEKS_FAMILY_ARTIST_ID];

//If a Song ID is passed, display information for that Song
if (isset($_REQUEST['song-id']) && $_REQUEST['song-id'] != "") {
	$nSongID = (int) preg_replace("/[^0-9]/", "", $_REQUEST['song-id']);
	$criteria['id'] = $nSongID;
}

//If a CD ID is passed, get songs for that CD
if (isset($_REQUEST['cd-id']) && $_REQUEST['cd-id'] != "") {
	$nCDID = (int) preg_replace("/[^0-9]/", "", $_REQUEST['cd-id']);
	$criteria['cdId'] = $nCDID;
}

$aSongs = $songRepo->find($criteria);
if (sizeof($aSongs) < 1 && $nSongID > 0) {
	error_log('song.php: Error getting Song id ' . $nSongID);
}

if (sizeof($aSongs) > 0) {
	$oThisSong = $aSongs[0];
	$sPageName = "{$oThisSong->name}";
	$sPageDescription = "{$oThisSong->description}";
	$sPageTitle = "Award-winning Music";
	$sPageSubTitle = "{$oThisSong->name}";
	$sSongBreadcrumbItem = array($oThisSong->name, 'song.php?song-id=' . $oThisSong->id);

	if ($oThisSong->cdId <> SINGLES_CD_ID && !empty($oThisSong->cdId)) {
		$oThisCD = $cdRepo->findById((int) $oThisSong->cdId);
		if ($oThisCD !== null) {
			$sCDBreadcrumbItem = array($oThisCD->name, 'cd.php?cd-id=' . $oThisCD->id);
		}
	}
}

//Custom FB Image
$sFBImage = "FB_Performing.png";

$aBreadcrumb = array(
	0 => array('Home', 'index.php'),
	1 => array('Music', 'music.php'),
);
if ($oThisSong && $oThisSong->cdId <> SINGLES_CD_ID) {
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
		<?php if (sizeof($aSongs) > 0) {
			?>
			<div class="row">
				<main id="main-content" class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 col-md-6 col-md-7">
							<article class="frame-box green-frame in-front-of-theme-image song-information">
								<?php
								echo "<figure class=\"song-thumbnail float-left\">";
								if (!empty($oThisSong->thumbnail)) {
									echo "<img src=\"" . IMG_DIR . "/{$oThisSong->thumbnail}\">";
								} else if (!empty($oThisCD) && !empty($oThisCD->thumbnail)) {
									echo "<img src=\"" . IMG_DIR . "/{$oThisCD->thumbnail}\">";
								}
								echo "</figure>";
								echo "<div>";
								echo "<div class=\"song-description\">{$oThisSong->description}</div>";
								echo "</div>";

								?>

							</article>
						</div>
						<div class="col-xs-12 col-md-6 col-md-5 behind-theme-image avoid-theme">
							<section id="song-buttons">
								<?php
								if (!empty($oThisSong->purchaseLink)) {
									$sPurchaseLink = $oThisSong->purchaseLink;
								} else if (!empty($oThisCD) && !empty($oThisCD->purchaseLink)) {
									$sPurchaseLink = $oThisCD->purchaseLink;
								}

								renderPurchaseButton($sPurchaseLink);
								if (!empty($oThisSong->lyricsHtml)) {
									renderLyricsButton(LYRICS_TYPE_SONG, $oThisSong->id);
								}
								?>
							</section>

						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 avoid-theme">
							<?php
							renderAwards(AWARD_TYPE_SONG, $oThisSong->id, AWARD_SIZE_LARGE);
							?>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-10 col-sm-push-1 col-md-8 col-md-push-2 in-front-of-theme-image ">
							<section id="song-review" class="frame-box yellow-box">
								<?php
								$aSongReviews = $reviewRepo->find(['songId' => $oThisSong->id]);
								if (sizeof($aSongReviews) < 1) {
									if (!empty($oThisSong->cdId) && $oThisSong->cdId > 0) {
										$aSongReviews = $reviewRepo->find(['cdId' => $oThisSong->cdId]);
									} else {
										$oDefaultReview = $reviewRepo->findById(DEFAULT_REVIEW_ID);
										$aSongReviews = $oDefaultReview ? [$oDefaultReview] : [];
									}
								}

								if (sizeof($aSongReviews) > 1) {
									renderReviewSlideshow($aSongReviews);
								} else if (sizeof($aSongReviews) > 0) {
									renderReview($aSongReviews[0]);
								} else {
									$aGeneralReviews = $reviewRepo->find(['general' => true]);
									if (sizeof($aGeneralReviews) > 0) {
										renderReviewSlideshow($aGeneralReviews);
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
								echo "<div class=\"cd-title\"><a href=\"{$ROOT}/cd.php?cd-id={$oThisCD->id}\">{$oThisCD->name}</a></div>";
								echo "</div>";
								echo "</div>";
								renderAwards(AWARD_TYPE_CD, $oThisCD->id, AWARD_SIZE_XSMALL);
								echo "</section>";
							}
							?>

						</div>
					</div>

					<?php
					if (!empty($oThisSong->sampleMp3)) {
						?>
						<div class="row">
							<div class="col-xs-12">
								<h2 class="listen">Listen to clip:</h2>
								<!--<div class="mp3-player-container">-->
								<audio controls data-info-att="Music: Steve Weeks"
									data-info-att-link="https://www.steveweeksmusic.com">
									<source src="<?php echo MP3_DIR . "/{$oThisSong->sampleMp3}"; ?>" type="audio/mpeg" />
									<a
										href="<?php echo MP3_DIR . "/{$oThisSong->sampleMp3}"; ?>"><?php echo $oThisSong->name; ?></a>
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
			echo "<div>NO SONG FOUND FOR ID {$nSongID} </div>";
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
