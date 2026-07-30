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
	
	//include Tour Class		
 	include_once (CLASS_DIR . "/class_CD.php");
	
	$nArtistID = $_GET['Artist_ID'];

	$oPotentialCDs = new CD();
	
	$oPotentialCDs->nArtistID = $nArtistID;
	
	if($oPotentialCDs->getCD())
	{
		if(sizeof($oPotentialCDs->aCDRecords) > 0) 
		{
			echo "<OPTION VALUE=''>ALL</OPTION>";
			echo "<OPTION VALUE='0'>SINGLES</OPTION>";
			foreach($oPotentialCDs->aCDRecords as $oPotentialCD)
			{
				echo "<OPTION VALUE=". $oPotentialCD->nCDID . ">" . $oPotentialCD->sCDName . "</OPTION>"; 
			}
		}
		else
		{
			echo "<OPTION VALUE='0'>SINGLES</OPTION>";
		}
	
	}
	else
	{
		echo "<OPTION>ERROR RETRIEVING CD" . $oPotentialCDs->sErrorMessage . "</OPTION>";
	}

?>
</body>
</html>
