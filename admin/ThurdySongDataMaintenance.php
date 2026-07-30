<?php
/*
*******************************************************************
ThurdySongDataMaintenance.php
This PHP file defines the Maintenance page for 
managing Songs.
NOTES
Date        Change
-------------------------------------------------------------
2018-11-21	Created.
2021-08-30	Updated for PHP 8
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

//include Song Class		
include_once(CLASS_DIR . "/class_ThurdySongData.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of Song records from the DB
global $aThurdySongDataRecords;

global $nThisSongId;

$sActiveMenuItem = MISC_ACTIVE;
$sPageName = "Thurdy Song Data Maintenance";

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
		<form name="ThurdySongDataMaint" action="ThurdySongDataMaintenance.php" method="post">

			<?php

			//Instantiate needed objects
			$thisSong = new ThurdySongData();
			$form = new Form();

			//Get the Song ID query string parameter
			if (isset($_REQUEST['SONGID']) && $_REQUEST['SONGID'] != "") {
				$nThisSongId = $_REQUEST['SONGID'];
				if (!is_numeric($nThisSongID)) {
					error_log('SongMaintenance.php: INVALID SONG ID: ' . $nThisSongID);
				}
			}

			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisSongId)) {
				$thisSong->nSongId = $nThisSongId;

				//Search the Database for records matching the search criteria			
				if ($thisSong->getThurdySongData()) {

					//Records found
					if (sizeof($thisSong->aThurdySongDataRecords) > 0) {

						//Only One Record should be returned.  Add this to the form field array						
						//so that it displays in the form fields and to the values in the
						//current Object.
						loadData($thisSong->aThurdySongDataRecords[0], $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//The record was not found
						$form->sMessage = "Song record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					}
				} else {
					//Error
					$form->sMessage = $thisSong->sErrorMessage;
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_NEW;
				}
			} else {
				//Based on which button was selected, perform processing necessary 
				//before the page is rendered
				// **************
				// *   ADD      *
				// **************
				if (isset($_POST["btnAdd"])) {
					//Load values into DB array
					buildObject($thisSong);

					//Insert record
					if ($thisSong->insertThurdySongData()) {
						//Load the form fields with the newly populated object
						loadData($thisSong, $form);

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Song Added";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {

						//The insert of the new expense failed
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED: {$thisSong->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;

					}

				}
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) {

					//Load values from form field array into DB object
					buildObject($thisSong);

					//Update record
					if ($thisSong->updateSongData()) {
						//Load the form fields with the newly populated object
						loadData($thisSong, $form);

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Song Updated";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//Failed to update expense record
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed - {$thisSong->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   DELETE   *
				// **************
				else if (isset($_POST["btnDelete"])) {

					//Load DB record
					buildObject($thisSong);

					//Delete record
					if ($thisSong->deleteThurdySongData()) {
						//Clear the form fields
						clearFormFields($form);

						//Success
						$form->sMessage = "Song Data Deleted";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
						$form->sMessage = "DELETE FAILED: {$thisSong->sErrorMessage}";
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_EDIT;
					}

				}
				// **************
				// *   SEARCH   *
				// **************
				else if (isset($_POST["btnSearch"])) {
					//Load Array of Search Values
					buildObject($thisSong);
					$thisSong->sOrderByField = "SONG_ID";

					//Search the Database for records matching the search criteria			
					if ($thisSong->getThurdySongData()) {
						//No records found
						if (sizeof($thisSong->aThurdySongDataRecords) < 1) {
							$form->sMessage = "No Song Data records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($thisSong->aThurdySongDataRecords) == 1) {
							//Only One Record returned.  Add this to the form field array
							//so that it displays in the form fields
							loadData($thisSong->aThurdySongDataRecords[0], $form);
							//Store the ID of the performance
							$nThisSongId = $thisSong->aThurdySongDataRecords[0]->nSongId;

							$form->sMessage = "One Song record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;

						}
						//If Multiple records found, the array of search reults will be populated
						else {
							//Multiiple records returned
							$form->sMessage = "Select Song record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
						}

					} else {
						//Attempt to get records failed
						$form->sMessage = $thisSong->sErrorMessage;
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_NEW;
					}
				}
				// *************
				// *   CLEAR   *
				// *************
				else if (isset($_POST["btnClear"]) || isset($_POST["btnCancel"])) {

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
					$_POST['hdnThurdySongDataID'] = NULL;

					$form->sMessage = "Search for records or Add new record";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;
				}
				// ***************
				// *  1st TIME   *
				// ***************
				else {
					copyFormFields($form);
					$form->sMessage = "Search for records or Add new record";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;
				}

			}

			?>
			<!-- Hidden Fields -->
			<input type="hidden" name="hdnThurdySongDataID" value="<?php echo $_POST['hdnThurdySongDataID'] ?>" />

			<div class="row">
				<?php
				include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Thurdy Song Data Maintenance
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
						echo "<div class='hidden-xs col-sm-6 result-header'>Song<BR>ID</div>";
						echo "<div class='hidden-xs col-sm-6 result-header'>Title</div>";
						echo "<div class='visible-xs col-xs-12 result-header'>Songs</div>";
						echo "</div>";

						foreach ($thisSong->aThurdySongDataRecords as $oThurdySongDataRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							echo "<div class='col-xs-12 col-sm-6 {$sResultStyleClass}'>";
							echo "<A HREF='./ThurdySongDataMaintenance.php?SONGID={$oThurdySongDataRecord->nSongId}'>{$oThurdySongDataRecord->nSongId}</A></div>";

							echo "<div class='col-xs-12 col-sm-6 {$sResultStyleClass}'>";
							echo "<A HREF='./ThurdySongDataMaintenance.php?SONGID={$oThurdySongDataRecord->nSongId}'>{$oThurdySongDataRecord->sSongTitle}</A></div>";
							echo "</div>";

						}
						?>
					</div>
				</div>
				<?php
			} else {
				?>
				<div class="row">
					<div class="col-xs-12 FieldGroupTitle">
						DETAILS
					</div>
					<div class="col-xs-12 FieldGroup">
						<div class="row">
							<div class="col-xs-12 col-md-5">
								SONG:
								<?php renderArtistSongDropDown($_POST['selSong'], THURDY_ARTIST_ID); ?>
							</div>
							<div class="col-xs-12 col-md-5">
								DROP:
								<?php renderDropDropDown($_POST['selDrop']); ?>
							</div>
							<div class="col-xs-12 col-md-5">
								VIDEO LINK:
								<input type="text" name="txtVideoLink" value="<?php echo $_POST['txtVideoLink']; ?>"
									size="40" />
							</div>

							<div class="col-xs-12 col-md-5">
								SUBMITTED BY:
								<input type="text" name="txtVideoSubmittedBy"
									value="<?php echo $_POST['txtVideoSubmittedBy']; ?>" size="30" />
							</div>
							<div class="col-xs-12 col-md-5">
								LIVE VERSION:
								<input type="text" name="txtLiveVersion" value="<?php echo $_POST['txtLiveVersion']; ?>"
									size="40" />
							</div>
							<div class="col-xs-12 col-md-5">
								<div class="row">
									<div class="col-xs-12">
										Art Image
									</div>
									<div class="col-xs-12">
										<?php
										if (!empty($_POST['txtArtImage'])) {
											$sFullImagePath = DROP_IMG_DIR . "/{$_POST['txtArtImage']}";
										} else {
											$sFullImagePath = NULL;
										}
										echo "<div style='padding: 15px;'><IMG SRC='" . $sFullImagePath . "' BORDER=0 Height=100 ></div>";
										?>
									</div>
									<div class="col-xs-12">
										<input type="text" name="txtArtImage" id="txtArtImage"
											value="<?php echo $_POST['txtArtImage']; ?>" size="30" /><BR />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 FieldGroup">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class=" col-xs-3 FormFieldNoEdit">
										ID: <?php echo $_POST['hdnThurdySongDataID']; ?>
									</div>
									<div class=" col-xs-9 FormFieldNoEdit">
										LAST UPDATED: <?php echo $_POST['txtLastUpdate']; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div>
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
			}
			?>

		</form>
	</div>
	<?php


	/*
	 ********************************************************************************
	 * buildObject
	 * 
	 * This function loads builds a song object from the data typed into the 
	 * form fields.
	 ********************************************************************************
	 */
	function buildObject($oSong)
	{

		//Load the Array used to populate the form fields based on the newly loaded object
		$oSong->nSongId = $_POST['selSong'];
		$oSong->nThurdySongDataId = $_POST['hdnThurdySongDataID'];
		$oSong->sVideoLink = $_POST['txtVideoLink'];
		$oSong->sVideoSubmittedBy = $_POST['txtVideoSubmittedBy'];
		$oSong->sLiveVersion = $_POST['txtLiveVersion'];
		$oSong->nDropId = $_POST['selDrop'];
		$oSong->sArtImage = $_POST['txtArtImage'];

	}


	/*
	 ********************************************************************************
	 * loadData
	 * 
	 * This function loads the form field array from a populated Song object
	 * so that it will be displayed in the form fields
	 ********************************************************************************
	 */
	function loadData(&$oSong, $form)
	{
		if (!is_null($oSong->nSongId)) {
			//Load Hidden Fields
			$_POST['hdnThurdySongDataID'] = $oSong->nThurdySongDataId;

			$_POST['selSong'] = $oSong->nSongId;
			$_POST['txtSongTitle'] = htmlentities($oSong->sSongTitle, ENT_QUOTES);
			$_POST['txtVideoLink'] = htmlentities($oSong->sVideoLink, ENT_QUOTES);
			$_POST['txtVideoSubmittedBy'] = htmlentities($oSong->sVideoSubmittedBy, ENT_QUOTES);
			$_POST['txtLiveVersion'] = htmlentities($oSong->sLiveVersion, ENT_QUOTES);
			$_POST['selDrop'] = $oSong->nDropId;
			$_POST['txtArtImage'] = htmlentities($oSong->sArtImage, ENT_QUOTES);
		} else {
			//Load Form field values into array 
			foreach ($_POST as $fieldName => $fieldValue) {

				//$_POST[$fieldName]= htmlentities(stripslashes($fieldValue));
				$_POST[$fieldName] = $fieldValue;

			}


		}

	}


	/*
	 ********************************************************************************
	 * clearFormFields
	 * 
	 * This function clears the form fields
	 ********************************************************************************
	 */
	function clearFormFields($form)
	{
		//Hidden Fileds must be set to 0
		$_POST = array();
	}

	/*
	 ********************************************************************************
	 * copyFormFields
	 * 
	 * This function copies the data from the form back into the form field array
	 ********************************************************************************
	 */
	function copyFormFields($form)
	{
		//Load Form field values into array 
		foreach ($_POST as $fieldName => $fieldValue) {
			$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
		}

	}


	?>

</body>
<script type="text/javascript">
	<!--
	
	//******************************************
	// validate_form
	// This script checks the user imnput for
	// errors or missing data 
	//******************************************
	function validate_form ( )
	{
		bValid = true;
		sErrorMessage = "";
	
		//Revenue Type is a required Field
		if ( document.ThurdySongDataMaint.selSong.value == "" || document.ThurdySongDataMaint.selSong.value == 0 )
		{
			sErrorMessage += "Song Required\n";
			bValid = false;
		}
	
		if (!bValid)
		{
			alert(sErrorMessage);
		}
	
		return bValid;
	}
	//-->
</script>

</html>