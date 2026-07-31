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
2026-07-30	Migrated to new Datalayer Mileage repository
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
include_once(DATALAYER_DIR . "/Mileage.php");
include_once(DATALAYER_DIR . "/MileageRepository.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once (CLASS_DIR . "/tc_calendar.php");

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
	$mileageRepo = new \Datalayer\MileageRepository();

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
				$aMileageRecords = $mileageRepo->find(['mileageYear' => $nYear]);
	
				if (!empty($aMileageRecords))
				{
				
					foreach ($aMileageRecords as $mileage)
					{
						$nYearlyMileage += $mileage->mileage;
	
						if($nStartYear == $nEndYear)
						{
							$sQueryString = "?MILEAGE_ID={$mileage->id}"; 
						
							//Alternate the report style
							setReportStyleClass($sReportStyleClass);
							
							echo "<div class='row {$sReportStyleClass}'>";
							echo "<div class='col-xs-12 col-sm-2 {$sReportStyleClass}'>{$mileage->mileageDate}</div>";
							echo "<div class='col-xs-12 col-sm-3 {$sReportStyleClass}'>{$mileage->reason}</div>";
							echo "<div class='col-xs-12 col-sm-7 {$sReportStyleClass}'>";
							echo "<a href='" . ADMIN_DIR . "/MileageMaintenance.php{$sQueryString}'>";  
							echo $mileage->mileage;
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
			}
	
			?>
		</div>
	</div>
	
</form>
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
