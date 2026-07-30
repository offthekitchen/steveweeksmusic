<?php
/*
*******************************************************************
Mileage.php
This PHP file generates a Mileage Report 
NOTES
Date        Change
-------------------------------------------------------------
2015-12-03  Created
2017-04-12	Made Responsive
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
$sPageName = "Mileage Report Report";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>
	<?php
	
	//include Mileage Class		
 	include_once (CLASS_DIR . "/class_Mileage.php");

 	//include Form Class		
 	include (CLASS_DIR . "/class_Form.php");

	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");
	
	//If a Year is passed, pre-select that Year
	if (isset($_REQUEST['YEAR']) && $_REQUEST['YEAR'] != "")
	{
		$_POST['selYear'] = $_REQUEST['YEAR'];
		$_POST['btnGenerate'] = "GenerateReport";
	}
	?>
<div class="container-fluid">
<form name="MileageReport" action="MileageReport.php" method="post">
<?php

	//Instantiate needed objects
	$form = new Form();
	$aMileageData = array();
	$oThisMileage = new Mileage();

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
				MILEAGE REPORT:  
				<?php
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
			include (ADMIN_INCLUDE_DIR . "/ReportFilters.php");
			?>
		</div>
	</div>
	<div class="row">		
		<div class="col-xs-12 FieldGroup">
			<?php
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
	
			$sReportStyleClass == REPORT_STYLE_CLASS;
			
			echo "<table>";
			
			for ($nYear = $nStartYear; $nYear <= $nEndYear; $nYear++)
			{
				$nYearlyMileage = 0;
				$oMileages = new Mileage();
				$oMileages->nMileageYear = $nYear;
	
				if ($oMileages->getMileage())
				{
				
					foreach ($oMileages->aMileageRecords as $oMileage)
					{
						$nYearlyMileage += $oMileage->nMileage;
	
						if($nStartYear == $nEndYear)
						{
							$sQueryString = "?MILEAGE_ID={$oMileage->nMileageID}"; 
						
							//Alternate the report style
							setReportStyleClass($sReportStyleClass);
							
							echo "<div class='row {$sReportStyleClass}'>";
							echo "<div class='col-xs-12 col-sm-2 {$sReportStyleClass}'>{$oMileage->dtMileageDate}</div>";
							echo "<div class='col-xs-12 col-sm-3 {$sReportStyleClass}'>{$oMileage->sReason}</div>";
							echo "<div class='col-xs-12 col-sm-7 {$sReportStyleClass}'>";
							echo "<a href='" . ADMIN_DIR . "/MileageMaintenance.php{$sQueryString}'>";  
							echo $oMileage->nMileage;
							echo "</a>";
							echo "</div>";
							echo "</div>";
						}
	
					}
					//Alternate the report style
					setReportStyleClass($sReportStyleClass);
	
					$sQueryString = "?MILEAGE_YEAR={$nYear}"; 
	
					echo "<div class='row {$sReportStyleClass}'>";
					echo "<div class='col-xs-12 col-sm-2 {$sReportStyleClass}'>{$nYear}</div>";
					echo "<div class='col-xs-12 col-sm-3 {$sReportStyleClass}'>Total</div>";
					echo "<div class='col-xs-12 col-sm-7 {$sReportStyleClass}'>";
					echo "<a href='" . ADMIN_DIR . "/MileageMaintenance.php{$sQueryString}'>";  
					echo $nYearlyMileage;
					echo "</a>";
					echo "</div>";
					echo "</div>";
				}
				else
				{
					//ERROR: Failed to retrieve Mileages
				}
			}
	
			?>
		</div>
	</div>
	
</form>
</body>
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

<?php
/*
 ********************************************************************************
 * buildMileageData()
 * 
 * This function builds a table pf Mileage Data
 ********************************************************************************
*/
function buildMileageData(&$oMileage)
{

	if ($oMileage->nCDID > 0)
	{
		$oCD = new CD();
		$oCD->nCDID = $oMileage->nCDID;
		if ($oCD->getCD())
		{
			$sCDName = $oCD->aCDRecords[0]->sCDName;
			
			if (empty($oMileage->sUPC))
			{
				$sUPC = $oCD->aCDRecords[0]->sUPC; 
			}
			else
			{
				$sUPC = $oMileage->sUPC; 
			}
			
		} 
		else
		{
			//ERROR RETRIEVING CD
		}
	}
	else
	{
		$sUPC = $oMileage->sUPC; 
	}
	

	echo "<table border='1' width='750px'>";	
	echo "<tr>";
	echo "<th width='250px'>Mileage Name</td>";
	echo "<td width='500px'>{$oMileage->sMileageName}</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<th>CD</td>";
	echo "<td>{$oCD->aCDRecords[0]->sCDName}</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<th>UPC</td>";
	echo "<td>{$sUPC}</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<th>Run Time</td>";
	echo "<td>{$oMileage->sRunTime}</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<th>ISRC</td>";
	echo "<td>{$oMileage->sISRC}</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<th>Catalog #</td>";
	echo "<td>{$oMileage->sCatalogNumber}</td>";
	echo "</tr>";
	echo "</table>";
}
?>