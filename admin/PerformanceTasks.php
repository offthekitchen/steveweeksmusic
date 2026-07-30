<?php
/*
*******************************************************************
PerformanceTasks.php
This PHP file defines the Performance Tasks Page
NOTES
Date        Change
-------------------------------------------------------------
2017-01-28	Made Responsive
2022-03-15	Added Misc Performance Tasks
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
 	 
 $sActiveMenuItem = PERFORMANCES_ACTIVE;	
 $sPageName ="Performance Tasks";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<!-- DIV used for Image Preview Popup -->
	<div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15" id="preview_div"></div>
	<div class="container-fluid">
	<?php
	
	//include Performance Class		
 	include_once (CLASS_DIR . "/class_Performance.php");
	
 	//include Performance Task Class		
 	include_once (CLASS_DIR . "/class_PerformanceTask.php");

	//Array of Performance records from the DB
	global $aPerformanceRecords;
	
	?>

	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
<?php	
	//Instantiate needed objects
	$oPerformances = new Performance();
	
	?>	
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Performance Tasks
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroupTitle">
							CONTRACT/LOA NEEDED
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroup">
							<?php
							$oPerformances = new Performance();
							$oPerformances->sContract = "N";
							$oPerformances->bFuturePerformances = TRUE;
							if($oPerformances->getPerformance())
							{
								if(sizeof($oPerformances->aPerformanceRecords) > 0)
								{
									foreach($oPerformances->aPerformanceRecords as $oPerformance)
									{
										echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID=" . $oPerformance->nPerformanceID . "'>" . $oPerformance->sPerformanceName . "</A> " .  $oPerformance->dtPerformanceDate . " " .  $oPerformance->sLocationCity . ", " .  $oPerformance->sLocationState . "<BR>";
									}
								}
								else
								{
									echo "NONE";
								}
							}
							?>
					</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroupTitle">
							NEED TO BOOK AIRFARE
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroup">
							<?php
							$oPerformances = new Performance();
							$oPerformances->sAirfare = "N";
							$oPerformances->bFuturePerformances = TRUE;
							if($oPerformances->getPerformance())
							{
								if (sizeof($oPerformances->aPerformanceRecords) > 0)
								{
									foreach($oPerformances->aPerformanceRecords as $oPerformance)
									{
										echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID=" . $oPerformance->nPerformanceID . "'>" . $oPerformance->sPerformanceName . "</A> " .  $oPerformance->dtPerformanceDate . " " .  $oPerformance->sLocationCity . ", " .  $oPerformance->sLocationState . "<BR>";
									}
								}
								else
								{
									echo "NONE";
								}
							}
							?>
					</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroupTitle">
							NEED TO BOOK HOTEL
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroup">
							<?php
							$oPerformances = new Performance();
							$oPerformances->sHotel = "N";
							$oPerformances->bFuturePerformances = TRUE;
							if($oPerformances->getPerformance())
							{
								if (sizeof($oPerformances->aPerformanceRecords) > 0)
								{
									foreach($oPerformances->aPerformanceRecords as $oPerformance)
									{
										echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID=" . $oPerformance->nPerformanceID . "'>" . $oPerformance->sPerformanceName . "</A> " .  $oPerformance->dtPerformanceDate . " " .  $oPerformance->sLocationCity . ", " .  $oPerformance->sLocationState . "<BR>";
									}
								}
								else
								{
									echo "NONE";
								}
							}
							?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroupTitle">
							NEED TO BOOK RENTAL CAR
					</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroup">
							<?php
							$oPerformances = new Performance();
							$oPerformances->sRentalCar = "N";
							$oPerformances->bFuturePerformances = TRUE;
							if($oPerformances->getPerformance())
							{
								if (sizeof($oPerformances->aPerformanceRecords) > 0)
								{
									foreach($oPerformances->aPerformanceRecords as $oPerformance)
									{
										echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID=" . $oPerformance->nPerformanceID . "'>" . $oPerformance->sPerformanceName . "</A> " .  $oPerformance->dtPerformanceDate . " " .  $oPerformance->sLocationCity . ", " .  $oPerformance->sLocationState . "<BR>";
									}
								}
								else
								{
									echo "NONE";
								}
							}
							?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroupTitle">
							HAVEN'T BEEN PAID
					</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroup">
							<?php
							$oPerformances = new Performance();
							$oPerformances->bPaid = FALSE;
							$oPerformances->bFuturePerformances = FALSE;
							if($oPerformances->getPerformance())
							{
								if (sizeof($oPerformances->aPerformanceRecords) > 0)
								{
									foreach($oPerformances->aPerformanceRecords as $oPerformance)
									{
										echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID=" . $oPerformance->nPerformanceID . "'>" . $oPerformance->sPerformanceName . "</A> " .  $oPerformance->dtPerformanceDate . " " .  $oPerformance->sLocationCity . ", " .  $oPerformance->sLocationState;
										echo " - $" . $oPerformance->nBookedAmount . "<BR>";
									}
								}
								else
								{
									echo "NONE";
								}
							}
							?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroupTitle">
					MISC
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 FieldGroup">

							<?php
							$oPerformanceTasks = new PerformanceTask();
							if($oPerformanceTasks->getPerformanceTask())
							{
								if (sizeof($oPerformanceTasks->aPerformanceTaskRecords) > 0)
								{
									foreach($oPerformanceTasks->aPerformanceTaskRecords as $oPerformanceTask)
									{
										if($oPerformanceTask->bComplete){
											echo "<del> ";
										}
										if(!empty($oPerformanceTask->nPerformanceID)){
											echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID={$oPerformanceTask->nPerformanceID}'>{$oPerformanceTask->sPerformanceName} ({$oPerformanceTask->sLocationCity})</A>: ";
										}
										if(!empty($oPerformanceTask->nTourID)){
											echo "<A HREF='" . ADMIN_DIR . "/TourMaintenance.php?ID={$oPerformanceTask->nTourID}'>{$oPerformanceTask->sTourName}</A>: ";
										}
										echo " <A HREF='" . ADMIN_DIR . "/PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTask->nPerformanceTaskID}'>{$oPerformanceTask->sDescription}</A>";
										if($oPerformanceTask->bComplete){
											echo "</del>";
										}										
										echo "<BR>";
									}
								}
								else
								{
									echo "NONE";
								}
							}
							?>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
