<?php
/*
*******************************************************************
ExpenseMaintenance.php
This PHP file defines the Maintenance page for 
managing Expenses.
NOTES
Date        Change
-------------------------------------------------------------
2015-05-05	Refactored and added dynamic building of Product Drop-down.
2015-05-24  Corrected problem with Expense Total
2015-10-19	Added PERF_RELATED parm processing
2015-10-23	Added YEAR parm processing<br />
2016-09-15 	Added check for ADD_EXPENSE parm
2016-12-05	Responsified Page
2018-02-22	Added Render Vendor Dropdown call
2021-08-30	Updated for PHP 8
2022-01-25	Added ability for no product search
2024-05-17	Added Colorado Sessions Flag
2026-07-30	Migrated to new Datalayer Expense repository
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
include_once(DATALAYER_DIR . "/Expense.php");
include_once(DATALAYER_DIR . "/ExpenseRepository.php");
include_once(DATALAYER_DIR . "/Category.php");
include_once(DATALAYER_DIR . "/CategoryRepository.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//include Form Class
include(CLASS_DIR . "/class_Form.php");

//Array of Expense records from the DB
$aExpenseRecords = [];

$sActiveMenuItem = FINANCES_ACTIVE;
$sPageName = "Expense Maintenance";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Expense Maintenance</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
include(ADMIN_INCLUDE_DIR . "/AdminHead-Responsive.php");
?>

<body>

	<!-- DIV used for Image Preview Popup -->
	<div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15" id="preview_div"></div>

<div class="container-fluid">

<form name="ExpenseMaint" action="ExpenseMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$expenseRepo = new \Datalayer\ExpenseRepository();
		$categoryRepo = new \Datalayer\CategoryRepository();
		$thisExpense = new \Datalayer\Expense();
		$form = new Form();
		$aExpenseCategories = [];

		//If a Vendor ID is passed, go ahead and search expenses for that Vendor
		if (isset($_REQUEST['VENDOR_ID']) && $_REQUEST['VENDOR_ID'] != "") {
			$_POST['selVendor'] = $_REQUEST['VENDOR_ID'];
			$_POST['btnSearch'] = "Search";
		}

		//If a Product ID is passed, go ahead and search expenses for that Product
		if (isset($_REQUEST['PRODUCT_ID']) && $_REQUEST['PRODUCT_ID'] != "") {
			$_POST['selProduct'] = $_REQUEST['PRODUCT_ID'];

			//If the action is not to Add search for Expenses
			if (!isset($_REQUEST['ACTION']) || $_REQUEST['ACTION'] != "ADD_EXPENSE") {
				$_POST['btnSearch'] = "Search";
			}
		}

		//If a Tour ID is passed, go ahead and search expenses for that Tour
		if (isset($_REQUEST['TOUR_ID']) && $_REQUEST['TOUR_ID'] != "") {
			$_POST['hdnTourID'] = $_REQUEST['TOUR_ID'];
			if (!isset($_REQUEST['ACTION']) || $_REQUEST['ACTION'] != "ADD_EXPENSE") {
				$_POST['selTour'] = $_REQUEST['TOUR_ID'];
				$_POST['btnSearch'] = "Search";
			}
		}

		//If a Tax Category ID is passed, go ahead and search expenses for that Tax Category
		if (isset($_REQUEST['TAX_CATEGORY_ID']) && $_REQUEST['TAX_CATEGORY_ID'] != "") {
			$_POST['selTaxCategory'] = $_REQUEST['TAX_CATEGORY_ID'];
			$_POST['btnSearch'] = "Search";
		}

		//If a Year is passed, go ahead and search expenses for that Year
		if (isset($_REQUEST['YEAR']) && $_REQUEST['YEAR'] != "") {
			$_POST['nYear'] = $_REQUEST['YEAR'];
			$_POST['btnSearch'] = "Search";
		}

		//If a Category ID is passed, go ahead and search expenses for that Category
		if (isset($_REQUEST['CATEGORY_ID']) && $_REQUEST['CATEGORY_ID'] != "") {
			$_POST['chkCategory' . $_REQUEST['CATEGORY_ID']] = "CategoryAssociated";
			$_POST['btnSearch'] = "Search";
		}

		//If a Start Date is passed, go ahead and search revenues for that date
		if (isset($_REQUEST['START_DATE']) && $_REQUEST['START_DATE'] != "") {
			$_POST['hdnStartDate'] = $_REQUEST['START_DATE'];
			$_POST['btnSearch'] = "Search";
		}

		//If an End Date is passed, go ahead and search revenues for that date
		if (isset($_REQUEST['END_DATE']) && $_REQUEST['END_DATE'] != "") {
			$_POST['hdnEndDate'] = $_REQUEST['END_DATE'];
			$_POST['btnSearch'] = "Search";
		}

		//If a Performance Related indicator is passed, go ahead and search revenues for that date
		if (isset($_REQUEST['PERF_RELATED']) && $_REQUEST['PERF_RELATED'] == "TRUE") {
			$_POST['bPerformanceRelated'] = TRUE;
			$_POST['btnSearch'] = "Search";
		}

		//If a Colorado Sessions indicator is passed, go ahead and search revenues for Colorado Sessions revenues
		if (isset($_REQUEST['COLORADO_SESSIONS']) && $_REQUEST['COLORADO_SESSIONS'] == "TRUE") {
			$sColoradoSessionsFormId = 'chkCategory' . COLORAD_SESSIONS_CAT_ID;
			$_POST[$sColoradoSessionsFormId] = TRUE;
			$_POST['btnSearch'] = "Search";
		}

		//Get the ID query string parameter
		$nThisExpenseID = $_REQUEST['ID'] ?? null;

		try {
			$aExpenseCategories = $categoryRepo->find(['expenseRelated' => true]);

			if (empty($aExpenseCategories)) {
				$form->sMessage = "Error Retrieving Expense Categories.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;
			} else {
				//If an ID was passed to the page, retrieve that record for update
				if (!is_null($nThisExpenseID) && $nThisExpenseID !== '') {
					$entity = $expenseRepo->findById((int) $nThisExpenseID);

					if ($entity) {
						$thisExpense = $entity;
						$aExpenseRecords = [$entity];
						loadExpense($thisExpense, $form, $aExpenseCategories);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$form->sMessage = "Expense record not found.";
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
						buildExpenseObject($thisExpense);

						if ($expenseRepo->insert($thisExpense)) {
							if (updateExpenseCategories((int) $thisExpense->id, $aExpenseCategories)) {
								$thisExpense = $expenseRepo->findById((int) $thisExpense->id) ?? $thisExpense;
								$aExpenseRecords = [$thisExpense];
								loadExpense($thisExpense, $form, $aExpenseCategories);

								$form->nMessageType = MESSAGE_TYPE_INFO;
								$form->sMessage = "Expense Added";
								$form->nFormMode = FORM_MODE_EDIT;
							} else {
								$form->nMessageType = MESSAGE_TYPE_ERROR;
								$form->sMessage = "ERROR: Failed to update expense category associations";
								$form->nFormMode = FORM_MODE_EDIT;
							}
						} else {
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "ADD RECORD FAILED";
							$form->nFormMode = FORM_MODE_EDIT;
						}
					}
					// **************
					// *   UPDATE   *
					// **************
					else if (isset($_POST["btnUpdate"])) {
						buildExpenseObject($thisExpense);

						if ($expenseRepo->update($thisExpense)) {
							if (updateExpenseCategories((int) $thisExpense->id, $aExpenseCategories)) {
								$thisExpense = $expenseRepo->findById((int) $thisExpense->id) ?? $thisExpense;
								$aExpenseRecords = [$thisExpense];
								loadExpense($thisExpense, $form, $aExpenseCategories);

								$form->nMessageType = MESSAGE_TYPE_INFO;
								$form->sMessage = "Expense Updated";
								$form->nFormMode = FORM_MODE_EDIT;
							} else {
								$form->nMessageType = MESSAGE_TYPE_ERROR;
								$form->sMessage = "ERROR: Failed to update expense category associations";
								$form->nFormMode = FORM_MODE_EDIT;
							}
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
						buildExpenseObject($thisExpense);

						if (!empty($thisExpense->id) && $expenseRepo->delete((int) $thisExpense->id)) {
							if (removeExpenseCategories((int) $thisExpense->id)) {
								clearFormFields($form);

								$form->sMessage = "Expense Deleted";
								$form->nMessageType = MESSAGE_TYPE_INFO;
								$form->nFormMode = FORM_MODE_NEW;
							} else {
								$form->sMessage = "DELETED EXPENSE BUT FAILED TO DELETE CATEGORY ASSOCIATIONS";
								$form->nMessageType = MESSAGE_TYPE_ERROR;
								$form->nFormMode = FORM_MODE_EDIT;
							}
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
						buildExpenseObject($thisExpense);

						$aExpenseRecords = $expenseRepo->find(buildExpenseSearchCriteria($thisExpense, $aExpenseCategories));

						if (sizeof($aExpenseRecords) < 1) {
							$form->sMessage = "No Expense records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($aExpenseRecords) == 1) {
							$thisExpense = $aExpenseRecords[0];
							loadExpense($thisExpense, $form, $aExpenseCategories);

							$form->sMessage = "One Expense record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							$form->sMessage = "Select Expense record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
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
						$_POST['hdnExpenseID'] = NULL;

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
			}
		} catch (\Throwable $e) {
			$form->sMessage = $e->getMessage();
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->nFormMode = FORM_MODE_NEW;
		}

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnExpenseID" value="<?php echo $_POST['hdnExpenseID'] ?? '' ?>" />
	<input type="hidden" id="hdnProductID" name="hdnProductID" value="<?php echo $_POST['hdnProductID'] ?? '' ?>" />
	<div class="row">
<?php
include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12 Title">
					Expense Maintenance
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
			echo "<div class=\"row result-header\">";
			echo "	<div class='hidden-xs col-sm-4 col-md-3 result-header'>Date</div>";
			echo "	<div class='hidden-xs col-sm-4 col-md-3 result-header'>Amount</div>";
			echo "	<div class='hidden-xs col-sm-4 col-md-6 result-header'>Description</div>";
			echo "	<div class='visible-xs col-xs-12 result-header'>Expenses</div>";
			echo "</div>";

			$nExpenseAmountTotal = 0;
			foreach ($aExpenseRecords as $oExpenseRecord) {

				//Alternate the result style
				if ($sResultStyleClass == RESULT_STYLE_CLASS) {
					$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
				} else {
					$sResultStyleClass = RESULT_STYLE_CLASS;
				}
				echo "<div class=\"row {$sResultStyleClass}\">";

				echo "	<div class='col-xs-12 col-sm-4 col-md-3 result-selector {$sResultStyleClass}'><A HREF='./ExpenseMaintenance.php?ID={$oExpenseRecord->id}'>{$oExpenseRecord->expenseDate}</A></div>";
				echo "	<div class='col-xs-12 col-sm-4 col-md-3 result-selector {$sResultStyleClass}'><A HREF='./ExpenseMaintenance.php?ID={$oExpenseRecord->id}'> \${$oExpenseRecord->expenseAmount} </A></div>";
				echo "	<div class='col-xs-12 col-sm-4 col-md-6 result-selector {$sResultStyleClass}'><A HREF='./ExpenseMaintenance.php?ID={$oExpenseRecord->id}'>{$oExpenseRecord->description}</A></div>";
				$nExpenseAmountTotal += $oExpenseRecord->expenseAmount;
				echo "</div>";
			}

			echo "<div class=\"row\">";
			echo "	<div class='col-xs-12 col-sm-4 col-md-3'>Total</div>";
			echo "	<div class='col-xs-12 col-sm-4 col-md-3'> \$ {$nExpenseAmountTotal}</div>";
			echo "	<div class='col-xs-12 col-sm-4 col-md-6'></div>";
			echo "</div>";
			?>

		</div>

	<?php
	} else {
	?>
		<div class="row">
			<div class="col-xs-12 col-md-8 FieldGroupTitle">
				DETAILS
			</div>
			<div class="col-xs-12 col-md-8 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
						DATE:<BR />
						<?php
						renderDatePicker("ExpenseDate", $_POST['ExpenseDate'] ?? ($thisExpense->expenseDate ?? ''));
						?>
					</div>
					<div class="col-xs-12">
						DESCRIPTION:
						<input type="text" name="txtExpenseDescription" value="<?php echo $_POST['txtExpenseDescription'] ?? ''; ?>" size="60" />&nbsp;&nbsp;
					</div>
					<div class="col-xs-12">
						AMOUNT:
						<input type="text" name="txtExpenseAmount" value="<?php echo $_POST['txtExpenseAmount'] ?? ''; ?>" size="20" />&nbsp;&nbsp;
					</div>
					<div class="col-xs-12">
						<?php
						renderTourDropDown();
						?>
					</div>
					<div class="col-xs-12">
						<?php
						renderProductDropDown($_POST['selProduct'] ?? 0);
						?>
					</div>
					<div class="col-xs-12">
						<?php
						renderVendorDropDown($_POST['selVendor'] ?? 0);
						?>
					</div>
					<div class="col-xs-12">
						<?php
						renderTaxCategoryDropDown($_POST['selTaxCategory'] ?? 0);
						?>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
						CATEGORIES
					</div>
						<?php

						foreach ($aExpenseCategories as $oCategory) {
							$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

							echo "<div class='col-xs-2 category-selector {$sResultStyleClass}'>";
							echo "<input type='checkbox' name='chkCategory{$oCategory->id}' class='result-checkbox'";
							echo " value='CategoryAssociated'";
							if (isset($_POST['chkCategory' . $oCategory->id])) {
								echo " checked ";
							}
							echo ">";
							echo "</div>";
							echo "<div class='col-xs-10 category-name  result-checkbox-text {$sResultStyleClass}'>";
							echo $oCategory->name . "</div>";
						}
						?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class ="col-xs-3 FormFieldNoEdit">
				ID: <?php echo $_POST['hdnExpenseID'] ?? ''; ?>
			</div>
			<div class ="col-xs-9 FormFieldNoEdit">
				LAST UPDATED: <?php echo $_POST['txtLastUpdate'] ?? ''; ?>
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
</form>
</div>
<?php


/*
 ********************************************************************************
 * buildExpenseObject
 *
 * This function builds an Expense entity from the data typed into the form fields.
 ********************************************************************************
*/
function buildExpenseObject(\Datalayer\Expense $expense): void
{
	$id = $_POST['hdnExpenseID'] ?? null;
	$expense->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$expense->description = html_entity_decode($_POST['txtExpenseDescription'] ?? '', ENT_QUOTES);
	$amount = html_entity_decode($_POST['txtExpenseAmount'] ?? '', ENT_QUOTES);
	$expense->expenseAmount = ($amount !== '' && is_numeric($amount)) ? (float) $amount : null;

	$tourId = $_POST['hdnTourID'] ?? null;
	if (empty($tourId) && isset($_POST['selTour'])) {
		$tourId = $_POST['selTour'];
	}
	$expense->tourId = (is_numeric($tourId) && (int) $tourId > 0) ? (int) $tourId : null;

	$productId = $_POST['selProduct'] ?? null;
	$expense->productId = (is_numeric($productId) && (int) $productId > 0) ? (int) $productId : null;

	$vendorId = $_POST['selVendor'] ?? null;
	$expense->vendorId = (is_numeric($vendorId) && (int) $vendorId > 0) ? (int) $vendorId : null;

	$taxCategoryId = $_POST['selTaxCategory'] ?? null;
	$expense->taxCategoryId = (is_numeric($taxCategoryId) && (int) $taxCategoryId > 0) ? (int) $taxCategoryId : null;

	$dtExpenseDate = $_REQUEST["ExpenseDate"] ?? "";
	if ($dtExpenseDate > "0000-00-00") {
		$dtExpenseDate = $_POST["ExpenseDate"] ?? "";
	}
	if ($dtExpenseDate > "0000-00-00") {
		$expense->expenseDate = $dtExpenseDate;
	}
}


