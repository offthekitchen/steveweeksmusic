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

$oCDs = new CD();
$oCDs->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;

if (!$oCDs->getCD()) {
	//TODO: What about an error?
	echo "ERROR RETRIEVING CDs: {$oCDs->sErrorMessage}";
}

$oSingles = new Song();
$oSingles->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;
$oSingles->nCDID = SINGLES_CD_ID;
$oSingles->sOrderBy = RELEASE_ORDER;
$oSingles->bActive = TRUE;
$oSingles->bDisplayOnSite = TRUE;

if (!$oSingles->getSong()) {
	error_log(`ERROR RETRIEVING SONGS IN music.php - Song ID: {$sSongId}`);
}

//Page Name 
$sPageName = "Music";
//Page Name 
$sPageTitle = "Award-winning Music";
//Custom FB Image
$sFBImage = "FB_Performing.png";

$aBreadcrumb = array(
	0 => array('Home', 'index.php'),
	1 => array('Music', 'music.php'),
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
						<p>I am so <em>GRATEFUL</em> for everyone who has purchased my CDs and Songs over the past few
							years.&nbsp;&nbsp;In this day and age of free music, it is a huge compliment when someone
							likes your music enough to want to own it.&nbsp;&nbsp;I hope you find something here you
							enjoy, and please don't be afraid to <em><a href="<?php echo $ROOT; ?>/contact.php">REACH
									OUT</a></em>. It makes my day hearing from folks who are listening to my tunes!
						</p>
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12">
				<section id="cd-list" class="row">
					<div class="col-xs-12">
						<header class="section-title">
							<h2>CDs</h2>
						</header>
					</div>
					<?php
					$nListingNumber = 1;
					foreach ($oCDs->aCDRecords as $oCD) {
						echo "<div class=\"col-xs-12 col-md-4 full-width-xs\">";
						echo "<a href=\"{$ROOT}/cd.php?cd-id={$oCD->nCDID}\">";
						echo "<aside class=\"music-listing listing-{$nListingNumber}\">";
						echo renderCDListing($oCD);
						echo "</aside>";
						echo "</a>";
						echo "</div>";
						$nListingNumber++;
					}
					?>
				</section>
				<section id="song-list" class="row">
					<div class="col-xs-12">
						<header class="section-title">
							<h2>Singles</h2>
						</header>
					</div>
					<?php
					$nListingNumber = 1;
					foreach ($oSingles->aSongRecords as $oSingle) {
						echo "<div class=\"col-xs-12 col-md-4 full-width-xs\">";
						echo "<a href=\"{$ROOT}/song.php?song-id={$oSingle->nSongID}\">";
						echo "<aside class=\"music-listing listing-{$nListingNumber}\">";
						echo renderSongListing($oSingle);
						echo "</aside>";
						echo "</a>";
						echo "</div>";
						$nListingNumber++;
					}
					?>
				</section>
			</main>
		</div>
		<div class="row">
			<?php
			include(INCLUDE_DIR . "/footer.php");
			?>
			<script>
				//******************************************
				// alternate classes on CDs and Singles
				//******************************************
				$(document).ready(function () {
					//Set alternating classes on CDs and singles
					$("#cd-list aside.music-listing:odd").addClass("green-box basic-box in-front-of-theme-image");
					$("#cd-list aside.music-listing:even").addClass("purple-box basic-box behind-theme-image avoid-theme-xs avoid-theme-sm");
					$("#song-list aside.music-listing:odd").addClass("orange-box basic-box in-front-of-theme-image");
					$("#song-list aside.music-listing:even").addClass("yellow-box basic-box behind-theme-image avoid-theme-xs avoid-theme-sm");
					//This selector ensures that the 3rd odd items are behind the theme in MD and LG because
					//items dsplay in 3 columns in those views
					$("aside.listing-3, aside.listing-9").addClass("avoid-theme-md avoid-theme-lg");

				})
			</script>
		</div>
	</div>
</BODY>

</HTML>