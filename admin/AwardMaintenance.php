<?php
/*
*******************************************************************
AwardMaintenance.php
This PHP file defines the Maintenance page for managing Artists.
NOTES
Date        Change
-------------------------------------------------------------
2017-03-20	Made responsive
2017-09-02	Improved Responsivity
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
	$sActiveMenuItem = DISCOGRAPHY_ACTIVE;
	$sPageName = "Award Maintenance";	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>
	<!-- Javascript required for Calendar picker -->
	<script language="javascript" src="./javascript/calendar.js"></script>

	<script type="text/javascript">
		<!--
		function validate_form() {
			bValid = true;
			sErrorMessage = "";

			//Award Name is a required Field
			if (document.AwardMaint.txtAwardName.value == "") {
				sErrorMessage += "Award Name Required\n";
				bValid = false;
			}

			//Artist is a required Field
			if (document.SongMaint.selArtist.value == "0") {
				sErrorMessage += "Artist Required\n";
				bValid = false;
			}

			//If any errors, alert
			if (!bValid) {
				alert(sErrorMessage);
			}

			return bValid;
		}
		//
		-->
	</script>

</head>

<body>

	<!-- DIV used for Image Preview Popup -->
	<div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15" id="preview_div"></div>

	<?php

	//include Award Class		
	include_once(CLASS_DIR . "/class_Award.php");

	//Require the Class for the calendar picker
	require_once(CLASS_DIR . "/tc_calendar.php");

	//include Form Class		
	include(CLASS_DIR . "/class_Form.php");

	//Array of Award records from the DB
	global $aAwardRecords;

	?>
	<div class="container-fluid">
		<form name="AwardMaint" action="AwardMaintenance.php" method="post">

			<?php
			//Instantiate needed objects
			$thisAward = new Award();
			$form = new Form();

			//Get the ID query string parameter
			$nThisAwardID = $_REQUEST['Award_ID'];

			//If an artist ID is passed, go ahead and search Awards for that artist
			if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "") {
				$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
				$_POST['btnSearch'] = "Search";
			}

			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisAwardID)) {

				$thisAward->nAwardID = $nThisAwardID;

				//Search the Database for records matching the search criteria			
				if ($thisAward->getAward()) {

					//Records found
					if (sizeof($thisAward->aAwardRecords) > 0) {

						//Only One Record should be returned.  Add this to the form field array
						//so that it displays in the form fields and to the values in the
						//current Object.
						loadAward($thisAward->aAwardRecords[0], $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//The record was not found
						$form->sMessage = "Award record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					}
				} else {
					//Error
					$form->sMessage = $thisAward->sErrorMessage;
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
					buildAwardObject($thisAward);

					//Insert record
					if ($thisAward->insertAward()) {
						//reload Review
						$insertedAward = new Award();
						$insertedAward->nAwardID = $thisAward->nAwardID;
						$insertedAward->getAward();
						$thisAward = $insertedAward->aAwardRecords[0];

						//Load the form fields with the newly populated object
						loadAward($thisAward, $form);

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Award Added";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {

						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED: {$thisAward->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) {

					//Load values from form field array into DB object
					buildAwardObject($thisAward);

					//Update record
					if ($thisAward->updateAward()) {

						//reload Award
						$thisAward->getAward();

						//Load the form fields with the newly populated DB object						
						loadAward($thisAward->aAwardRecords[0], $form);

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Award Updated";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed - {$thisAward->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   DELETE   *
				// **************
				else if (isset($_POST["btnDelete"])) {

					//Load DB record
					buildAwardObject($thisAward);

					//Delete record
					if ($thisAward->deleteAward()) {
						//Clear the form fields
						clearFormFields($form);

						//Success
						$form->sMessage = "Award Deleted";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
						$form->sMessage = "DELETE FAILED: {$thisAward->sErrorMessage}";
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   SEARCH   *
				// **************
				else if (isset($_POST["btnSearch"])) {
					//Load Array of Search Values
					buildAwardObject($thisAward);

					//Search the Database for records matching the search criteria			
					if ($thisAward->getAward()) {
						//No records found
						if (sizeof($thisAward->aAwardRecords) < 1) {
							$form->sMessage = "No Award records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($thisAward->aAwardRecords) == 1) {
							//Only One Record returned.  Add this to the form field array
							//so that it displays in the form fields
							loadAward($thisAward->aAwardRecords[0], $form);

							$form->sMessage = "One Award record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						}
						//If Multiple records found, the array of search reults will be populated
						else {
							//Multiiple records returned
							$form->sMessage = "Select Award record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
						}
					} else {
						//Attempt to get records failed
						$form->sMessage = $thisAward->sErrorMessage;
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_NEW;
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
					$_POST['hdnAwardID'] = NULL;

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

			?>
			<!-- Hidden Fields -->
			<input type="hidden" name="hdnAwardID" value="<?php echo $_POST['hdnAwardID'] ?>" />

			<div class="row">
				<?php
				include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Award Maintenance
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
						echo "	<div class='hidden-xs col-sm-12 col-sm-3 result-header'>Award Date</div>";
						echo "	<div class='hidden-xs col-sm-12 col-sm-9 result-header'>Award Name</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Awards</div>";
						echo "</div>";


						foreach ($thisAward->aAwardRecords as $oAwardRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";
							echo "	<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./AwardMaintenance.php?Award_ID={$oAwardRecord->nAwardID}'>{$oAwardRecord->dtAwardDate}</a></div>";
							echo "	<div class='col-xs-12 col-sm-9 {$sResultStyleClass}'><A HREF='./AwardMaintenance.php?Award_ID={$oAwardRecord->nAwardID}'>{$oAwardRecord->sAwardName}</a></div>";
							echo "</div>";
						}
						?>

						<div class="row" style="height: 20px;"></div>
					</div>

				<?php
			} else {
				?>

					<div class="row">
						<div class="col-xs-12 col-md-6 FieldGroup">
							<div class="row">
								<div class="col-xs-12">
									NAME: <input type="text" name="txtAwardName" value="<?php echo $_POST['txtAwardName']; ?>" size="40" />
								</div>
								<div class="col-xs-12">
									AWARD URL: <input type="text" name="txtAwardURL" value="<?php echo $_POST['txtAwardURL']; ?>" size="30" />
								</div>
								<div class="col-xs-2 category-selector">
									<input type="checkbox" name="chkPerformanceRelated" class="result-checkbox" value="PerformanceRelated" <?php if ($_POST['chkPerformanceRelated']) {
																																				echo " checked ";
																																			}; ?> />
								</div>
								<div class="col-xs-10 result-checkbox-text">
									PERFORMANCE RELATED
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
									<?php
									renderSongDropDown($_POST['selSong']);
									?>
								</div>
								<div class="col-xs-12">
									AWARD DATE: <br>
									<?php
									renderDatePicker("AwardDate", $thisAward->aAwardRecords[0]->dtAwardDate);
									?>
								</div>
								<div class="col-xs-12">
									DESCRIPTION: <textarea name="txtAwardDescription" rows="4" cols="50"><?php echo $_POST['txtAwardDescription']; ?></textarea>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-md-3 FieldGroup">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-12 thumbnail-preview">
									<span class="Subtitle">Image</span><br />
									<?php
									if (!empty($_POST['txtAwardImage'])) {
										$sFullImagePath = IMG_DIR . "/{$_POST['txtAwardImage']}";
									} else {
										$sFullImagePath = NULL;
									}
									$form->renderImagePreview($sFullImagePath, "");
									?>
									<br /><br />
									<input type="text" name="txtAwardImage" id="txtAwardImage" value="<?php echo $_POST['txtAwardImage']; ?>" size="30" /><BR />
									<input type="button" name="btnPreviewImage" onclick="preview_image('txtAwardImage','<?php echo IMG_DIR ?>')" value="Preview" />
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="row FormFieldNoEdit">
								<div class="col-xs-3">
									ID: <?php echo $_POST['hdnAwardID']; ?>
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
 * buildAwardObject
 * 
 * This function loads builds a Award object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
	function buildAwardObject($Award)
	{

		//Load the Array used to populate the form fields based on the newly loaded object
		$Award->nAwardID = $_POST['hdnAwardID'];

		//DEBUG
		$Award->sAwardName = html_entity_decode($_POST['txtAwardName'], ENT_QUOTES);
		$Award->sAwardDescription = html_entity_decode($_POST['txtAwardDescription'], ENT_QUOTES);
		$Award->sAwardImage = html_entity_decode($_POST['txtAwardImage'], ENT_QUOTES);
		$Award->sAwardURL = html_entity_decode($_POST['txtAwardURL'], ENT_QUOTES);
		$Award->bFuzzyNameSearch = TRUE;
		$Award->nArtistID = $_POST['selArtist'];
		$Award->nCDID = $_POST['selCD'];
		$Award->nSongID = $_POST['selSong'];

		//Release Date	
		$dtAwardDate = isset($_REQUEST["AwardDate"]) ? $_REQUEST["AwardDate"] : "";
		if ($dtAwardDate > "0000-00-00") {
			//If no datepicker is displayed, use the hidden field
			$dtAwardDate = isset($_POST["AwardDate"]) ? $_POST["AwardDate"] : "";
		}
		if ($dtAwardDate > "0000-00-00") {
			$Award->dtAwardDate  	= $dtAwardDate;
		}

		if ($_POST['chkPerformanceRelated'] == "PerformanceRelated") {
			$Award->bPerformanceRelated = TRUE;
		}

	}


	/*
 ********************************************************************************
 * loadAward
 * 
 * This function loads the form field array from a populated Award object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
	function loadAward(&$Award, $form)
	{

		if (!is_null($Award->nAwardID)) {
			//Load Hidden Fields
			$_POST['hdnAwardID'] = $Award->nAwardID;
			$_POST['txtAwardName'] = htmlentities($Award->sAwardName, ENT_QUOTES);
			$_POST['txtAwardDescription'] = $Award->sAwardDescription;
			$_POST['txtAwardImage'] = htmlentities($Award->sAwardImage, ENT_QUOTES);
			$_POST['txtAwardURL'] = htmlentities($Award->sAwardURL, ENT_QUOTES);
			$_POST['AwardDate'] = htmlentities($Award->dtAwardDate, ENT_QUOTES);
			$_POST['selArtist'] = $Award->nArtistID;
			$_POST['selCD'] = $Award->nCDID;
			$_POST['selSong'] = $Award->nSongID;
			$_POST['chkPerformanceRelated'] = $Award->bPerformanceRelated;
			$_POST['txtLastUpdate'] = htmlentities($Award->dtLastUpdate, ENT_QUOTES);
		} else {
			//Load Form field values into array 
			foreach ($_POST as $fieldName => $fieldValue) {

				$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
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

</html>