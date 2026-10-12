<?php
/*
*******************************************************************
cd.php
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
include_once(DATALAYER_DIR . "/CD.php");
include_once(DATALAYER_DIR . "/CDRepository.php");
include_once(DATALAYER_DIR . "/Song.php");
include_once(DATALAYER_DIR . "/SongRepository.php");
include_once(DATALAYER_DIR . "/Review.php");
include_once(DATALAYER_DIR . "/ReviewRepository.php");

//If a CD ID is passed, display information for that CD
if (isset($_REQUEST['cd-id']) && $_REQUEST['cd-id'] != "") {
	$nCDID = (int) preg_replace("/[^0-9]/", "", $_REQUEST['cd-id']);
} else {
	$nCDID = DEFAULT_CD_ID;
}

$cdRepo = new \Datalayer\CDRepository();
$oThisCD = $cdRepo->findById($nCDID);

$aSongs = [];
if ($oThisCD !== null) {
	$songRepo = new \Datalayer\SongRepository();
	$aSongs = $songRepo->find(['cdId' => $oThisCD->id]);
} else {
	error_log('cd.php: Error getting CD id ' . $nCDID);
}

if ($oThisCD !== null) {


	//Page Name 
	$sPageName = "{$oThisCD->name}";
	//Page Name 
	$sPageSubTitle = "{$oThisCD->shortName}";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0 => array('Home', 'index.php'),
		1 => array('Music', 'music.php'),
		2 => array($oThisCD->name, 'cd.php?cd-id=' . $oThisCD->id)
	);

	$sActiveMenuItem = MUSIC_ACTIVE;

	$reviewRepo = new \Datalayer\ReviewRepository();
	$aCDReviews = $reviewRepo->find(['cdId' => $oThisCD->id]);
	if (sizeof($aCDReviews) < 1) {
		$oDefaultReview = $reviewRepo->findById(DEFAULT_REVIEW_ID);
		$aCDReviews = $oDefaultReview ? [$oDefaultReview] : [];
	}

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
									<img src="<?php echo IMG_DIR . "/{$oThisCD->image}"; ?>">
								</figure>
								<?php
								renderPurchaseButton($oThisCD->purchaseLink);
								renderLyricsButton(LYRICS_TYPE_CD, $oThisCD->id);
								?>
							</div>
						</div>
						<div class="col-xs-12 col-md-7">
							<section class="behind-theme-image avoid-theme basic-box">
								<?php
								echo "<div>";
								echo $oThisCD->description;
								echo "</div>";
								renderAwards(AWARD_TYPE_CD, $oThisCD->id, AWARD_SIZE_SMALL);
								?>
							</section>
						</div>
					</article>
					<div id="cd-list" class="row">
						<div class="col-xs-12 col-md-7 col-md-push-5">
							<section class="in-front-of-theme-image frame-box purple-frame">
								<?php
								if (sizeof($aCDReviews) > 1) {
									renderReviewSlideshow($aCDReviews);
								} else if (sizeof($aCDReviews) > 0) {
									renderReview($aCDReviews[0]);
								}
								?>
							</section>
						</div>
						<div class="col-xs-12 col-md-5 col-md-pull-7">
							<section class="behind-theme-image avoid-theme">
								<h4>Tracks</h4>
								<ol>
									<?php
									foreach ($aSongs as $oSong) {
										if (!is_numeric($oSong->id)) {
											error_log('cd.php: INVALID Song ID: ' . $oSong->id);
										}
										echo "<li>";
										echo "<a href=\"{$ROOT}/song.php?song-id={$oSong->id}\">";
										echo "<div class=\"track-title\">{$oSong->name}</div>";
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
