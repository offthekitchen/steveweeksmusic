<?php
/*
*******************************************************************
VendorReport.php
This PHP file generates a Vendor Report (payments and expenses by vendor / by year)
NOTES
Date        Change
-------------------------------------------------------------
2026-10-14  Created (modeled on Artist Report; payments instead of revenues)
*******************************************************************
*/

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

//This include defines the relative path to the root directory from this sub-directory
include_once ("root.inc.php");

//inlcude web site settings
include_once ($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//inlcude admin settings
include_once (ADMIN_DIR . "/includes/AdminSettings.php");
//inlcude Common Functions
include_once (ADMIN_INCLUDE_DIR . "/CommonFunctions.php");

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Vendor.php");
include_once(DATALAYER_DIR . "/VendorRepository.php");
include_once(DATALAYER_DIR . "/Payment.php");
include_once(DATALAYER_DIR . "/PaymentRepository.php");
include_once(DATALAYER_DIR . "/Expense.php");
include_once(DATALAYER_DIR . "/ExpenseRepository.php");

//include Form Class
include_once(ADMIN_INCLUDE_DIR . "/Form.php");

$sActiveMenuItem = REPORTS_ACTIVE;
$sPageName = "Vendor Report";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>
<div class="container-fluid">
<form name="VendorReport" action="VendorReport.php" method="post">
<?php
	//If a Vendor ID is passed, pre-select that Vendor
	if (isset($_REQUEST['VENDOR_ID']) && $_REQUEST['VENDOR_ID'] != "")
	{
		$_POST['selVendor'] = $_REQUEST['VENDOR_ID'];
		$_POST['btnGenerate'] = "GenerateReport";
	}
	//If a Year is passed, pre-select that Year
	if (isset($_REQUEST['YEAR']) && $_REQUEST['YEAR'] != "")
	{
		$_POST['selYear'] = $_REQUEST['YEAR'];
		$_POST['btnGenerate'] = "GenerateReport";
	}

	//Instantiate needed objects
	$form = new Form();
	$vendorRepo = new \Datalayer\VendorRepository();
	$paymentRepo = new \Datalayer\PaymentRepository();
	$expenseRepo = new \Datalayer\ExpenseRepository();
	$sBaseQueryString = "";

	//Set the Year range based on year selection
	if (($_POST['selYear'] ?? 0) > 0)
	{
		$nStartYear = (int) $_POST['selYear'];
		$nEndYear = (int) $_POST['selYear'];
	}
	else
	{
		$nStartYear = START_YEAR;
		$nEndYear = (int) date('Y');
	}

	$vendorCriteria = [];
	if (($_POST['selVendor'] ?? 0) > 0)
	{
		$vendorCriteria['id'] = (int) $_POST['selVendor'];
	}

	try {
		$aVendorRecords = $vendorRepo->find($vendorCriteria);
	} catch (\Throwable $e) {
		$aVendorRecords = [];
		$form->nMessageType = MESSAGE_TYPE_ERROR;
		$form->sMessage = "FAILED TO GET VENDORS: " . $e->getMessage();
	}

?>
	<div class="row">
		<?php
		include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
		?>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12 Title">
				VENDOR REPORT:
				<?php
				if(($_POST['selVendor'] ?? 0) > 0 && !empty($aVendorRecords))
				{
					echo " {$aVendorRecords[0]->name}";
				}
				if(($_POST['selYear'] ?? 0) > 0)
				{
					echo "  {$_POST['selYear']}";
				}
				?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 Buttons">
				<input type="submit" name="btnGenerate" value="Generate Report" />
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
			//Include Filters
			$bYearFilter = TRUE;
			$bVendorFilter = TRUE;
			include (ADMIN_INCLUDE_DIR . "/ReportFilters.php");
			?>
		</div>
	</div>
<?php
if (isset($_POST["btnGenerate"]))
{
?>
	<div class="row">
		<div class="col-xs-12 FieldGroup">
			<div class="FieldGroupTitle">
				VENDOR DATA
				<?php
				if (($_POST['selYear'] ?? 0) > 0)
				{
					echo " FOR {$_POST['selYear']}";
				}
				?>
			</div>
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<th>Vendor</th>
						<th>Payment</th>
						<th>Expense</th>
						<th>Net</th>
					</tr>
				<?php
					$nTotalPaymentAmount = 0;
					$nTotalExpenseAmount = 0;

					foreach ($aVendorRecords as $vendor)
					{
						// ReportFilters already adds VENDOR_ID when a specific vendor is selected
						if (($_POST['selVendor'] ?? 0) > 0) {
							$sExpenseQueryString = $sBaseQueryString;
							$sPaymentQueryString = $sBaseQueryString;
						} else {
							$sExpenseQueryString = "{$sBaseQueryString}&VENDOR_ID={$vendor->id}";
							$sPaymentQueryString = "{$sBaseQueryString}&VENDOR_ID={$vendor->id}";
						}

						$paymentCriteria = ['vendorId' => $vendor->id];
						$expenseCriteria = ['vendorId' => $vendor->id];
						if (($_POST['selYear'] ?? 0) > 0) {
							$paymentCriteria['paymentYear'] = (int) $_POST['selYear'];
							$expenseCriteria['expenseYear'] = (int) $_POST['selYear'];
						}

						$nTotalVendorPaymentAmount = sumPaymentAmount($paymentRepo, $paymentCriteria);
						$nTotalVendorExpenseAmount = sumExpenseAmount($expenseRepo, $expenseCriteria);

						$nTotalPaymentAmount += $nTotalVendorPaymentAmount;
						$nTotalExpenseAmount += $nTotalVendorExpenseAmount;

						$nTotalVendorNet = $nTotalVendorPaymentAmount - $nTotalVendorExpenseAmount;

						echo "<tr>";
						echo "<td>{$vendor->name}</td>";
						echo "<td>$";
						echo "<a href='" . ADMIN_DIR . "/PaymentMaintenance.php{$sPaymentQueryString}'>";
						echo number_format((float)$nTotalVendorPaymentAmount, 2, '.', ',');
						echo "</a>";
						echo "</td>";
						echo "<td>$";
						echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sExpenseQueryString}'>";
						echo number_format((float)$nTotalVendorExpenseAmount, 2, '.', ',');
						echo "</a>";
						echo "</td>";
						echo "<td>$";
						echo number_format((float)$nTotalVendorNet, 2, '.', ',');
						echo "</td>";
						echo "</tr>";
					}

					echo "<tr>";
					echo "<td>Total</td>";
					echo "<td> $";
					if ($nTotalPaymentAmount > 0)
					{
						echo "<a href='" . ADMIN_DIR . "/PaymentMaintenance.php{$sBaseQueryString}'>";
						echo number_format((float)$nTotalPaymentAmount, 2, '.', ',');
						echo "</a>";
					}
					else
					{
						echo number_format((float)$nTotalPaymentAmount, 2, '.', ',');
					}
					echo "</td>";
					echo "<td> $";
					if ($nTotalExpenseAmount > 0)
					{
						echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sBaseQueryString}'>";
						echo number_format((float)$nTotalExpenseAmount, 2, '.', ',');
						echo "</a>";
					}
					else
					{
						echo number_format((float)$nTotalExpenseAmount, 2, '.', ',');
					}
					echo "</td>";
					echo "<td> $";
					echo number_format((float)($nTotalPaymentAmount - $nTotalExpenseAmount), 2, '.', ',');
					echo "</td>";
					echo "</tr>";
				?>
				</table>
			</div>
		</div>
	</div>
