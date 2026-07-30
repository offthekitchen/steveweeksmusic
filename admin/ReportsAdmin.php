<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Reports Administration</title>
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
				<A HREF='<?php echo ADMIN_DIR; ?>/SalesTaxReport.php'><IMG SRC='<?php echo IMG_DIR; ?>/SalesTaxReport.jpg'>
				<BR />Sales Tax Report</A>
			</TD>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/IncomeTaxReport.php'><IMG SRC='<?php echo IMG_DIR; ?>/IncomeTaxReport.jpg'>
				<BR />Income Tax Report</A>
			</TD>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/TourReport.php'><IMG SRC='<?php echo IMG_DIR; ?>/TourReport.jpg'>
				<BR />Tour Report</A>
			</TD>
		</TR>
	</TABLE>	
</body>
</html>
