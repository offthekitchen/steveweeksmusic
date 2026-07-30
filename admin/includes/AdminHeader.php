<?php
/*
*******************************************************************
AdminHeader.php
This PHP file defines the header for the Admin Home Page including
the navigation menu
NOTES
Date        Change
-------------------------------------------------------------
2015-06-04	Removed Product Type
2015-10-35	Added Product Report Menu Item
2015-12-07	Added Mileage Maintenance and Mileage Report links
2016-01-05	Added Song Report link
2016-07-11	Added Reviews and Award Maintenance
2016-11-22	Added Revenue Type Report
*******************************************************************
*/	
?>
<TABLE BORDER=0 align="center" width="98%" bgcolor="#FFFFFF">
  <TR valign="middle">
    <TD align="left" bgcolor="485B89" valign="middle">
	  <table align="left" border="0" cellpadding="0" cellspacing="0">
	    <tr valign="left">
		  <td align="left" bgcolor="485B89" valign="middle" width="41" >
		    <a name="Top" href="http://www.steveweeksmusic.com" target="_blank"><img src="<?php echo IMG_DIR ?>/LogoSmall.png" border="0"  align="left"/></a>
		  </td>
	      <td width="89">		  </td>
		  <td align="left" valign="middle" bgcolor="485B89">
		  	<FONT color="#FFFFFF" size="+3">STEVE WEEKS MUSIC MAINTENANCE</FONT>		  
		  </td>
		  <td width="1">		  
		  </td>
		</tr>
	  </table>
	</TD>
  </TR>
  <TR height="10px">
    <TD align="center" height="10px">
	  <table align="center" width="100%" cellpadding="0px" height="10px">
	    <tr>
		  <td>
		    <div class="menustyle" id="catalogmenu">
			<ul>
			<li><a href="<?php echo ADMIN_DIR; ?>/AdminMain.php">Admin Home</a></li>
			<li><a href="<?php echo ADMIN_DIR; ?>/ProductAdmin.php" rel="prodmenu">Products</a></li>
			<li><a href="<?php echo ADMIN_DIR; ?>/PerformanceAdmin.php" rel="perfmenu">Performances</a></li>
			<li><a href="<?php echo ADMIN_DIR; ?>/FinancesAdmin.php" rel="financemenu">Finances</a></li>
			<li><a href="<?php echo ADMIN_DIR; ?>/DiscographyAdmin.php" rel="discographymenu">Discography</a></li>
			<li><a href="<?php echo ADMIN_DIR; ?>/ReportsAdmin.php" rel="reportsmenu">Reports</a></li>
			</ul>
			</div>

			<!--Product drop down menu -->                                                   
			<div id="prodmenu" class="dropmenudiv">
			<a href="<?php echo ADMIN_DIR; ?>/ProductMaintenance.php">Maintain Products</a>
			<a href="<?php echo ADMIN_DIR; ?>/VendorMaintenance.php">Maintain Vendors</a>
			</div>

			<!--Performance drop down menu -->                                                   
			<div id="perfmenu" class="dropmenudiv">
			<a href="<?php echo ADMIN_DIR; ?>/PerformanceMaintenance.php">Maintain Performances</a>
			<a href="<?php echo ADMIN_DIR; ?>/TourMaintenance.php">Maintain Tours</a>
			<a href="<?php echo ADMIN_DIR; ?>/UnavailableDatesMaintenance.php">Maintain Unavailable Dates</a>
			<a href="<?php echo ADMIN_DIR; ?>/MileageMaintenance.php">Maintain Mileages</a>
			<a href="<?php echo ADMIN_DIR; ?>/PerformanceTasks.php">Performance Tasks</a>
			<a href="<?php echo ADMIN_DIR; ?>/PerformanceSongsMaintentance.php">Maintain Performance Songs</a>
			<a href="<?php echo ADMIN_DIR; ?>/Setlist.php">Generate Setlists</a>
			<a href="<?php echo ADMIN_DIR; ?>/PerformanceEmailSignature.php">Performance Email Signature</a>
			<a href="<?php echo $ROOT; ?>/Availability.php" target="_blank">Availability</a>
			</div>
			
			<!--Finance drop down menu -->                                                   
			<div id="financemenu" class="dropmenudiv">
			<a href="<?php echo ADMIN_DIR; ?>/PaymentMaintenance.php">Maintain Payments</a>
			<a href="<?php echo ADMIN_DIR; ?>/RevenueMaintenance.php">Maintain Revenues</a>
			<a href="<?php echo ADMIN_DIR; ?>/ExpenseMaintenance.php">Maintain Expenses</a>
			<a href="<?php echo ADMIN_DIR; ?>/CategoryMaintenance.php">Maintain Categories</a>
			<a href="<?php echo ADMIN_DIR; ?>/TaxCategoryMaintenance.php">Maintain Tax Categories</a>
			<a href="<?php echo ADMIN_DIR; ?>/RevenueTypeMaintenance.php">Maintain Revenue Type</a>
			</div>

			<!--Discography drop down menu -->                                                   
			<div id="discographymenu" class="dropmenudiv">
			<a href="<?php echo ADMIN_DIR; ?>/CDMaintenance.php">Maintain CDs</a>
			<a href="<?php echo ADMIN_DIR; ?>/SongMaintenance.php">Maintain Songs</a>
			<a href="<?php echo ADMIN_DIR; ?>/AwardMaintenance.php">Maintain Awards</a>
			<a href="<?php echo ADMIN_DIR; ?>/ReviewMaintenance.php">Maintain Reviews</a>
			<a href="<?php echo ADMIN_DIR; ?>/ArtistMaintenance.php">Maintain Artists</a>
			<a href="http://www.thurdy.com/admin/SongMaintenance.php" target="Thurdy">Maintain Thurdy Songs</a>
			<a href="http://www.thurdy.com/admin/ThurdyCommentMaintenance.php" target="Thurdy">Maintain Thurdy Comments</a>
			</div>

			<!--Reports drop down menu -->                                                   
			<div id="reportsmenu" class="dropmenudiv">
			<a href="<?php echo ADMIN_DIR; ?>/SalesTaxReport.php">Sales Tax Report</a>
			<a href="<?php echo ADMIN_DIR; ?>/IncomeTaxReport.php">Income Tax Report</a>
			<a href="<?php echo ADMIN_DIR; ?>/SongReport.php">Song Report</a>
			<a href="<?php echo ADMIN_DIR; ?>/PerformanceReport.php">Performance Report</a>
			<a href="<?php echo ADMIN_DIR; ?>/MileageReport.php">Mileage Report</a>
			<a href="<?php echo ADMIN_DIR; ?>/ProductReport.php">Product Report</a>
			<a href="<?php echo ADMIN_DIR; ?>/RevenueTypeReport.php">Revenue Type Report</a>
			</div>

			<script type="text/javascript">
			cssdropdown.startflaglinemenu("catalogmenu")
			</script>
		  </td>
		</tr>
	  </table>
	</TD>
  </TR>
  </TABLE>