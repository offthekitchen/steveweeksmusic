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
2026-07-30	Migrated to new Datalayer Artist/Product/Revenue/Expense repositories
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

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Artist.php");
include_once(DATALAYER_DIR . "/ArtistRepository.php");
include_once(DATALAYER_DIR . "/Product.php");
include_once(DATALAYER_DIR . "/ProductRepository.php");
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");
include_once(DATALAYER_DIR . "/Expense.php");
include_once(DATALAYER_DIR . "/ExpenseRepository.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once (CLASS_DIR . "/tc_calendar.php");

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
	$artistRepo = new \Datalayer\ArtistRepository();
	$productRepo = new \Datalayer\ProductRepository();
	$revenueRepo = new \Datalayer\RevenueRepository();
	$expenseRepo = new \Datalayer\ExpenseRepository();
	$sBaseQueryString="";

	//Set the Year range based on year selection 
	if ($_POST['selYear'] > 0)
	{
		$nStartYear = $_POST['selYear'];
		$nEndYear = $_POST['selYear'];
	}
	else
	{
		$nStartYear = START_YEAR;
		$nEndYear = date('Y');
	}
		
	$artistCriteria = [];
	if ($_POST['selArtist'] > 0)
	{
		$artistCriteria['id'] = (int) $_POST['selArtist'];
	}

	try {
		$aArtistRecords = $artistRepo->find($artistCriteria);
	} catch (\Throwable $e) {
		$aArtistRecords = [];
		$form->nMessageType = MESSAGE_TYPE_ERROR;
		$form->sMessage = "FAILED TO GET ARTISTS: " . $e->getMessage();
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
				if($_POST['selArtist'] > 0 && !empty($aArtistRecords))
				{
					echo " {$aArtistRecords[0]->name}";
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

					foreach($aArtistRecords as $artist)
					{
						$sQueryString = "{$sBaseQueryString}&ARTIST_ID={$artist->id}"; 
						
						$expenseCriteria = [];
						if ($_POST['selYear'] > 0) {
							$expenseCriteria['expenseYear'] = (int) $_POST['selYear'];
						}
						$nTotalArtistExpenseAmount = sumExpenseAmountByArtist(
							$expenseRepo,
							$productRepo,
							$artist->id,
							$expenseCriteria
						);

						$revenueCriteria = ['artistId' => $artist->id];
						if ($_POST['selYear'] > 0) {
							$revenueCriteria['paidYear'] = (int) $_POST['selYear'];
						}
						$nTotalArtistRevenueAmount = sumRevenueAmount($revenueRepo, $revenueCriteria);

						if(!empty($nTotalArtistRevenueAmount) && $nTotalArtistRevenueAmount > 0)
						{	
							$nTotalRevenueAmount += $nTotalArtistRevenueAmount;
						}
						if(!empty($nTotalArtistExpenseAmount) && $nTotalArtistExpenseAmount > 0)
						{	
							$nTotalExpenseAmount += $nTotalArtistExpenseAmount;
						}

						$nTotalArtistProfit = $nTotalArtistRevenueAmount - $nTotalArtistExpenseAmount;

						echo "<tr>";
						echo "<td>{$artist->name}</td>";
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
		<div class="col-xs-12 FieldGroup">
			<div class="FieldGroupTitle"> 
				REVENUES BY PRODUCT
				<?php
				if ($_POST['selYear'] > 0)
				{
					echo " FOR {$_POST['selYear']}";
				}
	
				if($_POST['selArtist'] > 0 && !empty($aArtistRecords))
				{
					echo " {$aArtistRecords[0]->name}";
				}
				?>
			</div>
			<div class="table-responsive">
				<table class="table table-striped"> 
					<tr>								
						<th></th>
						<?php
						 foreach($aArtistRecords as $artist)
						 {
							echo "<th>{$artist->name}</th>";
						 }
						?>
					</tr>
				<?php
				$aArtistTotals = array();
				
				$productCriteria = [];
				if ($_POST['selArtist'] > 0) {
					$productCriteria['artistId'] = (int) $_POST['selArtist'];
				}
				$aProductRecords = $productRepo->find($productCriteria);

				if (!empty($aProductRecords))
				{
				
					foreach ($aProductRecords as $product)
					{
						echo "<tr>";
						echo "<td>{$product->name}</td>";
						foreach($aArtistRecords as $artist)
						{
	
							$sQueryString = "{$sBaseQueryString}&PRODUCT_ID={$product->id}&ARTIST_ID={$artist->id}";
							$revenueCriteria = [
								'productId' => $product->id,
								'artistId' => $artist->id,
							];
							if ($_POST['selYear'] > 0) {
								$revenueCriteria['paidYear'] = (int) $_POST['selYear'];
							}
							$nProductRevenueTotal = sumRevenueAmount($revenueRepo, $revenueCriteria);
	
							echo "<td>";
							if($nProductRevenueTotal > 0)
							{
								echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
								echo "$" . number_format((float)$nProductRevenueTotal, 2, '.', ',');
								echo "</a>";	
								$aArtistTotals[$artist->id] = ($aArtistTotals[$artist->id] ?? 0) + $nProductRevenueTotal;
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
				
				foreach($aArtistRecords as $artist)
				{
					$sQueryString = "{$sBaseQueryString}&ARTIST_ID={$artist->id}";
					echo "<td>";
					echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
					echo "$" . number_format((float)($aArtistTotals[$artist->id] ?? 0), 2, '.', ',');	
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
				if ($_POST['selArtist'] > 0 && !empty($aArtistRecords))
				{
					echo " FOR {$aArtistRecords[0]->name}";
				}
				?>						
			</div>
			<div class="table-responsive">
				<table class="table table-striped"> 
					<tr>								
						<th></th>
						<?php
						 foreach($aArtistRecords as $artist)
						 {
							echo "<th>{$artist->name}</th>";
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
	
						foreach($aArtistRecords as $artist)
						{
	
							$sQueryString = "{$sBaseQueryString}&YEAR={$nYear}&ARTIST_ID={$artist->id}";
							$nYearRevenuesTotal = sumRevenueAmount($revenueRepo, [
								'artistId' => $artist->id,
								'paidYear' => $nYear,
							]);
	
							echo "<td>";
							if($nYearRevenuesTotal > 0)
							{
								echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
								echo "$" . number_format((float)$nYearRevenuesTotal, 2, '.', ',');
								echo "</a>";	
								$aArtistTotals[$artist->id] = ($aArtistTotals[$artist->id] ?? 0) + $nYearRevenuesTotal;
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
					
					foreach($aArtistRecords as $artist)
					{
						$sQueryString = "{$sBaseQueryString}&ARTIST_ID={$artist->id}";
						echo "<td>";
						echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
						echo "$" . number_format((float)($aArtistTotals[$artist->id] ?? 0), 2, '.', ',');	
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

<?php
function sumRevenueAmount(\Datalayer\RevenueRepository $revenueRepo, array $criteria): float
{
	$total = 0.0;
	foreach ($revenueRepo->find($criteria) as $revenue) {
		$total += (float) ($revenue->amount ?? 0);
	}
	return $total;
}

/**
 * Sum expenses for products belonging to an artist (legacy Expense.nArtistID join).
 * TODO: ExpenseRepository has no artistId key; composed via Product lookup.
 */
function sumExpenseAmountByArtist(
	\Datalayer\ExpenseRepository $expenseRepo,
	\Datalayer\ProductRepository $productRepo,
	int $artistId,
	array $expenseCriteria = []
): float {
	$total = 0.0;
	$aProducts = $productRepo->find(['artistId' => $artistId]);
	foreach ($aProducts as $product) {
		$criteria = array_merge($expenseCriteria, ['productId' => $product->id]);
		foreach ($expenseRepo->find($criteria) as $expense) {
			$total += (float) ($expense->expenseAmount ?? 0);
		}
	}
	return $total;
}
?>