<?php

	//Only display the yearly section if there is more than one year being reported
	if ($nEndYear > $nStartYear)
	{
?>
	<div class="row">
		<div class="col-xs-12 FieldGroup">
			<div class="FieldGroupTitle">
				PAYMENTS AND EXPENSES BY YEAR
				<?php
				if (($_POST['selVendor'] ?? 0) > 0 && !empty($aVendorRecords))
				{
					echo " FOR {$aVendorRecords[0]->name}";
				}
				?>
			</div>
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<th>Year</th>
						<th>Payment</th>
						<th>Expense</th>
						<th>Net</th>
					</tr>
					<?php
					$nGrandPaymentTotal = 0;
					$nGrandExpenseTotal = 0;

					for ($nYear = $nStartYear; $nYear <= $nEndYear; $nYear++)
					{
						$sExpenseQueryString = "{$sBaseQueryString}&YEAR={$nYear}";
						$sPaymentQueryString = "{$sBaseQueryString}&YEAR={$nYear}";

						$nYearPaymentTotal = 0.0;
						$nYearExpenseTotal = 0.0;
						foreach ($aVendorRecords as $vendor) {
							$nYearPaymentTotal += sumPaymentAmount($paymentRepo, [
								'vendorId' => $vendor->id,
								'paymentYear' => $nYear,
							]);
							$nYearExpenseTotal += sumExpenseAmount($expenseRepo, [
								'vendorId' => $vendor->id,
								'expenseYear' => $nYear,
							]);
						}
						$nYearNet = $nYearPaymentTotal - $nYearExpenseTotal;

						$nGrandPaymentTotal += $nYearPaymentTotal;
						$nGrandExpenseTotal += $nYearExpenseTotal;

						echo "<tr>";
						echo "<th>{$nYear}</th>";
						echo "<td>";
						if ($nYearPaymentTotal > 0)
						{
							echo "<a href='" . ADMIN_DIR . "/PaymentMaintenance.php{$sPaymentQueryString}'>";
							echo "$" . number_format((float)$nYearPaymentTotal, 2, '.', ',');
							echo "</a>";
						}
						else
						{
							echo "$" . number_format((float)$nYearPaymentTotal, 2, '.', ',');
						}
						echo "</td>";
						echo "<td>";
						if ($nYearExpenseTotal > 0)
						{
							echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sExpenseQueryString}'>";
							echo "$" . number_format((float)$nYearExpenseTotal, 2, '.', ',');
							echo "</a>";
						}
						else
						{
							echo "$" . number_format((float)$nYearExpenseTotal, 2, '.', ',');
						}
						echo "</td>";
						echo "<td>$" . number_format((float)$nYearNet, 2, '.', ',') . "</td>";
						echo "</tr>";
					}

					echo "<tr>";
					echo "<th>Total</th>";
					echo "<td>";
					echo "<a href='" . ADMIN_DIR . "/PaymentMaintenance.php{$sBaseQueryString}'>";
					echo "$" . number_format((float)$nGrandPaymentTotal, 2, '.', ',');
					echo "</a>";
					echo "</td>";
					echo "<td>";
					echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sBaseQueryString}'>";
					echo "$" . number_format((float)$nGrandExpenseTotal, 2, '.', ',');
					echo "</a>";
					echo "</td>";
					echo "<td>$" . number_format((float)($nGrandPaymentTotal - $nGrandExpenseTotal), 2, '.', ',') . "</td>";
					echo "</tr>";
				?>
				</table>
			</div>
		</div>
	</div>
<?php
	}
}

?>
</form>
</div>
</body>
</html>

<?php
function sumPaymentAmount(\Datalayer\PaymentRepository $paymentRepo, array $criteria): float
{
	$total = 0.0;
	foreach ($paymentRepo->find($criteria) as $payment) {
		$total += (float) ($payment->amount ?? 0);
	}
	return $total;
}

function sumExpenseAmount(\Datalayer\ExpenseRepository $expenseRepo, array $criteria): float
{
	$total = 0.0;
	foreach ($expenseRepo->find($criteria) as $expense) {
		$total += (float) ($expense->expenseAmount ?? 0);
	}
	return $total;
}
?>
