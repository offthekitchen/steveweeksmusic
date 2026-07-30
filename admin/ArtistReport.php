<?php
/*
*******************************************************************
Artist.php
This PHP file generates an Artist Report 
NOTES
Date        Change
-------------------------------------------------------------
2019-08-14  Created
2021-08-30	Updated for PHP 8
2024-04-12	Added Expense and Profit
*******************************************************************
*/	

	
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	
//This include defines the relative path to the root directory from this sub-directory
 include_once ("root.inc.php");

//inlcude web site settings
include_once ($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//inlcude admin settings
 include_once (ADMIN_DIR . "/includes/AdminSettings.php");
//inlcude Common Functions
 include_once (ADMIN_INCLUDE_DIR . "/CommonFunctions.php");
 $sActiveMenuItem = REPORTS_ACTIVE;	
 $sPageName = "Artist Report";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>


<!-- Javascript required for Calendar picker -->
<!-- <script language="javascript" src="<?php echo ADMIN_JS_DIR ?>/calendar.js"></script> -->

<body>
	<?php
	
	//include Artist Class		
 	include_once (CLASS_DIR . "/class_Artist.php");

	//include Product Class		
 	include_once (CLASS_DIR . "/class_Product.php");

	//include Revenue Class		
 	include_once (CLASS_DIR . "/class_Revenue.php");

 	//include Form Class		
 	include (CLASS_DIR . "/class_Form.php");

	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");

	?>
<div class="container-fluid">
<form name="ArtistReport" action="ArtistReport.php" method="post">
<?php
	//If a Artist ID is passed, pre-select that Artist
	if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "")
	{
		$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
		$_POST['btnGenerate'] = "GenerateReport";
	}
	//If a Year is passed, pre-select that Year
	if (isset($_REQUEST['YEAR']) && $_REQUEST['YEAR'] != "")
	{
		$_POST['selYear'] = $_REQUEST['YEAR'];
		$_POST['btnGenerate'] = "GenerateReport";
	}

	//Instantiate needed objects
	$form = new Form();
	$aArtistData = array();
	$oThisArtists = new Artist();
	$sBaseQueryString="";

	//Set the Year range based on year selection 
	if ($_POST['selYear'] > 0)
	{
		$oThisArtists->nPaidYear = $_POST['selYear'];		
		$nStartYear = $_POST['selYear'];
		$nEndYear = $_POST['selYear'];
	}
	else
	{
		$nStartYear = START_YEAR;
		$nEndYear = date('Y');
	}
		
	//If a Artist is selected, retrieve that product's information
	if ($_POST['selArtist'] > 0)
	{
		$oThisArtists->nArtistID = $_POST['selArtist'];		
	}

	if(!$oThisArtists->getArtist())
	{
		$form->nMessageType = MESSAGE_TYPE_ERROR;
		$form->sMessage = "FAILED TO GET ARTISTS: {$oThisArtist->sErrorMessage}";
	}
	
?>	
	<div class="row">
		<?php
		include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
		?>
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
				ARTIST REPORT:  
				<?php
				if($_POST['selArtist'] > 0)
				{
					echo " {$oThisArtists->aArtistRecords[0]->sArtistName}";
				}
				if($_POST['selYear'] > 0)
				{
					echo "  {$_POST['selYear']}";
				}
				?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 Buttons">
				<input type="submit" name="btnGenerate" value="Generate Report" onclick="javascript: return validate_form()" />
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FormMessage">
					<?php
					$form->renderFormMessage();
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">		
		<div class="col-xs-12 FieldGroup"> 
			<?php
			//Include Filters
			$bYearFilter = TRUE;
			$bArtistFilter = TRUE;
			include (ADMIN_INCLUDE_DIR . "/ReportFilters.php");
			?>
		</div>
	</div>
