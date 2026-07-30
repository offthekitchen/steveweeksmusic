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

//include Tour Class		
 include_once (CLASS_DIR . "/class_Tour.php");

//include Expense Class		
 include_once (CLASS_DIR . "/class_Expense.php");

//include Revenue Class		
 include_once (CLASS_DIR . "/class_Revenue.php");

//include Revenue Type Class		
 include_once (CLASS_DIR . "/class_RevenueType.php");

//include Tax Category Class		
 include_once (CLASS_DIR . "/class_TaxCategory.php");

 $sActiveMenuItem = PERFORMANCES_ACTIVE;	
$sPageName = "Tour Report";

//Instantiate needed objects
$oTour = new Tour();

$aTourRevenues = array();
$aTourExpenses = array();
	
//Get the ID query string parameter
if (isset($_REQUEST['ID']) && $_REQUEST['ID'] != "")
{ 
	$nThisTourID = $_REQUEST['ID'];
}
	
//If an ID was passed to the page, retrieve that record for update		
if (!is_null($nThisTourID))
{
				
	$oTour->nTourID = $nThisTourID;

	//Search the Database for records matching the search criteria			
	if (!$oTour->getTour())
	{
		$sErrorMessage = "ERROR RETRIEVING DATA FOR TOUR ID " . $oTour->nTourID . ": " . $oTour->sErrorMessage;
	}
	elseif (sizeof($oTour->aTourRecords) == 0)
	{
		$sErrorMessage = "NO DATA FOUND FOR TOUR ID " . $oTour->nTourID;
	}
	else
	{
		//Tour data successfully retrieved
		$thisTour =  $oTour->aTourRecords[0];
	}
	
}
else
{
	//If no Tour ID was passed, get the last tour as a default
	$oLastTour = new Tour();
	if ($oLastTour->getLastTour())
	{
		//Tour data successfully retrieved
		$thisTour =  $oLastTour->aTourRecords[0];
		$nThisTourID = $oLastTour->aTourRecords[0]->nTourID;
	}
	else
	{
		$sErrorMessage =  "ERROR RERIEVING LAST TOUR: " . $oLastTour->sErrorMessage;
	}
}	

