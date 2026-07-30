<?php
/*
*******************************************************************
ErrorReport.php
This PHP file generates a report of Errors
NOTES
Date        Change
-------------------------------------------------------------
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

//include Payment Class		
include_once(CLASS_DIR . "/class_Payment.php");

//include Revenue Class		
include_once(CLASS_DIR . "/class_Revenue.php");

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
		<form name="ErrorReport" action="SalesTaxReport.php" method="post">
			<?php
			//Instantiate needed objects
			$form = new Form();

			$oPayment = new Payment();
			if ($oPayment->getPaymentErrors()) {
			} else {
				//Error getting payment errors
				echo "ERROR RETRIEVING PAYMENT ERRORS";
				$form->sMessage = "Error retrieving Payment Errors: {$oRevenues->sErrorMessage}";
			}

			$oRevenue = new Revenue();

			if ($oRevenue->getRevenueErrors()) {
			} else {
				//Error getting revenue errors
				echo "ERROR RETRIEVING REVENUE ERRORS";
				$form->sMessage = "Error retrieving Revenue Errors: {$oRevenues->sErrorMessage}";
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
							if (sizeof($oPayment->aPaymentErrors) > 0) {
							?>
								<div class="table-responsive">
									<table class="table table-striped">

										<tr>
											<th></th>
											<th>Payment Record</th>
											<th>Error</th>
										</tr>

										<?php

										foreach ($oPayment->aPaymentErrors as $oPaymentError) {

											echo "<tr>";
											echo "<td align=\"left\">";
											echo "<img src=" . ADMIN_IMG_DIR . "/error-severity-{$oPaymentError->nErrorSeverity}.png height=15 width=15>";
											echo "</td>";
											echo "<td align=\"left\">";
											echo "<a href=\"./PaymentMaintenance.php?ID={$oPaymentError->nRecordId}\" target=\"_datacorrection\">{$oPaymentError->sRecordDesc}</a>";
											echo "</td>";
											echo "<td align=\"left\">";
											echo $oPaymentError->sErrorDesc;
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
							if (sizeof($oRevenue->aRevenueErrors) > 0) {
							?>
								<div class="table-responsive">
									<table class="table table-striped">

										<tr>
											<th></th>
											<th>Revenue Record</th>
											<th>Error</th>
										</tr>

										<?php

										foreach ($oRevenue->aRevenueErrors as $oRevenueError) {

											echo "<tr>";
											echo "<td align=\"left\">";
											echo "<img src=" . ADMIN_IMG_DIR . "/error-severity-{$oRevenueError->nErrorSeverity}.png height=15 width=15>";
											echo "</td>";
											echo "<td align=\"left\">";
											echo "<a href=\"./RevenueMaintenance.php?ID={$oRevenueError->nRecordId}\" target=\"_datacorrection\">{$oRevenueError->sRecordDesc}</a>";
											echo "</td>";
											echo "<td align=\"left\">";
											echo $oRevenueError->sErrorDesc;
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