/*
 ********************************************************************************
 * buildExpenseSearchCriteria
 *
 * Builds repository search criteria from the expense entity and form fields.
 ********************************************************************************
*/
function buildExpenseSearchCriteria(\Datalayer\Expense $expense, array $expenseCategories): array
{
	$criteria = [
		'id' => $expense->id,
		'description' => $expense->description,
		'fuzzyName' => true,
	];

	if (!empty($expense->expenseDate)) {
		$criteria['expenseDate'] = $expense->expenseDate;
	}

	if (!empty($expense->expenseAmount) && $expense->expenseAmount > 0) {
		$criteria['expenseAmount'] = $expense->expenseAmount;
	}

	$expenseYear = $_POST['nYear'] ?? null;
	if (!empty($expenseYear)) {
		$criteria['expenseYear'] = (int) $expenseYear;
	}

	$startDate = $_POST['hdnStartDate'] ?? null;
	if (!empty($startDate)) {
		$criteria['startDate'] = $startDate;
	}

	$endDate = $_POST['hdnEndDate'] ?? null;
	if (!empty($endDate)) {
		$criteria['endDate'] = $endDate;
	}

	if (!empty($_POST['bPerformanceRelated'])) {
		$criteria['performanceRelated'] = true;
		$criteria['performanceCategoryId'] = CATEGORY_PERFORMANCE;
	}

	if (!empty($expense->tourId)) {
		$criteria['tourId'] = $expense->tourId;
	}

	if (array_key_exists('selProduct', $_POST)) {
		$criteria['productId'] = $_POST['selProduct'];
	}

	if (!empty($expense->vendorId)) {
		$criteria['vendorId'] = $expense->vendorId;
	}

	if (!empty($expense->taxCategoryId)) {
		$criteria['taxCategoryId'] = $expense->taxCategoryId;
	}

	$categoryIds = [];
	foreach ($expenseCategories as $expenseCategory) {
		if (isset($_POST['chkCategory' . $expenseCategory->id])) {
			$categoryIds[] = $expenseCategory->id;
		}
	}
	if (!empty($categoryIds)) {
		$criteria['categoryIds'] = $categoryIds;
	}

	return $criteria;
}


