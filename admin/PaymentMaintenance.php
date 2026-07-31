<?php
/*
*******************************************************************
PaymentMaintenance.php
This PHP file defines the Maintenance page for 
managing Payments.
NOTES
Date        Change
-------------------------------------------------------------
2015-05-12  Added Purchase Order field
2015-09-13  Added link to Product Maintenanace for Product names and 
            refactored 
2015-09-27  Fixed formatting problems in revenue list
2016-12-31	Made Responsive
2017-04-30	Improved Layout
2017-07-03	Styled Add Revenue Button
2017-08-19	Improved Responsivity
2018-04-14	Changed revenue links
2019-05-09	Added Numeric check for Invoice
2019-07-19	Added renderVendorDropDown() call
2020-03-06	Added renderDatePicker
2021-08-30	Updated for PHP 8
2026-07-30	Migrated to new Datalayer Payment repository
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
include_once(DATALAYER_DIR . "/Payment.php");
include_once(DATALAYER_DIR . "/PaymentRepository.php");
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");

//include Form Class
include(CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//Array of Payment records from the DB
$aPaymentRecords = [];
$aPaymentRevenues = [];
$aPotentialRevenues = [];

$sActiveMenuItem = FINANCES_ACTIVE;
$sPageName = "Payment Maintenance";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<?php

	//Instantiate needed objects
	$paymentRepo = new \Datalayer\PaymentRepository();
	$revenueRepo = new \Datalayer\RevenueRepository();
	$thisPayment = new \Datalayer\Payment();
	$form = new Form();

	//Get the ID query string parameter
	$nThisPaymentID = $_REQUEST['ID'] ?? null;

	//If a Vendor ID is passed, go ahead and search payments for that vendor
	if (isset($_REQUEST['VENDOR_ID']) && $_REQUEST['VENDOR_ID'] != "") {
		$_POST['selVendorType'] = $_REQUEST['VENDOR_ID'];
		$_POST['btnSearch'] = "Search";
	}

	try {
		//If an ID was passed to the page, retrieve that record for update
		if (!is_null($nThisPaymentID) && $nThisPaymentID !== '') {
			$entity = $paymentRepo->findById((int) $nThisPaymentID);

			if ($entity) {
				$thisPayment = $entity;
				$aPaymentRecords = [$entity];
				loadPayment($thisPayment, $form);
				loadPaymentRevenueLists($thisPayment, $revenueRepo, $aPaymentRevenues, $aPotentialRevenues);

				$form->sMessage = "Update record.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;
			} else {
				$form->sMessage = "Payment record not found.";
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
				buildPaymentObject($thisPayment);

				if ($paymentRepo->insert($thisPayment)) {
					$thisPayment = $paymentRepo->findById((int) $thisPayment->id) ?? $thisPayment;
					$aPaymentRecords = [$thisPayment];
					loadPayment($thisPayment, $form);
					loadPaymentRevenueLists($thisPayment, $revenueRepo, $aPaymentRevenues, $aPotentialRevenues);

					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Payment Added";
					$form->nFormMode = FORM_MODE_EDIT;
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
				buildPaymentObject($thisPayment);

				if ($paymentRepo->update($thisPayment)) {
					if (!empty($thisPayment->paymentDate)) {
						$potentialRevenues = $revenueRepo->find([
							'paidDate' => $thisPayment->paymentDate,
							'excludePaymentId' => (int) $thisPayment->id,
							'includeProductInfo' => true,
						]);

						foreach ($potentialRevenues as $potentialRevenue) {
							$sCheckboxName = "chkRevenue{$potentialRevenue->id}";
							if (isset($_POST[$sCheckboxName]) && $_POST[$sCheckboxName] == 'RevenueAssociated') {
								$potentialRevenue->paymentId = (int) $thisPayment->id;
								if (!$revenueRepo->update($potentialRevenue)) {
									echo "ERROR:  FAILED TO UPDATE REVENUE:{$potentialRevenue->id}<BR>";
								}
							}
						}
					}

					$thisPayment = $paymentRepo->findById((int) $thisPayment->id) ?? $thisPayment;
					$aPaymentRecords = [$thisPayment];
					loadPayment($thisPayment, $form);
					loadPaymentRevenueLists($thisPayment, $revenueRepo, $aPaymentRevenues, $aPotentialRevenues);

					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Payment Updated";
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
				buildPaymentObject($thisPayment);

				$deleteError = null;
				if (!empty($thisPayment->id)) {
					$relatedRevenues = $revenueRepo->find(['paymentId' => (int) $thisPayment->id]);
					if (sizeof($relatedRevenues) > 0) {
						$deleteError = "PMT013 - Can not delete Payment because it has ";
						$deleteError .= "<A HREF='" . ADMIN_DIR . "/RevenueMaintenance.php?PAYMENT_ID=" . $thisPayment->id;
						$deleteError .= "'>" . sizeof($relatedRevenues) . " revenues</A>.";
					}
				}

				if ($deleteError !== null) {
					loadPayment($thisPayment, $form);
					loadPaymentRevenueLists($thisPayment, $revenueRepo, $aPaymentRevenues, $aPotentialRevenues);
					$form->sMessage = $deleteError;
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_EDIT;
				} else if (!empty($thisPayment->id) && $paymentRepo->delete((int) $thisPayment->id)) {
					clearFormFields($form);

					$form->sMessage = "Payment Deleted";
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
				buildPaymentObject($thisPayment);

				$aPaymentRecords = $paymentRepo->find(buildPaymentSearchCriteria($thisPayment));

				if (sizeof($aPaymentRecords) < 1) {
					$form->sMessage = "No Payment records found matching search criteria";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;
				} else if (sizeof($aPaymentRecords) == 1) {
					$thisPayment = $aPaymentRecords[0];
					loadPayment($thisPayment, $form);
					loadPaymentRevenueLists($thisPayment, $revenueRepo, $aPaymentRevenues, $aPotentialRevenues);

					$form->sMessage = "One Payment record found.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;
				} else {
					$form->sMessage = "Select Payment record to edit from results list below.";
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
			// *   COPY  *
			// *************
			else if (isset($_POST["btnCopy"])) {
				$_POST['hdnPaymentID'] = NULL;

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
<div class="container-fluid">
<form name="PaymentMaint" action="PaymentMaintenance.php" method="post">

	<!-- Hidden Fields -->
	<input type="hidden" name="hdnPaymentID" value="<?php echo $_POST['hdnPaymentID'] ?? '' ?>" />

	<div class="row">
<?php
include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12 Title">
				Payment Maintenance
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
		echo "	<div class='hidden-xs col-sm-3 result-header'>Payment Date</div>";
		echo "	<div class='hidden-xs col-sm-2 result-header'>Amount</div>";
		echo "	<div class='hidden-xs col-sm-7 result-header'>Payment Description</div>";
		echo "	<div class='visible-xs col-xs-12 col-sm-3 result-header'>Payments</div>";
		echo "</div>";

		$nPaymentAmountTotal = 0;

		foreach ($aPaymentRecords as $oPaymentRecord) {

			//Alternate the result style
			if ($sResultStyleClass == RESULT_STYLE_CLASS) {
				$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
			} else {
				$sResultStyleClass = RESULT_STYLE_CLASS;
			}
			echo "<div class='row {$sResultStyleClass}'>";

			echo "	<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./PaymentMaintenance.php?ID={$oPaymentRecord->id}'>{$oPaymentRecord->paymentDate}</a></div>";
			echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./PaymentMaintenance.php?ID={$oPaymentRecord->id}'> $ {$oPaymentRecord->amount}</a></div>";
			echo "	<div class='col-xs-12 col-sm-7 {$sResultStyleClass}'><A HREF='./PaymentMaintenance.php?ID={$oPaymentRecord->id}'>{$oPaymentRecord->description}</a></div>";
			$nPaymentAmountTotal += $oPaymentRecord->amount;
			echo "</div>";
		}

		echo "<div class='row'><div class='col-xs-12'><HR></div></div>";
		echo "<div class='row {$sResultStyleClass}'>";
		echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'></div>";
		echo "	<div class='col-xs-6 col-sm-5 {$sResultStyleClass}'>Total</div>";
		echo "	<div class='col-xs-6 col-sm-5 {$sResultStyleClass}'> $ {$nPaymentAmountTotal}</div>";
		echo "</div>";
		echo "<div class='row'><div class='col-xs-12'><HR></div></div>";

		?>

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
						DATE:<BR />
						<?php
						renderDatePicker("PaymentDate", $_POST['PaymentDate'] ?? ($thisPayment->paymentDate ?? ''));
						?>
					</div>
					<div class="col-xs-12 col-md-3">
						AMOUNT: <input type="text" name="txtPaymentAmount" value="<?php echo $_POST['txtPaymentAmount'] ?? ''; ?>" size="20" />
					</div>
					<div class="col-xs-12 col-md-9">
						DESCRIPTION: <input type="text" name="txtPaymentDescription" value="<?php echo $_POST['txtPaymentDescription'] ?? ''; ?>" size="60" />
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						<?php
						renderVendorDropDown($_POST['selVendor'] ?? 0);
						?>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						PO: <input type="text" name="txtPurchaseOrder" value="<?php echo $_POST['txtPurchaseOrder'] ?? ''; ?>" size="15" />
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						CHECK #: <input type="text" name="txtCheckNumber" value="<?php echo $_POST['txtCheckNumber'] ?? ''; ?>" size="15" />
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						INVOICE: <input type="text" name="txtInvoice" value="<?php echo $_POST['txtInvoice'] ?? ''; ?>" size="15" />&nbsp;&nbsp;
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-2 FieldGroupTitle">
					REVENUES
			</div>
			<div class="col-xs-12 col-sm-10">
				<?php
				if (($_POST['hdnPaymentID'] ?? 0) > 0) {
					echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?PAYMENT_ID={$thisPayment->id}";
					echo "&ACTION=ADD_REVENUE' class='secondaryLinkButton'>Add Revenue</a>";
				}
				?>
			</div>
			<div class="col-xs-12 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
					<?php
					if (($thisPayment->id ?? 0) > 0) {
						echo "<div class='row result-header'>";
						echo "<div class='hidden-xs col-sm-1 col-md-1 result-header'></div>";
						echo "<div class='hidden-xs col-sm-3 col-md-2 result-header'>Date</div>";
						echo "<div class='hidden-xs col-sm-4 col-md-2 result-header'>Description</div>";
						echo "<div class='hidden-xs col-sm-4 col-md-2 result-header'>Amount</div>";
						echo "<div class='hidden-xs hidden-sm col-md-2 result-header'>Product</div>";
						echo "<div class='hidden-xs hidden-sm col-md-1 result-header'>Quantity</div>";
						echo "<div class='hidden-xs col-md-2 result-header'></div>";
						echo "<div class='visible-xs col-xs-12 result-header'>REVENUES</div>";
						echo "</div>";

						$nRevenueAmountTotal = 0;
						$nProductQtyTotal = 0;

						foreach ($aPaymentRevenues as $oRevenue) {
							$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

							echo "<div class='row {$sResultStyleClass}'>";
							echo "<div class='col-xs-12 col-sm-1 col-md-1'>";
							echo "<input class='result-checkbox' type='checkbox' name='chkRevenue{$oRevenue->id}'";
							echo " value='RevenueAssociated' checked disabled>";
							echo "</div>";
							echo "<div class='col-xs-12 col-sm-3 col-md-2'>";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->id}'>";
							echo "{$oRevenue->revenueDate}</a></div>";
							echo "<div class='col-xs-12 col-sm-4 col-md-2'>";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->id}'>";
							echo "{$oRevenue->description}</a></div>";
							echo "<div class='col-xs-12 col-sm-4 col-md-2'>";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->id}'>";
							echo " $ {$oRevenue->amount}</a></div>";
							echo "<div class='hidden-xs hidden-sm col-md-2'>";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->id}'>";
							echo htmlentities($oRevenue->productName ?? '', ENT_QUOTES) . "</a></div>";
							echo "<div class='hidden-xs hidden-sm col-md-1'>";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->id}'>";
							echo "{$oRevenue->productQty}</a></div>";
							echo "<div class='col-xs-12 col-md-2'></div>";
							echo "</div>";
							$nRevenueAmountTotal += $oRevenue->amount;
							$nProductQtyTotal += $oRevenue->productQty ?? 0;
						}

						foreach ($aPotentialRevenues as $oPotentialRevenue) {
							$sResultStyleClass = RESULT_STYLE_CLASS;

							echo "<div class='row {$sResultStyleClass}'>";
							echo "<div class='col-xs-12 col-sm-1 col-md-1'>";
							echo "<input class='result-checkbox' type='checkbox' name='chkRevenue{$oPotentialRevenue->id}'";
							echo " value='RevenueAssociated' >";
							echo "</div>";
							echo "<div class='col-xs-12 col-sm-3 col-md-1'>";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->id}'>{$oPotentialRevenue->revenueDate}</a></div>";
							echo "<div class='col-xs-12 col-sm-4 col-md-2'>";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->id}'>";
							echo "{$oPotentialRevenue->description}</a></div>";
							echo "<div class='col-xs-12 col-sm-4 col-md-2'>";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->id}'> $ {$oPotentialRevenue->amount}</a></div>";
							echo "<div class='hidden-xs hidden-sm col-md-2'>";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->id}'>";
							echo htmlentities($oPotentialRevenue->productName ?? '', ENT_QUOTES) . "</a></div>";
							echo "<div class='hidden-xs hidden-sm col-md-2'><a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->id}'>{$oPotentialRevenue->productQty}</a></div>";
							echo "<div class='col-xs-12 col-md-2'>";
							if (!empty($oPotentialRevenue->paymentId) && $oPotentialRevenue->paymentId > 0) {
								echo "<font color='red'>Revenue already associated with ";
								echo "<a href='" . ADMIN_DIR . "/PaymentMaintenance.php?ID={$oPotentialRevenue->paymentId}";
								echo "'>Payment {$oPotentialRevenue->paymentId}</a></font>";
							}
							echo "</div>";
							echo "</div>";
						}

						echo "<div class='row'><div class='col-xs-12'><HR></div></div>";
						echo "<div class='row'>";
						echo "<div class='hidden-xs col-sm-2'></div>";
						echo "<div class='col-xs-2'> Total </div>";
						echo "<div class='col-xs-2'> $" . number_format((float) $nRevenueAmountTotal, 2, '.', '') . "</div>";
						if (number_format((float) $nRevenueAmountTotal, 2, '.', '') <> number_format((float) ($_POST['txtPaymentAmount'] ?? 0), 2, '.', '')) {
							echo "<div class='col-xs-12 col-sm-6'>";
							echo "<font color='red'>Revenue Total does not equal Payment Amount.</font>";
							echo "</div>";
						}
						echo "</div>";
					}
					?>
					</div>
				</div>
			</div>
				<div class="col-xs-12">
					<div class="row">
						<div class=" col-xs-3 FormFieldNoEdit">
							ID: <?php echo $_POST['hdnPaymentID'] ?? ''; ?>
						</div>
						<div class=" col-xs-9 FormFieldNoEdit">
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
	</div>
	<?php
	}
	?>

</form>
</div>
<?php


/*
 ********************************************************************************
 * buildPaymentObject
 *
 * This function builds a Payment entity from the data typed into the form fields.
 ********************************************************************************
*/
function buildPaymentObject(\Datalayer\Payment $payment): void
{
	$id = $_POST['hdnPaymentID'] ?? null;
	$payment->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$payment->description = html_entity_decode($_POST['txtPaymentDescription'] ?? '', ENT_QUOTES);

	$amount = html_entity_decode($_POST['txtPaymentAmount'] ?? '', ENT_QUOTES);
	$payment->amount = ($amount !== '' && is_numeric($amount)) ? (float) $amount : null;

	$checkNumber = html_entity_decode($_POST['txtCheckNumber'] ?? '', ENT_QUOTES);
	$payment->checkNumber = ($checkNumber !== '' && is_numeric($checkNumber)) ? (int) $checkNumber : null;

	$invoice = html_entity_decode($_POST['txtInvoice'] ?? '', ENT_QUOTES);
	$payment->invoice = ($invoice !== '' && is_numeric($invoice)) ? (int) $invoice : null;

	$payment->purchaseOrder = html_entity_decode($_POST['txtPurchaseOrder'] ?? '', ENT_QUOTES);

	$vendorId = $_POST['selVendor'] ?? null;
	$payment->vendorId = (is_numeric($vendorId) && (int) $vendorId > 0) ? (int) $vendorId : null;

	$dtPaymentDate = $_REQUEST["PaymentDate"] ?? "";
	if ($dtPaymentDate > "0000-00-00") {
		$dtPaymentDate = $_POST["PaymentDate"] ?? "";
	}
	if ($dtPaymentDate > "0000-00-00") {
		$payment->paymentDate = $dtPaymentDate;
	}
}