//Get Performance data for this tour
if (!empty($thisTour))
{				
	$nTotalExpenseAmount = 0.00;
	$nTotalRevenueAmount = 0.00;
	$dtDifference = strtotime($thisTour->dtTourEndDate) - strtotime($thisTour->dtTourStartDate);
	$nDays = floor($dtDifference/(60*60*24)) + 1;
		
	$oTourPerformances = new Performance();
	$oTourPerformances->nTourID = $nThisTourID;
	if($oTourPerformances->getPerformance())
	{
		//Store the number of performances
		$nPerformances = sizeof($oTourPerformances->aPerformanceRecords);
		$nTotalCDSales = 0;
		foreach($oTourPerformances->aPerformanceRecords as $oTourPerformance)
		{
			//Get Revenues for this Performance
			$oTourRevenues = new Revenue();
			$oTourRevenues->nPerformanceID = $oTourPerformance->nPerformanceID;
			//Get all the revenues for this performance
			if($oTourRevenues->getRevenue())
			{
				foreach($oTourRevenues->aRevenueRecords as $oRevenueRecord)
				{
					//If the revenue is a CD sale add the quantity to the CD sale total
					if($oRevenueRecord->nRevenueTypeID == REVENUE_TYPE_CD_SALE)
					{
						$nTotalCDSales += $oRevenueRecord->nProductQty;
					}
					
					//Add the Revenue Record to the end of the appropriate array for revenue type
					$aTourRevenues[$oRevenueRecord->nRevenueTypeID][] = $oRevenueRecord;

					$nTotalRevenueAmount += $oRevenueRecord->nRevenueAmount;						
				}
			}
			else
			{
				$sErrorMessage =  "ERROR RETRIEVING REVENUE INFORMATION: " . $oTourRevenues->sErrorMessage;
			}

		}
	}
	else
	{
		$sErrorMessage =  "ERROR RETRIEVING PERFORMANCE DATA FOR TOUR ID " . $thisTour->nTourID . ": " . $oTourPerformances->sErrorMessage;
	}

	$oTourExpenses = new Expense();
	$oTourExpenses->nTourID = $nThisTourID;
	//Get all the Expense for this tour
	if($oTourExpenses->getExpense())
	{
		foreach($oTourExpenses->aExpenseRecords as $oExpenseRecord)
		{
			//Add the Expense Record to the end of the appropriate array for the tax category
			$aTourExpenses[$oExpenseRecord->nTaxCategoryID][] = $oExpenseRecord;
			$nTotalExpenseAmount += $oExpenseRecord->nExpenseAmount;				
		}
	}
	else
	{
		$sErrorMessage =  "ERROR RETRIEVING EXPENSE INFORMATION: " . $oTourExpenses->sErrorMessage;
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
			Tour Report - <?php echo $thisTour->sTourName;?>
				</div>
			</div>
			<div class="col-xs-12">
				<A HREF='<?php echo ADMIN_DIR . "/TourMaintenance.php?ID=" . $thisTour->nTourID; ?>' class="secondaryLinkButton">Edit Tour</A>
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
										//Get all Revenue Types
										$oRevenueTypes = new RevenueType();
										if ($oRevenueTypes->getRevenueType())
										{
											foreach($oRevenueTypes->aRevenueTypeRecords as $oRevenueType)
											{
												$nRevenueTypeTotal = 0;
												//If an array of revenue objects was built for the revenue type display all revenues
												if(isset($aTourRevenues[$oRevenueType->nRevenueTypeID]))
												{
													echo "<tr>";
													echo "<th colspan=3 align=left>" . $oRevenueType->sRevenueTypeName . "</th>";
													echo "</tr>";
													echo "<tr>";
													echo "<th class='" . RESULT_STYLE_CLASS . "'>Date</th>";
													echo "<th class='" . RESULT_STYLE_CLASS . "'>Description</th>";
													echo "<th class='" . RESULT_STYLE_CLASS . "'>Amount</th>";
													echo "</tr>";
													
													foreach($aTourRevenues[$oRevenueType->nRevenueTypeID] as $oTourRevenue)
													{
														echo "<tr>";
														echo "<td class='" . RESULT_STYLE_CLASS . "'>" . $oTourRevenue->dtPaidDate . "</td>";
														echo "<td class='" . RESULT_STYLE_CLASS . "'>";
														echo "<A HREF='" . ADMIN_DIR . "/RevenueMaintenance.php?ID=" . $oTourRevenue->nRevenueID;
														echo "'>" . $oTourRevenue->sRevenueDescription . "</A></td>";
														echo "<td class='" . RESULT_STYLE_CLASS . "' align=right>$" . $oTourRevenue->nRevenueAmount . "</td>";
														echo "</tr>";
														//$nTotalRevenueAmount += $oTourRevenue->nRevenueAmount;
														$nRevenueTypeTotal += $oTourRevenue->nRevenueAmount;
					
													}
													echo "<tr>";
													echo "<td></td>";
													echo "<td class='" . RESULT_STYLE_CLASS . "'> " . $oRevenueType->sRevenueTypeName . " Subotal </td>";
													echo "<td class='" . RESULT_STYLE_CLASS . "' align=right> $" . number_format((float)$nRevenueTypeTotal, 2, '.', '') . "</td>";
													echo "</tr>";
												}		
												
											}
					
										}
										else
										{
											//ERRROR
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
										//Get all Tax Categories
										$TaxCategories = new TaxCategory();
										if ($TaxCategories->getTaxCategory())
										{
											foreach($TaxCategories->aTaxCategoryRecords as $oTaxCategory)
											{
												$nTaxCategoryTotal = 0;
												//If an array of expense objects was built for the Tax Category display all expenses
												if(isset($aTourExpenses[$oTaxCategory->nTaxCategoryID]))
												{
													echo "<tr>";
													echo "<th colspan=3 align=left>" . $oTaxCategory->sTaxCategoryName . "</th>";
													echo "</tr>";
													echo "<tr>";
													echo "<th class='" . RESULT_STYLE_CLASS . "'>Date</th>";
													echo "<th class='" . RESULT_STYLE_CLASS . "'>Description</th>";
													echo "<th class='" . RESULT_STYLE_CLASS . "'>Amount</th>";
													echo "</tr>";
													
													foreach($aTourExpenses[$oTaxCategory->nTaxCategoryID] as $oTourExpense)
													{
														echo "<tr>";
														echo "<td class='" . RESULT_STYLE_CLASS . "'>" . $oTourExpense->dtExpenseDate . "</td>";
														echo "<td class='" . RESULT_STYLE_CLASS . "'>";
														echo "<A HREF='" . ADMIN_DIR . "/ExpenseMaintenance.php?ID=" . $oTourExpense->nExpenseID;
														echo "'>" . $oTourExpense->sExpenseDescription . "</A></td>";
														echo "<td class='" . RESULT_STYLE_CLASS . "' align=right>$" . $oTourExpense->nExpenseAmount . "</td>";
														echo "</tr>";
														//$nTotalExpenseAmount += $oTourExpense->nExpenseAmount;
														$nTaxCategoryTotal += $oTourExpense->nExpenseAmount;;
					
													}
													echo "<tr>";
													echo "<td></td>";
													echo "<td class='" . RESULT_STYLE_CLASS . "'> " . $oTaxCategory->sTaxCategoryName . " Subotal </td>";
													echo "<td class='" . RESULT_STYLE_CLASS . "' align=right> $" . number_format((float)$nTaxCategoryTotal, 2, '.', '') . "</td>";
													echo "</tr>";
												}		
												
											}
					
										}
										else
										{
											//ERRROR
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
