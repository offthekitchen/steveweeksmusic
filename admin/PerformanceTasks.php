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
2026-07-30	Migrated to new Datalayer Performance/PerformanceTask repositories
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
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");
include_once(DATALAYER_DIR . "/PerformanceTask.php");
include_once(DATALAYER_DIR . "/PerformanceTaskRepository.php");
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");
include_once(DATALAYER_DIR . "/Tour.php");
include_once(DATALAYER_DIR . "/TourRepository.php");
 	 
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
	
	$performanceRepo = new \Datalayer\PerformanceRepository();
	$performanceTaskRepo = new \Datalayer\PerformanceTaskRepository();
	$revenueRepo = new \Datalayer\RevenueRepository();
	$tourRepo = new \Datalayer\TourRepository();
	
	?>

	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
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
							renderPerformanceTaskList(
								findFuturePerformancesByFlag($performanceRepo, 'contract', 'N')
							);
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
							renderPerformanceTaskList(
								findFuturePerformancesByFlag($performanceRepo, 'airfare', 'N')
							);
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
							renderPerformanceTaskList(
								findFuturePerformancesByFlag($performanceRepo, 'hotel', 'N')
							);
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
							renderPerformanceTaskList(
								findFuturePerformancesByFlag($performanceRepo, 'rentalCar', 'N')
							);
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
							renderUnpaidPerformanceList(
								findUnpaidPerformances($performanceRepo, $revenueRepo)
							);
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
							$aPerformanceTaskRecords = $performanceTaskRepo->find();
							if (!empty($aPerformanceTaskRecords))
							{
								foreach($aPerformanceTaskRecords as $performanceTask)
								{
									if($performanceTask->complete){
										echo "<del> ";
									}
									if(!empty($performanceTask->performanceId)){
										$performance = $performanceRepo->findById((int) $performanceTask->performanceId);
										if ($performance) {
											echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID={$performanceTask->performanceId}'>{$performance->name} ({$performance->locationCity})</A>: ";
										}
									}
									if(!empty($performanceTask->tourId)){
										$tour = $tourRepo->findById((int) $performanceTask->tourId);
										if ($tour) {
											echo "<A HREF='" . ADMIN_DIR . "/TourMaintenance.php?ID={$performanceTask->tourId}'>{$tour->name}</A>: ";
										}
									}
									echo " <A HREF='" . ADMIN_DIR . "/PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$performanceTask->id}'>{$performanceTask->description}</A>";
									if($performanceTask->complete){
										echo "</del>";
									}										
									echo "<BR>";
								}
							}
							else
							{
								echo "NONE";
							}
							?>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>

<?php
/**
 * Future performances matching a travel/contract flag value.
 * TODO: PerformanceRepository has no contract/airfare/hotel/rentalCar keys; filtered in PHP.
 *
 * @return \Datalayer\Performance[]
 */
function findFuturePerformancesByFlag(
	\Datalayer\PerformanceRepository $performanceRepo,
	string $field,
	string $value
): array {
	return array_values(array_filter(
		$performanceRepo->find(['future' => true]),
		static fn(\Datalayer\Performance $p): bool => ($p->$field ?? '') === $value
	));
}

/**
 * Past performances with booked amount but no performance-fee revenue.
 * TODO: PerformanceRepository has no unpaid key; composed via RevenueRepository lookup.
 *
 * @return \Datalayer\Performance[]
 */
function findUnpaidPerformances(
	\Datalayer\PerformanceRepository $performanceRepo,
	\Datalayer\RevenueRepository $revenueRepo
): array {
	$aPerformances = $performanceRepo->find(['future' => false]);
	return array_values(array_filter($aPerformances, static function (\Datalayer\Performance $performance) use ($revenueRepo): bool {
		if (empty($performance->bookedAmount) || $performance->bookedAmount <= 0) {
			return false;
		}
		$aRevenues = $revenueRepo->find([
			'performanceId' => $performance->id,
			'revenueTypeId' => REVENUE_TYPE_PERFORMANCE_FEE,
		]);
		return empty($aRevenues);
	}));
}

/** @param \Datalayer\Performance[] $aPerformances */
function renderPerformanceTaskList(array $aPerformances): void
{
	if (empty($aPerformances)) {
		echo "NONE";
		return;
	}
	foreach ($aPerformances as $performance) {
		echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID=" . $performance->id . "'>" . $performance->name . "</A> "
			. $performance->performanceDate . " "
			. $performance->locationCity . ", " . $performance->locationState . "<BR>";
	}
}

/** @param \Datalayer\Performance[] $aPerformances */
function renderUnpaidPerformanceList(array $aPerformances): void
{
	if (empty($aPerformances)) {
		echo "NONE";
		return;
	}
	foreach ($aPerformances as $performance) {
		echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID=" . $performance->id . "'>" . $performance->name . "</A> "
			. $performance->performanceDate . " "
			. $performance->locationCity . ", " . $performance->locationState;
		echo " - $" . $performance->bookedAmount . "<BR>";
	}
}
?>