/*
 ********************************************************************************
 * loadExpense
 *
 * This function loads the form field array from a populated Expense entity
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadExpense(\Datalayer\Expense $expense, $form, array $expenseCategories): void
{
	if (!is_null($expense->id)) {
		$_POST['hdnExpenseID'] = $expense->id;
		$_POST['hdnTourID'] = $expense->tourId;

		$_POST['txtExpenseDescription'] = htmlentities($expense->description ?? '', ENT_QUOTES);
		$_POST['ExpenseDate'] = htmlentities($expense->expenseDate ?? '', ENT_QUOTES);
		$_POST['txtExpenseAmount'] = htmlentities($expense->expenseAmount ?? '', ENT_QUOTES);
		$_POST['selProduct'] = (!empty($expense->productId)) ? $expense->productId : 0;
		$_POST['selVendor'] = (!empty($expense->vendorId)) ? $expense->vendorId : 0;
		$_POST['selTaxCategory'] = (!empty($expense->taxCategoryId)) ? $expense->taxCategoryId : 0;

		$associatedCategoryIds = getExpenseCategoryIds((int) $expense->id);
		foreach ($expenseCategories as $expenseCategory) {
			if (in_array((int) $expenseCategory->id, $associatedCategoryIds, true)) {
				$_POST['chkCategory' . $expenseCategory->id] = "CategoryAssociated";
			}
		}

		$_POST['txtLastUpdate'] = htmlentities($expense->lastUpdate ?? '', ENT_QUOTES);
	} else {
		foreach ($_POST as $fieldName => $fieldValue) {
			$_POST[$fieldName] = $fieldValue;
		}
	}
}


/*
 ********************************************************************************
 * clearFormFields
 ********************************************************************************
*/
function clearFormFields($form): void
{
	$_POST = array();
}