<?php
if (isset($_POST["btnGenerate"])) 
{
	//Only display this section if reporting on all revenue types 
	// if($_POST['selArtist'] == 0)
	// {
?>
	<div class="row"> 		
		<div class="col-xs-12 FieldGroup"> 
			<div class="FieldGroupTitle"> 
				ARTIST DATA
				<?php
				if ($_POST['selYear'] > 0)
				{
					echo " FOR {$_POST['selYear']}";
				}
				?>
			</div>
			<div class="table-responsive">	
				<table class="table table-striped"> 
					<tr>
						<th>Artist</th>
						<th>Revenue</th>
						<th>Expense</th>
						<th>Profit</th>
					</tr>
				<?php
					$nTotalRevenueAmount = 0;
					$nTotalExpenseAmount = 0;
					$nTotalProfitAmount = 0;

					foreach($oThisArtists->aArtistRecords as $oArtist)
					{
						$sQueryString = "{$sBaseQueryString}&ARTIST_ID={$oArtist->nArtistID}"; 
						
						$oExpense = new Expense();
						$oExpense->nArtistID = $oArtist->nArtistID;
						$oExpense->nExpenseYear = $_POST['selYear'];
						$nTotalArtistExpenseAmount = $oExpense->getExpenseAmountTotal();

						$oRevenue = new Revenue();
						$oRevenue->nArtistID = $oArtist->nArtistID;
						$oRevenue->nPaidYear = $_POST['selYear'];
						$nTotalArtistRevenueAmount = $oRevenue->getRevenueAmountTotal();

						if($nTotalArtistRevenueAmount == -1 || $nTotalArtistExpenseAmount == -1)
						{
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "FAILED TO GET DATA: {$oRevenue->sErrorMessage} {$oExpense->sErrorMessage}";
						}
						else
						{
							//Display a report row if any revenues were found matching criteria
							if(!empty($nTotalArtistRevenueAmount) && $nTotalArtistRevenueAmount > 0)
							{	
								$nTotalRevenueAmount += $nTotalArtistRevenueAmount;
							}
							//Display a report row if any revenues were found matching criteria
							if(!empty($nTotalArtistExpenseAmount) && $nTotalArtistExpenseAmount > 0)
							{	
								$nTotalExpenseAmount += $nTotalArtistExpenseAmount;
							}

							$nTotalArtistProfit = $nTotalArtistRevenueAmount - $nTotalArtistExpenseAmount;

							echo "<tr>";
							echo "<td>{$oArtist->sArtistName}</td>";
							echo "<td>$";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";  
							echo number_format((float)$nTotalArtistRevenueAmount, 2, '.', ',');
							echo "</a>";
							echo"</td>";
							echo "<td>$";
							echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sQueryString}'>";  
							echo number_format((float)$nTotalArtistExpenseAmount, 2, '.', ',');
							echo "</a>";
							echo"</td>";
							echo "<td>$";
							echo number_format((float)$nTotalArtistProfit, 2, '.', ',');
							echo"</td>";
							echo "</tr>";

						}
						
					}			

					echo "<tr>";
					echo "<td>Total</td>";
					echo "<td> $";
					if ($nTotalRevenueAmount > 0)
					{
						echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sBaseQueryString}'>";  
						echo number_format((float)$nTotalRevenueAmount, 2, '.', ',');
						echo "</a>";
					}
					else
					{
						echo number_format((float)$nTotalRevenueAmount, 2, '.', ',');
					}
					echo "</td>";
					echo "<td> $";
					if ($nTotalExpenseAmount > 0)
					{
						echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sBaseQueryString}'>";  
						echo number_format((float)$nTotalExpenseAmount, 2, '.', ',');
						echo "</a>";
					}
					else
					{
						echo number_format((float)$nTotalExpenseAmount, 2, '.', ',');
					}
					echo "</td>";
					echo "<td> $";
					echo number_format((float)($nTotalRevenueAmount - $nTotalExpenseAmount), 2, '.', ',');
					echo "</td>";
					echo "</tr>";
				?>
				</table>			
			</div>
		</div>
<?php
	//}
?>		
		<div class="col-xs-12 FieldGroup">
			<div class="FieldGroupTitle"> 
				REVENUES BY PRODUCT
				<?php
				if ($_POST['selYear'] > 0)
				{
					echo " FOR {$_POST['selYear']}";
				}
	
				if($_POST['selArtist'] > 0)
				{
					echo " {$oThisArtists->aArtistRecords[0]->sArtistName}";
				}
				?>
			</div>
			<div class="table-responsive">
				<table class="table table-striped"> 
					<tr>								
						<th></th>
						<?php
						 foreach($oThisArtists->aArtistRecords as $oArtist)
						 {
							echo "<th>{$oArtist->sArtistName}</th>";
						 }
						?>
					</tr>
				<?php
				$aArtistTotals = array();
				
				$oProducts = new Product;
				
				$oProducts->nArtistID = $oThisArtists->nArtistID;

				if ($oProducts->getProduct())
				{
				
					foreach ($oProducts->aProductRecords as $oProduct)
					{
						echo "<tr>";
						echo "<td>{$oProduct->sProductName}</td>";
						foreach($oThisArtists->aArtistRecords as $oArtist)
						{
	
							$sQueryString = "{$sBaseQueryString}&PRODUCT_ID={$oProduct->nProductID}&ARTIST_ID={$oArtist->nArtistID}";
							$oProductRevenues = new Revenue();
							$oProductRevenues->nProductID = $oProduct->nProductID;
							$oProductRevenues->nArtistID = $oArtist->nArtistID;
							$oProductRevenues->nPaidYear = $_POST['selYear'];
							$nProductRevenueTotal = $oProductRevenues->getRevenueAmountTotal();
	
							echo "<td>";
							if($nProductRevenueTotal > 0)
							{
								echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
								echo "$" . number_format((float)$nProductRevenueTotal, 2, '.', ',');
								echo "</a>";	
								$aArtistTotals[$oProductRevenues->nArtistID] += $nProductRevenueTotal;
							}
							else
							{
								echo "$" . number_format((float)$nProductRevenueTotal, 2, '.', ',');
							}
							echo "</td>";												
	
						}											
					}
				}

				echo "<tr>";
				echo "<th>Total</th>";
				
				foreach($oThisArtists->aArtistRecords as $oArtist)
				{
					$sQueryString = "{$sBaseQueryString}&ARTIST_ID={$oArtist->nArtistID}";
					echo "<td>";
					echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
					echo "$" . number_format((float)$aArtistTotals[$oArtist->nArtistID], 2, '.', ',');	
					echo "</a>";
					echo "</td>";
				}
			
				echo "</tr>";		
				?>
				</table>
			</div>
		</div>
	</div>
