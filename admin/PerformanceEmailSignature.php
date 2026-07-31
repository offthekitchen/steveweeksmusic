<?php
/*
*******************************************************************
PerformanceEmailSignature.php
This PHP file displays a list of upcoming performances
NOTES
Date        Change
-------------------------------------------------------------
2015-08-01	Created
2026-07-30	Migrated to new Datalayer Performance repository
*******************************************************************
*/	
	error_reporting(E_ALL ^ E_NOTICE);
	
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");

	//include new datalayer
	include_once(DATALAYER_DIR . "/Connection.php");
	include_once(DATALAYER_DIR . "/Performance.php");
	include_once(DATALAYER_DIR . "/PerformanceRepository.php");

	 $sActiveMenuItem = PERFORMANCES_ACTIVE;	
	$sPageName = "Artist Maintenance";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>
	<?php
	
	$performanceRepo = new \Datalayer\PerformanceRepository();
	
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");

		//Set a date one month from now
		
		$dtEndDate = date("Y-m-d",strtotime('+2 Month'));
		
		?>
		<div class="container-fluid"> 
		<div class="row">
		<div class="col-xs-12">
		<?php
		echo "ALL PERFORMANCES BETWEEN TODAY AND {$dtEndDate} <BR>";
		
		$aUpcomingPerformances = $performanceRepo->find([
			'future' => true,
			'endDate' => $dtEndDate,
		]);
		
		if (!empty($aUpcomingPerformances))
		{
			foreach($aUpcomingPerformances as $upcomingPerformance)
			{
				$sPerformanceURL = "http://www.steveweeksmusic.com/schedule.php?year=" . date("Y",strtotime($upcomingPerformance->performanceDate)) . "&eventID={$upcomingPerformance->id}#performance-{$upcomingPerformance->id}";
		
				echo "<BR><a href='{$sPerformanceURL}'>";
				echo "{$upcomingPerformance->location}, {$upcomingPerformance->locationCity}, {$upcomingPerformance->locationState} (" . date("l",strtotime($upcomingPerformance->performanceDate)) . ", " . date("m-d-Y",strtotime($upcomingPerformance->performanceDate)) . " at {$upcomingPerformance->performanceTime})";
				echo "</a>";			  			

			}
		}
		else
		{
			echo "NO UPCOMING PERFORMANCES FOUND.";
		}
	?>
		</div>
	</div>
</div>
</body>
</html>