/*
 ********************************************************************************
 * copyFormFields
 ********************************************************************************
*/
function copyFormFields($form): void
{
	foreach ($_POST as $fieldName => $fieldValue) {
		$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
	}
}


/*
 ********************************************************************************
 * getExpenseCategoryIds
 ********************************************************************************
*/
function getExpenseCategoryIds(int $expenseId): array
{
	$db = \Datalayer\Connection::getPdo();
	$stmt = $db->prepare('SELECT CATEGORY_ID FROM EXPENSE_CATEGORY_XREF WHERE EXPENSE_ID = :expenseId');
	$stmt->execute([':expenseId' => $expenseId]);
	return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
}


/*
 ********************************************************************************
 * updateExpenseCategories
 *
 * Updates expense category xref records based on selected checkboxes.
 ********************************************************************************
*/
function updateExpenseCategories(int $expenseId, array $expenseCategories): bool
{
	if ($expenseId <= 0) {
		return false;
	}

	if (!removeExpenseCategories($expenseId)) {
		return false;
	}

	$db = \Datalayer\Connection::getPdo();
	$stmt = $db->prepare('INSERT INTO EXPENSE_CATEGORY_XREF (EXPENSE_ID, CATEGORY_ID) VALUES (:expenseId, :categoryId)');

	foreach ($expenseCategories as $expenseCategory) {
		$checkboxName = "chkCategory{$expenseCategory->id}";
		if (isset($_POST[$checkboxName]) && $_POST[$checkboxName] == 'CategoryAssociated') {
			if (!$stmt->execute([
				':expenseId' => $expenseId,
				':categoryId' => (int) $expenseCategory->id,
			])) {
				return false;
			}
		}
	}

	return true;
}


