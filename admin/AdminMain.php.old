<?php
/*
*******************************************************************
AdminMain.php
This PHP file defines the Administrative Main 
NOTES
Date        Change
-------------------------------------------------------------
2015-10-29  Fixed link to PHPmyAdmin
2015-11-30	Changed link to PHPMyAdmin to match goDaddy consolidation
2015-12-07	Added Mileage Maintenance link
2015-12-07	Added Report links
2016-07-12	Added Review and Awards Maintenance
*******************************************************************
*/	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Admin Tools</title>
	<?php
	
	error_reporting(E_ALL ^ E_NOTICE);
	
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");

	//inlcude web site settings
 	include_once ($ROOT . "/includes/WebsiteSettings.php");
	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");
	?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHead.php");
	
	//include DB Class		
	include_once (CLASS_DIR . "/class_DB.php");

?>

</head>

<body>
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader.php");
	
	//Instantiate DB
	$DB = new Database();
	
?>	
<TABLE>
	<TR>
		<TD class="FieldGroup" valign="top" width="474">
			<table>
				<tr>
					<td rowspan="4">
						<img src="<?php echo ADMIN_IMG_DIR ?>/ServerIcon.png" />
					</td>
					<td class="Subtitle">
						Server Information
					</td>
				</tr>
				<tr>
					<td>
						<?php echo 'Current PHP version: ' . phpversion(); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php 
						$sVersion = $DB->getVersion();
						echo "Current mySQL version: {$sVersion}";
						?>
					</td>
				</tr>
				<tr>
					<td>
						<?php 
						$WebServerVersion = explode(" ",$_SERVER['SERVER_SOFTWARE']);
 						echo "Web Server version: {$WebServerVersion[0]} {$WebServerVersion[1]}" ;
						?>
					</td>
				</tr>
			</table>
	  </TD>
		<TD class="FieldGroup" valign="top" width="426" bgcolor="#F2DCFC">
			<table>
				<tr>
					<td rowspan="7" valign="top">
						<img src="<?php echo ADMIN_IMG_DIR ?>/MaintIcon.png" />
					</td>
					<td class="Subtitle" colspan="2">
						Products
					</td>
				</tr>
				<tr>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>\ProductMaintenance.php">Maintain Products</A>
					</td>
					<td width="30">
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>\VendorMaintenance.php">Maintain Vendors</A>
					</td>
					<td width="30">
					</td>
					<td>
					</td>
				</tr>
			</table>
	  </TD>
	</TR>
	<TR>
		<TD class="FieldGroup" valign="top" width="474" bgcolor="#FCDDBA"> 
			<table>
				<tr>
					<td width="40" rowspan="7" valign="top">
						<img src="<?php echo ADMIN_IMG_DIR ?>/PerformanceIcon.png" />					
					</td>
					<td class="Subtitle" colspan="2">
						Performances
					</td>
				</tr>
				<tr>
					<td width="215">
						<A HREF="<?php echo ADMIN_DIR; ?>/PerformanceMaintenance.php">Maintain Performances</A>					</td>
					<td width="14">					</td>
					<td width="176">
						<A HREF="<?php echo ADMIN_DIR; ?>/TourMaintenance.php">Tour Maintenance</A>					</td>
				</tr>
				<tr>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/UnavailableDatesMaintenance.php">Maintain Unavailable Dates</A>
					</td>
					<td width="14">					
					</td>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/MileageMaintenance.php">Maintain Mileages</A>
					</td>
				</tr>
				<tr>
					<td>
						<A HREF="<?php echo $ROOT; ?>/Availability.php" target="_blank">Availability</A>
					</td>
					<td width="14">					</td>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/PerformanceTasks.php">Performance Tasks</A>
					</td>
				</tr>
			</table>
	  </TD> 
		<TD class="FieldGroup" valign="top" bgcolor="#FFFFCC">
			<table>
				<tr>
					<td rowspan="5" valign="top">
						<img src="<?php echo ADMIN_IMG_DIR ?>/FinancesIcon.png" />
					</td>
					<td class="Subtitle">
						Finances
					</td>
				</tr>
				<tr>
					<td width="215">
						<A HREF="<?php echo ADMIN_DIR; ?>/ExpenseMaintenance.php">Maintain Expenses</A>					
					</td>
					<td width="14">					
					</td>
					<td width="176">
						<A HREF="<?php echo ADMIN_DIR; ?>/RevenueMaintenance.php">Maintain Revenues</A>					
					</td>
				</tr>
				<tr>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/PaymentMaintenance.php">Maintain Payments</A>
					</td>
					<td width="14">					
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/TaxCategoryMaintenance.php">Maintain Tax Categories</A>
					</td>
					<td width="14">					
					</td>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/CategoryMaintenance.php">Maintain Categories</A>
					</td>
				</tr>
				<tr>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/RevenueTypeMaintenance.php">Maintain Revenue Types</A>
					</td>
					<td width="14">					
					</td>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/PerformanceTasks.php">Performance Tasks</A>
					</td>
				</tr>
			</table>
		</TD>
	</TR>
	<TR>
		<TD class="FieldGroup" valign="top" width="474" bgcolor="#FFB7B7">
			<table>
				<tr>
					<td rowspan="7" valign="top">
						<img src="<?php echo ADMIN_IMG_DIR ?>/DiscographyIcon.png" />
					</td>
					<td class="Subtitle" colspan="2">
						Discography
					</td>
				</tr>
				<tr>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/CDMaintenance.php">Maintain CDs</A>
					</td>
					<td width="30">
					</td>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/SongMaintenance.php">Maintain Songs</A>
					</td>
				</tr>
				<tr>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/AwardMaintenance.php">Maintain Awards</A>
					</td>
					<td width="30">
					</td>
					<td>
						<A HREF="<?php echo ADMIN_DIR; ?>/ReviewMaintenance.php">Maintain Reviews</A>
					</td>
				</tr>
			</table>
	  </TD> 
		<TD class="FieldGroup" valign="top" bgcolor="#BFBFFF">
			<table>
				<tr>
					<td rowspan="4" valign="top">
						<img src="<?php echo ADMIN_IMG_DIR ?>/ReportIcon.png" />
					</td>
					<td class="Subtitle">
						Reports
					</td>
				</tr>
				<tr>
					<td width="215">
						<A HREF="<?php echo ADMIN_DIR; ?>/TourReport.php">Tour Report</A>					
					</td>
					<td width="14">					
					</td>
					<td width="176">
						<A HREF="<?php echo ADMIN_DIR; ?>/IncomeTaxReport.php">Income Tax Report</A>					
					</td>
				</tr>
				<tr>
					<td width="215">
						<A HREF="<?php echo ADMIN_DIR; ?>/SalesTaxReport.php">Sales Tax Report</A>					
					</td>
					<td width="14">					
					</td>
					<td width="176">
						<A HREF="<?php echo ADMIN_DIR; ?>/SongReport.php">Song Report</A>					
					</td>
				</tr>
				<tr>
					<td width="215">
						<A HREF="<?php echo ADMIN_DIR; ?>/PerformanceReport.php">Performance Report</A>					
					</td>
					<td width="14">					
					</td>
					<td width="176">
						<A HREF="<?php echo ADMIN_DIR; ?>/MileageReport.php">Mileage Report</A>					
					</td>
				</tr>
			</table>
		</TD>
	</TR>
	<TR>
		<TD class="FieldGroup" valign="top" width="474" bgcolor="#E8E8E8">
			<table>
				<tr>
					<td rowspan="5" valign="top">
						<img src="<?php echo ADMIN_IMG_DIR ?>/ToolsIcon.png" />
					</td>
					<td class="Subtitle">
						Other Tools
					</td>
				</tr>
				<tr>
					<td>
						<?php 
						if (ENVIRONMENT == "LOCAL")
						{
							$sphpMyAdminURL = "http://localhost/phpmyadmin/";
						}
						elseif (ENVIRONMENT == "PROD")
						{
							$sphpMyAdminURL = "https://a2plcpnl0421.prod.iad2.secureserver.net:2083/cpsess7456172144/3rdparty/phpMyAdmin/index.php#PMAURL-0:index.php?db=&table=&server=1&target=&token=7010b0bb600358b4f94b9d0ef68cd3ad";
						}
						?>
						<A HREF="<?php echo $sphpMyAdminURL; ?>" target="_blank">phpMyAdmin</A><SMALL> (Database Administration)</SMALL>
					</td>
					
				</tr>
				<tr>
					<td>
					</td>
				</tr>
				<tr>
					<td>
					</td>
				</tr>
				<tr>
					<td>
					</td>
				</tr>
			</table>
	  </TD>
		<TD class="FieldGroup" valign="top"  bgcolor="#DDF3CD">
			<table>
				<tr>
					<td rowspan="3" valign="top">
						<img src="<?php echo ADMIN_IMG_DIR ?>/DocIcon.png" />
					</td>
					<td class="Subtitle">
						Documentation
					</td>
				</tr>
				<tr>
					<td>
					</td>
				</tr>
				<tr>
					<td>
					</td>
				</tr>
			</table>
		</TD>
	</TR>
</TABLE>
</body>
</html>
