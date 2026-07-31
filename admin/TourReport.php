<?php
/*
*******************************************************************
TourReport.php
This PHP file defines the Tour Report page 
NOTES
Date        Change
-------------------------------------------------------------
2015-04-29	Corrected days calculation
2017-06-09	Made Responsive
2026-07-30	Migrated to new Datalayer repositories
*******************************************************************
*/	
error_reporting(E_ALL ^ E_NOTICE);
	
//This include defines the relative path to the root directory from this sub-directory
 include_once ("root.inc.php");

//inlcude web site settings
include_once ($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//inlcude admin settings
 include_once (ADMIN_DIR . "/includes/AdminSettings.php");

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Tour.php");
include_once(DATALAYER_DIR . "/TourRepository.php");
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");
include_once(DATALAYER_DIR . "/Expense.php");
include_once(DATALAYER_DIR . "/ExpenseRepository.php");
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");
include_once(DATALAYER_DIR . "/RevenueType.php");
include_once(DATALAYER_DIR . "/RevenueTypeRepository.php");
include_once(DATALAYER_DIR . "/TaxCategory.php");
include_once(DATALAYER_DIR . "/TaxCategoryRepository.php");

$sActiveMenuItem = PERFORMANCES_ACTIVE;
$sPageName = "Tour Report";

//Instantiate needed objects
$tourRepo = new \Datalayer\TourRepository();
$performanceRepo = new \Datalayer\PerformanceRepository();
$expenseRepo = new \Datalayer\ExpenseRepository();
$revenueRepo = new \Datalayer\RevenueRepository();
$revenueTypeRepo = new \Datalayer\RevenueTypeRepository();
$taxCategoryRepo = new \Datalayer\TaxCategoryRepository();

$aTourRevenues = array();
$aTourExpenses = array();
$thisTour = null;
	
//Get the ID query string parameter
if (isset($_REQUEST['ID']) && $_REQUEST['ID'] != "")
{ 
	$nThisTourID = $_REQUEST['ID'];
}
	
//If an ID was passed to the page, retrieve that record for update		
if (!is_null($nThisTourID))
{
	$thisTour = $tourRepo->findById((int) $nThisTourID);
	if (!$thisTour)
	{
		$sErrorMessage = "NO DATA FOUND FOR TOUR ID " . $nThisTourID;
	}
}
else
{
	//If no Tour ID was passed, get the last tour as a default
	$thisTour = $tourRepo->findLastCompleted();
	if ($thisTour)
	{
		$nThisTourID = $thisTour->id;
	}
	else
	{
		$sErrorMessage =  "ERROR RERIEVING LAST TOUR";
	}
}	

//Get Performance data for this tour
if (!empty($thisTour))
{				
	$nTotalExpenseAmount = 0.00;
	$nTotalRevenueAmount = 0.00;
	$dtDifference = strtotime($thisTour->endDate) - strtotime($thisTour->startDate);
	$nDays = floor($dtDifference/(60*60*24)) + 1;
		
	$aTourPerformances = $performanceRepo->find(['tourId' => (int) $nThisTourID]);
	$nPerformances = count($aTourPerformances);
	$nTotalCDSales = 0;
	foreach($aTourPerformances as $oTourPerformance)
	{
		$aPerformanceRevenues = $revenueRepo->find(['performanceId' => $oTourPerformance->id]);
		foreach ($aPerformanceRevenues as $oRevenueRecord)
		{
			//If the revenue is a CD sale add the quantity to the CD sale total
			if($oRevenueRecord->revenueTypeId == REVENUE_TYPE_CD_SALE)
			{
				$nTotalCDSales += $oRevenueRecord->productQty ?? 0;
			}
			
			//Add the Revenue Record to the end of the appropriate array for revenue type
			$aTourRevenues[$oRevenueRecord->revenueTypeId][] = $oRevenueRecord;

			$nTotalRevenueAmount += $oRevenueRecord->amount ?? 0;
		}
	}

	$aTourExpenseRecords = $expenseRepo->find(['tourId' => (int) $nThisTourID]);
	foreach($aTourExpenseRecords as $oExpenseRecord)
	{
		//Add the Expense Record to the end of the appropriate array for the tax category
		$aTourExpenses[$oExpenseRecord->taxCategoryId][] = $oExpenseRecord;
		$nTotalExpenseAmount += $oExpenseRecord->expenseAmount ?? 0;
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>
<?php
	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
	
	if (empty($sErrorMessage))
	{

?>	
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
			Tour Report - <?php echo $thisTour->name;?>
				</div>
			</div>
			<div class="col-xs-12">
				<A HREF='<?php echo ADMIN_DIR . "/TourMaintenance.php?ID=" . $thisTour->id; ?>' class="secondaryLinkButton">Edit Tour</A>
			</div>
		</div>
	</div>
	<div class="row"> 		
		<div class="col-xs-12 FieldGroup"> 
			<div class="FinancialData">
				<div class="row">		
					<div class="col-xs-12 col-md-6 FieldGroup">
						<div class="row">		
							<div class="col-xs-12 FieldGroupTitle"> 
								General Information
							</div>
							<div class="col-xs-12">
								<div class="table-responsive">
									<table class="table table-striped"> 
										<tr>
										<?php
											echo "<td class='" . RESULT_STYLE_CLASS . "'>Number of Days:</td>";
											echo "<td class='" . RESULT_STYLE_CLASS . "'>" . $nDays . "</td>";
										?>
										</tr>
										<tr>
										<?php
											echo "<td class='" . RESULT_STYLE_CLASS . "'>Number of Performances:</td>";
											echo "<td class='" . RESULT_STYLE_CLASS . "'>" . $nPerformances . "</td>";
										?>
										</tr>
										<tr>
										<?php
											echo "<td class='" . RESULT_STYLE_CLASS . "'>CDs Sold:</td>";
											echo "<td class='" . RESULT_STYLE_CLASS . "'>" . $nTotalCDSales . "</td>";
										?>
										</tr>
										<tr>
										<?php
											echo "<td class='" . RESULT_STYLE_CLASS . "'>CDs per Performance:</td>";
											if ($nPerformances > 0)
											{
												echo "<td class='" . RESULT_STYLE_CLASS . "'>" . round($nTotalCDSales/$nPerformances,0) . "</td>";
											}
											else
											{
												echo "<td class='" . RESULT_STYLE_CLASS . "'>0</td>";
											}	
										?>
										</tr>
									</table>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-6 FieldGroup">
						<div class="row">		
							<div class="col-xs-12 FieldGroupTitle"> 
								Profit
							</div>
							<div class="col-xs-12">
								<div class="table-responsive">
									<table class="table table-striped"> 
									<?php 
									$nTotalProfit = $nTotalRevenueAmount - $nTotalExpenseAmount; 
									?>
									<tr>
										<th class='<?php echo RESULT_STYLE_CLASS; ?>'></th>
										<th class='<?php echo RESULT_STYLE_CLASS; ?>'>Amount</th>
									</tr>
									<tr>
										<td class='<?php echo RESULT_STYLE_CLASS; ?>'>Total Profit:</td>
										<td class=<?php echo "'" . RESULT_STYLE_CLASS . "'>$" . number_format((float)$nTotalProfit, 2, '.', '') ?></td>
									</tr>
									<tr>
										<td class='<?php echo RESULT_STYLE_CLASS; ?>'>Profit per Day:</td>
										<td class=<?php echo "'" . RESULT_STYLE_CLASS . "'>$" . number_format((float)$nTotalProfit/$nDays, 2, '.', '') ?></td>
									</tr>
									<tr>
										<td class='<?php echo RESULT_STYLE_CLASS; ?>'>Profit per Performance:</td>
										<?php 
										if ($nPerformances > 0)
										{
											$nProfitPerPerformance =  number_format((float)$nTotalProfit/$nPerformances, 2, '.', '');
										}	
										else
										{
											$nProfitPerPerformance =  0.00;
										}
										?>
										<td class=<?php echo "'" . RESULT_STYLE_CLASS . "'>$" . $nProfitPerPerformance; ?></td>
									</tr>
									</table>
								</div>
							</div>
						</div>
					</div>					
				</div>
				<div class="row">		
					<div class="col-xs-12 col-md-6 FieldGroup">
						<div class="row">		
							<div class="col-xs-12 FieldGroupTitle"> 
								Revenue
							</div>
							<div class="col-xs-12">
								<div class="table-responsive">
									<table class="table table-striped"> 					
									<?php
										foreach ($revenueTypeRepo->find() as $oRevenueType)
										{
											$nRevenueTypeTotal = 0;
											//If an array of revenue objects was built for the revenue type display all revenues
											if(isset($aTourRevenues[$oRevenueType->id]))
											{
												echo "<tr>";
												echo "<th colspan=3 align=left>" . $oRevenueType->name . "</th>";
												echo "</tr>";
												echo "<tr>";
												echo "<th class='" . RESULT_STYLE_CLASS . "'>Date</th>";
												echo "<th class='" . RESULT_STYLE_CLASS . "'>Description</th>";
												echo "<th class='" . RESULT_STYLE_CLASS . "'>Amount</th>";
												echo "</tr>";
												
												foreach($aTourRevenues[$oRevenueType->id] as $oTourRevenue)
												{
													echo "<tr>";
													echo "<td class='" . RESULT_STYLE_CLASS . "'>" . $oTourRevenue->paidDate . "</td>";
													echo "<td class='" . RESULT_STYLE_CLASS . "'>";
													echo "<A HREF='" . ADMIN_DIR . "/RevenueMaintenance.php?ID=" . $oTourRevenue->id;
													echo "'>" . $oTourRevenue->description . "</A></td>";
													echo "<td class='" . RESULT_STYLE_CLASS . "' align=right>$" . $oTourRevenue->amount . "</td>";
													echo "</tr>";
													$nRevenueTypeTotal += $oTourRevenue->amount ?? 0;
				
												}
												echo "<tr>";
												echo "<td></td>";
												echo "<td class='" . RESULT_STYLE_CLASS . "'> " . $oRevenueType->name . " Subotal </td>";
												echo "<td class='" . RESULT_STYLE_CLASS . "' align=right> $" . number_format((float)$nRevenueTypeTotal, 2, '.', '') . "</td>";
												echo "</tr>";
											}		
											
										}
									?>
										<tr>
									<?php
											echo "<td class='" . RESULT_STYLE_CLASS . "' colspan='3'><HR></td>";
									?>
										</tr>
										<tr>
									<?php
											echo "<td class='" . RESULT_STYLE_CLASS . "'></td>";
											echo "<td class='" . RESULT_STYLE_CLASS . "'>TOTAL</td>";
											echo "<td class='" . RESULT_STYLE_CLASS . "'>$" . number_format((float)$nTotalRevenueAmount, 2, '.', '') . "</td>";
									?>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-6 FieldGroup">
						<div class="row">		
							<div class="col-xs-12 FieldGroupTitle"> 
								Expenses
							</div>
							<div class="col-xs-12">
								<div class="table-responsive">
									<table class="table table-striped"> 						
									<?php
										foreach ($taxCategoryRepo->find() as $oTaxCategory)
										{
											$nTaxCategoryTotal = 0;
											//If an array of expense objects was built for the Tax Category display all expenses
											if(isset($aTourExpenses[$oTaxCategory->id]))
											{
												echo "<tr>";
												echo "<th colspan=3 align=left>" . $oTaxCategory->name . "</th>";
												echo "</tr>";
												echo "<tr>";
												echo "<th class='" . RESULT_STYLE_CLASS . "'>Date</th>";
												echo "<th class='" . RESULT_STYLE_CLASS . "'>Description</th>";
												echo "<th class='" . RESULT_STYLE_CLASS . "'>Amount</th>";
												echo "</tr>";
												
												foreach($aTourExpenses[$oTaxCategory->id] as $oTourExpense)
												{
													echo "<tr>";
													echo "<td class='" . RESULT_STYLE_CLASS . "'>" . $oTourExpense->expenseDate . "</td>";
													echo "<td class='" . RESULT_STYLE_CLASS . "'>";
													echo "<A HREF='" . ADMIN_DIR . "/ExpenseMaintenance.php?ID=" . $oTourExpense->id;
													echo "'>" . $oTourExpense->description . "</A></td>";
													echo "<td class='" . RESULT_STYLE_CLASS . "' align=right>$" . $oTourExpense->expenseAmount . "</td>";
													echo "</tr>";
													$nTaxCategoryTotal += $oTourExpense->expenseAmount ?? 0;
				
												}
												echo "<tr>";
												echo "<td></td>";
												echo "<td class='" . RESULT_STYLE_CLASS . "'> " . $oTaxCategory->name . " Subotal </td>";
												echo "<td class='" . RESULT_STYLE_CLASS . "' align=right> $" . number_format((float)$nTaxCategoryTotal, 2, '.', '') . "</td>";
												echo "</tr>";
											}		
											
										}
									?>
										<tr>
									<?php
										echo "<td class='" . RESULT_STYLE_CLASS . "' colspan='3'><HR></td>";
									?>
										</tr>
										<tr>
									<?php
										echo "<td class='" . RESULT_STYLE_CLASS . "'></td>";
										echo "<td class='" . RESULT_STYLE_CLASS . "'>TOTAL</td>";
										echo "<td class='" . RESULT_STYLE_CLASS . "'>$" . number_format((float)$nTotalExpenseAmount, 2, '.', '') . "</td>";
									?>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<?php

	}
	else
	{
		echo $sErrorMessage;
	}
	
?>
</html>
