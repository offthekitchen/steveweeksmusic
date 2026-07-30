<?php
/*
*******************************************************************
SongMaintenance.php
This PHP file defines the Maintenance page for 
managing Songs.
NOTES
Date        Change
-------------------------------------------------------------
2015-12-01	Refactored and added Release Date, Run Time, ISRC, UPC
			and Catalog Number fields
2016-01-25	Added BMI Number and Active flag 
2016-01-27	Added Report Links 
2016-02-11	Added renderArtistDropDown()
2017-03-15	Made Responsive
2017-03-18	Made images Responsive
2017-12-03	Added DisplayOnSite Flag
2020-05-14	Added renderDatepicker()
2021-08-30	Updated for PHP 8
2026-07-30	Migrated to new Datalayer Song repository
*******************************************************************
*/
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

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

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of Song records from the DB
$aSongRecords = [];

$sActiveMenuItem = DISCOGRAPHY_ACTIVE;
$sPageName = "Song Maintenance";

?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<!-- DIV used for Image Preview Popup -->
	<div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15"
		id="preview_div"></div>

	<div class="container-fluid">
		<form name="SongMaint" action="SongMaintenance.php" method="post">

			<?php

			//Instantiate needed objects
			$songRepo = new \Datalayer\SongRepository();
			$thisSong = new \Datalayer\Song();
			$form = new Form();
			$nThisSongID = null;

			//Get the ID query string parameter
			if (isset($_REQUEST['SONG_ID']) && $_REQUEST['SONG_ID'] != "") {
				$nThisSongID = preg_replace("/[^0-9.]/", "", $_REQUEST['SONG_ID']);
			}

			//If an Artist ID is passed, go ahead and search songs for that artist
			if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "") {
				$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
				$_POST['btnSearch'] = "Search";
			}

			//If a CD ID is passed, go ahead and search songs for that CD
			if (isset($_REQUEST['CD_ID']) && $_REQUEST['CD_ID'] != "") {
				$_POST['selCD'] = $_REQUEST['CD_ID'];
				$_POST['btnSearch'] = "Search";
			}

			try {
				//If an ID was passed to the page, retrieve that record for update		
				if (!is_null($nThisSongID) && $nThisSongID !== '') {

					$entity = $songRepo->findById((int) $nThisSongID);

					if ($entity) {
						$thisSong = $entity;
						$aSongRecords = [$entity];
						loadSong($thisSong, $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;

					} else {
						$form->sMessage = "Song record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					}
				} else {
					//Based on which button was selected, perform processing necessary 
					//before the page is rendered
					// **************
					// *   ADD      *
					// **************
					if (isset($_POST["btnAdd"])) {
						buildSongObject($thisSong);

						if ($songRepo->insert($thisSong)) {
							$thisSong = $songRepo->findById((int) $thisSong->id) ?? $thisSong;
							$aSongRecords = [$thisSong];
							loadSong($thisSong, $form);

							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Song Added";
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "ADD RECORD FAILED";
							$form->nFormMode = FORM_MODE_NEW;
						}

					}
					// **************
					// *   UPDATE   *
					// **************
					else if (isset($_POST["btnUpdate"])) {

						buildSongObject($thisSong);

						if ($songRepo->update($thisSong)) {
							$thisSong = $songRepo->findById((int) $thisSong->id) ?? $thisSong;
							$aSongRecords = [$thisSong];
							loadSong($thisSong, $form);

							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Song Updated";
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "ERROR: Update Failed";
							$form->nFormMode = FORM_MODE_EDIT;
						}
					}
					// **************
					// *   DELETE   *
					// **************
					else if (isset($_POST["btnDelete"])) {

						buildSongObject($thisSong);

						if (!empty($thisSong->id) && $songRepo->delete((int) $thisSong->id)) {
							clearFormFields($form);

							$form->sMessage = "Song Deleted";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_NEW;
						} else {
							$form->sMessage = "DELETE FAILED";
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->nFormMode = FORM_MODE_EDIT;
						}

					}
					// **************
					// *   SEARCH   *
					// **************
					else if (isset($_POST["btnSearch"])) {
						buildSongObject($thisSong);

						$criteria = [
							'id' => $thisSong->id,
							'name' => $thisSong->name,
							'fuzzyName' => true,
							'trackNumber' => $thisSong->trackNumber,
							'artistId' => $thisSong->artistId,
							'orderBy' => 'name',
						];

						// CD_ID 0 (singles) is a valid search filter
						if ($thisSong->cdId !== null && $thisSong->cdId !== '') {
							$criteria['cdId'] = $thisSong->cdId;
						}

						if ($thisSong->active !== null) {
							$criteria['active'] = $thisSong->active;
						}
						if ($thisSong->displayOnSite !== null) {
							$criteria['displayOnSite'] = $thisSong->displayOnSite;
						}

						$aSongRecords = $songRepo->find($criteria);

						if (sizeof($aSongRecords) < 1) {
							$form->sMessage = "No Song records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($aSongRecords) == 1) {
							$thisSong = $aSongRecords[0];
							loadSong($thisSong, $form);

							$form->sMessage = "One Song record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;

						} else {
							$form->sMessage = "Select Song record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
						}
					}
					// *************
					// *   CLEAR   *
					// *************
					else if (isset($_POST["btnClear"])) {

						clearFormFields($form);

						$form->sMessage = "Search for records or Add new record";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					}
					// *************
					// *   CANCEL  *
					// *************
					else if (isset($_POST["btnCancel"])) {
						clearFormFields($form);
						$form->sMessage = "Search for records or Add new record";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					}
					// *************
					// *   COPY  *
					// *************
					else if (isset($_POST["btnCopy"])) {
						copyFormFields($form);
						$_POST['hdnSongID'] = NULL;

						$form->sMessage = "Search for records or Add new record";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					}
					// ***************
					// *  1st TIME   *
					// ***************
					else {
						$form->sMessage = "Search for records or Add new record";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					}

				}
			} catch (Exception $e) {
				error_log('SongMaintenance error: ' . $e->getMessage());
				$form->sMessage = "ERROR: " . $e->getMessage();
				$form->nMessageType = MESSAGE_TYPE_ERROR;
				$form->nFormMode = FORM_MODE_NEW;
			}

			?>
			<!-- Hidden Fields -->
			<input type="hidden" name="hdnSongID" value="<?php echo $_POST['hdnSongID'] ?>" />

			<?php
			include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
			?>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Song Maintenance
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 Buttons">
							<?php
							$form->renderButtons();
							?>
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
			<?php
			if ($form->nFormMode == FORM_MODE_SELECT) {
				?>

				<div class="row">

					<div class="col-xs-12">
						<?php

						$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

						//Header row for Results
						echo "<div class='row result-header'>";
						echo "	<div class='hidden-xs col-sm-12 result-header'>Song Name</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Songs</div>";
						echo "</div>";


						foreach ($aSongRecords as $oSongRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							echo "	<div class='col-xs-12 {$sResultStyleClass}'><A HREF='./SongMaintenance.php?SONG_ID={$oSongRecord->id}'>{$oSongRecord->name}</a></div>";
							echo "</div>";
						}
						?>
						<div class="row" style="height: 20px;"></div>
					</div>

					<?php
			} else {
				?>

					<div class="row">
						<div class="col-xs-12">

							<?php
							if ($_POST['hdnSongID'] > 0) {
								echo "<a href='" . ADMIN_DIR . "/SongReport.php?SONG_ID={$thisSong->id}";
								echo "' class='secondaryLinkButton'>Song Report</a>";
								echo "&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;";
								echo "<a href='" . ADMIN_DIR . "/SongReport.php?CD_ID={$thisSong->cdId}";
								echo "' class='secondaryLinkButton'>CD Report</a>";
							}
							?>
						</div>
						<div class="col-xs-12 col-md-6 FieldGroup">
							<div class="row">
								<div class="col-xs-12">
									NAME: <input type="text" name="txtSongName" value="<?php echo $_POST['txtSongName']; ?>"
										size="60" />
								</div>
								<div class="col-xs-12">
									TRACK #: <input type="text" name="txtTrackNumber"
										value="<?php echo $_POST['txtTrackNumber']; ?>" size="02" />
								</div>
								<div class="col-xs-12">
									LYRICS:
									<input type="text" name="txtLyricsHTML" value="<?php echo $_POST['txtLyricsHTML']; ?>"
										size="60" />
								</div>
								<div class="col-xs-12">
									SAMPLE MP3:
									<input type="text" name="txtSampleMP3" value="<?php echo $_POST['txtSampleMP3']; ?>"
										size="60" />
								</div>
								<div class="col-xs-12">
									ISRC:
									<input type="text" name="txtISRC" value="<?php echo $_POST['txtISRC']; ?>" size="60" />
								</div>
								<div class="col-xs-12">
									CATALOG #: <input type="text" name="txtCatalogNumber"
										value="<?php echo $_POST['txtCatalogNumber']; ?>" size="60" />
								</div>
								<div class="col-xs-12">
									RUN TIME:
									<input type="text" name="txtRunTime" value="<?php echo $_POST['txtRunTime']; ?>"
										size="60" /> </td>
								</div>
								<div class="col-xs-12">
									UPC: <input type="text" name="txtUPC" value="<?php echo $_POST['txtUPC']; ?>"
										size="60" />
								</div>
								<div class="col-xs-12">
									<?php
									renderArtistDropDown($_POST['selArtist']);
									?>
								</div>
								<div class="col-xs-12">
									<?php
									renderCDDropDown($_POST['selCD']);
									?>
								</div>
								<div class="col-xs-12">
									BMI NUMBER:
									<input type="text" name="txtBMINumber" value="<?php echo $_POST['txtBMINumber']; ?>"
										size="40" />
								</div>
								<div class="col-xs-3 col-sm-2">
									<input type="checkbox" name="chkActive" class="result-checkbox" value="ACTIVE" <?php if ($_POST['chkActive']) {
										echo " checked ";
									}
									; ?> />
								</div>
								<div class="col-xs-9 col-sm-10 result-checkbox-text">
									ACTIVE
								</div>
								<div class="col-xs-3 col-sm-2">
									<input type="checkbox" name="chkDisplayOnSite" class="result-checkbox" value="DISPLAY"
										<?php if ($_POST['chkDisplayOnSite']) {
											echo " checked ";
										}
										; ?> />
								</div>
								<div class="col-xs-9 col-sm-10 result-checkbox-text">
									DISPLAY ON SITE
								</div>
								<div class="col-xs-12">
									DESCRIPTION:<br /><textarea name="txtDescription" rows="4"
										cols="70"><?php echo $_POST['txtDescription']; ?></textarea>
								</div>
								<div class="col-xs-12">
									RELEASE DATE:<BR />
									<?php
									renderDatePicker("ReleaseDate", $_POST['ReleaseDate']);
									?>
								</div>
								<div class="col-xs-12">
									PURCHASE LINK: <input type="text" name="txtPurchaseLink"
										value="<?php echo $_POST['txtPurchaseLink']; ?>" size="40" />
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-md-3 FieldGroup">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-12 image-preview">
									<span class="Subtitle">Image</span><br />
									<?php
									if (!empty($_POST['txtImage'])) {
										$sFullImagePath = IMG_DIR . "/{$_POST['txtImage']}";
									} else {
										$sFullImagePath = NULL;
									}
									$form->renderImagePreview($sFullImagePath, "");
									?>
									<br /><br />
									<input type="text" name="txtImage" id="txtImage"
										value="<?php echo $_POST['txtImage']; ?>" size="30" /><BR />
									<input type="button" name="btnPreviewImage"
										onclick="preview_image('txtImage','<?php echo IMG_DIR ?>')" value="Preview" />
								</div>
								<div class="col-xs-12 col-sm-6 col-md-12 thumbnail-preview">
									<span class="Subtitle">Thumbnail Image</span><br />
									<?php
									if (!empty($_POST['txtThumbnail'])) {
										$sFullImagePath = IMG_DIR . "/{$_POST['txtThumbnail']}";
									} else {
										$sFullImagePath = NULL;
									}
									$form->renderImagePreview($sFullImagePath, "");
									?>
									<br /><br />
									<input type="text" name="txtThumbnail" id="txtThumbnail"
										value="<?php echo $_POST['txtThumbnail']; ?>" size="30" /><BR />
									<input type="button" name="btnPreviewThumbnail"
										onclick="preview_image('txtThumbnail','<?php echo IMG_DIR ?>')" value="Preview" />
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-3 FormFieldNoEdit">
									ID: <?php echo $_POST['hdnSongID']; ?>
								</div>
								<div class="col-xs-9 FormFieldNoEdit">
									LAST UPDATED: <?php echo $_POST['txtLastUpdate']; ?>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 Buttons">
									<?php
									$form->renderButtons();
									?>
								</div>
							</div>
							<div class="row">
								<dic class="col-xs-12 FormMessage">
									<?php
									$form->renderFormMessage();
									?>
							</div>
						</div>
					</div>
				</div>

				<?php
			}
			?>

		</form>
	</div>
	<?php


	/*
	 ********************************************************************************
	 * buildSongObject
	 * 
	 * This function loads builds a Song object from the data typed into the 
	 * form fields.
	 ********************************************************************************
	 */
	function buildSongObject(\Datalayer\Song $Song)
	{
		$id = $_POST['hdnSongID'] ?? null;
		$Song->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

		$Song->name = html_entity_decode($_POST['txtSongName'] ?? '', ENT_QUOTES);
		$Song->image = html_entity_decode($_POST['txtImage'] ?? '', ENT_QUOTES);
		$Song->thumbnail = html_entity_decode($_POST['txtThumbnail'] ?? '', ENT_QUOTES);
		$Song->purchaseLink = html_entity_decode($_POST['txtPurchaseLink'] ?? '', ENT_QUOTES);
		$Song->description = html_entity_decode($_POST['txtDescription'] ?? '', ENT_QUOTES);
		$Song->trackNumber = (int) ($_POST['txtTrackNumber'] ?? 0);

		$cdId = $_POST['selCD'] ?? null;
		if ($cdId === '' || $cdId === null) {
			$Song->cdId = null;
		} else {
			$Song->cdId = (int) $cdId;
		}

		$Song->lyricsHtml = html_entity_decode($_POST['txtLyricsHTML'] ?? '', ENT_QUOTES);
		$Song->runTime = html_entity_decode($_POST['txtRunTime'] ?? '', ENT_QUOTES);
		$Song->isrc = html_entity_decode($_POST['txtISRC'] ?? '', ENT_QUOTES);
		$Song->catalogNumber = html_entity_decode($_POST['txtCatalogNumber'] ?? '', ENT_QUOTES);
		$Song->upc = html_entity_decode($_POST['txtUPC'] ?? '', ENT_QUOTES);
		$Song->sampleMp3 = html_entity_decode($_POST['txtSampleMP3'] ?? '', ENT_QUOTES);
		$Song->artistId = (int) ($_POST['selArtist'] ?? 0);

		if (is_numeric($_POST['txtBMINumber'] ?? null)) {
			$Song->bmiNumber = (int) $_POST['txtBMINumber'];
		} else {
			$Song->bmiNumber = 0;
		}

		if (($_POST['chkActive'] ?? '') == "ACTIVE") {
			$Song->active = TRUE;
		}

		if (($_POST['chkDisplayOnSite'] ?? '') == "DISPLAY") {
			$Song->displayOnSite = TRUE;
		}

		//Release Date	
		$dtReleaseDate = isset($_REQUEST["ReleaseDate"]) ? $_REQUEST["ReleaseDate"] : "";
		if ($dtReleaseDate > "0000-00-00") {
			$dtReleaseDate = isset($_POST["ReleaseDate"]) ? $_POST["ReleaseDate"] : "";
		}
		if ($dtReleaseDate > "0000-00-00") {
			$Song->releaseDate = $dtReleaseDate;
		}
	}


	/*
	 ********************************************************************************
	 * loadSong
	 * 
	 * This function loads the form field array from a populated Song object
	 * so that it will be displayed in the form fields
	 ********************************************************************************
	 */
	function loadSong(\Datalayer\Song $Song, $form)
	{
		if (!is_null($Song->id)) {
			$_POST['hdnSongID'] = $Song->id;

			$_POST['selCD'] = $Song->cdId;
			$_POST['txtSongName'] = htmlentities($Song->name ?? '', ENT_QUOTES);
			$_POST['txtTrackNumber'] = htmlentities((string) $Song->trackNumber, ENT_QUOTES);
			$_POST['txtImage'] = htmlentities($Song->image ?? '', ENT_QUOTES);
			$_POST['txtThumbnail'] = htmlentities($Song->thumbnail ?? '', ENT_QUOTES);
			$_POST['txtPurchaseLink'] = htmlentities($Song->purchaseLink ?? '', ENT_QUOTES);
			$_POST['txtDescription'] = htmlentities($Song->description ?? '', ENT_QUOTES);
			$_POST['txtLyricsHTML'] = htmlentities($Song->lyricsHtml ?? '', ENT_QUOTES);
			$_POST['txtSampleMP3'] = htmlentities($Song->sampleMp3 ?? '', ENT_QUOTES);
			$_POST['ReleaseDate'] = htmlentities($Song->releaseDate ?? '', ENT_QUOTES);
			$_POST['txtRunTime'] = htmlentities($Song->runTime ?? '', ENT_QUOTES);
			$_POST['txtISRC'] = htmlentities($Song->isrc ?? '', ENT_QUOTES);
			$_POST['txtCatalogNumber'] = htmlentities($Song->catalogNumber ?? '', ENT_QUOTES);
			$_POST['txtUPC'] = htmlentities($Song->upc ?? '', ENT_QUOTES);
			$_POST['txtLastUpdate'] = htmlentities($Song->lastUpdate ?? '', ENT_QUOTES);
			$_POST['selArtist'] = $Song->artistId;
			$_POST['txtBMINumber'] = htmlentities((string) $Song->bmiNumber, ENT_QUOTES);
			$_POST['chkActive'] = $Song->active;
			$_POST['chkDisplayOnSite'] = $Song->displayOnSite;

		} else {
			foreach ($_POST as $fieldName => $fieldValue) {
				$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
			}
		}
	}


	/*
	 ********************************************************************************
	 * clearFormFields
	 ********************************************************************************
	 */
	function clearFormFields($form)
	{
		$_POST = array();
	}

	/*
	 ********************************************************************************
	 * copyFormFields
	 ********************************************************************************
	 */
	function copyFormFields($form)
	{
		foreach ($_POST as $fieldName => $fieldValue) {
			$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
		}
	}

	?>

</body>
<script type="text/javascript">
	<!--
	function validate_form ( )
	{
		bValid = true;
		sErrorMessage = "";
	
		//Song Name is a required Field
		if ( document.SongMaint.txtSongName.value == "" )
		{
			sErrorMessage += "Song Name Required\n";
			bValid = false;
		}
	
		//Artist is a required Field
		if ( document.SongMaint.selArtist.value == "0" )
		{
			sErrorMessage += "Artist Required\n";
			bValid = false;
		}
	
		//BMI Number must be numeric
		if ( !is_Numeric(document.SongMaint.txtBMINumber.value) )
		{
			sErrorMessage += "BMI NUmber Must be Numeric\n";
			bValid = false;
		}
	
		//If any errors, alert
		if (!bValid)
		{
			alert(sErrorMessage);
		}
	
		return bValid;
	}
	
	function is_Numeric(num) {
	  return !isNaN(parseFloat(num)) && isFinite(num);
	}
	
	function getCDs() {
		artistElement = document.getElementById("selArtist"); 
		nArtistID = artistElement.options[artistElement.selectedIndex].value;
	
		if (nArtistID > 0) {
	
			if (window.XMLHttpRequest) {
				xmlhttp = new XMLHttpRequest();
			} else {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("selCD").innerHTML = xmlhttp.responseText;
				}
			}
			xmlhttp.open("GET","GetPotentialCDs.php?Artist_ID="+nArtistID,true);
			xmlhttp.send();
		}
		
	}
	//-->
</script>

</html>