/*
 ********************************************************************************
 * buildPaymentSearchCriteria
 ********************************************************************************
*/
function buildPaymentSearchCriteria(\Datalayer\Payment $payment): array
{
	$criteria = [
		'id' => $payment->id,
		'description' => $payment->description,
		'fuzzyName' => true,
	];

	if (!empty($payment->paymentDate)) {
		$criteria['paymentDate'] = $payment->paymentDate;
	}

	if (!empty($payment->amount) && $payment->amount > 0) {
		$criteria['amount'] = $payment->amount;
	}

	if (!empty($payment->vendorId)) {
		$criteria['vendorId'] = $payment->vendorId;
	}

	if (!empty($payment->purchaseOrder)) {
		$criteria['purchaseOrder'] = $payment->purchaseOrder;
	}

	if (!empty($payment->checkNumber)) {
		$criteria['checkNumber'] = $payment->checkNumber;
	}

	if (!empty($payment->invoice)) {
		$criteria['invoice'] = $payment->invoice;
	}

	return $criteria;
}


/*
 ********************************************************************************
 * loadPayment
 *
 * This function loads the form field array from a populated Payment entity
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadPayment(\Datalayer\Payment $payment, $form): void
{
	if (!is_null($payment->id)) {
		$_POST['hdnPaymentID'] = $payment->id;

		$_POST['txtPaymentDescription'] = htmlentities($payment->description ?? '', ENT_QUOTES);
		$_POST['txtPaymentAmount'] = htmlentities($payment->amount ?? '', ENT_QUOTES);
		$_POST['txtCheckNumber'] = htmlentities($payment->checkNumber ?? '', ENT_QUOTES);
		$_POST['txtInvoice'] = htmlentities($payment->invoice ?? '', ENT_QUOTES);
		$_POST['txtPurchaseOrder'] = htmlentities($payment->purchaseOrder ?? '', ENT_QUOTES);

		$_POST['PaymentDate'] = htmlentities($payment->paymentDate ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($payment->lastUpdate ?? '', ENT_QUOTES);

		$_POST['selVendor'] = (!empty($payment->vendorId)) ? $payment->vendorId : 0;
	}
}


/*
 ********************************************************************************
 * loadPaymentRevenueLists
 ********************************************************************************
*/
function loadPaymentRevenueLists(
	\Datalayer\Payment $payment,
	\Datalayer\RevenueRepository $revenueRepo,
	array &$paymentRevenues,
	array &$potentialRevenues
): void {
	$paymentRevenues = [];
	$potentialRevenues = [];

	if (empty($payment->id)) {
		return;
	}

	$paymentRevenues = $revenueRepo->find([
		'paymentId' => (int) $payment->id,
		'includeProductInfo' => true,
	]);

	if (!empty($payment->paymentDate)) {
		$potentialRevenues = $revenueRepo->find([
			'paidDate' => $payment->paymentDate,
			'excludePaymentId' => (int) $payment->id,
			'includeProductInfo' => true,
		]);
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

	//Reason is a required Field
    if ( document.PaymentMaint.txtPaymentDescription.value == "" )
    {
        sErrorMessage += "Payment Name Required\n";
        bValid = false;
    }

	//Payment Date is a required Field
    if ( document.PaymentMaint.PaymentDate.value == "" || document.PaymentMaint.PaymentDate.value == "0000-00-00" )
    {
        sErrorMessage += "Payment Date Required\n";
        bValid = false;
    }

	//Payment Amount is a required, numeric, positive Field
    if ( document.PaymentMaint.txtPaymentAmount.value == "" )
    {
        sErrorMessage += "Payment Amount Required\n";
        bValid = false;
    }
	else if(!isNumeric(document.PaymentMaint.txtPaymentAmount.value))
	{
        sErrorMessage += "Payment Amount Must be Numeric\n";
        bValid = false;
	}
	else if(Number(document.PaymentMaint.txtPaymentAmount.value) <= 0)
	{
        sErrorMessage += "Payment Amount must be greater than 0\n";
        bValid = false;
	}

	if(!isNumeric(document.PaymentMaint.txtInvoice.value) && document.PaymentMaint.txtInvoice.value != "")
	{
        sErrorMessage += "Invoice Must be Numeric\n";
        bValid = false;
	}


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
