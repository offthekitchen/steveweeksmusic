<?php
/*
*******************************************************************
RevenueType.php
This PHP file generates a Revenue Type Report 
NOTES
Date        Change
-------------------------------------------------------------
2016-11-22  Created
2017-04-15	Made Responsive
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
 	
//include RevenueType Class		
include_once (CLASS_DIR . "/class_RevenueType.php");

//include Product Class		
include_once (CLASS_DIR . "/class_Product.php");

//include Revenue Class		
include_once (CLASS_DIR . "/class_Revenue.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once (CLASS_DIR . "/tc_calendar.php");

 $sActiveMenuItem = REPORTS_ACTIVE;
 $sPageName = "Revenue Type Report";


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>
<body>

<div class="container-fluid">
<form name="RevenueTypeReport" action="RevenueTypeReport.php" method="post">
<?php
	//If a RevenueType ID is passed, pre-select that RevenueType
	if (isset($_REQUEST['REVENUE_TYPE_ID']) && $_REQUEST['REVENUE_TYPE_ID'] != "")
	{
		$_POST['selRevenueType'] = $_REQUEST['REVENUE_TYPE_ID'];
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
	$aRevenueTypeData = array();
	$oThisRevenueTypes = new RevenueType();

	//Set the Year range based on year selection 
	if ($_POST['selYear'] > 0)
	{
		$oThisRevenueTypes->nPaidYear = $_POST['selYear'];		
		$nStartYear = $_POST['selYear'];
		$nEndYear = $_POST['selYear'];
	}
	else
	{
		$nStartYear = START_YEAR;
		$nEndYear = date('Y');
	}
		
	//If a RevenueType is selected, retrieve that product's information
	if ($_POST['selRevenueType'] > 0)
	{
		$oThisRevenueTypes->nRevenueTypeID = $_POST['selRevenueType'];		
	}

	if(!$oThisRevenueTypes->getRevenueType())
	{
		$form->nMessageType = MESSAGE_TYPE_ERROR;
		$form->sMessage = "FAILED TO GET REVENUE_TYPES: {$oThisRevenueType->sErrorMessage}";
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
				REVENUE_TYPE REPORT:  
				<?php
				if($_POST['selRevenueType'] > 0)
				{
					echo " {$oThisRevenueTypes->aRevenueTypeRecords[0]->sRevenueTypeName}";
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
			$bRevenueTypeFilter = TRUE;
			include (ADMIN_INCLUDE_DIR . "/ReportFilters.php");
			?>
		</div>
	</div>
<?php
if (isset($_POST["btnGenerate"])) 
{
	//Only display this section if reporting on all revenute types 
	if($_POST['selRevenueType'] == 0)
	{
?>
	<div class="row"> 		
		<div class="col-xs-12 FieldGroup"> 
			<div class="FieldGroupTitle"> 
				REVENUES BY TYPE
				<?php
				if ($_POST['selYear'] > 0)
				{
					echo " FOR {$_POST['selYear']}";
				}
				?>
			</div>
			<div class="table-responsive">	
				<table class="table table-striped"> 
				<?php
					$nTotalRevenueAmount = 0;

					foreach($oThisRevenueTypes->aRevenueTypeRecords as $oRevenueType)
					{
						$sQueryString = "{$sBaseQueryString}&REVENUE_TYPE_ID={$oRevenueType->nRevenueTypeID}"; 
						
						$oRevenue = new Revenue();
						$oRevenue->nRevenueTypeID = $oRevenueType->nRevenueTypeID;
						$oRevenue->nPaidYear = $_POST['selYear'];
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
								echo "<td>{$oRevenueType->sRevenueTypeName}</td>";
								echo "<td>$";
								echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";  
								echo number_format((float)$nTotalRevenueTypeAmount, 2, '.', ',');
								echo "</a>";
								echo"</td>";
								echo "</tr>";
							}

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
					echo "</tr>";
				?>
				</table>			
			</div>
		</div>
<?php
	}
?>		
		<div class="col-xs-12 FieldGroup">
			<div class="FieldGroupTitle"> 
				REVENUES BY PRODUCT
				<?php
				if ($_POST['selYear'] > 0)
				{
					echo " FOR {$_POST['selYear']}";
				}
	
				if($_POST['selRevenueType'] > 0)
				{
					echo " {$oThisRevenueTypes->aRevenueTypeRecords[0]->sRevenueTypeName}";
				}
				?>
			</div>
			<div class="table-responsive">
				<table class="table table-striped"> 
					<tr>								
						<th></th>
						<?php
						 foreach($oThisRevenueTypes->aRevenueTypeRecords as $oRevenueType)
						 {
							echo "<th>{$oRevenueType->sRevenueTypeName}</th>";
						 }
						?>
					</tr>
				<?php
				$aRevenueTypeTotals = array();
				
				$oProducts = new Product;
				
				if ($oProducts->getProduct())
				{
				
					foreach ($oProducts->aProductRecords as $oProduct)
					{
						echo "<tr>";
						echo "<td>{$oProduct->sProductName}</td>";
						foreach($oThisRevenueTypes->aRevenueTypeRecords as $oRevenueType)
						{
	
							$sQueryString = "{$sBaseQueryString}&PRODUCT_ID={$oProduct->nProductID}&REVENUE_TYPE_ID={$oRevenueType->nRevenueTypeID}";
							$oProductRevenues = new Revenue();
							$oProductRevenues->nProductID = $oProduct->nProductID;
							$oProductRevenues->nRevenueTypeID = $oRevenueType->nRevenueTypeID;
							$oProductRevenues->nPaidYear = $_POST['selYear'];
							$nProductRevenueTotal = $oProductRevenues->getRevenueAmountTotal();
	
							echo "<td>";
							if($nProductRevenueTotal > 0)
							{
								echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
								echo "$" . number_format((float)$nProductRevenueTotal, 2, '.', ',');
								echo "</a>";	
								$aRevenueTypeTotals[$oProductRevenues->nRevenueTypeID] += $nProductRevenueTotal;
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
				
				foreach($oThisRevenueTypes->aRevenueTypeRecords as $oRevenueType)
				{
					$sQueryString = "{$sBaseQueryString}&REVENUE_TYPE_ID={$oRevenueType->nRevenueTypeID}";
					echo "<td>";
					echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
					echo "$" . number_format((float)$aRevenueTypeTotals[$oRevenueType->nRevenueTypeID], 2, '.', ',');	
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
				if ($_POST['selRevenueType'] > 0)
				{
					echo " FOR {$oThisRevenueTypes->aRevenueTypeRecords[0]->sRevenueTypeName}";
				}
				?>						
			</div>
			<div class="table-responsive">
				<table class="table table-striped"> 
					<tr>								
						<th></th>
						<?php
						 foreach($oThisRevenueTypes->aRevenueTypeRecords as $oRevenueType)
						 {
							echo "<th>{$oRevenueType->sRevenueTypeName}</th>";
						 }
						?>
					</tr>
					<?php
					$sReportStyleClass == REPORT_STYLE_CLASS;
					$aRevenueTypeTotals = array();
					
					for ($nYear = $nStartYear; $nYear <= $nEndYear; $nYear++)
					{									
						//Alternate the report style
						setReportStyleClass($sReportStyleClass);
					
						echo "<tr>";
						echo "<th>{$nYear}</th>";
	
						foreach($oThisRevenueTypes->aRevenueTypeRecords as $oRevenueType)
						{
	
							$sQueryString = "{$sBaseQueryString}&YEAR={$nYear}&REVENUE_TYPE_ID={$oRevenueType->nRevenueTypeID}";
							$oYearRevenues = new Revenue();
							$oYearRevenues->nRevenueTypeID = $oRevenueType->nRevenueTypeID;
							$oYearRevenues->nPaidYear = $nYear;
							$nYearRevenuesTotal = $oYearRevenues->getRevenueAmountTotal();
	
							echo "<td>";
							if($nYearRevenuesTotal > 0)
							{
								echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
								echo "$" . number_format((float)$nYearRevenuesTotal, 2, '.', ',');
								echo "</a>";	
								$aRevenueTypeTotals[$oYearRevenues->nRevenueTypeID] += $nYearRevenuesTotal;
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
					
					foreach($oThisRevenueTypes->aRevenueTypeRecords as $oRevenueType)
					{
						$sQueryString = "{$sBaseQueryString}&REVENUE_TYPE_ID={$oRevenueType->nRevenueTypeID}";
						echo "<td>";
						echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
						echo "$" . number_format((float)$aRevenueTypeTotals[$oRevenueType->nRevenueTypeID], 2, '.', ',');	
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
    if ( document.RevenueTypeReport.StartDate.value == "" || document.RevenueTypeReport.StartDate.value == "0000-00-00" || document.RevenueTypeReport.EndDate.value == "" || document.RevenueTypeReport.EndDate.value == "0000-00-00" )
    {
		sYear = new Date().getFullYear();
		document.RevenueTypeReport.StartDate.value = sYear + "-01-01";
		document.RevenueTypeReport.EndDate.value = sYear + "-12-31";
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
