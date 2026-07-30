<?php
/*
*******************************************************************
SongReport.php
This PHP file generates a Song Report 
NOTES
Date        Change
-------------------------------------------------------------
2016-01-05  Created
2016-01-25  Added BMI Number and link to Song Maintenance
2016-01-27  Changed dropdowns to use common functions
2017-04-09	Made Responsive
*******************************************************************
*/

error_reporting(E_ALL ^ E_NOTICE);

//This include defines the relative path to the root directory from this sub-directory
include_once("root.inc.php");

//inlcude web site settings
include_once($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once(SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//inlcude admin settings
include_once(ADMIN_DIR . "/includes/AdminSettings.php");
//inlcude Common Functions
include_once(ADMIN_INCLUDE_DIR . "/CommonFunctions.php");

//include Song Class		
include_once(CLASS_DIR . "/class_Song.php");

//include Expense Class		
include_once(CLASS_DIR . "/class_CD.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

$sActiveMenuItem = REPORTS_ACTIVE;
$sPageName = "Song Report";

?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>
	<?php



	//Require the Class for the calendar picker
	//require_once (CLASS_DIR . "/tc_calendar.php");
	
	?>
	<div class="container-fluid">
		<form name="SongReport" action="SongReport.php" method="post">
			<?php
			//If a Song ID is passed, pre-select that Song
			if (isset($_REQUEST['SONG_ID']) && $_REQUEST['SONG_ID'] != "") {
				$_POST['selSong'] = $_REQUEST['SONG_ID'];
				$_POST['btnGenerate'] = "GenerateReport";
			}

			//If a CD ID is passed, pre-select Songs for that CD
			if (isset($_REQUEST['CD_ID']) && $_REQUEST['CD_ID'] != "") {
				$_POST['selCD'] = $_REQUEST['CD_ID'];
				$_POST['btnGenerate'] = "GenerateReport";
			}

			//Instantiate needed objects
			$form = new Form();
			$aSongData = array();
			$oThisSong = new Song();

			include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");


			?>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							SONG REPORT:
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 Buttons">
							<input type="submit" name="btnGenerate" value="Generate Report"
								onclick="javascript: return validate_form()" />
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 FormMessage">
							<?php
							$form->renderFormMessage();
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroup">
					<?php

					if (!isset($_POST['selSong'])) {
						$_POST['selSong'] = 0;
					}

					if (!isset($_POST['selCD'])) {
						$_POST['selCD'] = 0;
					}

					//if a song is selected, reset the CD dropdown to "All" since it doesn't matter
					if ($_POST['selSong'] > 0) {
						$_POST['selCD'] = NULL;
					}
					renderCDDropDown($_POST['selCD']);
					echo "<BR>";
					renderSongDropDown($_POST['selSong']);

					if (isset($_POST["btnGenerate"])) {
						$oSongs = new Song();

						if ($_POST['selSong'] > 0) {
							$oSongs->nSongID = $_POST['selSong'];
						}

						if (is_numeric($_POST['selCD'])) {
							$oSongs->nCDID = $_POST['selCD'];
						}

						if ($oSongs->getSong()) {
							foreach ($oSongs->aSongRecords as $oSong) {
								echo "<div class='row'><div class='col-xs-12'> ";
								buildSongData($oSong);
								echo "</div></div>";
							}
						} else {
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "FAILED TO GET SONG DATA: {$oThisSong->sErrorMessage}";
						}
					}
					?>
				</div>
			</div>
		</form>
	</div>
</body>
<script type="text/javascript">
	<!--
	function validate_form ( )
	{
		bValid = true;
		sErrorMessage = "";
	
		//If any errors, alert
		if (!bValid)
		{
			alert(sErrorMessage);
		}
	
		return bValid;
	}
	//-->
</script>

</html>

<?php
/*
 ********************************************************************************
 * buildSongData()
 * 
 * This function builds a table of Song Data
 ********************************************************************************
 */
function buildSongData(&$oSong)
{

	//Default some values in case there is no CD associated with it to avoid fdormatting problems
	$dtReleaseDate = "Unknown";
	$dtReleaseDate = "Unknown";
	$sUPC = "Unknown";

	if ($oSong->nCDID > 0) {
		$oCD = new CD();
		$oCD->nCDID = $oSong->nCDID;
		if ($oCD->getCD()) {
			$sCDName = $oCD->aCDRecords[0]->sCDName;

			if (empty($oSong->sUPC)) {
				$sUPC = $oCD->aCDRecords[0]->sUPC;
			} else {
				$sUPC = $oSong->sUPC;
			}
			if (empty($oSong->dtReleaseDate) || $oSong->dtReleaseDate == '0000-00-00') {
				$dtReleaseDate = $oCD->aCDRecords[0]->dtReleaseDate;
			} else {
				$dtReleaseDate = $oSong->dtReleaseDate;
			}
		} else {
			//ERROR RETRIEVING CD
		}
	} else {
		$sUPC = $oSong->sUPC;
		$dtReleaseDate = $oSong->dtReleaseDate;
	}

	echo "<div class='SongInfo'>";
	echo "<div class='row'>";
	echo "<dl>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>Song Name:</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd class='wordWrap'><a href='" . ADMIN_DIR . "/SongMaintenance.php?SONG_ID={$oSong->nSongID}'>{$oSong->sSongName}</a></dd>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>CD</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	if (!empty($oCD->aCDRecords[0]->sCDName)) {
		echo "<dd class='wordWrap'><a href='" . ADMIN_DIR . "/CDMaintenance.php?CD_ID={$oCD->aCDRecords[0]->nCDID}' class='CDName'>{$oCD->aCDRecords[0]->sCDName}</a></dd>";
	} else {
		echo "<dd>None</dd>";
	}
	echo "</div>";

	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>Release Date</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd>{$dtReleaseDate}</dd>";
	echo "</div>";

	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>UPC</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd>{$sUPC}</dd>";
	echo "</div>";

	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>Run Time</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd>{$oSong->sRunTime}</dd>";
	echo "</div>";

	echo "<div>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>ISRC</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd>{$oSong->sISRC}</dd>";
	echo "</div>";

	echo "<div>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>Catalog #</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd>{$oSong->sCatalogNumber}</dd>";
	echo "</div>";

	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>BMI #</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd>{$oSong->nBMINumber}</dd>";
	echo "</div>";

	echo "</dl>";
	echo "</div>";
	echo "</div>";
}
?>