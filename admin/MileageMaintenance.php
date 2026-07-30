<?php
/*
*******************************************************************
MileageMaintenance.php
This PHP file defines the Maintenance page for 
managing Mileages.
NOTES
Date        Change
-------------------------------------------------------------
2015-12-07	Created
2016-12-29	Improved Responsivity for phone
2017-08-14	Improved Responsivity
2020-07-20	Added renderDatePicker()
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

//include Mileage Class		
include_once(CLASS_DIR . "/class_Mileage.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

$sActiveMenuItem = PERFORMANCES_ACTIVE;	
$sPageName = "Mileage Maintenance";

//Array of Mileage records from the DB
global $aMileageRecords;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<div class="container-fluid">
		<form name="MileageMaint" action="MileageMaintenance.php" method="post">

			<?php

			//Instantiate needed objects
			$thisMileage = new Mileage();
			$form = new Form();

			//If a Product ID is passed, go ahead and search revenues for that Product
			if (isset($_REQUEST['MILEAGE_YEAR']) && $_REQUEST['MILEAGE_YEAR'] != "") {
				$thisMileage->nMileageYear = $_REQUEST['MILEAGE_YEAR'];
				$_POST['btnSearch'] = "Search";
			}

			//Get the ID query string parameter
			$nThisMileageID = $_REQUEST['MILEAGE_ID'];

			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisMileageID)) {

				$thisMileage->nMileageID = $nThisMileageID;

				//Search the Database for records matching the search criteria			
				if ($thisMileage->getMileage()) {

					//Records found
					if (sizeof($thisMileage->aMileageRecords) > 0) {

						//Only One Record should be returned.  Add this to the form field array
						//so that it displays in the form fields and to the values in the
						//current Object.
						loadMileage($thisMileage->aMileageRecords[0], $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//The record was not found
						$form->sMessage = "Mileage record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					}
				} else {
					//Error
					$form->sMessage = $thisMileage->sErrorMessage;
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
					buildMileageObject($thisMileage);

					//Insert record
					if ($thisMileage->insertMileage()) {
						//Load the form fields with the newly populated object
						loadMileage($thisMileage, $form);

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Mileage Added";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {

						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED: " . $thisMileage->sErrorMessage;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) {

					//Load values from form field array into DB object
					buildMileageObject($thisMileage);

					//Update record
					if ($thisMileage->updateMileage()) {

						//reload Mileage
						if (!$thisMileage->getMileage()) {
							echo "Mileage was updated, but error was encountered retrieving Mileage data {$thisMileage->sErrorMessage}";
						}

						//Load the form fields with the newly populated DB object						
						loadMileage($thisMileage->aMileageRecords[0], $form);

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Mileage Updated";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed - " . $thisMileage->sErrorMessage;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   DELETE   *
				// **************
				else if (isset($_POST["btnDelete"])) {

					//Load DB record
					buildMileageObject($thisMileage);

					//Delete record
					if ($thisMileage->deleteMileage()) {
						//Clear the form fields
						clearFormFields($form);

						//Success
						$form->sMessage = "Mileage Deleted";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
						$form->sMessage = "DELETE FAILED: " . $thisMileage->sErrorMessage;
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   SEARCH   *
				// **************
				else if (isset($_POST["btnSearch"])) {
					//Load Array of Search Values
					buildMileageObject($thisMileage);

					//Search the Database for records matching the search criteria			
					if ($thisMileage->getMileage()) {
						//No records found
						if (sizeof($thisMileage->aMileageRecords) < 1) {
							$form->sMessage = "No Mileage records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($thisMileage->aMileageRecords) == 1) {
							//Only One Record returned.  Add this to the form field array
							//so that it displays in the form fields
							loadMileage($thisMileage->aMileageRecords[0], $form);

							$form->sMessage = "One Mileage record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						}
						//If Multiple records found, the array of search reults will be populated
						else {
							//Multiiple records returned
							$form->sMessage = "Select Mileage record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
						}
					} else {
						//Attempt to get records failed
						$form->sMessage = $thisMileage->sErrorMessage;
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
					$_POST['hdnMileageID'] = NULL;

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
			<input type="hidden" name="hdnMileageID" value="<?php echo $_POST['hdnMileageID'] ?>" />
			<div class="row">
			<?php
			include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
			?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Mileage Maintenance
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
						echo "	<div class='hidden-xs col-sm-4 result-header'>Date</div>";
						echo "	<div class='hidden-xs col-sm-4 col-md-2 result-header'>Mileage</div>";
						echo "	<div class='hidden-xs col-sm-4 col-md-5 result-header'>Reason</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Mileages</div>";
						echo "</div>";


						foreach ($thisMileage->aMileageRecords as $oMileageRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							echo "	<div class='col-xs-12 col-sm-4 {$sResultStyleClass}'><A HREF='./MileageMaintenance.php?MILEAGE_ID={$oMileageRecord->nMileageID}'>{$oMileageRecord->dtMileageDate}</a></div>";
							echo "	<div class='col-xs-12 col-sm-4 col-md-2 {$sResultStyleClass}'><A HREF='./MileageMaintenance.php?MILEAGE_ID={$oMileageRecord->nMileageID}'>{$oMileageRecord->nMileage}</a></div>";
							echo "	<div class='col-xs-12 col-sm-4 col-md-5 {$sResultStyleClass}'><A HREF='./MileageMaintenance.php?MILEAGE_ID={$oMileageRecord->nMileageID}'>{$oMileageRecord->sReason}</a></div>";
							echo "</div>";
						}
						?>
						<div class="row" style="height: 20px;"></div>

					</div>

				<?php
			} else {
				?>

					<div class="row">
						<div class="col-xs-12  FieldGroup">
							<div class="row">
								<div class="col-xs-12 col-sm-8">
									MILEAGE: <input type="text" name="txtMileage" value="<?php echo $_POST['txtMileage']; ?>" size="60" />
								</div>
								<div class="col-xs-12 col-sm-4">
									DATE: <BR />
									<?php
									renderDatePicker("MileageDate", $_POST['MileageDate']);
									?>
								</div>
								<div class="col-xs-12">
									REASON: <input type="text" name="txtReason" value="<?php echo $_POST['txtReason']; ?>" size="60" />
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-3 FormFieldNoEdit">
									ID: <?php echo $_POST['hdnMileageID']; ?>
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
 * buildMileageObject
 * 
 * This function loads builds a Mileage object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
	function buildMileageObject($oMileage)
	{

		//Load the Array used to populate the form fields based on the newly loaded object
		$oMileage->nMileageID = $_POST['hdnMileageID'];

		$oMileage->nMileage = html_entity_decode($_POST['txtMileage'], ENT_QUOTES);
		$oMileage->bFuzzyNameSearch = TRUE;
		$oMileage->sReason = html_entity_decode($_POST['txtReason'], ENT_QUOTES);

		//Mileage Date	
		$dtMileageDate = isset($_REQUEST["MileageDate"]) ? $_REQUEST["MileageDate"] : "";
		if ($dtMileageDate > "0000-00-00") {
			//If no datepicker is displayed, use the hidden field
			$dtMileageDate = isset($_POST["MileageDate"]) ? $_POST["MileageDate"] : "";
		}
		if ($dtMileageDate > "0000-00-00") {
			$oMileage->dtMileageDate  = $dtMileageDate;
		}
	}


	/*
 ********************************************************************************
 * loadMileage
 * 
 * This function loads the form field array from a populated Mileage object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
	function loadMileage(&$oMileage, $form)
	{

		if (!is_null($oMileage->nMileageID)) {
			//Load Hidden Fields
			$_POST['hdnMileageID'] = $oMileage->nMileageID;

			$_POST['txtMileage'] = htmlentities($oMileage->nMileage, ENT_QUOTES);
			$_POST['txtReason'] = $oMileage->sReason;
			$_POST['MileageDate'] = htmlentities($oMileage->dtMileageDate, ENT_QUOTES);
			$_POST['txtLastUpdate'] = htmlentities($oMileage->dtLastUpdate | '0000-00-00', ENT_QUOTES);
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
		function validate_form() {
			bValid = true;
			sErrorMessage = "";

			//Mileage is a required Field
			if (document.MileageMaint.txtMileage.value == "" || document.MileageMaint.txtMileage.value == "0") {
				sErrorMessage += "Mileage Required\n";
				bValid = false;
			}

			//Mileage Date is required
			if (document.MileageMaint.MileageDate.value == "" || document.MileageMaint.MileageDate.value == "0000-00-00") {
				sErrorMessage += "Mileage Date Required\n";
				bValid = false;
			}

			//Reason is a required Field
			if (document.MileageMaint.txtReason.value == "") {
				sErrorMessage += "Resaon Required\n";
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
</html>