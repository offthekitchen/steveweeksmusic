<?php
/*
*******************************************************************
search.php
This PHP file defines the search page 
managing Songs.
NOTES
Date        Change
-------------------------------------------------------------

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

	//include Song Class		
	include_once (CLASS_DIR . "/class_Performance.php");
	include_once (CLASS_DIR . "/class_Song.php");
	include_once (CLASS_DIR . "/class_CD.php");
	include_once (CLASS_DIR . "/class_SearchTerms.php");
	
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
	  
	//Search for any songs matching the search text
	$oSongs = new Song();
	$oSongs->sSongName = $sSearchText;
	$oSongs->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;
	$oSongs->bFuzzyNameSearch = TRUE;
	
	if (!$oSongs->getSong())
	{
		//TODO: Error
	}
	
	//Search for any songs matching the search text
	$oCDs = new CD();
	$oCDs->sCDName = $sSearchText;
	$oCDs->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;
	$oCDs->bFuzzyNameSearch = TRUE;
	
	if (!$oCDs->getCD())
	{
		//TODO: Error
	}   
	
	//Search for any performance matching the search text
	$oPerformances = new Performance();
	$oPerformances->sPerformanceName = $sSearchText;
	$oPerformances->sLocation = $sSearchText;
	$oPerformances->sLocationCity = $sSearchText;
	$oPerformances->bOrSearch = TRUE;
	$oPerformances->bFuzzyLocationSearch = TRUE;
	$oPerformances->bFuzzyCitySearch = TRUE;
	
	if (!$oPerformances->getPerformance())
	{
		//TODO: Error
	}   
	
	//Search for any performance matching the search text
	$oSearchTerms = new SearchTerms();
	$oSearchTerms->sSearchTerms = $sSearchText;
	$oSearchTerms->nWebsite = 1;
	
	if (!$oSearchTerms->getSearchTerms())
	{
		//TODO: Error
	}  	
	
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
									if(sizeof($oSongs->aSongRecords) > 0  || sizeof($oCDs->aCDRecords) > 0  || sizeof($oPerformances->aPerformanceRecords) > 0 || sizeof($oSearchTerms->aSearchTermsRecords) > 0)
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

					if(sizeof($oSearchTerms->aSearchTermsRecords) > 0)
					{
						echo "<h3>Pages</h3>";
						echo "<ul class=\"search-results-list\">";					
						foreach ($oSearchTerms->aSearchTermsRecords as $oSearchTerms)
						{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/{$oSearchTerms->sPageURL}\">{$oSearchTerms->sPageName}</a>";
							echo "</li>";
						}
						echo "</ul>";
					}					
					
					$bLyricsFound = FALSE;

					if(sizeof($oSongs->aSongRecords) > 0)
					{
						echo "<h3>Songs</h3>";
						echo "<ul class=\"search-results-list\">";					
						foreach ($oSongs->aSongRecords as $oSong)
						{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/song.php?song-id={$oSong->nSongID}\">{$oSong->sSongName}</a>";
							echo "</li>";
							
							//while looping through songs, check for the existence of lyrics
							if (!empty($oSong->sLyricsHTML))
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
						foreach ($oSongs->aSongRecords as $oSong)
						{
							if (!empty($oSong->sLyricsHTML))
							{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/lyrics.php?song-id={$oSong->nSongID}\">{$oSong->sSongName}</a>";
							echo "</li>";
							}
						}
						echo "</ul>";
					}
					
					if(sizeof($oCDs->aCDRecords) > 0)
					{

						echo "<h3>CDs</h3>";
						echo "<ul class=\"search-results-list\">";					
						foreach ($oCDs->aCDRecords as $oCD)
						{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/cd.php?cd-id={$oCD->nCDID}\">{$oCD->sCDName}</a>";
							echo "</li>";
						}
						echo "</ul>";
					}

					if(sizeof($oPerformances->aPerformanceRecords) > 0)
					{
						echo "<h3>Performances</h3>";
						echo "<ul class=\"search-results-list\">";					
						foreach ($oPerformances->aPerformanceRecords as $oPerformance)
						{
							echo "<li class=\"search-results-item\">";
							echo "<a href=\"{$ROOT}/schedule.php?year={$oPerformance->nPerformanceYear}#performance-{$oPerformance->nPerformanceID}\">{$oPerformance->sPerformanceName}";
							echo " - {$oPerformance->sLocation}, {$oPerformance->sLocationCity}, {$oPerformance->sLocationState}</a>";
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