/*
 ********************************************************************************
 * removeExpenseCategories
 ********************************************************************************
*/
function removeExpenseCategories(int $expenseId): bool
{
	if ($expenseId <= 0) {
		return false;
	}

	$db = \Datalayer\Connection::getPdo();
	$stmt = $db->prepare('DELETE FROM EXPENSE_CATEGORY_XREF WHERE EXPENSE_ID = :expenseId');
	return $stmt->execute([':expenseId' => $expenseId]);
}

?>

</body>

<script type="text/javascript">

//******************************************
// validate_form
// This script checks the user imnput for
// errors or missing data
//******************************************
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

	//Expense Name is a required Field
    if ( document.ExpenseMaint.txtExpenseDescription.value == "" )
    {
        sErrorMessage += "Expense Description Required\n";
        bValid = false;
    }

	//Expense Date is a required Field
    if ( document.ExpenseMaint.ExpenseDate.value == "" || document.ExpenseMaint.ExpenseDate.value == "0000-00-00" )
    {
        sErrorMessage += "Expense Date Required\n";
        bValid = false;
    }

	//Tax Category is a required Field
    if ( document.ExpenseMaint.selTaxCategory.value == "" || document.ExpenseMaint.selTaxCategory.value == 0 )
    {
        sErrorMessage += "Tax Category Required\n";
        bValid = false;
    }

	//Expense Amount is a required, numeric, positive Field
    if ( document.ExpenseMaint.txtExpenseAmount.value == "" )
    {
        sErrorMessage += "Expense Amount Required\n";
        bValid = false;
    }
	else if(!isNumeric(document.ExpenseMaint.txtExpenseAmount.value))
	{
        sErrorMessage += "Expense Amount Must be Numeric\n";
        bValid = false;
	}
	else if(Number(document.ExpenseMaint.txtExpenseAmount.value) <= 0)
	{
        sErrorMessage += "Expense Amount must be greater than 0\n";
        bValid = false;
	}

	//If any errors, alert
	if (!bValid)
	{
		alert(sErrorMessage);
	}

    return bValid;
}

