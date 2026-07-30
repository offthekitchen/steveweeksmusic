<?php
/*
*******************************************************************
IncomeTaxReport.php
This PHP file generates an Income Tax Report 
NOTES
Date        Change
-------------------------------------------------------------
2015-10-20  Changed to use Report Filters include
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

 //include TaxCategory Class		
 include_once (CLASS_DIR . "/class_TaxCategory.php");

 //include Expense Class		
  include_once (CLASS_DIR . "/class_Expense.php");

 //include Revenue Class		
  include_once (CLASS_DIR . "/class_Revenue.php");

 //include Revenue_Type Class		
  include_once (CLASS_DIR . "/class_RevenueType.php");

  //include Form Class		
  include (CLASS_DIR . "/class_Form.php");

 $sActiveMenuItem = REPORTS_ACTIVE;	
 $sPageName = "Income Tax Report";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<!-- Javascript required for Calendar picker -->
<script language="javascript" src="<?php echo ADMIN_JS_DIR ?>/calendar.js"></script>

<body>

<div class="container-fluid">	
<form name="IncomeTaxReport" action="IncomeTaxReport.php" method="post">

<?php
	if (isset($_REQUEST['YEAR']) && $_REQUEST['YEAR'] != "")
	{
		//If a Year is passed, default filters to that year 
		$_POST['selYear'] = $_REQUEST['YEAR'];
		$_POST['StartDate'] = "{$_REQUEST['YEAR']}-01-01";
		$_POST['EndDate'] = "{$_REQUEST['YEAR']}-12-31";
		$_POST['btnGenerate'] = "GenerateReport";
	}
	elseif (isset($_POST['selYear']) && $_POST['selYear'] != 0)
	{
		//If a Year was chosen, default calendar filters
		$_POST['btnGenerate'] = "GenerateReport";
		$_POST['StartDate'] = "{$_POST['selYear']}-01-01";
		$_POST['EndDate'] = "{$_POST['selYear']}-12-31";
		$_POST['btnGenerate'] = "GenerateReport";
	}
	elseif(!isset($_POST['StartDate']) || !isset($_POST['EndDate']))
	{
		$nCurrentYear = date("Y");
/* 
		$_POST['selYear'] = $nCurrentYear;
		$_POST['StartDate'] = "{$nCurrentYear}-01-01";
		$_POST['EndDate'] = "{$nCurrentYear}-12-31";
		$_POST['btnGenerate'] = "GenerateReport"; */
	}
	else

	//Instantiate needed objects
	$oExpenseTaxCategories = new TaxCategory();
	$form = new Form();

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
				INCOME TAX REPORT
				<?php
				if(isset($_POST['StartDate']) && isset($_POST['EndDate']))
				{
					echo " (" . $_POST['StartDate'] . " to " . $_POST['EndDate'] . ")";
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
		<div class="col-xs-12 col-md-5">
			<div class="row">
				<div class="col-xs-12 FieldGroupTitle"> 
					REVENUE
				</div>	
				<div class="col-xs-12 FieldGroup"> 
					<div class="FinancialData">
						<div class="row">		
							<div class="col-xs-12">
								<?php
								if (isset($_POST["btnGenerate"])) 
								{
									$nTotalRevenueAmount = 0;
									$oRevenueTypes = new RevenueType();
									if ($oRevenueTypes->getRevenueType())
									{
										foreach($oRevenueTypes->aRevenueTypeRecords as $oRevenueType)
										{
										
											//Alternate the report style
											if ($sReportStyleClass == REPORT_STYLE_CLASS)
											{
												$sReportStyleClass = REPORT_STYLE_CLASS_ALT;
											}
											else
											{
												$sReportStyleClass = REPORT_STYLE_CLASS;
											}
											
											$oRevenue = new Revenue();
											$oRevenue->nRevenueTypeID = $oRevenueType->nRevenueTypeID;
											$oRevenue->dtStartDate = isset($_POST["StartDate"]) ? $_POST["StartDate"] : "";
											$oRevenue->dtEndDate = isset($_POST["EndDate"]) ? $_POST["EndDate"] : "";
											$nTotalRevenueTypeAmount = $oRevenue->getRevenueAmountTotal();
											if($nTotalRevenueTypeAmount == -1)
											{
												echo "FAILED TO GET REVENUES: " . $oRevenue->sErrorMessage . "<BR>";
											}
											else
											{
												if(empty($nTotalRevenueTypeAmount))
												{	
													$nTotalRevenueTypeAmount = 0.00;
												}
												$nTotalRevenueAmount += $nTotalRevenueTypeAmount;
												
												echo "<div class='row'>";
												echo "<div class='col-xs-6 " . $sReportStyleClass . "'>" . $oRevenueType->sRevenueTypeName . "</div>";
												echo "<div class='col-xs-6 " . $sReportStyleClass . "'>$";
												if ($nTotalRevenueTypeAmount > 0)
												{
													echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?START_DATE=" . $_POST['StartDate'];
													echo "&END_DATE=" . $_POST['EndDate'];
													echo "&REVENUE_TYPE_ID=" . $oRevenue->nRevenueTypeID . "'>";  
													echo number_format((float)$nTotalRevenueTypeAmount, 2, '.', '');
													echo "</a>";
												}
												else
												{
													echo number_format((float)$nTotalRevenueTypeAmount, 2, '.', '');
												}
												echo"</div>";
												echo "</div>";
											}
											
										}			
			
										echo "<div class='row'><div class='col-xs-12'><HR></div></div>";
										echo "<div class='row'>";
										echo "<div class='col-xs-6'>Total</div>";
										echo "<div class='col-xs-6'> $";
										if ($nTotalRevenueAmount > 0)
										{
											echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?START_DATE=" . $_POST['StartDate'];
											echo "&END_DATE=" . $_POST['EndDate'] . "'>";
											echo number_format((float)$nTotalRevenueAmount, 2, '.', '');
											echo "</a>";
										}
										else
										{
											echo number_format((float)$nTotalRevenueAmount, 2, '.', '');
										}
										echo "</div>";
										echo "</div>";
									}
									else
									{
										//ERROR
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	

		<div class="col-xs-12 col-md-5">
			<div class="row">

				<div class="col-xs-12 FieldGroupTitle"> 
					EXPENSES
				</div>
				<div class="col-xs-12 FieldGroup"> 
					<div class="FinancialData">
						<div class="row">		
							<div class="col-xs-12">
								<?php
								if (isset($_POST["btnGenerate"])) 
								{
									$oExpenseTaxCategories = new TaxCategory();
									$oExpenseTaxCategories->bExpenseRelated = TRUE;
									if($oExpenseTaxCategories->getTaxCategory())
									{
										$nExpenseTotal = 0;
										foreach($oExpenseTaxCategories->aTaxCategoryRecords as $oExpenseTaxCategory)
										{

											//Alternate the report style
											if ($sReportStyleClass == REPORT_STYLE_CLASS)
											{
												$sReportStyleClass = REPORT_STYLE_CLASS_ALT;
											}
											else
											{
												$sReportStyleClass = REPORT_STYLE_CLASS;
											}

											$oTaxCategoryExpenses = new Expense();
											$oTaxCategoryExpenses->nTaxCategoryID = $oExpenseTaxCategory->nTaxCategoryID;
											$oTaxCategoryExpenses->dtStartDate = isset($_POST["StartDate"]) ? $_POST["StartDate"] : "";
											$oTaxCategoryExpenses->dtEndDate = isset($_POST["EndDate"]) ? $_POST["EndDate"] : "";

											$nTotalTaxCategoryAmount = $oTaxCategoryExpenses->getExpenseAmountTotal();
											if($nTotalTaxCategoryAmount == -1)
											{
												echo "FAILED TO GET EXPENSES: " . $oTaxCategoryExpenses->sErrorMessage . "<BR>";
											}
											else
											{
												if(empty($nTotalTaxCategoryAmount))
												{	
													$nTotalTaxCategoryAmount = 0.00;
												}
												$nTotalExpenseAmount += $nTotalTaxCategoryAmount;
												
												echo "<div class='row'>";
												echo "<div class='col-xs-6 " . $sReportStyleClass . "'>" . $oExpenseTaxCategory->sTaxCategoryName . "</div>";
												echo "<div class='col-xs-6 " . $sReportStyleClass . "'>$";
												if ($nTotalTaxCategoryAmount > 0)
												{
													echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php?START_DATE=" . $_POST['StartDate'];
													echo "&END_DATE=" . $_POST['EndDate'];
													echo "&TAX_CATEGORY_ID=" . $oExpenseTaxCategory->nTaxCategoryID . "'>";  
													echo number_format((float)$nTotalTaxCategoryAmount, 2, '.', '');
													echo "</a>";
												}
												else
												{
													echo number_format((float)$nTotalTaxCategoryAmount, 2, '.', '');
												}
												echo"</div>";
												echo "</div>";
											}
										}
										echo "<div class='row'><div class='col-xs-12'><HR></div></div>";
										echo "<div class='row'>";
										echo "<div class='col-xs-6'>Total</div>";
										echo "<div class='col-xs-6'> $";
										if ($nTotalExpenseAmount > 0)
										{
											echo "<a href='" . ADMIN_DIR . "/ExpenseMaintenance.php?START_DATE=" . $_POST['StartDate'];
											echo "&END_DATE=" . $_POST['EndDate'] . "'>";
											echo number_format((float)$nTotalExpenseAmount, 2, '.', '');
											echo "</a>";
										}
										else
										{
											echo number_format((float)$nTotalExpenseAmount, 2, '.', '');
										}
										echo "</div>";
										echo "</div>";
									}
									else
									{
										echo "FAILED TO GET EXPENSE TAX CATEGORIES: " . $oExpenseTaxCategories->sErrorMessage;
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-2">
			<div class="FieldGroupTitle" >
			PROFIT: 
			</div>
			<div class="FieldGroup">
				<div class="row">
				<div class="col-xs-12">		
					<?php 
					$nTotalProfit = $nTotalRevenueAmount - $nTotalExpenseAmount;
					echo "&nbsp;&nbsp;$" . number_format((float)$nTotalProfit, 2, '.', ''); 
					?>
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

	//If no Dates are chosen, default then to current year
    if ( document.IncomeTaxReport.StartDate.value == "" || document.IncomeTaxReport.StartDate.value == "0000-00-00" || document.IncomeTaxReport.EndDate.value == "" || document.IncomeTaxReport.EndDate.value == "0000-00-00" )
    {
		sYear = new Date().getFullYear();
		document.IncomeTaxReport.StartDate.value = sYear + "-01-01";
		document.IncomeTaxReport.EndDate.value = sYear + "-12-31";
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
