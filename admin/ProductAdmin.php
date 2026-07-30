<?php
/*
*******************************************************************
ProductAdmin.php
This PHP file defines the Basic page for Product Administration 
functions 
NOTES
Date        Change
-------------------------------------------------------------
2015-10-21	Refactored 
*******************************************************************
*/	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Product Administration</title>
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
				<A HREF='<?php echo ADMIN_DIR; ?>/ProductMaintenance.php'><IMG SRC='<?php echo ADMIN_IMG_DIR; ?>/ProductMaintenance.jpg'>
				<BR />Product Maintenance</A>
			</TD>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/VendorMaintenance.php'><IMG SRC='<?php echo ADMIN_IMG_DIR; ?>/VendorMaintenance.jpg'>
				<BR />Vendor Maintenance</A>
			</TD>
		</TR>
	</TABLE>	
</body>
</html>
