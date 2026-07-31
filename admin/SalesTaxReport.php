<?php
/*
*******************************************************************
SalesTaxReport.php
This PHP file generates a Sales tax report in the Colorado Revenue
Sales Tax format
NOTES
Date        Change
-------------------------------------------------------------
2015-10-02  Changed report to use Report Filters include file
2021-08-30	Updated for PHP 8
2026-07-30	Migrated to new Datalayer Revenue repository
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

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");

//include Form Class
include (CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once (CLASS_DIR . "/tc_calendar.php");

 $sActiveMenuItem = REPORTS_ACTIVE;	

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<?php

	//Sales Tax Rate Constants
 	define("EL_PASO_TAX_RATE",0.0123);
 	define("DISTRICT_TAX_RATE",0.01);
	 define("COLORADO_TAX_RATE",0.029);
	 define("SERVICE_FEE_RATE",0.0333);

	if (isset($_POST["btnGenerate"])) 
	{
		$nSalesRevenueTotal = 0.00;
		$nCDSalesRevenueTotal = 0.00;
		$nColoradoRevenueTotal = 0.00;
		$nElPasoRevenueTotal = 0.00;
		$nCharitableSalesTotal = 0.00;
		$nPerformanceFeesTotal = 0.00;
		$nDeductibleTotal = 0.00;
		$nResaleTotal = 0.00;
	
		//Determine date range based on filter choices
		if (isset($_POST["selYear"]) && $_POST["selYear"] > 0)
		{
			$sReportStartYear = $_POST["selYear"];
			$sReportEndYear = $_POST["selYear"];
		}
		else
		{
			$sReportStartYear = "2003";
			$sReportEndYear = date("Y");
		}

		switch($_POST["selQuarter"])
		{
			case "0":
				$sReportStartMonth = "01";
				$sReportStartDay = "01";
				$sReportEndMonth = "12";
				$sReportEndDay = "31";
				break;
			case "1":
				$sReportStartMonth = "01";
				$sReportStartDay = "01";
				$sReportEndMonth = "03";
				$sReportEndDay = "31";
				break;
			case "2":
				$sReportStartMonth = "04";
				$sReportStartDay = "01";
				$sReportEndMonth = "06";
				$sReportEndDay = "30";
				break;
			case "3":
				$sReportStartMonth = "07";
				$sReportStartDay = "01";
				$sReportEndMonth = "09";
				$sReportEndDay = "30";
				break;
			case "4":
				$sReportStartMonth = "10";
				$sReportStartDay = "01";
				$sReportEndMonth = "12";
				$sReportEndDay = "31";
				break;
			default:
				$sReportStartMonth = "01";
				$sReportStartDay = "01";
				$sReportEndMonth = "12";
				$ReportsEndDay = "31";
		}

		$dtReportStartDate = "$sReportStartYear-$sReportStartMonth-$sReportStartDay";
		$dtReportEndDate = "$sReportEndYear-$sReportEndMonth-$sReportEndDay";

		$revenueRepo = new \Datalayer\RevenueRepository();
		$aRevenues = $revenueRepo->find([
			'startDate' => $dtReportStartDate,
			'endDate' => $dtReportEndDate,
		]);

		foreach ($aRevenues as $oRevenue)
		{
			//Keep a running sum of performance fees
			if($oRevenue->revenueTypeId == REVENUE_TYPE_PERFORMANCE_FEE)
			{
				$nPerformanceFeesTotal += $oRevenue->amount ?? 0;
			}
			elseif($oRevenue->revenueTypeId == REVENUE_TYPE_CD_SALE)
			{
				$nCDSalesRevenueTotal += $oRevenue->amount ?? 0;
				
				//Revenues can only be Charitable or Resale and if neither they are eligible for tax
				if($oRevenue->charitable)
				{
					$nCharitableSalesTotal += $oRevenue->amount ?? 0;
				}
				elseif($oRevenue->resale)
				{
					$nResaleTotal += $oRevenue->amount ?? 0;
				}
				else
				{
					if($oRevenue->coloradoRevenue)
					{
						$nColoradoRevenueTotal += $oRevenue->amount ?? 0;
					}
					if($oRevenue->elPasoRevenue)
					{
						$nElPasoRevenueTotal += $oRevenue->amount ?? 0;
					}
				}
			}
		}
		
		//Store some sums for convenient use later
		$nDeductibleTotal = $nCharitableSalesTotal + $nPerformanceFeesTotal;
		$nExemptTotal = $nDeductibleTotal + $nResaleTotal;
		$nSalesRevenueTotal = $nCDSalesRevenueTotal + $nPerformanceFeesTotal;
									
	}

?>
<body>
<div class="container-fluid">	
<form name="SalesTaxReport" action="SalesTaxReport.php" method="post">

<?php

		//Instantiate needed objects
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
					SALES TAX REPORT
					<?php
					if(isset($dtReportStartDate) && isset($dtReportEndDate))
					{
						echo " ({$dtReportStartDate} to {$dtReportEndDate})";
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
			$bQuarterFilter = TRUE;
			include (ADMIN_INCLUDE_DIR . "/ReportFilters.php");
			?>
		</div>
	</div>
	<div class="row">		
		<div class="col-xs-12 FieldGroup"> 
			<div class="FinancialData">
				<div class="row">		
					<div class="col-xs-12 FieldGroupTitle"> 
						DEDUCTIONS
					</div>
				</div>	
				<div class="row">		
					<div class="col-xs-12 col-sm-3 FinancialLine">
						Service Sales									
					</div>
					<div class="col-xs-12 col-sm-3 FinancialLineItem">
						<?php echo number_format((float)$nPerformanceFeesTotal, 2, '.', ''); ?>
					</div>
					<div class="hidden-xs col-sm-6 FinancialLineItem">
					</div>
					<div class="col-xs-12 col-sm-3 FinancialLine">
						Charitable Sales									
					</div>
					<div class="col-xs-12 col-sm-3 FinancialLineItem">
						<?php echo number_format((float)$nCharitableSalesTotal, 2, '.', ''); ?>
					</div>
					<div class="hidden-xs col-sm-6 FinancialLineItem">
					</div>
				</div>	
				<div class="row">		
					<div class="col-xs-12 col-sm-3 FinancialLine">
						<b>Total</b>									
					</div>
					<div class="col-xs-12 col-sm-3 FinancialLineItem">
						<?php echo number_format((float)$nDeductibleTotal, 2, '.', ''); ?>
					</div>
					<div class="hidden-xs col-sm-6 FinancialLineItem">
					</div>
				</div>
			</div>
		</div>
			<div class="col-xs-12 FieldGroup"> 
				<div class="FinancialData">
					<div class="row">		
						<div class="col-xs-12 FieldGroupTitle"> 
							TAX TABLE
						</div>
					</div>
					<div class="row FinancialHeader">	
						<div class="hidden-xs col-sm-3">
						</div>	
						<div class="col-xs-4 col-sm-3 FinancialHeader"> 
							State
						</div>
						<div class="col-xs-4 col-sm-3 FinancialHeader"> 
							Special District
						</div>	
						<div class="col-xs-4 col-sm-3 FinancialHeader"> 
							El Paso
						</div>	

					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 FinancialLine">
							1. Gross Sales
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php echo number_format((float)$nPerformanceFeesTotal + $nCDSalesRevenueTotal, 2, '.', ''); ?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 FinancialLine">
							2a. Resale									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php echo number_format((float)$nResaleTotal, 2, '.', ''); ?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 FinancialLine">
							2b. Deductions									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php echo number_format((float)$nDeductibleTotal, 2, '.', ''); ?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 FinancialLine">
							2c. Total									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nExemptTotal, 2, '.', ''); 
							?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 FinancialLine">
							3. Net Sales									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							$nNetSales = $nSalesRevenueTotal - $nExemptTotal;
							echo number_format((float)$nNetSales, 2, '.', ''); 
							?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 FinancialLine">
							3a. Net Sales<BR />Outside of Taxing Area									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nNetSales - $nColoradoRevenueTotal, 2, '.', ''); 
							?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nNetSales - $nElPasoRevenueTotal, 2, '.', ''); 
							?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nNetSales - $nElPasoRevenueTotal, 2, '.', ''); 
							?>
						</div>


					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 FinancialLine">
							3b. Exemptions									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							0.00
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							0.00
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							0.00
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 FinancialLine">
							3c. Overpayment									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							0.00
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							0.00
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							0.00
						</div>
					</div>
					<div class="row"> 
						<div class="col-xs-12 col-sm-3 FinancialLine">
							4. Net Taxable									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nColoradoRevenueTotal, 2, '.', ''); 
							?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nElPasoRevenueTotal, 2, '.', ''); 
							?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nElPasoRevenueTotal, 2, '.', ''); 
							?>
						</div>


					</div>
					<div class="row"> 
						<div class="col-xs-12 col-sm-3 FinancialLine">
							5. Amount of Tax									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
								<?php
								$nColoradoTax = round($nColoradoRevenueTotal * COLORADO_TAX_RATE,2);
								echo number_format((float)$nColoradoTax, 2, '.', '');
								?> 
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							$nDistrictTax = round($nElPasoRevenueTotal * DISTRICT_TAX_RATE,2);
							echo number_format((float)$nDistrictTax, 2, '.', ''); 
							?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							$nElPasoTax = round($nElPasoRevenueTotal * EL_PASO_TAX_RATE,2);
							echo number_format((float)$nElPasoTax, 2, '.', ''); 
							?>
						</div>
					</div>
					<div class="row"> 
						<div class="col-xs-12 col-sm-3 FinancialLine">
							8b. Service Fee Allowed									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
								<?php
								$nServiceFeeAllowed = round($nColoradoTax * SERVICE_FEE_RATE , 2);
								echo number_format((float)$nServiceFeeAllowed, 2, '.', '');
								?> 
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							
						</div>
					</div>
					<div class="row"> 
						<div class="col-xs-12 col-sm-3 FinancialLine">
							9. Sales Tax Due									
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
								<?php
								$nColoradoTaxDue = round($nColoradoTax - $nServiceFeeAllowed,2);
								echo number_format((float)$nColoradoTaxDue, 2, '.', '');
								?> 
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nDistrictTax, 2, '.', ''); 
							?>
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nElPasoTax, 2, '.', ''); 
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
						<hr />
						</div> 
					</div>
					<div class="row"> 
						<div class="col-xs-12 col-sm-3 FinancialLine">
							TOTAL AMOUNT DUE
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							
						</div>
						<div class="col-xs-4 col-sm-3 FinancialLineItem">
							<?php
							echo number_format((float)$nElPasoTax + $nDistrictTax + $nColoradoTaxDue, 2, '.', ''); 
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
//******************************************
// validate_form
// This script is called when the user
// presses the Generate Report button
//******************************************
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

	//If no Dates are chosen, default to the previous quarter
    if ( document.SalesTaxReport.StartDate.value == "" || document.SalesTaxReport.StartDate.value == "0000-00-00" || document.SalesTaxReport.EndDate.value == "" || document.SalesTaxReport.EndDate.value == "0000-00-00" )
    {
		//Determine the start and end dates for the previous quarter
		nYear = new Date().getFullYear();
		nMonth = new Date().getMonth();
 		switch(nMonth)
		{
			case 0:
			case 1:
			case 2:
				nReportYear = nYear-1;
				nReportStartMonth = 10;
				nReportEndMonth = 12;
				nReportEndDay = 31;
				break;
			case 3:
			case 4:
			case 5:
				nReportYear = nYear;
				nReportStartMonth = 1;
				nReportEndMonth = 3;
				nReportEndDay = 31;
				break;
			case 6:
			case 7:
			case 8:
				nReportYear = nYear;
				nReportStartMonth = 4;
				nReportEndMonth = 6;
				nReportEndDay = 30;
				break;
			case 9:
			case 10:
			case 11:
				nReportYear = nYear;
				nReportStartMonth = 7;
				nReportEndMonth = 9;
				nReportEndDay = 30;
				break;
		}
		
		//Set the Dates on the form 
		document.SalesTaxReport.StartDate.value = nReportYear + "-" + nReportStartMonth + "-01";
		document.SalesTaxReport.EndDate.value = nReportYear + "-" + nReportEndMonth + "-" + nReportEndDay;
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
