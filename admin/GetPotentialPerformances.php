<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
<?php
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");

	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");

	include_once(DATALAYER_DIR . "/Connection.php");
	include_once(DATALAYER_DIR . "/Performance.php");
	include_once(DATALAYER_DIR . "/PerformanceRepository.php");

	$dtIncludeDate = $_GET['date'] ?? '';

	try {
		$performanceRepo = new \Datalayer\PerformanceRepository();
		$aPerformanceRecords = $performanceRepo->find(['performanceDate' => $dtIncludeDate]);

		if (sizeof($aPerformanceRecords) > 0) {
			echo "<OPTION VALUE='0'>NONE</OPTION>";
			foreach ($aPerformanceRecords as $oPotentialPerformance) {
				echo "<OPTION VALUE=" . $oPotentialPerformance->id . ">" . $oPotentialPerformance->name;
				echo " - " . $oPotentialPerformance->location . "</OPTION>";
			}
		} else {
			echo "<OPTION VALUE='0' SELECTED>NONE</OPTION>";
		}
	} catch (\Throwable $e) {
		echo "<OPTION>ERROR RETRIEVING PERFORMANCES" . $e->getMessage() . "</OPTION>";
	}

?>
</body>
</html>
