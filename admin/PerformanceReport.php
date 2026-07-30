<?php
/*
*******************************************************************
PerformanceReport.php
This PHP file generates a Performance Report 
NOTES
Date        Change
-------------------------------------------------------------
2015-10-19  Created
2017-04-15	Made Responsive
2021-08-30	Updated for PHP 8
2024-05-09	Added Colorado Sessions Flag to Report
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
	
//include Performance Class		
include_once (CLASS_DIR . "/class_Performance.php");

//include Expense Class		
include_once (CLASS_DIR . "/class_Expense.php");

//include Revenue Class		
include_once (CLASS_DIR . "/class_Revenue.php");

//include Revenue_Type Class		
include_once (CLASS_DIR . "/class_RevenueType.php");

//include TaxCategory Class		
include_once (CLASS_DIR . "/class_TaxCategory.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once (CLASS_DIR . "/tc_calendar.php");

$nCurrentYear = date('Y');

$sActiveMenuItem = REPORTS_ACTIVE;	
$sPageName = "Performance Report"
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>
<div class="container-fluid">	
<form name="PerformanceReport" action="PerformanceReport.php" method="post">

<?php
	if (isset($_REQUEST['YEAR']) && $_REQUEST['YEAR'] != "")
	{
		//If a Year is passed, go ahead and run the report for that Year
		$_POST['selYear'] = $_REQUEST['YEAR'];
		$_POST['btnGenerate'] = "GenerateReport";
		$nStartYear = $_POST['selYear'];
		$nEndYear = $_POST['selYear'];
	}
	elseif (isset($_POST['selYear']) && $_POST['selYear'] != 0)
	{
		//If a Year was passed, go ahead and run the report for that Year
		$_POST['btnGenerate'] = "GenerateReport";
		$nStartYear = $_POST['selYear'];
		$nEndYear = $_POST['selYear'];
		$_POST['btnGenerate'] = "GenerateReport";
	}
	else
	{
		
		$nStartYear = START_YEAR;
		$nEndYear = $nCurrentYear;
	}

	// Set Colorado Sessions Flag
	$bColoradoSessions = $_POST['chkColoradoSessions'];
	$aCategoryIDs = array();
	if($bColoradoSessions == TRUE){
		$aCategoryIDs[0] = 22;
	}

	$aPerformanceData = array();

	//Instantiate needed objects
	$form = new Form();

	for($nYear = $nStartYear; $nYear <= $nEndYear; $nYear++)
	{
		$dtStartDate = "{$nYear}-01-01";
		$dtEndDate = "{$nYear}-12-31";
		
		//Initialize Array of Performance Data for Year
		$aPerformanceData[$nYear]['YEAR'] = $nYear;
		$aPerformanceData[$nYear]['TOTAL_REVENUE'] = 0.00;		
		$aPerformanceData[$nYear]['CD_QUANTITY'] = 0;

		// Get Performances
		$oPerformances = new Performance();
		$oPerformances->dtStartDate = $dtStartDate;
		$oPerformances->dtEndDate = $dtEndDate;
		if ($bColoradoSessions == TRUE) { $oPerformances->bColoradoSessions = TRUE; }
		//$oPerformances->bColoradoSessions = $bColoradoSessions;
	 	$aPerformanceData[$nYear]['PERFORMANCE_QUANTITY'] = $oPerformances->getNumberOfPerformances();
		
		// Get Revenues
		$oPerformanceRevenues = new Revenue();
		$oPerformanceRevenues->bPerformanceRelated = TRUE;
		$oPerformanceRevenues->dtStartDate = $dtStartDate;
		$oPerformanceRevenues->dtEndDate = $dtEndDate;
		$oPerformanceRevenues->aRevenueCategoryIDs = $aCategoryIDs;
		if ($oPerformanceRevenues->getRevenue())
		{
			$aPerformanceData[$nYear]['YEAR'] = $nYear;

			foreach($oPerformanceRevenues->aRevenueRecords as $oRevenue)
			{
				$aPerformanceData[$nYear]['TOTAL_REVENUE'] += $oRevenue->nRevenueAmount;
				if ($oRevenue->nRevenueTypeID == REVENUE_TYPE_CD_SALE)
				{
					$aPerformanceData[$nYear]['CD_QUANTITY'] += $oRevenue->nProductQty;
					$aPerformanceData[$nYear]['CD_REVENUE'] += $oRevenue->nRevenueAmount;
				}
				elseif ($oRevenue->nRevenueTypeID == REVENUE_TYPE_PERFORMANCE_FEE)
				{
					$aPerformanceData[$nYear]['PERFORMANCE_REVENUE'] += $oRevenue->nRevenueAmount;
				}
				
			}
		}
		else
		{
			//ERROR RETRIEVING REVENUES
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->sMessage = "FAILED TO GET REVENUES: " . $oPerformanceRevenues->sErrorMessage;
		}  

		// Get Expenses
		$oPerformanceExpenses = new Expense();
		$oPerformanceExpenses->bPerformanceRelated = TRUE;
		$oPerformanceExpenses->dtStartDate = $dtStartDate;
		$oPerformanceExpenses->dtEndDate = $dtEndDate;
		$oPerformanceExpenses->aExpenseCategoryIDs = $aCategoryIDs;
		if ($oPerformanceExpenses->getExpense())
		{
			foreach($oPerformanceExpenses->aExpenseRecords as $oExpense)
			{
				$aPerformanceData[$nYear]['TOTAL_EXPENSE'] += $oExpense->nExpenseAmount;
			}
		}
		else
		{
			//ERROR RETRIEVING EXPENSES
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->sMessage = "FAILED TO GET EXPENSES: " . $oPerformanceExpenses->sErrorMessage;
		}  
		
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
				PERFORMANCE REPORT
				<?php
				if(isset($_POST['selYear']) && $_POST['selYear'] != "0")
				{
					echo " ({$_POST['selYear']})";
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
			$bColoradoSessionsFilter = TRUE;
			include (ADMIN_INCLUDE_DIR . "/ReportFilters.php");
			?>
		</div>
	</div>
	<div class="row"> 		
		<div class="col-xs-12 FieldGroup"> 
			<div class="FinancialData">
				<div class="row">		
					<div class="col-xs-12 FieldGroupTitle"> 
						Yearly Data
					</div>
				</div>	
				<div class="row">		
					<div class="col-xs-12 FieldGroup">
						<div class="table-responsive">
							<table class="table table-striped"> 
								<tr>
									<th>Year</th>
									<th>Revenue</th>
									<th>Perf. Revenue</th>
									<th>CD Revenue</th>
									<th>Expense</th>
									<th>Profit</th>
									<th>CDs Sold</th>
									<th>Performances</th>
									<th>Profit/Performance</th>
									<th>CDs/Perf.</th>
									<th>Profit/CD</th>
								</tr>
								<?php
								
								foreach($aPerformanceData as $aPerformanceDataYear)
								{
									$nYearProfitPerPerformance = 0;
									$nYearCDsPerPerformance = 0;

									//Set Base Query string parms for links to match this year
									$sBaseQueryParms="?PERF_RELATED=TRUE&START_DATE={$aPerformanceDataYear['YEAR']}-01-01&END_DATE={$aPerformanceDataYear['YEAR']}-12-31";

									//Calculations
									$nYearProfit = $aPerformanceDataYear['TOTAL_REVENUE'] - $aPerformanceDataYear['TOTAL_EXPENSE'];
									$nYearProfitPerPerformance = $aPerformanceDataYear['PERFORMANCE_QUANTITY'] > 0 ? $nYearProfit/$aPerformanceDataYear['PERFORMANCE_QUANTITY'] : 0; 
									$nYearCDsPerPerformance = $aPerformanceDataYear['PERFORMANCE_QUANTITY'] > 0 ? $aPerformanceDataYear['CD_QUANTITY']/$aPerformanceDataYear['PERFORMANCE_QUANTITY']: 0;

									$nYearProfitPerCD = $aPerformanceDataYear['CD_QUANTITY'] > 0 ? $aPerformanceDataYear['CD_REVENUE']/$aPerformanceDataYear['CD_QUANTITY']: 0;

									
									//Accumulate Totas
									$nTotalRevenue += $aPerformanceDataYear['TOTAL_REVENUE'];
									$nTotalPerformanceRevenue += $aPerformanceDataYear['PERFORMANCE_REVENUE'];
									$nTotalCDRevenue += $aPerformanceDataYear['CD_REVENUE'];
									$nTotalExpense += $aPerformanceDataYear['TOTAL_EXPENSE'];
									$nTotalProfit = $nTotalRevenue - $nTotalExpense;
									$nTotalCDQuantity += $aPerformanceDataYear['CD_QUANTITY'];
									$nTotalPerformanceQuantity += $aPerformanceDataYear['PERFORMANCE_QUANTITY'];
									
									echo "<tr>";
									echo "<td width=75px>{$aPerformanceDataYear['YEAR']}</td>";
									//Total Revenue for Year
									echo "<td >$";
									echo "<A HREF='" . ADMIN_DIR . "/RevenueMaintenance.php{$sBaseQueryParms}'>";
									echo number_format((float)$aPerformanceDataYear['TOTAL_REVENUE'], 2, '.', ','). "</td>";
									//Performance Revenue for Year
									echo "<td >$";
									echo "<A HREF='" . ADMIN_DIR . "/RevenueMaintenance.php{$sBaseQueryParms}&REVENUE_TYPE_ID=" . REVENUE_TYPE_PERFORMANCE_FEE . "'>";
									echo number_format((float)$aPerformanceDataYear['PERFORMANCE_REVENUE'], 2, '.', ','). "</td>";
									//CD Sales Revenue for Year
									echo "<td >$";
									echo "<A HREF='" . ADMIN_DIR . "/RevenueMaintenance.php{$sBaseQueryParms}&REVENUE_TYPE_ID=" . REVENUE_TYPE_CD_SALE . "'>";
									echo number_format((float)$aPerformanceDataYear['CD_REVENUE'], 2, '.', ','). "</td>";
									//Total Expense for Year
									echo "<td >$";
									echo "<A HREF='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sBaseQueryParms}'>";
									echo number_format((float)$aPerformanceDataYear['TOTAL_EXPENSE'], 2, '.', ','). "</td>";
									echo "<td >$" . number_format((float)$nYearProfit, 2, '.', ','). "</td>";
									echo "<td >{$aPerformanceDataYear['CD_QUANTITY']}</td>";
									echo "<td >{$aPerformanceDataYear['PERFORMANCE_QUANTITY']}</td>";
									echo "<td >$" . number_format((float)$nYearProfitPerPerformance, 2, '.', ','). "</td>";
									echo "<td >" . number_format((float)$nYearCDsPerPerformance, 2, '.', ','). "</td>";
									echo "<td >$" . number_format((float)$nYearProfitPerCD, 2, '.', ','). "</td>";
									echo "</tr>";

								}
								
								$nTotalProfitPerPerformance = $nTotalPerformanceQuantity > 0 ? number_format((float)$nTotalProfit/$nTotalPerformanceQuantity, 2, '.', '')  : 0;
								$nTotalCDsPerPerformance = $nTotalPerformanceQuantity > 0 ? number_format((float)$nTotalCDQuantity/$nTotalPerformanceQuantity, 2, '.', '') : 0;
								$nTotalProfitPerCD = $nTotalCDQuantity > 0 ? number_format((float)$nTotalCDRevenue/$nTotalCDQuantity, 2, '.', '') : 0;
								
								echo "<tr>";
								echo "<td >TOTAL</td>";
								echo "<td >$" . number_format((float)$nTotalRevenue, 2, '.', ','). "</td>";
								echo "<td >$" . number_format((float)$nTotalPerformanceRevenue, 2, '.', ','). "</td>";
								echo "<td >$" . number_format((float)$nTotalCDRevenue, 2, '.', ','). "</td>";
								echo "<td >$" . number_format((float)$nTotalExpense, 2, '.', ','). "</td>";
								echo "<td >$" . number_format((float)$nTotalProfit, 2, '.', ','). "</td>";
								echo "<td >{$nTotalCDQuantity}</td>";
								echo "<td >{$nTotalPerformanceQuantity}</td>";
								echo "<td >$" . number_format((float)$nTotalProfitPerPerformance, 2, '.', ','). "</td>";
								echo "<td >" . number_format((float)$nTotalCDsPerPerformance, 2, '.', ','). "</td>";
								echo "<td >$" . number_format((float)$nTotalProfitPerCD, 2, '.', ','). "</td>";
								echo "</tr>";
								
								?>
								
							</table>
					</div>
			  </div>
			</div>
		</div>
	</div>
</form>
</div>

</body>
<script type="text/javascript">
<!--
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

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
