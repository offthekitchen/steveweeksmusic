<?php
/*
*******************************************************************
schedule.php
This PHP file defines the Schedule Page
NOTES
Date        Change
-------------------------------------------------------------
2016-09-02	Corrected date check for next performance
2017-01-27	Added logic to open details for passed Performance ID
2017-01-30	Added flag to only retrieve booked performances
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
include_once(INCLUDE_DIR . "/commonFunctions.php");

include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");

$nEventID = 0;
$iYearIndex = 0;

//If a Year is passed show performances for that year.  Otherwise default to the current year
if (isset($_REQUEST['year']) && $_REQUEST['year'] != "" && is_numeric($_REQUEST['year']) ) {
	$nPerformanceYear = (int) $_REQUEST['year'];
} else {
	$nPerformanceYear = (int) date("Y");
}

//If a performance ID is passed get the ID because it's details will be opened
if (isset($_REQUEST['eventID']) && $_REQUEST['eventID'] != "") {
	$nEventID = $_REQUEST['eventID'];
}

//If a flag is passed to show even adult shows, set it
$sShows = 'FAMILY';
if (isset($_REQUEST['shows']) && $_REQUEST['shows'] != "") {
	$sShows = $_REQUEST['shows'];
}


//Page Name 
$sPageName = "Schedule";
//Page Name 
$sPageTitle = "Performance Schedule {$nPerformanceYear}";
//Custom FB Image
$sFBImage = "FB_Performing.png";

$aBreadcrumb = array(
	0 => array('Home', 'index.php'),
	1 => array('Music', 'music.php'),
	2 => array('Performance', 'performance.php'),
	3 => array('Schedule', 'schedule.php')
);


$sActiveMenuItem = SCHEDULE_ACTIVE;

$sCurrentDate = date("Y-mm-dd");
$bNextPerformanceRendered = FALSE;

$criteria = [
	'performanceYear' => $nPerformanceYear,
	'booked' => true,
];

if ($sShows == 'FAMILY') {
	$criteria['adultShow'] = false;
} elseif ($sShows == 'ADULT') {
	$criteria['adultShow'] = true;
}

$performanceRepo = new \Datalayer\PerformanceRepository();
$aPerformances = $performanceRepo->find($criteria);
$aPerformanceYears = $performanceRepo->findDistinctYears();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
//Inlcude a common <HEAD> section
include(INCLUDE_DIR . "/HTMLHead.php");
?>

<BODY>
	<div class="container-fluid">
		<div class="row">
			<?php
			include(INCLUDE_DIR . "/header.php");
			?>
		</div>
		<div class="row">
			<main id="main-content" class="col-xs-12">
				<div class="row">
					<div class="col-xs-12 col-sm-10 col-sm-push-1 col-md-8 col-md-push-2">
						<div class="frame-box in-front-of-theme-image green-frame call-to-action">
							Well, COVID certainly threw a monkey wrench into my schedule for 2020.&nbsp;&nbsp;
							I am optimitiscally booking shows for 2021, so if you are willing to take the chance or you're looking for a virtual
							show, just reach out.&nbsp;&nbsp;I'm hopeful that I'll be back on the road and in front of wonderful folks again soon!
							<BR><BR>
							<?php
							renderNextPerformanceDescription();
							?>
							<BR><BR>
							If you'd like to see all my shows including ones that aren't just family music
							<a href="<?php echo "{$ROOT}/schedule.php?year={$nPerformanceYear}&shows=ALL";?>">CLICK HERE</a>
							<a href="<?php echo "{$ROOT}/performance.php"; ?>" class="button link-button booking-information">Booking</a>
							<div class="row">
								<?php
								$iYearIndex = sizeof($aPerformanceYears) - 6;
								while ($iYearIndex < sizeof($aPerformanceYears)) {
									if ($aPerformanceYears[$iYearIndex] != $nPerformanceYear) {
										echo "<div class=\"col-xs-4 col-sm-2 col-md-2 year-link\">";
										echo "<a href=\"{$ROOT}/schedule.php?year={$aPerformanceYears[$iYearIndex]}&shows={$sShows}\">";
										echo "{$aPerformanceYears[$iYearIndex]}</a></div>";
									} else {
										echo "<div class=\"col-xs-4 col-sm-2 col-md-2 year-link\">";
										echo "{$aPerformanceYears[$iYearIndex]}";
										echo "</div>";
									}
									$iYearIndex++;
								}

								?>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<section id="performance-listings" class="col-xs-12 col-sm-10 col-sm-push-1 col-md-8 col-md-push-2 full-width-xs">
						<?php
						foreach ($aPerformances as $oPerformance) {
							echo "<aside id=\"performance-" . $oPerformance->id . "\" class=\"subtle-blue performance-listing\">";
							if (!$bNextPerformanceRendered && $sCurrentDate <= date("Y-mm-dd", strtotime($oPerformance->performanceDate)) && !$oPerformance->adultShow) {
								renderNextPerformance(FALSE, FALSE);
								$bNextPerformanceRendered = TRUE;
							} else if ($oPerformance->id == $nEventID) {
								renderPerformanceListing($oPerformance, FALSE, FALSE);
							} else {
								renderPerformanceListing($oPerformance, FALSE, TRUE);
							}
							echo "</aside>";
						}
						?>
					</section>
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
