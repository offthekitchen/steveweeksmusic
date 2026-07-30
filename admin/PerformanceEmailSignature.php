<?php
/*
*******************************************************************
PerformanceEmailSignature.php
This PHP file displays a list of upcoming performances
NOTES
Date        Change
-------------------------------------------------------------
2015-08-01	Created
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

	 $sActiveMenuItem = PERFORMANCES_ACTIVE;	
	$sPageName = "Artist Maintenance";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>
	<?php
	
	//include Performance Class		
 	include_once (CLASS_DIR . "/class_Performance.php");

	//Array of Performance records from the DB
	global $aPerformanceRecords;
	
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");

		//Instantiate needed objects
		$oUpcomingPerformances = new Performance();

		//Set a date one month from now
		
		$dtEndDate = date("Y-m-d",strtotime('+2 Month'));
		
		?>
		<div class="container-fluid"> 
		<div class="row">
		<div class="col-xs-12">
		<?php
		echo "ALL PERFORMANCES BETWEEN TODAY AND {$dtEndDate} <BR>";
		
		$oUpcomingPerformances->dtEndDate = $dtEndDate;
		$oUpcomingPerformances->bFuturePerformances = TRUE;
		
		//Search the Database for records matching the search criteria			
		if ($oUpcomingPerformances->getPerformance())
		{
			//Records found
			if (sizeof($oUpcomingPerformances->aPerformanceRecords) > 0)
			{
				foreach($oUpcomingPerformances->aPerformanceRecords as $oUpcomingPerformance)
				{
					$sPerformanceURL = "http://www.steveweeksmusic.com/schedule.php?year=" . date("Y",strtotime($oUpcomingPerformance->dtPerformanceDate)) . "&eventID={$oUpcomingPerformance->nPerformanceID}#performance-{$oUpcomingPerformance->nPerformanceID}";
			
					echo "<BR><a href='{$sPerformanceURL}'>";
					echo "{$oUpcomingPerformance->sLocation}, {$oUpcomingPerformance->sLocationCity}, {$oUpcomingPerformance->sLocationState} (" . date("l",strtotime($oUpcomingPerformance->dtPerformanceDate)) . ", " . date("m-d-Y",strtotime($oUpcomingPerformance->dtPerformanceDate)) . " at {$oUpcomingPerformance->sPerformanceTime})";
					echo "</a>";			  			

				}
			}
			else
			{
				echo "NO UPCOMING PERFORMANCES FOUND.";
			}
		}
		else
		{
			echo "ERROR RETRIEVING PERFORMANCE DATA! ";
		}
	?>
		</div>
	</div>
</div>
</body>
</html>
