<?php
/*
*******************************************************************
PerformanceReport.php
This PHP file generates a Colorado Sessions Report 
NOTES
Date        Change
-------------------------------------------------------------
2024-05-09  Created
2026-07-30	Migrated to new Datalayer repositories
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
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");
include_once(DATALAYER_DIR . "/Expense.php");
include_once(DATALAYER_DIR . "/ExpenseRepository.php");
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");

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
<form name="ColoradoSessionsReport" action="ColoradoSessionsReport.php" method="post">

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
		
		$nStartYear = "2021";
		$nEndYear = $nCurrentYear;
	}

	// Set Colorado Sessions Category
	$aCategoryIDs = array();
    $aCategoryIDs[0] = COLORAD_SESSIONS_CAT_ID;

	$aPerformanceData = array();

	//Instantiate needed objects
	$performanceRepo = new \Datalayer\PerformanceRepository();
	$expenseRepo = new \Datalayer\ExpenseRepository();
	$revenueRepo = new \Datalayer\RevenueRepository();
	$form = new Form();

	for($nYear = $nStartYear; $nYear <= $nEndYear; $nYear++)
	{
		$dtStartDate = "{$nYear}-01-01";
		$dtEndDate = "{$nYear}-12-31";
		
		//Initialize Array of Performance Data for Year
		$aPerformanceData[$nYear]['YEAR'] = $nYear;
		$aPerformanceData[$nYear]['TOTAL_REVENUE'] = 0.00;
		$aPerformanceData[$nYear]['CD_QUANTITY'] = 0;
		$aPerformanceData[$nYear]['CD_REVENUE'] = 0;
		$aPerformanceData[$nYear]['PERFORMANCE_REVENUE'] = 0;
		$aPerformanceData[$nYear]['OTHER_REVENUE'] = 0;
		$aPerformanceData[$nYear]['TOTAL_EXPENSE'] = 0;

		// Get Performances
		$aPerformances = $performanceRepo->find([
			'startDate' => $dtStartDate,
			'endDate' => $dtEndDate,
			'coloradoSessions' => true,
		]);
		$aPerformanceData[$nYear]['PERFORMANCE_QUANTITY'] = count($aPerformances);
		
		// Get Revenues
		$aPerformanceRevenues = $revenueRepo->find([
			'performanceRelated' => true,
			'startDate' => $dtStartDate,
			'endDate' => $dtEndDate,
			'categoryIds' => $aCategoryIDs,
		]);
		foreach ($aPerformanceRevenues as $oRevenue)
		{
			$aPerformanceData[$nYear]['TOTAL_REVENUE'] += $oRevenue->amount ?? 0;
			if ($oRevenue->revenueTypeId == REVENUE_TYPE_CD_SALE)
			{
				$aPerformanceData[$nYear]['CD_QUANTITY'] += $oRevenue->productQty ?? 0;
				$aPerformanceData[$nYear]['CD_REVENUE'] += $oRevenue->amount ?? 0;
			}
			elseif ($oRevenue->revenueTypeId == REVENUE_TYPE_PERFORMANCE_FEE)
			{
				$aPerformanceData[$nYear]['PERFORMANCE_REVENUE'] += $oRevenue->amount ?? 0;
			}
			else {
				$aPerformanceData[$nYear]['OTHER_REVENUE'] += $oRevenue->amount ?? 0;
			}
		}

		// Get Expenses
		$aPerformanceExpenses = $expenseRepo->find([
			'startDate' => $dtStartDate,
			'endDate' => $dtEndDate,
			'categoryIds' => $aCategoryIDs,
		]);
		foreach ($aPerformanceExpenses as $oExpense)
		{
			$aPerformanceData[$nYear]['TOTAL_EXPENSE'] += $oExpense->expenseAmount ?? 0;
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
				COLORADO SESSIONS REPORT
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
									<th>Other Revenue</th>
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
									$sBaseQueryParms="?START_DATE={$aPerformanceDataYear['YEAR']}-01-01&END_DATE={$aPerformanceDataYear['YEAR']}-12-31&COLORADO_SESSIONS=TRUE";

									//Calculations
									$nYearProfit = $aPerformanceDataYear['TOTAL_REVENUE'] - $aPerformanceDataYear['TOTAL_EXPENSE'];
									$nYearProfitPerPerformance = $aPerformanceDataYear['PERFORMANCE_QUANTITY'] > 0 ? $nYearProfit/$aPerformanceDataYear['PERFORMANCE_QUANTITY'] : 0; 
									$nYearCDsPerPerformance = $aPerformanceDataYear['PERFORMANCE_QUANTITY'] > 0 ? $aPerformanceDataYear['CD_QUANTITY']/$aPerformanceDataYear['PERFORMANCE_QUANTITY']: 0;

									$nYearProfitPerCD = $aPerformanceDataYear['CD_QUANTITY'] > 0 ? $aPerformanceDataYear['CD_REVENUE']/$aPerformanceDataYear['CD_QUANTITY']: 0;

									
									//Accumulate Totals
									$nTotalRevenue += $aPerformanceDataYear['TOTAL_REVENUE'];
									$nTotalPerformanceRevenue += $aPerformanceDataYear['PERFORMANCE_REVENUE'];
									$nTotalOtherRevenue += $aPerformanceDataYear['OTHER_REVENUE'];
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
									// Other Revenue for Year
									echo "<td >$";
									echo "<A HREF='" . ADMIN_DIR . "/RevenueMaintenance.php{$sBaseQueryParms}'>";
									echo number_format((float)$aPerformanceDataYear['OTHER_REVENUE'], 2, '.', ','). "</td>";
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
								echo "<td >$" . number_format((float)$nTotalOtherRevenue, 2, '.', ','). "</td>";
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