<?php

	//Only display the yearly section if there is more than one year being reported
	if ($nEndYear > $nStartYear)
	{
?>
	<div class="row"> 		
		<div class="col-xs-12 FieldGroup"> 
			<div class="FieldGroupTitle">
				REVENUES BY YEAR
				<?php
				if ($_POST['selArtist'] > 0)
				{
					echo " FOR {$oThisArtists->aArtistRecords[0]->sArtistName}";
				}
				?>						
			</div>
			<div class="table-responsive">
				<table class="table table-striped"> 
					<tr>								
						<th></th>
						<?php
						 foreach($oThisArtists->aArtistRecords as $oArtist)
						 {
							echo "<th>{$oArtist->sArtistName}</th>";
						 }
						?>
					</tr>
					<?php
					$sReportStyleClass == REPORT_STYLE_CLASS;
					$aArtistTotals = array();
					
					for ($nYear = $nStartYear; $nYear <= $nEndYear; $nYear++)
					{									
						//Alternate the report style
						setReportStyleClass($sReportStyleClass);
					
						echo "<tr>";
						echo "<th>{$nYear}</th>";
	
						foreach($oThisArtists->aArtistRecords as $oArtist)
						{
	
							$sQueryString = "{$sBaseQueryString}&YEAR={$nYear}&ARTIST_ID={$oArtist->nArtistID}";
							$oYearRevenues = new Revenue();
							$oYearRevenues->nArtistID = $oArtist->nArtistID;
							$oYearRevenues->nPaidYear = $nYear;
							$nYearRevenuesTotal = $oYearRevenues->getRevenueAmountTotal();
	
							echo "<td>";
							if($nYearRevenuesTotal > 0)
							{
								echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
								echo "$" . number_format((float)$nYearRevenuesTotal, 2, '.', ',');
								echo "</a>";	
								$aArtistTotals[$oYearRevenues->nArtistID] += $nYearRevenuesTotal;
							}
							else
							{
								echo "$" . number_format((float)$nYearRevenuesTotal, 2, '.', ',');
							}
							echo "</td>";												
	
						}											
	
					}

					echo "<tr>";
					echo "<th>Total</th>";
					
					foreach($oThisArtists->aArtistRecords as $oArtist)
					{
						$sQueryString = "{$sBaseQueryString}&ARTIST_ID={$oArtist->nArtistID}";
						echo "<td>";
						echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
						echo "$" . number_format((float)$aArtistTotals[$oArtist->nArtistID], 2, '.', ',');	
						echo "</a>";
						echo "</td>";
					}
				
					echo "</tr>";		
				?>
				</table>						
			</div>
		</div>
	</div>
<?php
	}
}

?>
</form>
</div>
</body>
<script type="text/javascript">
<!--
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

	//If no Dates are chosen, default then to current year
    if ( document.ArtistReport.StartDate.value == "" || document.ArtistReport.StartDate.value == "0000-00-00" || document.ArtistReport.EndDate.value == "" || document.ArtistReport.EndDate.value == "0000-00-00" )
    {
		sYear = new Date().getFullYear();
		document.ArtistReport.StartDate.value = sYear + "-01-01";
		document.ArtistReport.EndDate.value = sYear + "-12-31";
    }

	//If any errors, alert
	if (!bValid)
	{
		alert(sErrorMessage);
	}

    return bValid;
}
//-->
</script>
</html>
