<?php
/*
*******************************************************************
ErrorReport.php
This PHP file generates a report of Errors
NOTES
Date        Change
-------------------------------------------------------------
2021-08-30	Updated for PHP 8
2026-07-30	Migrated to new Datalayer Payment/Revenue repositories
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

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Payment.php");
include_once(DATALAYER_DIR . "/PaymentRepository.php");
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

$sActiveMenuItem = REPORTS_ACTIVE;
$sPageName = "Error Report";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<div class="container-fluid">
		<form name="ErrorReport" action="ErrorReport.php" method="post">
			<?php
			//Instantiate needed objects
			$form = new Form();

			try {
				$aPaymentErrors = findPaymentErrors();
				$aRevenueErrors = findRevenueErrors();
			} catch (\Throwable $e) {
				echo "ERROR RETRIEVING DATA ERRORS";
				$form->sMessage = "Error retrieving data errors: " . $e->getMessage();
				$aPaymentErrors = [];
				$aRevenueErrors = [];
			}

			?>
			<div class="row">
			<?php
			include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
			?>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroup">
					<div class="row">
						<div class="col-xs-12">
							<div class="FieldGroupTitle">Payment Errors</div>
							<?php
							if (sizeof($aPaymentErrors) > 0) {
							?>
								<div class="table-responsive">
									<table class="table table-striped">

										<tr>
											<th></th>
											<th>Payment Record</th>
											<th>Error</th>
										</tr>

										<?php

										foreach ($aPaymentErrors as $paymentError) {

											echo "<tr>";
											echo "<td align=\"left\">";
											echo "<img src=" . ADMIN_IMG_DIR . "/error-severity-{$paymentError['severity']}.png height=15 width=15>";
											echo "</td>";
											echo "<td align=\"left\">";
											echo "<a href=\"./PaymentMaintenance.php?ID={$paymentError['recordId']}\" target=\"_datacorrection\">{$paymentError['recordDesc']}</a>";
											echo "</td>";
											echo "<td align=\"left\">";
											echo $paymentError['errorDesc'];
											echo "</td>";
											echo "</tr>";
										}

										?>

									</table>
								</div>

							<?php
							} else {
								echo "<div>NO ERRORS FOUND</div>";
							}
							?>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							<div class="FieldGroupTitle">Revenue Errors</div>
							<?php
							if (sizeof($aRevenueErrors) > 0) {
							?>
								<div class="table-responsive">
									<table class="table table-striped">

										<tr>
											<th></th>
											<th>Revenue Record</th>
											<th>Error</th>
										</tr>

										<?php

										foreach ($aRevenueErrors as $revenueError) {

											echo "<tr>";
											echo "<td align=\"left\">";
											echo "<img src=" . ADMIN_IMG_DIR . "/error-severity-{$revenueError['severity']}.png height=15 width=15>";
											echo "</td>";
											echo "<td align=\"left\">";
											echo "<a href=\"./RevenueMaintenance.php?ID={$revenueError['recordId']}\" target=\"_datacorrection\">{$revenueError['recordDesc']}</a>";
											echo "</td>";
											echo "<td align=\"left\">";
											echo $revenueError['errorDesc'];
											echo "</td>";
											echo "</tr>";
										}

										?>

									</table>
								</div>
							<?php
							} else {
								echo "<div>NO ERRORS FOUND</div>";
							}
							?>
						</div>
					</div>

				</div>
		</form>
	</div>
</body>

</html>

<?php
/**
 * Payment validation errors (legacy getPaymentErrors logic).
 * TODO: Move to PaymentRepository when error-report queries are added to datalayer.
 *
 * @return array<int, array{recordId: int, recordDesc: string, errorDesc: string, severity: int}>
 */
function findPaymentErrors(): array
{
	$db = \Datalayer\Connection::getPdo();
	$errors = [];

	$sql = 'SELECT * FROM PAYMENT
	         WHERE PAYMENT.PAYMENT_AMOUNT <> (
	               SELECT SUM(REVENUE.REVENUE_AMOUNT) FROM REVENUE
	                WHERE REVENUE.PAYMENT_ID = PAYMENT.PAYMENT_ID
	         )';
	$stmt = $db->query($sql);
	while ($row = $stmt->fetch()) {
		$errors[] = [
			'recordId' => (int) $row['PAYMENT_ID'],
			'recordDesc' => 'Payment: ' . $row['PAYMENT_DESCRIPTION'] . ': $' . $row['PAYMENT_AMOUNT'],
			'errorDesc' => 'Payment Amount does not match revenues',
			'severity' => 3,
		];
	}

	$sql = 'SELECT * FROM PAYMENT
	         WHERE NOT EXISTS (
	               SELECT * FROM REVENUE WHERE REVENUE.PAYMENT_ID = PAYMENT.PAYMENT_ID
	         )';
	$stmt = $db->query($sql);
	while ($row = $stmt->fetch()) {
		$errors[] = [
			'recordId' => (int) $row['PAYMENT_ID'],
			'recordDesc' => 'Payment: ' . $row['PAYMENT_DESCRIPTION'] . ': $' . $row['PAYMENT_AMOUNT'],
			'errorDesc' => 'Payment has no revenues',
			'severity' => 3,
		];
	}

	return $errors;
}

/**
 * Revenue validation errors (legacy getRevenueErrors logic).
 * TODO: Move to RevenueRepository when error-report queries are added to datalayer.
 *
 * @return array<int, array{recordId: int, recordDesc: string, errorDesc: string, severity: int}>
 */
function findRevenueErrors(): array
{
	$db = \Datalayer\Connection::getPdo();
	$errors = [];

	$sql = "SELECT * FROM REVENUE
	         WHERE REVENUE.PAID_DATE = '0000-00-00'
	            OR REVENUE.REVENUE_DATE = '0000-00-00'";
	$stmt = $db->query($sql);
	while ($row = $stmt->fetch()) {
		$errors[] = [
			'recordId' => (int) $row['REVENUE_ID'],
			'recordDesc' => 'Revenue: ' . $row['REVENUE_DESCRIPTION'] . ': $' . $row['REVENUE_AMOUNT'],
			'errorDesc' => 'Revenue date and/or paid date is 0000-00-00',
			'severity' => 3,
		];
	}

	$sql = 'SELECT * FROM REVENUE
	         WHERE NOT EXISTS (
	               SELECT * FROM PAYMENT WHERE PAYMENT.PAYMENT_ID = REVENUE.PAYMENT_ID
	         )';
	$stmt = $db->query($sql);
	while ($row = $stmt->fetch()) {
		$errors[] = [
			'recordId' => (int) $row['REVENUE_ID'],
			'recordDesc' => 'Revenue: ' . $row['REVENUE_DESCRIPTION'] . ': $' . $row['REVENUE_AMOUNT'],
			'errorDesc' => 'Revenue has no matching payment',
			'severity' => 3,
		];
	}

	return $errors;
}
?>
