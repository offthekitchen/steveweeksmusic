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
	include_once(DATALAYER_DIR . "/Tour.php");
	include_once(DATALAYER_DIR . "/TourRepository.php");

	$dtIncludeDate = $_GET['date'] ?? '';

	try {
		$tourRepo = new \Datalayer\TourRepository();
		$aTourRecords = $tourRepo->find(['includeDate' => $dtIncludeDate]);

		if (sizeof($aTourRecords) > 0) {
			echo "<OPTION VALUE='0'>NONE</OPTION>";
			foreach ($aTourRecords as $oPotentialTour) {
				echo "<OPTION VALUE=" . $oPotentialTour->id . ">" . $oPotentialTour->name . "</OPTION>";
			}
		} else {
			echo "<OPTION VALUE='0' SELECTED>NONE</OPTION>";
		}
	} catch (\Throwable $e) {
		echo "<OPTION>ERROR RETRIEVING TOURS" . $e->getMessage() . "</OPTION>";
	}

?>
</body>
</html>
