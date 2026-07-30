<?php
/*
*******************************************************************
UnavailableDatesMaintenance.php
This PHP file defines the Maintenance page for managing unavailable
dates.
NOTES
Date        Change
-------------------------------------------------------------
2016-12-09	Made Responsive
2016-12-29	Improved Responsivity for phone
2019-11-05	Changed Datepickers to use common function
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

//include UnavailableDate Class		
include_once(CLASS_DIR . "/class_UnavailableDate.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of UnavailableDate records from the DB
global $aUnavailableDateRecords;

$sActiveMenuItem = PERFORMANCES_ACTIVE;
$sPageName = "Unavailable Dates Maintenance";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<div class="container-fluid">
		<form name="UnavailableDateMaint" action="UnavailableDatesMaintenance.php" method="post">

			<?php

			//Instantiate needed objects
			$thisUnavailableDate = new UnavailableDate();
			$form = new Form();

			//Get the ID query string parameter
			$nThisUnavailableDateID = $_REQUEST['ID'];

			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisUnavailableDateID)) {

				$thisUnavailableDate->nUnavailableID = $nThisUnavailableDateID;

				//Search the Database for records matching the search criteria			
				if ($thisUnavailableDate->getUnavailableDate()) {

					//Records found
					if (sizeof($thisUnavailableDate->aUnavailableDateRecords) > 0) {

						//Only One Record should be returned.  Add this to the form field array
						//so that it displays in the form fields and to the values in the
						//current Object.
						echo "*****DATE IS: {$thisUnavailableDate->aUnavailableDateRecords[0]->dtUnavailableStartDate}";
						loadUnavailableDate($thisUnavailableDate->aUnavailableDateRecords[0], $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//The record was not found
						$form->sMessage = "Unavailable Date record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					}
				} else {
					//Error
					$form->sMessage = $thisUnavailableDate->sErrorMessage;
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
					buildUnavailableDateObject($thisUnavailableDate);

					//Insert record
					if ($thisUnavailableDate->insertUnavailableDate()) {

						//reload UnavailableDate
						$nNewUnavailableDateID = $thisUnavailableDate->nUnavailableID;
						$thisUnavailableDate = new UnavailableDate();
						$thisUnavailableDate->nUnavailableID = $nNewUnavailableDateID;

						if ($thisUnavailableDate->getUnavailableDate()) {
							//Load the form fields with the newly populated object
							loadUnavailableDate($thisUnavailableDate->aUnavailableDateRecords[0], $form);
							//Store the ID of the performance
							$nThisUnavailableDateID = $thisUnavailableDate->aUnavailableDateRecords[0]->nUnavailableID;

							//Success
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Unavailable Date Added";
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							//Problem reloading screen
							clearFormFields($form);
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "Unavailable Date Added, but error occured while reloading the data";
							$form->nFormMode = FORM_MODE_NEW;
						}
					} else {

						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED: {$thisUnavailableDate->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) {

					//Load values from form field array into DB object
					buildUnavailableDateObject($thisUnavailableDate);

					//Update record
					if ($thisUnavailableDate->updateUnavailableDate()) {

						//reload UnavailableDate
						$thisUnavailableDate->getUnavailableDate();

						//Load the form fields with the newly populated DB object						
						loadUnavailableDate($thisUnavailableDate->aUnavailableDateRecords[0], $form);
						//Store the ID of the performance
						$nThisUnavailableDateID = $thisUnavailableDate->aUnavailableDateRecords[0]->nUnavailableID;

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Unavailable Date Updated";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed - {$thisUnavailableDate->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// ************************
				// *   DELETE ALL BOOKED  *
				// ************************
				else if (isset($_POST["btnDeleteAll"])) {

					$_POST['chkBooked'] = TRUE;

					//Load Booked Dates
					buildUnavailableDateObject($thisUnavailableDate);
					if ($thisUnavailableDate->getUnavailableDate()) {
						if (sizeof($thisUnavailableDate->aUnavailableDateRecords) < 1) {
							$form->sMessage = "No Booked Unavailable Date records found to delete " . sizeof($thisUnavailableDate->aUnavailableDateRecords);
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else {
							$deleteCount = 0;
							$failedCount = 0;
							foreach ($thisUnavailableDate->aUnavailableDateRecords as $oUnavailableDateRecord) {
								//Delete record
								if ($oUnavailableDateRecord->deleteUnavailableDate()) {
									$deleteCount++;
								} else {
									$failedCount++;
								}
							}
							$form->sMessage = "Deleted {$deleteCount} records.  {$failedCount} Failed.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_NEW;
						}
					}
					else {
						//Attempt to get records failed
						$form->sMessage = $thisUnavailableDate->sErrorMessage;
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_NEW;
					}
					clearFormFields($form);
				}
				// **************
				// *   DELETE   *
				// **************			
				else if (isset($_POST["btnDelete"])) {

					//Load DB record
					buildUnavailableDateObject($thisUnavailableDate);

					//Delete record
					if ($thisUnavailableDate->deleteUnavailableDate()) {
						//Clear the form fields
						clearFormFields($form);

						//Success
						$form->sMessage = "Unavailable Date Deleted";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
						$form->sMessage = "DELETE FAILED: {$thisUnavailableDate->sErrorMessage}";
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   SEARCH   *
				// **************
				else if (isset($_POST["btnSearch"])) {

					//Load Array of Search Values
					buildUnavailableDateObject($thisUnavailableDate);

					//Search the Database for records matching the search criteria			
					if ($thisUnavailableDate->getUnavailableDate()) {
						//No records found
						if (sizeof($thisUnavailableDate->aUnavailableDateRecords) < 1) {
							$form->sMessage = "No Unavailable Date records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($thisUnavailableDate->aUnavailableDateRecords) == 1) {
							//Only One Record returned.  Add this to the form field array
							//so that it displays in the form fields
							loadUnavailableDate($thisUnavailableDate->aUnavailableDateRecords[0], $form);
							//Store the ID of the performance
							$nThisUnavailableDateID = $thisUnavailableDate->aUnavailableDateRecords[0]->nUnavailableID;

							$form->sMessage = "One Unavailable Date record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						}
						//If Multiple records found, the array of search reults will be populated
						else {
							//Multiiple records returned
							$form->sMessage = "Select Unavailable Date record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
						}
					} else {
						//Attempt to get records failed
						$form->sMessage = $thisUnavailableDate->sErrorMessage;
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
					$_POST['hdnUnavailableID'] = NULL;

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
			<input type="hidden" name="hdnUnavailableID" value="<?php echo $_POST['hdnUnavailableID'] ?>" />
			<div class="row">
				<?php
				include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Unavailable Date Maintenance
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
						echo "	<div class='hidden-xs col-sm-2 result-header'>Date</div>";
						echo "	<div class='hidden-xs col-sm-10 result-header'>Reason</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Unavailable Dates</div>";
						echo "</div>";


						foreach ($thisUnavailableDate->aUnavailableDateRecords as $oUnavailableDateRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./UnavailableDatesMaintenance.php?ID={$oUnavailableDateRecord->nUnavailableID}'>{$oUnavailableDateRecord->dtUnavailableDate}</a></div>";
							echo "	<div class='col-xs-12 col-sm-10 {$sResultStyleClass}'><A HREF='./UnavailableDatesMaintenance.php?ID={$oUnavailableDateRecord->nUnavailableID}'>{$oUnavailableDateRecord->sReason}</a></div>";
							echo "</div>";
						}
						?>
						<div class="row" style="height: 20px;"></div>
						<?php
						if ($_POST['chkBooked']) {
							echo "<div class=\"row\">";
							echo "<div class=\"col-xs-12\">";
							echo "<input type=\"submit\" name=\"btnDeleteAll\" class=\"formButton\" value=\"DeleteAll\">";
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
							<div class="col-xs-12">
								START DATE:<BR />
								<?php
								renderDatePicker("UnavailableStartDate", $thisUnavailableDate->aUnavailableDateRecords[0]->dtUnavailableDate);
								?>
							</div>
							<div class="col-xs-12">

								END DATE:<BR />
								<?php
								renderDatePicker("UnavailableEndDate", $thisUnavailableDate->dtUnavailableEndDate);
								?>
							</div>
							<div class="col-xs-12">
								REASON: <input type="text" name="txtReason" value="<?php echo $_POST['txtReason']; ?>" size="60" />&nbsp;&nbsp;
							</div>
							<div class="col-xs-1">
								<input type="checkbox" name="chkBooked" value="booked" <?php if ($_POST['chkBooked']) {
																							echo " checked ";
																						} ?> />
							</div>
							<div class="col-xs-11">
								BOOKED DATE
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="row">
							<div class=" col-xs-3 FormFieldNoEdit">
								ID: <?php echo $_POST['hdnUnavailableID']; ?>
							</div>
							<div class="col-xs-12 FormFieldNoEdit">
								LAST UPDATED: <?php echo $_POST['txtLastUpdate']; ?>
							</div>
						</div>
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
<?php
			}
?>
</div>
</form>

<?php


/*
 ********************************************************************************
 * buildUnavailableDateObject
 * 
 * This function loads builds a flag object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildUnavailableDateObject($oUnavailableDate)
{

	//Load the Array used to populate the form fields based on the newly loaded object
	$oUnavailableDate->nUnavailableID = $_POST['hdnUnavailableID'];

	$oUnavailableDate->sReason = html_entity_decode($_POST['txtReason'], ENT_QUOTES);
	$oUnavailableDate->bFuzzyReasonSearch = TRUE;

	//UnavailableDate Start Date	
	$dtUnavailableStartDate = isset($_REQUEST["UnavailableStartDate"]) ? $_REQUEST["UnavailableStartDate"] : "";
	if ($dtUnavailableStartDate > "0000-00-00") {
		//If no datepicker is displayed, use the hidden field
		$dtUnavailableStartDate = isset($_POST["UnavailableStartDate"]) ? $_POST["UnavailableStartDate"] : "";
	}
	if ($dtUnavailableStartDate > "0000-00-00") {
		$oUnavailableDate->dtUnavailableStartDate  	= $dtUnavailableStartDate;
		$oUnavailableDate->dtUnavailableDate  		= $dtUnavailableStartDate;
	}

	//UnavailableDate End Date	
	$dtUnavailableEndDate = isset($_REQUEST["UnavailableEndDate"]) ? $_REQUEST["UnavailableEndDate"] : "";
	if ($dtUnavailableEndDate > "0000-00-00") {
		//If no datepicker is displayed, use the hidden field
		$dtUnavailableEndDate = isset($_POST["UnavailableEndDate"]) ? $_POST["UnavailableEndDate"] : "";
	}
	if ($dtUnavailableEndDate > "0000-00-00") {
		$oUnavailableDate->dtUnavailableEndDate  = $dtUnavailableEndDate;
	}

	//Load Booked Date Boolean only if checked (don't search for this field if not checked)
	if (isset($_POST['chkBooked']) && $_POST['chkBooked'] == 'booked') {
		$oUnavailableDate->bBookedDate = TRUE;
	}
	//If we are searching and the Booked Date checkbox isn't checked, don't use it in the search
	else if (!isset($_POST["btnSearch"])) {
		$oUnavailableDate->bBookedDate = FALSE;
	}
}


/*
 ********************************************************************************
 * loadUnavailableDate
 * 
 * This function loads the form field array from a populated UnavailableDate object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadUnavailableDate(&$oUnavailableDate, $form)
{

	if (!is_null($oUnavailableDate->nUnavailableID)) {
		//Load Hidden Fields
		$_POST['hdnUnavailableID'] = $oUnavailableDate->nUnavailableID;


		$_POST['txtReason'] = htmlentities($oUnavailableDate->sReason, ENT_QUOTES);

		$_POST['UnavailableStartDate'] = htmlentities($oUnavailableDate->dtUnavailableDate, ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($oUnavailableDate->dtLastUpdate, ENT_QUOTES);

		if ($oUnavailableDate->bBookedDate) {
			$_POST['chkBooked'] = TRUE;
		} else {
			$_POST['chkBooked'] = FALSE;
		}
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
	function validate_form() {
		bValid = true;
		sErrorMessage = "";

		//Reason is a required Field
		if (document.UnavailableDateMaint.txtReason.value == "") {
			sErrorMessage += "Reason Required\n";
			bValid = false;
		}

		//Unavailable Start Date is a required Field
		if (document.UnavailableDateMaint.UnavailableStartDate.value == "" || document.UnavailableDateMaint.UnavailableStartDate.value == "0000-00-00") {
			sErrorMessage += "Unavailable Start Date Required\n";
			bValid = false;
		}

		//Unavailable Start Date can not be greater than the end date
		if ((document.UnavailableDateMaint.UnavailableStartDate.value > document.UnavailableDateMaint.UnavailableEndDate.value) && (document.UnavailableDateMaint.UnavailableEndDate.value > '0000-00-00')) {
			sErrorMessage += "Start Date Can Not Be Greater Than End Date\n";
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