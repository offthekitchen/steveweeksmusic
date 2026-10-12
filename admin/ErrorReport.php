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
2026-10-12	Moved error-report SQL into Payment/Revenue repositories
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
include_once(ADMIN_INCLUDE_DIR . "/Form.php");

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
				$paymentRepo = new \Datalayer\PaymentRepository();
				$revenueRepo = new \Datalayer\RevenueRepository();
				$aPaymentErrors = $paymentRepo->findPaymentErrors();
				$aRevenueErrors = $revenueRepo->findRevenueErrors();
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
