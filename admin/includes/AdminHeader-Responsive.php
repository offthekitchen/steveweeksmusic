<?php
/*
*******************************************************************
AdminHeader-Rsponsive.php
This PHP file defines the responsive header for the Admin Home Page 
including the navigation menu
NOTES
Date        Change
-------------------------------------------------------------
2011-23-16	Created Responsive version
2012-04-16	Corrected "Reports" menu
2019-08-15  Added Artist Report
*******************************************************************
*/
?>
<!--Admin Header -->
<div class="col-xs-12" align="center" bgcolor="#FFFFFF">
	<div class="row">
		<div class="col-xs-12 admin-header">
			<div class="row">
				<div class="col-xs-12 col-md-2 admin-header">
					<a name="Top" href="http://www.steveweeksmusic.com" target="_blank"><img src="<?php echo IMG_DIR ?>/LogoSmall.png" border="0" /></a>
				</div>
				<div class="col-xs-12 col-md-10 admin-header">
					STEVE WEEKS MUSIC MAINTENANCE
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li <?php if ($sActiveMenuItem == ADMIN_HOME_ACTIVE) {
								echo "class=\"active\"";
							} ?>><a href="<?php echo ADMIN_DIR; ?>/AdminMain.php">Admin Home</a></li>

						<li class="dropdown<?php if ($sActiveMenuItem == PRODUCTS_ACTIVE) {
												echo " active";
											} ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								Products <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo ADMIN_DIR; ?>/ProductMaintenance.php">Product Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/VendorMaintenance.php">Vendor Maintenance</a></li>
							</ul>
						</li>

						<li class="dropdown<?php if ($sActiveMenuItem == PERFORMANCES_ACTIVE) {
												echo " active";
											} ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Performance <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo $ROOT; ?>/Availability.php" target="_blank">Availability Calendar</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/UnavailableDatesMaintenance.php">Availability Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/Setlist.php">Generate Setlists</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/MileageMaintenance.php">Mileage Maintenance</a></li>	
								<li><a href="<?php echo ADMIN_DIR; ?>/PerformanceEmailSignature.php">Performance Email Signature</a></li>							
								<li><a href="<?php echo ADMIN_DIR; ?>/PerformanceMaintenance.php">Performance Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/PerformanceSongsMaintenance.php">Performance Song Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/PerformanceTasks.php">Performance Tasks</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/TourMaintenance.php">Tour Maintenance</a></li>
							</ul>
						</li>

						<li class="dropdown<?php if ($sActiveMenuItem == FINANCES_ACTIVE) {
												echo " active";
											} ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								Finances <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo ADMIN_DIR; ?>/CategoryMaintenance.php">Category Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/ExpenseMaintenance.php">Expenses Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/PaymentMaintenance.php">Payment Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/RevenueMaintenance.php">Revenue Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/RevenueTypeMaintenance.php">Revenue Type Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/TaxCategoryMaintenance.php">Tax Category Maintenance</a></li>
							</ul>
						</li>

						<li class="dropdown<?php if ($sActiveMenuItem == DISCOGRAPHY_ACTIVE) {
												echo " active";
											} ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								Discography <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
		 						<li><a href="<?php echo ADMIN_DIR; ?>/ArtistMaintenance.php">Artist Maintenance</a></li>
								 <li><a href="<?php echo ADMIN_DIR; ?>/AwardMaintenance.php">Award Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/CDMaintenance.php">CD Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/ReviewMaintenance.php">Review Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/SongMaintenance.php">Song Maintenance</a></li>
							</ul>
						</li>

						<li class="dropdown<?php if ($sActiveMenuItem == REPORTS_ACTIVE) {
												echo " active";
											} ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								Reports <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo ADMIN_DIR; ?>/ArtistReport.php">Artist Report</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/ColoradoSessionsReport.php">Colorado Sessions Report</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/ErrorReport.php">Error Report</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/IncomeTaxReport.php">Income Tax Report</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/MileageReport.php">Mileage Report</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/PerformanceReport.php">Performance Report</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/ProductReport.php">Product Report</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/RevenueTypeReport.php">Revenue Type Report</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/SalesTaxReport.php">Sales Tax Report</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/SongReport.php">Song Report</a></li>
							</ul>
						</li>

						<li class="dropdown<?php if ($sActiveMenuItem == MISC_ACTIVE) {
												echo " active";
											} ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								Misc <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo ADMIN_DIR; ?>/SearchTermsMaintenance.php">Search Term Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/ThurdyCommentMaintenance.php">Thurdy Comment Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/ThurdyDropMaintenance.php">Thurdy Drop Maintenance</a></li>
								<li><a href="<?php echo ADMIN_DIR; ?>/ThurdySongDataMaintenance.php">Thurdy Song Data Maintenance</a></li>
							</ul>
						</li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</nav>
	</div>
</div>
<!--End Admin Header -->