<?php
/*
*******************************************************************
Product.php
This PHP file generates a Product Report 
NOTES
Date        Change
-------------------------------------------------------------
2015-10-27  Removed Profit per product reporting (made no sense)<br />
2017-04-15	Made Responsive
2020-03-14	Added logic to for revenues and expenses with no product
2021-08-30	Updated for PHP 8
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

 $sActiveMenuItem = REPORTS_ACTIVE;	
 $sPageName = "Product Report";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>
	<?php
	
	//include new datalayer
	include_once(DATALAYER_DIR . "/Connection.php");
	include_once(DATALAYER_DIR . "/Product.php");
	include_once(DATALAYER_DIR . "/ProductRepository.php");
	include_once(DATALAYER_DIR . "/Expense.php");
	include_once(DATALAYER_DIR . "/ExpenseRepository.php");
	include_once(DATALAYER_DIR . "/Revenue.php");
	include_once(DATALAYER_DIR . "/RevenueRepository.php");
	include_once(DATALAYER_DIR . "/RevenueType.php");
	include_once(DATALAYER_DIR . "/RevenueTypeRepository.php");
	include_once(DATALAYER_DIR . "/TaxCategory.php");
	include_once(DATALAYER_DIR . "/TaxCategoryRepository.php");
	include_once(DATALAYER_DIR . "/Category.php");
	include_once(DATALAYER_DIR . "/CategoryRepository.php");

	//include Form Class
	include (CLASS_DIR . "/class_Form.php");

	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");

?>
<div class="container-fluid">
<form name="ProductReport" action="ProductReport.php" method="post">
<?php
	//If a Product ID is passed, pre-select that Product
	if (isset($_REQUEST['PRODUCT_ID']) && $_REQUEST['PRODUCT_ID'] != "")
	{
		$_POST['selProduct'] = $_REQUEST['PRODUCT_ID'];
		$_POST['btnGenerate'] = "GenerateReport";
	}
	//If a Year is passed, pre-select that Year
	if (isset($_REQUEST['YEAR']) && $_REQUEST['YEAR'] != "")
	{
		$_POST['selYear'] = $_REQUEST['YEAR'];
		$_POST['btnGenerate'] = "GenerateReport";
	}
	//If a Category ID is passed, pre-select that Category
	if (isset($_REQUEST['CATEGORY_ID']) && $_REQUEST['CATEGORY_ID'] != "")
	{
		$_POST['selCategory'] = $_REQUEST['CATEGORY_ID'];
		$_POST['btnGenerate'] = "GenerateReport";
	}

	//Instantiate needed objects
	$productRepo = new \Datalayer\ProductRepository();
	$categoryRepo = new \Datalayer\CategoryRepository();
	$revenueRepo = new \Datalayer\RevenueRepository();
	$expenseRepo = new \Datalayer\ExpenseRepository();
	$revenueTypeRepo = new \Datalayer\RevenueTypeRepository();
	$taxCategoryRepo = new \Datalayer\TaxCategoryRepository();
	$form = new Form();
	$aProductData = array();
	$oThisProduct = null;
	$oThisCategory = null;
	$aProducts = array();
	$oNoProduct = new \Datalayer\Product();
	$oNoProduct->id = 0;
	$oNoProduct->name = "NO PRODUCT";
	
	//If a Product is selected, retrieve that product's information
	if (($_POST['selProduct'] ?? 0) > 0)
	{
		$oThisProduct = $productRepo->findById((int) $_POST['selProduct']);
		if (!$oThisProduct)
		{
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->sMessage = "FAILED TO GET PRODUCT";
		}
	}

	//If a category is selected, retrieve that category's information
	if (($_POST['selCategory'] ?? 0) > 0)
	{
		$oThisCategory = $categoryRepo->findById((int) $_POST['selCategory']);
		if (!$oThisCategory)
		{
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->sMessage = "FAILED TO GET CATEGORY";
		}
	}

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

	//If multiple Years are being reported, retrieve yearly data
	if ($nEndYear > $nStartYear)
	{
		for ($nYear = $nStartYear; $nYear <= $nEndYear; $nYear++)
		{
			$aRevenueCriteria = [];
			$aExpenseCriteria = ['expenseYear' => $nYear];

			if (($_POST['selProduct'] ?? 0) > 0)
			{
				$aRevenueCriteria['productId'] = (int) $_POST['selProduct'];
				$aExpenseCriteria['productId'] = (int) $_POST['selProduct'];
			}

			if (($_POST['selCategory'] ?? 0) > 0)
			{
				$aRevenueCriteria['categoryIds'] = [(int) $_POST['selCategory']];
				$aExpenseCriteria['categoryIds'] = [(int) $_POST['selCategory']];
			}

			$aRevenueCriteria['paidYear'] = $nYear;

			$aYearlyData[$nYear]['Revenue'] = 0.0;
			foreach ($revenueRepo->find($aRevenueCriteria) as $oRevenue) {
				$aYearlyData[$nYear]['Revenue'] += $oRevenue->amount ?? 0;
			}

			$aYearlyData[$nYear]['Quantity'] = 0;
			foreach ($revenueRepo->find($aRevenueCriteria) as $oRevenue) {
				$aYearlyData[$nYear]['Quantity'] += $oRevenue->productQty ?? 0;
			}

			$aPerformanceRevenueCriteria = $aRevenueCriteria;
			$aPerformanceRevenueCriteria['performanceRelated'] = true;
			$aYearlyData[$nYear]['PerformanceQuantity'] = 0;
			foreach ($revenueRepo->find($aPerformanceRevenueCriteria) as $oRevenue) {
				$aYearlyData[$nYear]['PerformanceQuantity'] += $oRevenue->productQty ?? 0;
			}

			$aYearlyData[$nYear]['Expense'] = 0.0;
			foreach ($expenseRepo->find($aExpenseCriteria) as $oExpense) {
				$aYearlyData[$nYear]['Expense'] += $oExpense->expenseAmount ?? 0;
			}
			
			$aYearlyData[$nYear]['Profit'] = $aYearlyData[$nYear]['Revenue'] - $aYearlyData[$nYear]['Expense'];
		}
	}

	//If multiple products are being reported, retrieve data for each product
	if (($_POST['selProduct'] ?? 0) == 0)
	{
		$aProducts = $productRepo->find();
		array_unshift($aProducts, $oNoProduct);
		
		foreach ($aProducts as $oProduct)
		{
			$aRevenueCriteria = [];
			$aExpenseCriteria = [];

			if ($oProduct->id == 0) {
				$aRevenueCriteria['productId'] = 0;
				$aExpenseCriteria['productId'] = 0;
			} else {
				$aRevenueCriteria['productId'] = $oProduct->id;
				$aExpenseCriteria['productId'] = $oProduct->id;
			}

			if (($_POST['selCategory'] ?? 0) > 0)
			{
				$aRevenueCriteria['categoryIds'] = [(int) $_POST['selCategory']];
				$aExpenseCriteria['categoryIds'] = [(int) $_POST['selCategory']];
			}

			if (($_POST['selYear'] ?? 0) > 0)
			{
				$aRevenueCriteria['paidYear'] = (int) $_POST['selYear'];
				$aExpenseCriteria['expenseYear'] = (int) $_POST['selYear'];
			}

			$aProductData[$oProduct->id]['ProductName'] = $oProduct->name;

			$aProductData[$oProduct->id]['Revenue'] = 0.0;
			foreach ($revenueRepo->find($aRevenueCriteria) as $oRevenue) {
				$aProductData[$oProduct->id]['Revenue'] += $oRevenue->amount ?? 0;
			}

			$aProductData[$oProduct->id]['Quantity'] = 0;
			foreach ($revenueRepo->find($aRevenueCriteria) as $oRevenue) {
				$aProductData[$oProduct->id]['Quantity'] += $oRevenue->productQty ?? 0;
			}

			$aPerformanceRevenueCriteria = $aRevenueCriteria;
			$aPerformanceRevenueCriteria['performanceRelated'] = true;
			$aProductData[$oProduct->id]['PerformanceQuantity'] = 0;
			foreach ($revenueRepo->find($aPerformanceRevenueCriteria) as $oRevenue) {
				$aProductData[$oProduct->id]['PerformanceQuantity'] += $oRevenue->productQty ?? 0;
			}

			$aProductData[$oProduct->id]['Expense'] = 0.0;
			foreach ($expenseRepo->find($aExpenseCriteria) as $oExpense) {
				$aProductData[$oProduct->id]['Expense'] += $oExpense->expenseAmount ?? 0;
			}
			
			$aProductData[$oProduct->id]['Profit'] = $aProductData[$oProduct->id]['Revenue'] - $aProductData[$oProduct->id]['Expense'];
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
					PRODUCT REPORT:  
				<?php
				if(($_POST['selProduct'] ?? 0) > 0 && $oThisProduct)
				{
					echo " {$oThisProduct->name}";
				}
				if(($_POST['selYear'] ?? 0) > 0)
				{
					echo "  {$_POST['selYear']}";
				}
				if(($_POST['selCategory'] ?? 0) > 0 && $oThisCategory)
				{
					echo "  {$oThisCategory->name}";
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
			$bProductFilter = TRUE;
			$bCategoryFilter = TRUE;
			include (ADMIN_INCLUDE_DIR . "/ReportFilters.php");
			?>
		</div>
	</div>
<?php
if (isset($_POST["btnGenerate"])) 
{
?>		
	<div class="row">		
		<div class="col-xs-12 col-sm-6 FieldGroup">
			<div class="FieldGroupTitle"> 
				REVENUE
			</div>
			<div class="table-responsive">	
				<table class="table table-striped"> 				
				<?php
					$nTotalRevenueAmount = 0;
					foreach ($revenueTypeRepo->find() as $oRevenueType)
					{
						$sQueryString = "{$sBaseQueryString}&REVENUE_TYPE_ID={$oRevenueType->id}";

						$aRevenueCriteria = ['revenueTypeId' => $oRevenueType->id];
						if (($_POST['selProduct'] ?? null) !== null && $_POST['selProduct'] !== '') {
							$aRevenueCriteria['productId'] = (int) $_POST['selProduct'];
						}
						if (($_POST['selYear'] ?? 0) > 0) {
							$aRevenueCriteria['paidYear'] = (int) $_POST['selYear'];
						}
						if (($_POST['selCategory'] ?? 0) > 0) {
							$aRevenueCriteria['categoryIds'] = [(int) $_POST['selCategory']];
						}

						$nTotalRevenueTypeAmount = 0.0;
						foreach ($revenueRepo->find($aRevenueCriteria) as $oRevenue) {
							$nTotalRevenueTypeAmount += $oRevenue->amount ?? 0;
						}

						if(!empty($nTotalRevenueTypeAmount) && $nTotalRevenueTypeAmount > 0)
						{
							$nTotalRevenueAmount += $nTotalRevenueTypeAmount;

							echo "<tr>";
							echo "<td width='200px'>{$oRevenueType->name}</td>";
							echo "<td>$";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
							echo number_format((float)$nTotalRevenueTypeAmount, 2, '.', ',');
							echo "</a>";
							echo"</td>";
							echo "</tr>";
						}
					}

					echo "<tr><td colspan=2><HR></td></tr>";
					echo "<tr>";
					echo "<td width='200px'>Total</td>";
					echo "<td width='200px'> $";
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
					echo "</tr>";
				?>
				</table>			
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 FieldGroup">
			<div class="FieldGroupTitle"> 
				EXPENSES
			</div>
			<div class="table-responsive">	
			<?php
				echo "<table class='table table-striped'>";
				$nExpenseTotal = 0;
				foreach ($taxCategoryRepo->find() as $oTaxCategory)
				{
					$sQueryString = "{$sBaseQueryString}&TAX_CATEGORY_ID={$oTaxCategory->id}";

					$aExpenseCriteria = ['taxCategoryId' => $oTaxCategory->id];
					if (($_POST['selProduct'] ?? null) !== null && $_POST['selProduct'] !== '') {
						$aExpenseCriteria['productId'] = (int) $_POST['selProduct'];
					}
					if (!empty($_POST['selYear'])) {
						$aExpenseCriteria['expenseYear'] = (int) $_POST['selYear'];
					}
					if (!empty($_POST['selCategory'])) {
						$aExpenseCriteria['categoryIds'] = [(int) $_POST['selCategory']];
					}

					$nProductExpenseTotal = 0.0;
					foreach ($expenseRepo->find($aExpenseCriteria) as $oProductExpense) {
						$nProductExpenseTotal += $oProductExpense->expenseAmount ?? 0;
						$nExpenseTotal += $oProductExpense->expenseAmount ?? 0;
					}

					if($nProductExpenseTotal > 0)
					{
						echo "<tr>";
						echo "<td width='200px'>{$oTaxCategory->name}</td>";
						echo "<td> $";
						echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sQueryString}'>";
						echo number_format((float)$nProductExpenseTotal, 2, '.', ',');
						echo "</a>";
						echo "</td>";
						echo "</tr>";
					}
				}
				echo "<tr><td colspan=2><HR></td></tr>";
				echo "<tr>";
				echo "<td width='200px'>Total</td>";
				echo "<td width='200px'> $";
				if ($nExpenseTotal > 0)
				{
					echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sBaseQueryString}'>";
					echo number_format((float)$nExpenseTotal, 2, '.', ',') . "</a>";
				}
				else
				{
					echo number_format((float)$nExpenseTotal, 2, '.', ',');
				}
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->sMessage = "SUCCESS";
			?>
			</div>
		</div>							
	</div>
<?php
}
//Only display the data for each product if no Product is selected
if (($_POST['selProduct'] ?? 0) == 0)
{
?>
	<div class="row">		
		<div class="col-xs-12 FieldGroup">
			<div class="FieldGroupTitle"> 
				PRODUCT DATA
			</div>
			<div class="table-responsive">	
				<table class="table table-striped"> 
					<tr>
						<th></th>
						<th>Quantity</th>	
						<th>Perf. Qty</th>	
						<th>Revenue</th>	
						<th>Expense</th>	
						<th>Profit</th>	
					</tr>
					<?php
						$aProductTotals = array();

						foreach ($aProducts as $oProduct)
						{
						
							$sQueryString = "{$sBaseQueryString}&PRODUCT_ID={$oProduct->id}";

							//Only display a yearly row if there is data to report 
							if(($aProductData[$oProduct->id]['Revenue'] ?? 0) > 0 || ($aProductData[$oProduct->id]['Expense'] ?? 0) > 0 || ($aProductData[$oProduct->id]['Quantity'] ?? 0) > 0)
							{
							
								echo "<tr>";
								echo "<td align='left'><small>{$oProduct->name}</small></td>";
								// Product Sales Quantity
								echo "<td>";
								echo number_format((float)$aProductData[$oProduct->id]['Quantity'],0,"",",");	
								$aProductTotals['Quantity'] += $aProductData[$oProduct->id]['Quantity'];
								echo "</td>";
								// Product Performance Sales Quantity
								echo "<td>";
								echo number_format((float)$aProductData[$oProduct->id]['PerformanceQuantity'],0,"",",");	
								$aProductTotals['PerformanceQuantity'] += $aProductData[$oProduct->id]['PerformanceQuantity'];
								echo "</td>";
								// Revenue										
								echo "<td>";
								if($aProductData[$oProduct->id]['Revenue'] > 0)
								{
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
									echo "$" . number_format((float)$aProductData[$oProduct->id]['Revenue'], 2, '.', ',');
									echo "</a>";	
									$aProductTotals['Revenue'] += $aProductData[$oProduct->id]['Revenue'];
								}
								else
								{
									echo "$" . number_format((float)$aProductData[$oProduct->id]['Revenue'], 2, '.', ',');
								}
								echo "</td>";
								// Expense
								echo "<td>";
								if($aProductData[$oProduct->id]['Expense'] > 0)
								{
									echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sQueryString}'>";
									echo "$" . number_format((float)$aProductData[$oProduct->id]['Expense'], 2, '.', ',');
									echo "</a>";	
									$aProductTotals['Expense'] += $aProductData[$oProduct->id]['Expense'];
								}
								else
								{
									echo "$" . number_format((float)$aProductData[$oProduct->id]['Expense'], 2, '.', ',');
								}
								echo "</td>";
								// Profit
								if(intval($aProductData[$oProduct->id]['Profit']) < 0){
									echo "<td style=\"color: red;\">";
								}
								else{
									echo "<td>";
								}
								echo "$" . number_format((float)$aProductData[$oProduct->id]['Profit'], 2, '.', ',');	
								echo "</td>";
								echo "</tr>";
							}		
						}

						$aProductTotals['Profit'] = $aProductTotals['Revenue'] - $aProductTotals['Expense'];
						if($aProductTotals['Quantity'] > 0)
						{
							$aProductTotals['ProductProfit'] = $aProductTotals['Profit'] / $aProductTotals['Quantity'];
						}
						else
						{
							$aProductTotals['ProductProfit'] = 0.00;
						}
						echo "<tr><td colspan=10><hr></td></tr>";
						echo "<tr>";
						echo "<th>Total</th>";
						// Total Product Sales Quantity
						echo "<td>";
						echo number_format((float)$aProductTotals['Quantity'],0,"",",");	
						echo "</td>";
						// Total Product Performance Sales Quantity
						echo "<td>";
						echo number_format((float)$aProductTotals['PerformanceQuantity'],0,"",",");	
						echo "</td>";
						// Total Revenue
						echo "<td>";
						echo "$" . number_format((float)$aProductTotals['Revenue'], 2, '.', ',');	
						echo "</td>";
						// Total Expense
						echo "<td>";
						echo "$" . number_format((float)$aProductTotals['Expense'], 2, '.', ',');	
						echo "</td>";
						// Total Profit
						if((float)$aProductTotals['Profit'] < 0){
							echo "<td style=\"color: red\">";
						}
						else{
							echo "<td>";
						}
						echo "$" . number_format((float)$aProductTotals['Profit'], 2, '.', ',');	
						echo "</td>";
						echo "</tr>";		
					?>
				</table>
			</div>
		</div>							
	</div>
<?php
}
//Only display the yearly section if there is more than one year being reported
if ($nEndYear > $nStartYear)
{
?>
	<div class="row">		
		<div class="col-xs-12 FieldGroup">
			<div class="FieldGroupTitle"> 
				YEARLY SUMMARY
			</div>
			<div class="table-responsive">	
				<table class="table table-striped"> 
					<tr>
						<th></th>
						<th>Quantity</th>	
						<th>Perf. Qty</th>	
						<th>Revenue</th>	
						<th>Expense</th>	
						<th>Yearly Profit</th>	
						<th>Total Profit</th>
					</tr>
					<?php
						$aYearlyTotals = array();

						for ($nYear = $nStartYear; $nYear <= $nEndYear; $nYear++)
						{
						
							//Only display a yearly row if there is data to report 
							if($aYearlyData[$nYear]['Revenue'] > 0 || $aYearlyData[$nYear]['Expense'] > 0 ||$aYearlyData[$nYear]['Quantity'] > 0)
							{
							
								echo "<tr>";
								echo "<th>{$nYear}</th>";
								// Yearly Product Sales Quantity
								echo "<td>";
								echo number_format((float)$aYearlyData[$nYear]['Quantity'],0,"",",");	
								$aYearlyTotals['Quantity'] += $aYearlyData[$nYear]['Quantity'];
								echo "</td>";
								// Yearly Product Performance Sales Quantity
								echo "<td>";
								echo number_format((float)$aYearlyData[$nYear]['PerformanceQuantity'],0,"",",");	
								$aYearlyTotals['PerformanceQuantity'] += $aYearlyData[$nYear]['PerformanceQuantity'];
								echo "</td>";
								// Yearly Revenue										
								echo "<td>";
								if($aYearlyData[$nYear]['Revenue'] > 0)
								{
									$sQueryString = "{$sBaseQueryString}&YEAR={$nYear}";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
									echo "$" . number_format((float)$aYearlyData[$nYear]['Revenue'], 2, '.', ',');
									echo "</a>";	
									$aYearlyTotals['Revenue'] += $aYearlyData[$nYear]['Revenue'];
								}
								else
								{
									echo "$" . number_format((float)$aYearlyData[$nYear]['Revenue'], 2, '.', ',');
								}
								echo "</td>";
								// Yearly Expense
								echo "<td>";
								if($aYearlyData[$nYear]['Expense'] > 0)
								{
									$sQueryString = "{$sBaseQueryString}&YEAR={$nYear}";
									echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sQueryString}'>";
									echo "$" . number_format((float)$aYearlyData[$nYear]['Expense'], 2, '.', ',');
									echo "</a>";	
									$aYearlyTotals['Expense'] += $aYearlyData[$nYear]['Expense'];
								}
								else
								{
									echo "$" . number_format((float)$aYearlyData[$nYear]['Expemnse'], 2, '.', ',');
								}
								echo "</td>";
								// Yearly Profit
								echo "<td>";
								echo "$" . number_format((float)$aYearlyData[$nYear]['Profit'], 2, '.', ',');	
								echo "</td>";
								// Total Profit to date
								echo "<td>";
								echo "$" . number_format((float)$aYearlyTotals['Revenue']-$aYearlyTotals['Expense'], 2, '.', ',');	
								echo "</td>";
								echo "</tr>";
							}		
						}

						$aYearlyTotals['Profit'] = $aYearlyTotals['Revenue'] - $aYearlyTotals['Expense'];
						if($aYearlyTotals['Quantity'] > 0)
						{
							$aYearlyTotals['ProductProfit'] = $aYearlyTotals['Profit'] / $aYearlyTotals['Quantity'];
						}
						else
						{
							$aYearlyTotals['ProductProfit'] = 0.00;
						}
						echo "<tr><td colspan=10><hr></td></tr>";
						echo "<tr>";
						echo "<th>Total</th>";
						// Total Product Sales Quantity
						echo "<td>";
						echo number_format((float)$aYearlyTotals['Quantity'],0,"",",");	
						echo "</td>";
						// Total Product Performance Sales Quantity
						echo "<td>";
						echo number_format((float)$aYearlyTotals['PerformanceQuantity'],0,"",",");	
						echo "</td>";
						// Total Revenue
						echo "<td>";
						echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sBaseQueryString}'>";
						echo "$" . number_format((float)$aYearlyTotals['Revenue'], 2, '.', ',');	
						echo "</a>";
						echo "</td>";
						// Total Expense
						echo "<td>";
						echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sBaseQueryString}'>";
						echo "$" . number_format((float)$aYearlyTotals['Expense'], 2, '.', ',');	
						echo "</a>";
						echo "</td>";
						// Total Profit
						echo "<td>";
						echo "$" . number_format((float)$aYearlyTotals['Profit'], 2, '.', ',');	
						echo "</td>";
						echo "<td>";
						echo "</td>";
						echo "</tr>";		
					?>
				</table>
			</div>
		</div>
	</div>	
<?php
}
?>
</form>
</div>		  
<body>
<script type="text/javascript">
<!--
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

	//If no Dates are chosen, default then to current year
    if ( document.ProductReport.StartDate.value == "" || document.ProductReport.StartDate.value == "0000-00-00" || document.ProductReport.EndDate.value == "" || document.ProductReport.EndDate.value == "0000-00-00" )
    {
		sYear = new Date().getFullYear();
		document.ProductReport.StartDate.value = sYear + "-01-01";
		document.ProductReport.EndDate.value = sYear + "-12-31";
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
