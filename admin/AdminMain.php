	<?php
	
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");
	
	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");
	 	
	//include DB Class		
	include_once (CLASS_DIR . "/class_DB.php");
		
	//Instantiate DB
	$DB = new Database();

	$sActiveMenuItem = ADMIN_HOME_ACTIVE;
	$sPageName = "Main";

	?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");	
?>	
<div class="container-fluid"> 
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="AdminGroup">
				<div class="row">
					<div class="col-xs-12 col-sm-3">	
						<img src="<?php echo ADMIN_IMG_DIR ?>/ServerIcon.png" />
					</div>
					<div class="col-xs-9">
						<div class="Subtitle">
							Server Information
						</div>	
						<div>
							<?php echo 'Current PHP version: ' . phpversion(); ?>
						</div>
						<div>
							<?php 
							$sVersion = $DB->getVersion();
							echo "Current mySQL version: {$sVersion}";
							?>
						</div>
						<div>
							<?php 
							$WebServerVersion = explode(" ",$_SERVER['SERVER_SOFTWARE']);
							echo "Web Server version: {$WebServerVersion[0]} {$WebServerVersion[1]}" ;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="AdminGroup" style="background-color: #F2DCFC;">
				<div class="row">
					<div class="col-xs-12 col-sm-4">	
						<img src="<?php echo ADMIN_IMG_DIR ?>/MaintIcon.png" />
					</div>
					<div class="col-xs-12 col-sm-8">
						<div class="Subtitle">
							Products
						</div>	
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>\ProductMaintenance.php">Product Maintenance</A>
						</div>
						<div class="admin-link"> 
							<A HREF="<?php echo ADMIN_DIR; ?>\VendorMaintenance.php">Vendor Maintenance</A>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
	<div class="row">	
		<div class="col-xs-12 col-sm-6">
			<div class="AdminGroup" style="background-color: #FCDDBA;">
				<div class="row">
					<div class="col-xs-12 col-sm-4">	
						<img src="<?php echo ADMIN_IMG_DIR ?>/PerformanceIcon.png" />					
					</div>
					<div class="col-xs-12 col-sm-8">
						<div class="Subtitle">
							Performances
						</div>
						<div class="admin-link">
							<A HREF="<?php echo $ROOT; ?>/Availability.php" target="_blank">Availability Calendar</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/UnavailableDatesMaintenance.php">Availability Maintenance</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/Setlist.php">Generate Setlist</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/MileageMaintenance.php">Mileage Maintenance</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/PerformanceEmailSignature.php">Performance Email Signatures</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/PerformanceMaintenance.php">Performance Maintenance</A>					
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/PerformanceSongsMaintenance.php">Performance Song Maintenance</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/PerformanceTasks.php">Performance Tasks</A>
						</div>
						<div class="admin-link">	
							<A HREF="<?php echo ADMIN_DIR; ?>/TourMaintenance.php">Tour Maintenance</A>
						</div>	
					</div>	
				</div>
			</div>
	  	</div> 
		<div class="col-xs-12 col-sm-6">
			<div class="AdminGroup" style="background-color: #FFFFCC;">
				<div class="row">
					<div class="col-xs-12 col-sm-4">
						<img src="<?php echo ADMIN_IMG_DIR ?>/FinancesIcon.png" />
					</div>
					<div class="col-xs-12 col-sm-8">
						<div class="Subtitle">
							Finances
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/CategoryMaintenance.php">Category Maintenance</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/ExpenseMaintenance.php">Expense Maintenance</A>					
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/PaymentMaintenance.php">Payment Maintenance</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/RevenueMaintenance.php">Revenue Maintenance</A>					
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/RevenueTypeMaintenance.php">Revenue Type Maintenance</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/TaxCategoryMaintenance.php">Tax Category Maintenance</A>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">	
		<div class="col-xs-12 col-sm-6">
			<div class="AdminGroup" style="background-color: #FFB7B7;">
				<div class="row">
					<div class="col-xs-12 col-sm-4">	
						<img src="<?php echo ADMIN_IMG_DIR ?>/DiscographyIcon.png" />
					</div>
					<div class="col-xs-12 col-sm-8">
						<div class="Subtitle">
							Discography
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/AwardMaintenance.php">Award Maintenance</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/CDMaintenance.php">CD Maintenance</A>
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/ReviewMaintenance.php">Review Maintenance</A>
						</div>

						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/SongMaintenance.php">Song Maintenance</A>
						</div>
					</div>	
				</div>
			</div>
		</div>	
		<div class="col-xs-12 col-sm-6">
			<div class="AdminGroup" style="background-color: #BFBFFF;">
				<div class="row">
					<div class="col-xs-12 col-sm-4">	
						<img src="<?php echo ADMIN_IMG_DIR ?>/ReportIcon.png" />
					</div>
					<div class="col-xs-12 col-sm-8">
						<div class="Subtitle">
							Reports
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/ArtistReport.php">Artist Report</A>					
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/ErrorReport.php">Error Report</A>	
						</div>	
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/IncomeTaxReport.php">Income Tax Report</A>					
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/MileageReport.php">Mileage Report</A>	
						</div>		

						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/PerformanceReport.php">Performance Report</A>					
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/ProductReport.php">Product Report</A>	
						</div>		
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/RevenueTypeReport.php">Revenue Type Report</A>	
						</div>		
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/SalesTaxReport.php">Sales Tax Report</A>					
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/SongReport.php">Song Report</A>					
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/TourReport.php">Tour Report</A>					
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">	
		<div class="col-xs-12 col-sm-6">
			<div class="AdminGroup" style="background-color: #E8E8E8;">
				<div class="row">
					<div class="col-xs-12 col-sm-4">	
						<img src="<?php echo ADMIN_IMG_DIR ?>/ToolsIcon.png" />
					</div>
					<div class="col-xs-12 col-sm-8">
						<div class="Subtitle">
						Other Tools
						</div>
						<div class="admin-link">
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
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="AdminGroup" style="background-color: #DDF3CD;">
				<div class="row">
					<div class="col-xs-12 col-sm-4">	
						<img src="<?php echo ADMIN_IMG_DIR ?>/DocIcon.png" />
					</div>
					<div class="col-xs-12 col-sm-8">
						<div class="Subtitle">
						Misc
					</div>	
					<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/AdminUserMaintenance.php">Admin Users</A>
						</div>
						<div class="admin-link">						
							<A HREF="<?php echo ADMIN_DIR; ?>/SearchTermsMaintenance.php">Search Terms Maintenance</A>					
						</div>
						<div class="admin-link">						
							<A HREF="<?php echo ADMIN_DIR; ?>/ThurdyCommentMaintenance.php">Thurdy Comment Maintenance</A>					
						</div>
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/ThurdyDropMaintenance.php">Thurdy Drop Maintenance</A>					
						</div>	
						<div class="admin-link">
							<A HREF="<?php echo ADMIN_DIR; ?>/ThurdySongMaintenance.php">Thurdy Song Maintenance</A>					
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>	
</body>
