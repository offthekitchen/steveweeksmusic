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

//include Error Log Class
include_once(CLASS_DIR . "/class_ErrorLog.php");

$oSongs = new Song();
$oThisSong = new Song();
$oSongs->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;
$oSongs->sOrderBy = NAME_ORDER;

//If a Song ID is passed, display information for that Song
if (isset($_REQUEST['song-id']) && $_REQUEST['song-id'] != "") {
	$oSongs->nSongID = preg_replace("/[^0-9]/", "", $_REQUEST['song-id']);
}

//If a CD ID is passed, display lyrics for songs on that CD
if (isset($_REQUEST['cd-id']) && $_REQUEST['cd-id'] != "") {
	$oSongs->nCDID = preg_replace("/[^0-9]/", "", $_REQUEST['cd-id']);
	$oSongs->sOrderBy = TRACK_ORDER;

	//get CD
	$oCD = new CD();
	$oCD->nCDID = $oSongs->nCDID;
	if (!$oCD->getCD()) {
		$error = new ErrorLog('lyrics.php', 'getCD()', 'Error getting CD', $oCD->nCDID, 'CD');
		$error->writeErrorLog();
	} else {
		if (sizeof($oCD->aCDRecords) > 0) {
			$oThisCD = $oCD->aCDRecords[0];
		}
	}

}

if (!$oSongs->getSong()) {
	$error = new ErrorLog('lyrics.php', 'getSong()', 'Error getting Song', $oSongs->nSongID, 'SONG');
	$error->writeErrorLog();
}

$sPageName = "Lyrics";
$sPageTitle = "Lyrics";
$sLastBreadcrumbItem = [];

if (sizeof($oSongs->aSongRecords) == 1) {
	$oThisSong = $oSongs->aSongRecords[0];
	$sPageName = "{$oThisSong->sSongName} Lyrics";
	$sLastBreadcrumbItem = array($oThisSong->sSongName, 'lyrics.php?song-id=' . $oThisSong->nSongID);
} else if ($oSongs->nCDID > 0 && !empty($oThisCD)) {
	$sPageName = "{$oThisCD->sCDName} Lyrics";
	$sLastBreadcrumbItem = array($oThisCD->sCDName, 'lyrics.php?cd-id=' . $oThisCD->nCDID);
	$sPageSubTitle = "{$oThisCD->sCDName}";
}


//Custom FB Image
$sFBImage = "FB_Performing.png";

$aBreadcrumb = array(
	0 => array('Home', 'index.php'),
	1 => array('Music', 'music.php'),
	2 => array('Lyrics', 'lyrics.php')
);

array_push($aBreadcrumb, $sLastBreadcrumbItem);

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
		<div class="row">
			<main id="main-content" class="col-xs-12 col-sm-10 col-sm-push-1 col-md-8 col-md-push-2">
				<?php
				if (sizeof($oSongs->aSongRecords) == 0) {
					echo "<section class=\"frame-box purple-frame\">";
					echo "NO LYRICS FOUND";
					echo "</section>";

				} else if (sizeof($oSongs->aSongRecords) == 1) {
					?>
						<section class="frame-box purple-frame song-lyrics in-front-of-theme-image full-width-xs">
							<header>
								<h3>
									<?php
									if (!is_numeric($oThisSong->nSongID)) {
										error_log('lyrics.php(2): INVALID Song ID: ' . $oThisSong->nSongID);
									}

									echo "<a href=\"{$ROOT}/song.php?song-id={$oThisSong->nSongID}\">{$oThisSong->sSongName}</a>";
									?>
								</h3>
							</header>
							<?php
							if (file_exists(LYRICS_DIR . "/{$oThisSong->sLyricsHTML}")) {
								include(LYRICS_DIR . "/{$oThisSong->sLyricsHTML}");
							}
							?>
						</section>
					<?php
				} else if (sizeof($oSongs->aSongRecords) > 1) {
					echo "<section class=\"search-results-section behind-theme-image avoid-theme\">";
					echo "<ul class=\"search-results-list\">";
					foreach ($oSongs->aSongRecords as $oSong) {
						if (!empty($oSong->sLyricsHTML)) {
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/lyrics.php?song-id={$oSong->nSongID}\">{$oSong->sSongName}</a>";
							echo "</li>";
						}
					}
					echo "</ul>";
					echo "</section>";
				}
				?>
			</main>
		</div>
		<?php
		if (!empty($oThisSong->sSampleMP3)) {
			?>
			<div class="row">
				<div class="col-xs-12">
					<h2 class="listen">Listen to clip:</h2>
					<!--<div class="mp3-player-container">-->
					<audio controls data-info-att="Music: Steve Weeks" data-info-att-link="https://www.steveweeksmusic.com">
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
		<div class="row">
			<?php
			include(INCLUDE_DIR . "/footer.php");
			?>
		</div>
	</div>
</BODY>

</HTML>
<script src="<?php echo JS_DIR; ?>/bootstrap3_player.js"></script>