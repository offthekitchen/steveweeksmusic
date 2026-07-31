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
2026-07-30	Migrated to new Datalayer Song repository
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

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Song.php");
include_once(DATALAYER_DIR . "/SongRepository.php");
include_once(DATALAYER_DIR . "/CD.php");
include_once(DATALAYER_DIR . "/CDRepository.php");

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
			$songRepo = new \Datalayer\SongRepository();
			$cdRepo = new \Datalayer\CDRepository();

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
						$criteria = [];

						if ($_POST['selSong'] > 0) {
							$criteria['id'] = (int) $_POST['selSong'];
						}

						if (is_numeric($_POST['selCD'])) {
							$criteria['cdId'] = (int) $_POST['selCD'];
						}

						try {
							$aSongRecords = $songRepo->find($criteria);

							if (!empty($aSongRecords)) {
								foreach ($aSongRecords as $song) {
									echo "<div class='row'><div class='col-xs-12'> ";
									buildSongData($song, $cdRepo);
									echo "</div></div>";
								}
							} else {
								$form->nMessageType = MESSAGE_TYPE_ERROR;
								$form->sMessage = "FAILED TO GET SONG DATA: No records found.";
							}
						} catch (\Throwable $e) {
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "FAILED TO GET SONG DATA: " . $e->getMessage();
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
function buildSongData(\Datalayer\Song $song, \Datalayer\CDRepository $cdRepo)
{

	//Default some values in case there is no CD associated with it to avoid formatting problems
	$dtReleaseDate = "Unknown";
	$sUPC = "Unknown";
	$cd = null;

	if (!empty($song->cdId) && $song->cdId > 0) {
		$cd = $cdRepo->findById((int) $song->cdId);
		if ($cd) {
			if (empty($song->upc)) {
				$sUPC = $cd->upc;
			} else {
				$sUPC = $song->upc;
			}
			if (empty($song->releaseDate) || $song->releaseDate == '0000-00-00') {
				$dtReleaseDate = $cd->releaseDate;
			} else {
				$dtReleaseDate = $song->releaseDate;
			}
		}
	} else {
		$sUPC = $song->upc;
		$dtReleaseDate = $song->releaseDate;
	}

	echo "<div class='SongInfo'>";
	echo "<div class='row'>";
	echo "<dl>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>Song Name:</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd class='wordWrap'><a href='" . ADMIN_DIR . "/SongMaintenance.php?SONG_ID={$song->id}'>{$song->name}</a></dd>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>CD</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	if ($cd && !empty($cd->name)) {
		echo "<dd class='wordWrap'><a href='" . ADMIN_DIR . "/CDMaintenance.php?CD_ID={$cd->id}' class='CDName'>{$cd->name}</a></dd>";
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
	echo "<dd>{$song->runTime}</dd>";
	echo "</div>";

	echo "<div>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>ISRC</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd>{$song->isrc}</dd>";
	echo "</div>";

	echo "<div>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>Catalog #</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd>{$song->catalogNumber}</dd>";
	echo "</div>";

	echo "<div class='col-xs-12 col-sm-2'>";
	echo "<dt>BMI #</dt>";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<dd>{$song->bmiNumber}</dd>";
	echo "</div>";

	echo "</dl>";
	echo "</div>";
	echo "</div>";
}
?>
