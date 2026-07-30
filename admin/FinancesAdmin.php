<?php
/*
*******************************************************************
FinanceAdmin.php
This PHP file defines the admin page for finance related tasks.
NOTES
Date        Change
-------------------------------------------------------------
05/02/2016  Changed image names
*******************************************************************
*/	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Discography Administration</title>
	<?php
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");
	?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHead.php");
?>

</head>

<body>

	<?php
	
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader.php");
	?>
	<TABLE align="left">
		<TR>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/ExpenseMaintenance.php'><IMG SRC='<?php echo ADMIN_IMG_DIR; ?>/ExpenseMaint.png'>
				<BR />Expense Maintenance</A>
			</TD>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/PaymentMaintenance.php'><IMG SRC='<?php echo ADMIN_IMG_DIR; ?>/PaymentMaint.png'>
				<BR />Payment Maintenance</A>
			</TD>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/RevenueMaintenance.php'><IMG SRC='<?php echo ADMIN_IMG_DIR; ?>/RevenueMaint.png'>
				<BR />Revenue Maintenance</A>
			</TD>
		</TR>
		<TR>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/TaxCategoryMaintenance.php'><IMG SRC='<?php echo ADMIN_IMG_DIR; ?>/TaxCategoryMaint.png'>
				<BR />Tax Category Maintenance</A>
			</TD>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/CategoryMaintenance.php'><IMG SRC='<?php echo ADMIN_IMG_DIR; ?>/CategoryMaint.png'>
				<BR />Category Maintenance</A>
			</TD>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/RevenueTypeMaintenance.php'><IMG SRC='<?php echo ADMIN_IMG_DIR; ?>/RevenueTypeMaint.png'>
				<BR />Revenue Type Maintenance</A>
			</TD>
		</TR>
	</TABLE>	
</body>
</html>
