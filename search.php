<?php
/*
*******************************************************************
search.php
This PHP file defines the search page 
managing Songs.
NOTES
Date        Change
-------------------------------------------------------------
2026-10-12	Migrated to shared PDO datalayer
*******************************************************************
*/	//Don't Display Notice messages from PHP Server
	error_reporting(E_ALL ^ E_NOTICE);

	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//Common Functions
   	include (INCLUDE_DIR . "/commonFunctions.php");

	include_once(DATALAYER_DIR . "/Connection.php");
	include_once(DATALAYER_DIR . "/Song.php");
	include_once(DATALAYER_DIR . "/SongRepository.php");
	include_once(DATALAYER_DIR . "/CD.php");
	include_once(DATALAYER_DIR . "/CDRepository.php");
	include_once(DATALAYER_DIR . "/Performance.php");
	include_once(DATALAYER_DIR . "/PerformanceRepository.php");
	include_once(DATALAYER_DIR . "/SearchTerms.php");
	include_once(DATALAYER_DIR . "/SearchTermsRepository.php");
	
	$sSearchText = '';

	//Get the search text from the query string parameter
	if (isset($_REQUEST['search-text']) && $_REQUEST['search-text'] != "") {
		$sSearchText = $_REQUEST['search-text'];
	}
	
	//Page Name 
	$sPageName = "Search Results";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('Search Results','search.php'),
		); 

	$sActiveMenuItem = HOME_ACTIVE;	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
 
 	$bResultsFound = FALSE;

	$quotes_to_remove = array('"', "'");
	$sSearchText = preg_replace('/[^a-zA-Z0-9\s]/', '', $sSearchText);
	  
	$songRepo = new \Datalayer\SongRepository();
	$aSongs = $songRepo->find([
		'name' => $sSearchText,
		'fuzzyName' => true,
		'artistId' => STEVE_WEEKS_FAMILY_ARTIST_ID,
	]);
	
	$cdRepo = new \Datalayer\CDRepository();
	$aCDs = $cdRepo->findByNameFuzzy($sSearchText, STEVE_WEEKS_FAMILY_ARTIST_ID);
	
	$performanceRepo = new \Datalayer\PerformanceRepository();
	$aPerformancesById = [];
	foreach ($performanceRepo->find(['name' => $sSearchText, 'fuzzyName' => true]) as $perf) {
		$aPerformancesById[$perf->id] = $perf;
	}
	foreach ($performanceRepo->find(['location' => $sSearchText, 'fuzzyLocation' => true]) as $perf) {
		$aPerformancesById[$perf->id] = $perf;
	}
	foreach ($performanceRepo->find(['locationCity' => $sSearchText, 'fuzzyCity' => true]) as $perf) {
		$aPerformancesById[$perf->id] = $perf;
	}
	$aPerformances = array_values($aPerformancesById);
	
	$searchTermsRepo = new \Datalayer\SearchTermsRepository();
	$aSearchTerms = $searchTermsRepo->findByWebsiteAndTerms(1, $sSearchText);
	
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
				<section class="row showcase-section">
					<div class="col-xs-12">
						<div class="frame-box green-frame in-front-of-theme-image">
							<div class="search-results-header">
								<?php
									if(sizeof($aSongs) > 0  || sizeof($aCDs) > 0  || sizeof($aPerformances) > 0 || sizeof($aSearchTerms) > 0)
									{
										$bResultsFound = TRUE;
										echo "<h4>Search Results For</h4>";
									}
									else
									{
										echo "<h4>No Results Found For</h4>";
									}
								?>
								<div class="search-phrase">
									<?php
									echo "\"{$sSearchText}\"";
									?>
								</div>	
							</div>
						</div>
					</div>
					<div class="col-xs-12">
					<?php
					echo "<section class=\"search-results-section behind-theme-image avoid-theme\">";

					if(sizeof($aSearchTerms) > 0)
					{
						echo "<h3>Pages</h3>";
						echo "<ul class=\"search-results-list\">";					
						foreach ($aSearchTerms as $oSearchTerm)
						{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/{$oSearchTerm->pageUrl}\">{$oSearchTerm->pageName}</a>";
							echo "</li>";
						}
						echo "</ul>";
					}					
					
					$bLyricsFound = FALSE;

					if(sizeof($aSongs) > 0)
					{
						echo "<h3>Songs</h3>";
						echo "<ul class=\"search-results-list\">";					
						foreach ($aSongs as $oSong)
						{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/song.php?song-id={$oSong->id}\">{$oSong->name}</a>";
							echo "</li>";
							
							//while looping through songs, check for the existence of lyrics
							if (!empty($oSong->lyricsHtml))
							{
								$bLyricsFound = TRUE;
							}
						}
						echo "</ul>";
					}
					
					if($bLyricsFound)
					{
						echo "<h3>Lyrics</h3>";
						echo "<ul class=\"search-results-list\">";					
						foreach ($aSongs as $oSong)
						{
							if (!empty($oSong->lyricsHtml))
							{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/lyrics.php?song-id={$oSong->id}\">{$oSong->name}</a>";
							echo "</li>";
							}
						}
						echo "</ul>";
					}
					
					if(sizeof($aCDs) > 0)
					{

						echo "<h3>CDs</h3>";
						echo "<ul class=\"search-results-list\">";					
						foreach ($aCDs as $oCD)
						{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/cd.php?cd-id={$oCD->id}\">{$oCD->name}</a>";
							echo "</li>";
						}
						echo "</ul>";
					}

					if(sizeof($aPerformances) > 0)
					{
						echo "<h3>Performances</h3>";
						echo "<ul class=\"search-results-list\">";					
						foreach ($aPerformances as $oPerformance)
						{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/schedule.php?year={$oPerformance->performanceYear}#performance-{$oPerformance->id}\">{$oPerformance->name}";
							echo " - {$oPerformance->location}, {$oPerformance->locationCity}, {$oPerformance->locationState}</a>";
							echo "</li>";
						}
						echo "</ul>";
					}
					
					if (!$bResultsFound)
					{
						echo "<div class=\"no-results-text\">Hmmmm ...I couldn't find anything matching \"{$sSearchText}\" on the site, but here are some things that you might find helpful:</div>";
						echo "<ul class=\"search-results-list\">";					
						echo "<li class=\"search-results-item\">";
						echo "<a href=\"{$ROOT}/index.php\">Home Page</a>";
						echo "</li>";
						echo "<li class=\"search-results-item\">";
						echo "<a href=\"{$ROOT}/contact.php\">Contact</a>";
						echo "</li>";
						echo "<li class=\"search-results-item\">";
						echo "<a href=\"{$ROOT}/lyrics.php\">Lyrics</a>";
						echo "</li>";
						echo "<li class=\"search-results-item\">";
						echo "<a href=\"{$ROOT}/music.php\">CDs & Singles</a>";
						echo "</li>";
						echo "</li>";
						echo "<li class=\"search-results-item\">";
						echo "<a href=\"{$ROOT}/schedule.php\">Performance Schedule</a>";
						echo "</li>";
						echo "</ul>";				
					}
					echo "</section>";
					?>
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