//*********************************************
// buildTourLink
// This function builds a link to the Tour
// Maintenance page based on the Tour
// chosen in the drop-down list
//*********************************************
function buildTourLink()
{
	setTourID();
	if(document.getElementById("selTour").value > 0)
	{
		sTourMaintLink = "<SMALL><I><A HREF='./TourMaintenance.php?ID=" + document.getElementById("selTour").value + "'>Edit Tour</A></I></SMALL>";
		document.getElementById("lnkTourMaint").innerHTML = sTourMaintLink;
	}
	else
	{
		document.getElementById("lnkTourMaint").innerHTML = "";
	}
}

//*********************************************
// getTours
// This function builds the tour drop-down list
//*********************************************
function getTours() {
	sDate = document.getElementById("ExpenseDate").value;

    if (sDate == "" || sDate == "0000-00-00") {
        document.getElementById("selTour").innerHTML = "<OPTION Value='0'>NONE</OPTION>";
		document.getElementById("hdnTourID").value = 0;
        return;
    } else {
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("selTour").innerHTML = xmlhttp.responseText;
				if(document.getElementById("selTour").value == 0)
				{
					document.getElementById("hdnTourID").value = 0;
				}
            }
        }
        xmlhttp.open("GET","GetPotentialTours.php?date="+sDate,true);
        xmlhttp.send();
    }

}

//*********************************************
// setTourID
// This function sets the Tour ID hidden form
// field to that of the tour selected in the
// drop-down list
//*********************************************
function setTourID() {
	document.getElementById("hdnTourID").value = document.getElementById("selTour").value;
}
</script>

</html>
