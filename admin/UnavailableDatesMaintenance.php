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
2026-07-30	Migrated to new Datalayer UnavailableDate repository
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
include_once(DATALAYER_DIR . "/UnavailableDate.php");
include_once(DATALAYER_DIR . "/UnavailableDateRepository.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//Array of UnavailableDate records from the DB
$aUnavailableDateRecords = [];

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
			$unavailableDateRepo = new \Datalayer\UnavailableDateRepository();
			$thisUnavailableDate = new \Datalayer\UnavailableDate();
			$form = new Form();

			//Get the ID query string parameter
			$nThisUnavailableDateID = $_REQUEST['ID'] ?? null;

			try {
			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisUnavailableDateID) && $nThisUnavailableDateID !== '') {

				$entity = $unavailableDateRepo->findById((int) $nThisUnavailableDateID);

				if ($entity) {
					$thisUnavailableDate = $entity;
					$aUnavailableDateRecords = [$entity];
					loadUnavailableDate($thisUnavailableDate, $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;
				} else {
					$form->sMessage = "Unavailable Date record not found.";
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
					buildUnavailableDateObject($thisUnavailableDate);
					$startDate = getUnavailableDateFromRequest('UnavailableStartDate');
					$endDate = getUnavailableDateFromRequest('UnavailableEndDate');
					$datesToInsert = getUnavailableDateRange($startDate, $endDate);

					if (empty($datesToInsert)) {
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$insertFailed = false;
						$lastInserted = null;

						foreach ($datesToInsert as $dateToInsert) {
							$newDate = new \Datalayer\UnavailableDate();
							$newDate->unavailableDate = $dateToInsert;
							$newDate->reason = $thisUnavailableDate->reason;

							if (!$unavailableDateRepo->insert($newDate)) {
								$insertFailed = true;
								break;
							}
							$lastInserted = $newDate;
						}

						if ($insertFailed || $lastInserted === null) {
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "ADD RECORD FAILED";
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							$thisUnavailableDate = $unavailableDateRepo->findById((int) $lastInserted->id) ?? $lastInserted;
							$aUnavailableDateRecords = [$thisUnavailableDate];
							loadUnavailableDate($thisUnavailableDate, $form);

							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Unavailable Date Added";
							$form->nFormMode = FORM_MODE_EDIT;
						}
					}
				}
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) {

					buildUnavailableDateObject($thisUnavailableDate);

					if ($unavailableDateRepo->update($thisUnavailableDate)) {
						$thisUnavailableDate = $unavailableDateRepo->findById((int) $thisUnavailableDate->id) ?? $thisUnavailableDate;
						$aUnavailableDateRecords = [$thisUnavailableDate];
						loadUnavailableDate($thisUnavailableDate, $form);

						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Unavailable Date Updated";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// ************************
				// *   DELETE ALL BOOKED  *
				// ************************
				else if (isset($_POST["btnDeleteAll"])) {

					$_POST['chkBooked'] = TRUE;

					$aUnavailableDateRecords = $unavailableDateRepo->find(['bookedDate' => true]);

					if (sizeof($aUnavailableDateRecords) < 1) {
						$form->sMessage = "No Booked Unavailable Date records found to delete " . sizeof($aUnavailableDateRecords);
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
						$deleteCount = 0;
						$failedCount = 0;
						foreach ($aUnavailableDateRecords as $oUnavailableDateRecord) {
							if (!empty($oUnavailableDateRecord->id) && $unavailableDateRepo->delete((int) $oUnavailableDateRecord->id)) {
								$deleteCount++;
							} else {
								$failedCount++;
							}
						}
						$form->sMessage = "Deleted {$deleteCount} records.  {$failedCount} Failed.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					}
					clearFormFields($form);
				}
				// **************
				// *   DELETE   *
				// **************			
				else if (isset($_POST["btnDelete"])) {

					buildUnavailableDateObject($thisUnavailableDate);

					if (!empty($thisUnavailableDate->id) && $unavailableDateRepo->delete((int) $thisUnavailableDate->id)) {
						clearFormFields($form);

						$form->sMessage = "Unavailable Date Deleted";
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

					$searchBookedDate = null;
					buildUnavailableDateObject($thisUnavailableDate, $searchBookedDate);

					$criteria = [
						'id' => $thisUnavailableDate->id,
						'reason' => $thisUnavailableDate->reason,
						'fuzzyReason' => true,
						'unavailableDate' => $thisUnavailableDate->unavailableDate,
					];
					if ($searchBookedDate !== null) {
						$criteria['bookedDate'] = $searchBookedDate;
					}

					$aUnavailableDateRecords = $unavailableDateRepo->find($criteria);

					if (sizeof($aUnavailableDateRecords) < 1) {
						$form->sMessage = "No Unavailable Date records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					} else if (sizeof($aUnavailableDateRecords) == 1) {
						$thisUnavailableDate = $aUnavailableDateRecords[0];
						loadUnavailableDate($thisUnavailableDate, $form);

						$form->sMessage = "One Unavailable Date record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$form->sMessage = "Select Unavailable Date record to edit from results list below.";
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
			} catch (\Throwable $e) {
				$form->sMessage = $e->getMessage();
				$form->nMessageType = MESSAGE_TYPE_ERROR;
				$form->nFormMode = FORM_MODE_NEW;
			}

			?>
			<!-- Hidden Fields -->
			<input type="hidden" name="hdnUnavailableID" value="<?php echo $_POST['hdnUnavailableID'] ?? '' ?>" />
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


						foreach ($aUnavailableDateRecords as $oUnavailableDateRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./UnavailableDatesMaintenance.php?ID={$oUnavailableDateRecord->id}'>" . htmlentities($oUnavailableDateRecord->unavailableDate ?? '', ENT_QUOTES) . "</a></div>";
							echo "	<div class='col-xs-12 col-sm-10 {$sResultStyleClass}'><A HREF='./UnavailableDatesMaintenance.php?ID={$oUnavailableDateRecord->id}'>" . htmlentities($oUnavailableDateRecord->reason ?? '', ENT_QUOTES) . "</a></div>";
							echo "</div>";
						}
						?>
						<div class="row" style="height: 20px;"></div>
						<?php
						if (!empty($_POST['chkBooked'])) {
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
								renderDatePicker("UnavailableStartDate", $_POST['UnavailableStartDate'] ?? ($thisUnavailableDate->unavailableDate ?? ''));
								?>
							</div>
							<div class="col-xs-12">

								END DATE:<BR />
								<?php
								renderDatePicker("UnavailableEndDate", $_POST['UnavailableEndDate'] ?? '');
								?>
							</div>
							<div class="col-xs-12">
								REASON: <input type="text" name="txtReason" value="<?php echo $_POST['txtReason'] ?? ''; ?>" size="60" />&nbsp;&nbsp;
							</div>
							<div class="col-xs-1">
								<input type="checkbox" name="chkBooked" value="booked" <?php if (!empty($_POST['chkBooked'])) {
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
								ID: <?php echo $_POST['hdnUnavailableID'] ?? ''; ?>
							</div>
							<div class="col-xs-12 FormFieldNoEdit">
								LAST UPDATED: <?php echo $_POST['txtLastUpdate'] ?? ''; ?>
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
 * getUnavailableDateFromRequest
 *
 * Reads a date field from the request using legacy datepicker behavior.
 ********************************************************************************
*/
function getUnavailableDateFromRequest(string $fieldName): ?string
{
	$dateValue = $_REQUEST[$fieldName] ?? '';
	if ($dateValue > '0000-00-00') {
		$dateValue = $_POST[$fieldName] ?? '';
	}
	if ($dateValue > '0000-00-00') {
		return $dateValue;
	}
	return null;
}


/*
 ********************************************************************************
 * getUnavailableDateRange
 *
 * Determines all dates between a start and end date inclusively.
 ********************************************************************************
*/
function getUnavailableDateRange(?string $startDate, ?string $endDate): array
{
	if (empty($startDate)) {
		return [];
	}

	if (empty($endDate) || $endDate <= '0000-00-00') {
		return [$startDate];
	}

	$dates = [];
	$current = strtotime($startDate);
	$end = strtotime($endDate);

	if ($current === false || $end === false || $current > $end) {
		return [$startDate];
	}

	while ($current <= $end) {
		$dates[] = date('Y-m-d', $current);
		$current += 86400;
	}

	return $dates;
}


/*
 ********************************************************************************
 * buildUnavailableDateObject
 * 
 * This function loads builds a flag object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildUnavailableDateObject(\Datalayer\UnavailableDate $UnavailableDate, ?bool &$searchBookedDate = null)
{
	$id = $_POST['hdnUnavailableID'] ?? null;
	$UnavailableDate->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$UnavailableDate->reason = html_entity_decode($_POST['txtReason'] ?? '', ENT_QUOTES);

	$startDate = getUnavailableDateFromRequest('UnavailableStartDate');
	if ($startDate !== null) {
		$UnavailableDate->unavailableDate = $startDate;
	}

	if (isset($_POST['chkBooked']) && $_POST['chkBooked'] == 'booked') {
		$searchBookedDate = true;
	} else if (!isset($_POST["btnSearch"])) {
		$searchBookedDate = null;
	} else {
		$searchBookedDate = null;
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
function loadUnavailableDate(\Datalayer\UnavailableDate $UnavailableDate, $form)
{

	if (!is_null($UnavailableDate->id)) {
		$_POST['hdnUnavailableID'] = $UnavailableDate->id;

		$_POST['txtReason'] = htmlentities($UnavailableDate->reason ?? '', ENT_QUOTES);
		$_POST['UnavailableStartDate'] = htmlentities($UnavailableDate->unavailableDate ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($UnavailableDate->lastUpdate ?? '', ENT_QUOTES);
	} else {
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
