<?php
/*
*******************************************************************
lyrics.php
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

$songRepo = new \Datalayer\SongRepository();
$cdRepo = new \Datalayer\CDRepository();

$aSongs = [];
$oThisSong = null;
$oThisCD = null;
$nSongID = 0;
$nCDID = 0;

$criteria = [
	'artistId' => STEVE_WEEKS_FAMILY_ARTIST_ID,
	'orderBy' => 'name',
];

//If a Song ID is passed, display information for that Song
if (isset($_REQUEST['song-id']) && $_REQUEST['song-id'] != "") {
	$nSongID = (int) preg_replace("/[^0-9]/", "", $_REQUEST['song-id']);
	$criteria['id'] = $nSongID;
}

//If a CD ID is passed, display lyrics for songs on that CD
if (isset($_REQUEST['cd-id']) && $_REQUEST['cd-id'] != "") {
	$nCDID = (int) preg_replace("/[^0-9]/", "", $_REQUEST['cd-id']);
	$criteria['cdId'] = $nCDID;
	$criteria['orderBy'] = 'track';

	$oThisCD = $cdRepo->findById($nCDID);
	if ($oThisCD === null) {
		error_log('lyrics.php: Error getting CD id ' . $nCDID);
	}
}

$aSongs = $songRepo->find($criteria);
if (sizeof($aSongs) < 1 && $nSongID > 0) {
	error_log('lyrics.php: Error getting Song id ' . $nSongID);
}

$sPageName = "Lyrics";
$sPageTitle = "Lyrics";
$sLastBreadcrumbItem = [];

if (sizeof($aSongs) == 1) {
	$oThisSong = $aSongs[0];
	$sPageName = "{$oThisSong->name} Lyrics";
	$sLastBreadcrumbItem = array($oThisSong->name, 'lyrics.php?song-id=' . $oThisSong->id);
} else if ($nCDID > 0 && !empty($oThisCD)) {
	$sPageName = "{$oThisCD->name} Lyrics";
	$sLastBreadcrumbItem = array($oThisCD->name, 'lyrics.php?cd-id=' . $oThisCD->id);
	$sPageSubTitle = "{$oThisCD->name}";
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
				if (sizeof($aSongs) == 0) {
					echo "<section class=\"frame-box purple-frame\">";
					echo "NO LYRICS FOUND";
					echo "</section>";

				} else if (sizeof($aSongs) == 1) {
					?>
						<section class="frame-box purple-frame song-lyrics in-front-of-theme-image full-width-xs">
							<header>
								<h3>
									<?php
									if (!is_numeric($oThisSong->id)) {
										error_log('lyrics.php(2): INVALID Song ID: ' . $oThisSong->id);
									}

									echo "<a href=\"{$ROOT}/song.php?song-id={$oThisSong->id}\">{$oThisSong->name}</a>";
									?>
								</h3>
							</header>
							<?php
							if (file_exists(LYRICS_DIR . "/{$oThisSong->lyricsHtml}")) {
								include(LYRICS_DIR . "/{$oThisSong->lyricsHtml}");
							}
							?>
						</section>
					<?php
				} else if (sizeof($aSongs) > 1) {
					echo "<section class=\"search-results-section behind-theme-image avoid-theme\">";
					echo "<ul class=\"search-results-list\">";
					foreach ($aSongs as $oSong) {
						if (!empty($oSong->lyricsHtml)) {
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/lyrics.php?song-id={$oSong->id}\">{$oSong->name}</a>";
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
		if (!empty($oThisSong) && !empty($oThisSong->sampleMp3)) {
			?>
			<div class="row">
				<div class="col-xs-12">
					<h2 class="listen">Listen to clip:</h2>
					<!--<div class="mp3-player-container">-->
					<audio controls data-info-att="Music: Steve Weeks" data-info-att-link="https://www.steveweeksmusic.com">
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
		<div class="row">
			<?php
			include(INCLUDE_DIR . "/footer.php");
			?>
		</div>
	</div>
</BODY>

</HTML>
<script src="<?php echo JS_DIR; ?>/bootstrap3_player.js"></script>
