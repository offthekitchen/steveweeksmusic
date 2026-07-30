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
	
	//include Product Class		
 	include_once (CLASS_DIR . "/class_Product.php");

	//include Expense Class		
 	include_once (CLASS_DIR . "/class_Expense.php");

	//include Revenue Class		
 	include_once (CLASS_DIR . "/class_Revenue.php");

	//include Revenue_Type Class		
 	include_once (CLASS_DIR . "/class_RevenueType.php");

	//include TaxCategory Class		
 	include_once (CLASS_DIR . "/class_TaxCategory.php");

	//include Category Class		
 	include_once (CLASS_DIR . "/class_Category.php");

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
	$form = new Form();
	$aProductData = array();
	$oThisProduct = new Product();
	$oThisCategory = new Category();
	
	//If a Product is selected, retrieve that product's information
	if ($_POST['selProduct'] > 0)
	{
		$oThisProduct->nProductID = $_POST['selProduct'];
		if(!$oThisProduct->getProduct())
		{
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->sMessage = "FAILED TO GET PRODUCT: {$oThisProduct->sErrorMessage}";
		}
	}

	//If a category is selected, retrieve that product's information
	if ($_POST['selCategory'] > 0)
	{
		$oThisCategory->nCategoryID = $_POST['selCategory'];
		if(!$oThisCategory->getCategory())
		{
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->sMessage = "FAILED TO GET CATEGORY: {$oThisCategory->sErrorMessage}";
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
			$oProductRevenues = new Revenue();
			$oProductExpenses = new Expense();
	
			if ($_POST['selProduct'] > 0)
			{
				$oProductRevenues->nProductID = $_POST['selProduct'];
				$oProductExpenses->nProductID = $_POST['selProduct'];
			}
	
			//If a specific Category is chosen, limit revenues and expenses to that category
			if ($_POST['selCategory'] > 0)
			{
				$oProductRevenues->aRevenueCategoryIDs[0] = $_POST['selCategory'];
				$oProductExpenses->aExpenseCategoryIDs[0] = $_POST['selCategory'];
			}
	
			$oProductRevenues->nPaidYear = $nYear;
			$oProductExpenses->nExpenseYear = $nYear;
			
			//Retrieve Total Revenue
			$aYearlyData[$nYear]['Revenue'] = $oProductRevenues->getRevenueAmountTotal();
			//Retrieve Total Quantity
			$aYearlyData[$nYear]['Quantity'] = $oProductRevenues->getProductQuantityTotal();
			//Retrieve Product Quantity related to performances
			$oProductRevenues->bPerformanceRelated = TRUE;
			$aYearlyData[$nYear]['PerformanceQuantity'] = $oProductRevenues->getProductQuantityTotal();
	
			//Retrieve Total Expenses
			$aYearlyData[$nYear]['Expense'] = $oProductExpenses->getExpenseAmountTotal();
			
			//Calculate Profit data
			$aYearlyData[$nYear]['Profit'] = $aYearlyData[$nYear]['Revenue'] - $aYearlyData[$nYear]['Expense'];
		}
	}

	//If multiple products are being reported, retrieve data for each product
	if ($_POST['selProduct'] == 0)
	{
		//Must add a fake product NO PRODUCT to match Product Data records
		$oNoProduct = new Product();
		$oNoProduct->nProductID = 0;
		$oNoProduct->sProductName = "NO PRODUCT";

		$oProducts = new Product();
		if ($oProducts->getProduct())
		{			
			array_unshift($oProducts->aProductRecords,$oNoProduct);
			
			foreach ($oProducts->aProductRecords as $oProduct)
			{

				$oProductRevenues = new Revenue();
				$oProductExpenses = new Expense();
		
				if($oProduct->nProductID == 0){
					$oProductRevenues->nProductID = 0;
					$oProductExpenses->nProductID = 0;
				}
				else
				{
					$oProductRevenues->nProductID = $oProduct->nProductID;
					$oProductExpenses->nProductID = $oProduct->nProductID;	
				}
		
				//If a specific Category is chosen, limit revenues and expenses to that category
				if ($_POST['selCategory'] > 0)
				{
					$oProductRevenues->aRevenueCategoryIDs[0] = $_POST['selCategory'];
					$oProductExpenses->aExpenseCategoryIDs[0] = $_POST['selCategory'];
				}
		
				//If a specific Year is chosen, limit revenues and expenses to that year
				if ($_POST['selYear'] > 0)
				{
					$oProductRevenues->nPaidYear = $_POST['selYear'];
					$oProductExpenses->nExpenseYear = $_POST['selYear'];
				}

				//Product Name
				$aProductData[$oProduct->nProductID]['ProductName'] = $oProduct->sProductName;
				//Retrieve Total Revenue
				$aProductData[$oProduct->nProductID]['Revenue'] = $oProductRevenues->getRevenueAmountTotal();
				//Retrieve Total Quantity
				$aProductData[$oProduct->nProductID]['Quantity'] = $oProductRevenues->getProductQuantityTotal();
				//Retrieve Product Quantity related to performances
				$oProductRevenues->bPerformanceRelated = TRUE;
				$aProductData[$oProduct->nProductID]['PerformanceQuantity'] = $oProductRevenues->getProductQuantityTotal();

				//Retrieve Total Expenses
				$aProductData[$oProduct->nProductID]['Expense'] = $oProductExpenses->getExpenseAmountTotal();
				
				//Calculate Profit data
				$aProductData[$oProduct->nProductID]['Profit'] = $aProductData[$oProduct->nProductID]['Revenue'] - $aProductData[$oProduct->nProductID]['Expense'];
			}


		}
		else
		{
			//ERROR: Failed to retrieve Products
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
				if($_POST['selProduct'] > 0)
				{
					echo " {$oThisProduct->aProductRecords[0]->sProductName}";
				}
				if($_POST['selYear'] > 0)
				{
					echo "  {$_POST['selYear']}";
				}
				if($_POST['selCategory'] > 0)
				{
					echo "  {$oThisCategory->aCategoryRecords[0]->sCategoryName}";
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
					$oRevenueTypes = new RevenueType();
					if ($oRevenueTypes->getRevenueType())
					{
						foreach($oRevenueTypes->aRevenueTypeRecords as $oRevenueType)
						{
							$sQueryString = "{$sBaseQueryString}&REVENUE_TYPE_ID={$oRevenueType->nRevenueTypeID}"; 
							
							$oRevenue = new Revenue();
							$oRevenue->nRevenueTypeID = $oRevenueType->nRevenueTypeID;
							$oRevenue->nProductID = $_POST['selProduct'];
							$oRevenue->nPaidYear = $_POST['selYear'];
							$oRevenue->aRevenueCategoryIDs[0] = $_POST['selCategory'];
							$nTotalRevenueTypeAmount = $oRevenue->getRevenueAmountTotal();
							if($nTotalRevenueTypeAmount == -1)
							{
								$form->nMessageType = MESSAGE_TYPE_ERROR;
								$form->sMessage = "FAILED TO GET REVENUES: {$oRevenue->sErrorMessage}";
							}
							else
							{
								//Display a report row if any revenues were found matching criteria
								if(!empty($nTotalRevenueTypeAmount) && $nTotalRevenueTypeAmount > 0)
								{	
									$nTotalRevenueAmount += $nTotalRevenueTypeAmount;
								
									echo "<tr>";
									echo "<td width='200px'>{$oRevenueType->sRevenueTypeName}</td>";
									echo "<td>$";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";  
									echo number_format((float)$nTotalRevenueTypeAmount, 2, '.', ',');
									echo "</a>";
									echo"</td>";
									echo "</tr>";
								}

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
					}
					else
					{
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "FAILED TO GET REVENUE TYPES: {$oRevenueTypes->sErrorMessage}";
					}
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
				$oTaxCategories = new TaxCategory();
				if($oTaxCategories->getTaxCategory())
				{
					echo "<table class='table table-striped'>";
					$nExpenseTotal = 0;
					foreach($oTaxCategories->aTaxCategoryRecords as $oTaxCategory)
					{
					
						$sQueryString = "{$sBaseQueryString}&TAX_CATEGORY_ID={$oTaxCategory->nTaxCategoryID}"; 
						
						$oProductExpenses = new Expense();
						$oProductExpenses->nTaxCategoryID = $oTaxCategory->nTaxCategoryID; 
						$oProductExpenses->nProductID = $_POST['selProduct'];
						$oProductExpenses->nExpenseYear = isset($_POST["selYear"]) ? $_POST["selYear"] : "";
						$oProductExpenses->aExpenseCategoryIDs[0] = isset($_POST["selCategory"]) ? $_POST["selCategory"] : "";
						if($oProductExpenses->getExpense())
						{
							
							$nProductExpenseTotal = 0;
							foreach($oProductExpenses->aExpenseRecords as $oProductExpense)
							{
								$nProductExpenseTotal += $oProductExpense->nExpenseAmount;
								$nExpenseTotal += $oProductExpense->nExpenseAmount;
							}
							
							if($nProductExpenseTotal > 0)
							{

								echo "<tr>";
								echo "<td width='200px'>{$oTaxCategory->sTaxCategoryName}</td>";
								echo "<td> $";
								echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sQueryString}'>";  
								echo number_format((float)$nProductExpenseTotal, 2, '.', ',');
								echo "</a>";
								echo "</td>";
								echo "</tr>";
							}
						}
						else
						{
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "FAILED TO GET EXPENSES: {$oProductExpenses->sErrorMessage}";
						}
					}
				}
				else
				{
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "FAILED TO GET TAX CATEGORIES: {$oTaxCategories->sErrorMessage}";
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
if ($_POST['selProduct'] == 0)
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

						array_unshift($oProducts->aProductRecords,$oNoProduct);

						foreach ($oProducts->aProductRecords as $oProduct)
						{
						
							$sQueryString = "{$sBaseQueryString}&PRODUCT_ID={$oProduct->nProductID}";

							//Only display a yearly row if there is data to report 
							if($aProductData[$oProduct->nProductID]['Revenue'] > 0 || $aProductData[$oProduct->nProductID]['Expense'] > 0 ||$aProductData[$oProduct->nProductID]['Quantity'] > 0)
							{
							
								echo "<tr>";
								echo "<td align='left'><small>{$oProduct->sProductName}</small></td>";
								// Product Sales Quantity
								echo "<td>";
								echo number_format((float)$aProductData[$oProduct->nProductID]['Quantity'],0,"",",");	
								$aProductTotals['Quantity'] += $aProductData[$oProduct->nProductID]['Quantity'];
								echo "</td>";
								// Product Performance Sales Quantity
								echo "<td>";
								echo number_format((float)$aProductData[$oProduct->nProductID]['PerformanceQuantity'],0,"",",");	
								$aProductTotals['PerformanceQuantity'] += $aProductData[$oProduct->nProductID]['PerformanceQuantity'];
								echo "</td>";
								// Revenue										
								echo "<td>";
								if($aProductData[$oProduct->nProductID]['Revenue'] > 0)
								{
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
									echo "$" . number_format((float)$aProductData[$oProduct->nProductID]['Revenue'], 2, '.', ',');
									echo "</a>";	
									$aProductTotals['Revenue'] += $aProductData[$oProduct->nProductID]['Revenue'];
								}
								else
								{
									echo "$" . number_format((float)$aProductData[$oProduct->nProductID]['Revenue'], 2, '.', ',');
								}
								echo "</td>";
								// Expense
								echo "<td>";
								if($aProductData[$oProduct->nProductID]['Expense'] > 0)
								{
									echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php{$sQueryString}'>";
									echo "$" . number_format((float)$aProductData[$oProduct->nProductID]['Expense'], 2, '.', ',');
									echo "</a>";	
									$aProductTotals['Expense'] += $aProductData[$oProduct->nProductID]['Expense'];
								}
								else
								{
									echo "$" . number_format((float)$aProductData[$oProduct->nProductID]['Expense'], 2, '.', ',');
								}
								echo "</td>";
								// Profit
								if(intval($aProductData[$oProduct->nProductID]['Profit']) < 0){
									echo "<td style=\"color: red;\">";
								}
								else{
									echo "<td>";
								}
								echo "$" . number_format((float)$aProductData[$oProduct->nProductID]['Profit'], 2, '.', ',');	
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
