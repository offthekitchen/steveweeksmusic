<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
<?php
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");

	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");

	include_once(DATALAYER_DIR . "/Connection.php");
	include_once(DATALAYER_DIR . "/Payment.php");
	include_once(DATALAYER_DIR . "/PaymentRepository.php");

	$dtPaymentDate = $_GET['date'] ?? '';

	try {
		$paymentRepo = new \Datalayer\PaymentRepository();
		$aPaymentRecords = $paymentRepo->find(['paymentDate' => $dtPaymentDate]);

		if (sizeof($aPaymentRecords) > 0) {
			echo "<OPTION VALUE='0'>NONE</OPTION>";
			foreach ($aPaymentRecords as $oPotentialPayment) {
				echo "<OPTION VALUE=" . $oPotentialPayment->id . ">" . $oPotentialPayment->description;
				echo " - " . $oPotentialPayment->amount . "</OPTION>";
			}
		} else {
			echo "<OPTION VALUE='0' SELECTED>NONE</OPTION>";
		}
	} catch (\Throwable $e) {
		echo "<OPTION>ERROR RETRIEVING PAYMENTS" . $e->getMessage() . "</OPTION>";
	}

?>
</body>
</html>
