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
2026-07-30	Migrated to new Datalayer RevenueType/Product/Revenue repositories
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
include_once(DATALAYER_DIR . "/RevenueType.php");
include_once(DATALAYER_DIR . "/RevenueTypeRepository.php");
include_once(DATALAYER_DIR . "/Product.php");
include_once(DATALAYER_DIR . "/ProductRepository.php");
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");

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
	$revenueTypeRepo = new \Datalayer\RevenueTypeRepository();
	$productRepo = new \Datalayer\ProductRepository();
	$revenueRepo = new \Datalayer\RevenueRepository();

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
		
	$revenueTypeCriteria = [];
	if ($_POST['selRevenueType'] > 0)
	{
		$revenueTypeCriteria['id'] = (int) $_POST['selRevenueType'];
	}

	try {
		$aRevenueTypeRecords = $revenueTypeRepo->find($revenueTypeCriteria);
	} catch (\Throwable $e) {
		$aRevenueTypeRecords = [];
		$form->nMessageType = MESSAGE_TYPE_ERROR;
		$form->sMessage = "FAILED TO GET REVENUE_TYPES: " . $e->getMessage();
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
				if($_POST['selRevenueType'] > 0 && !empty($aRevenueTypeRecords))
				{
					echo " {$aRevenueTypeRecords[0]->name}";
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

					foreach($aRevenueTypeRecords as $revenueType)
					{
						$sQueryString = "{$sBaseQueryString}&REVENUE_TYPE_ID={$revenueType->id}"; 
						
						$revenueCriteria = ['revenueTypeId' => $revenueType->id];
						if ($_POST['selYear'] > 0) {
							$revenueCriteria['paidYear'] = (int) $_POST['selYear'];
						}
						$nTotalRevenueTypeAmount = sumRevenueAmount($revenueRepo, $revenueCriteria);

						//Display a report row if any revenues were found matching criteria
						if(!empty($nTotalRevenueTypeAmount) && $nTotalRevenueTypeAmount > 0)
						{	
							$nTotalRevenueAmount += $nTotalRevenueTypeAmount;
						
							echo "<tr>";
							echo "<td>{$revenueType->name}</td>";
							echo "<td>$";
							echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";  
							echo number_format((float)$nTotalRevenueTypeAmount, 2, '.', ',');
							echo "</a>";
							echo"</td>";
							echo "</tr>";
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
	
				if($_POST['selRevenueType'] > 0 && !empty($aRevenueTypeRecords))
				{
					echo " {$aRevenueTypeRecords[0]->name}";
				}
				?>
			</div>
			<div class="table-responsive">
				<table class="table table-striped"> 
					<tr>								
						<th></th>
						<?php
						 foreach($aRevenueTypeRecords as $revenueType)
						 {
							echo "<th>{$revenueType->name}</th>";
						 }
						?>
					</tr>
				<?php
				$aRevenueTypeTotals = array();
				
				$aProductRecords = $productRepo->find();
				
				if (!empty($aProductRecords))
				{
				
					foreach ($aProductRecords as $product)
					{
						echo "<tr>";
						echo "<td>{$product->name}</td>";
						foreach($aRevenueTypeRecords as $revenueType)
						{
	
							$sQueryString = "{$sBaseQueryString}&PRODUCT_ID={$product->id}&REVENUE_TYPE_ID={$revenueType->id}";
							$revenueCriteria = [
								'productId' => $product->id,
								'revenueTypeId' => $revenueType->id,
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
								$aRevenueTypeTotals[$revenueType->id] = ($aRevenueTypeTotals[$revenueType->id] ?? 0) + $nProductRevenueTotal;
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
				
				foreach($aRevenueTypeRecords as $revenueType)
				{
					$sQueryString = "{$sBaseQueryString}&REVENUE_TYPE_ID={$revenueType->id}";
					echo "<td>";
					echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
					echo "$" . number_format((float)($aRevenueTypeTotals[$revenueType->id] ?? 0), 2, '.', ',');	
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
				if ($_POST['selRevenueType'] > 0 && !empty($aRevenueTypeRecords))
				{
					echo " FOR {$aRevenueTypeRecords[0]->name}";
				}
				?>						
			</div>
			<div class="table-responsive">
				<table class="table table-striped"> 
					<tr>								
						<th></th>
						<?php
						 foreach($aRevenueTypeRecords as $revenueType)
						 {
							echo "<th>{$revenueType->name}</th>";
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
	
						foreach($aRevenueTypeRecords as $revenueType)
						{
	
							$sQueryString = "{$sBaseQueryString}&YEAR={$nYear}&REVENUE_TYPE_ID={$revenueType->id}";
							$nYearRevenuesTotal = sumRevenueAmount($revenueRepo, [
								'revenueTypeId' => $revenueType->id,
								'paidYear' => $nYear,
							]);
	
							echo "<td>";
							if($nYearRevenuesTotal > 0)
							{
								echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
								echo "$" . number_format((float)$nYearRevenuesTotal, 2, '.', ',');
								echo "</a>";	
								$aRevenueTypeTotals[$revenueType->id] = ($aRevenueTypeTotals[$revenueType->id] ?? 0) + $nYearRevenuesTotal;
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
					
					foreach($aRevenueTypeRecords as $revenueType)
					{
						$sQueryString = "{$sBaseQueryString}&REVENUE_TYPE_ID={$revenueType->id}";
						echo "<td>";
						echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php{$sQueryString}'>";
						echo "$" . number_format((float)($aRevenueTypeTotals[$revenueType->id] ?? 0), 2, '.', ',');	
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

<?php
/**
 * Sum REVENUE_AMOUNT for records matching criteria (legacy getRevenueAmountTotal).
 */
function sumRevenueAmount(\Datalayer\RevenueRepository $revenueRepo, array $criteria): float
{
	$total = 0.0;
	foreach ($revenueRepo->find($criteria) as $revenue) {
		$total += (float) ($revenue->amount ?? 0);
	}
	return $total;
}
?>
