<?php
/*
*******************************************************************
TourMaintenance.php
This PHP file defines the Maintenance page for 
managing Tours.
NOTES
Date        Change
-------------------------------------------------------------
2016-11-24 	Created Responsive version
2016-12-29	Improved Responsivity for phone
2017-08-03	Improved Responsivity
2019-11-05	Changed Datepickers to use common function
2020-02-01  Added Notes
2022-03-15	Added Task Button and Tasks
2022-07-04	Added strikeout for complete tasks
2026-07-30	Migrated Tour to new Datalayer repository; tasks and performances via repositories
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
include_once(DATALAYER_DIR . "/Tour.php");
include_once(DATALAYER_DIR . "/TourRepository.php");
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");
include_once(DATALAYER_DIR . "/PerformanceTask.php");
include_once(DATALAYER_DIR . "/PerformanceTaskRepository.php");
include_once(DATALAYER_DIR . "/Expense.php");
include_once(DATALAYER_DIR . "/ExpenseRepository.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of Tour records from the DB
$aTourRecords = [];
$aTourPerformances = [];
$aPotentialPerformances = [];
$aPerformanceTasks = [];
	 
$sActiveMenuItem = PERFORMANCES_ACTIVE;	
$sPageName = "Tour Maintenance";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

<div class="container-fluid">		
<form name="TourMaint" action="TourMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$tourRepo = new \Datalayer\TourRepository();
		$performanceRepo = new \Datalayer\PerformanceRepository();
		$performanceTaskRepo = new \Datalayer\PerformanceTaskRepository();
		$expenseRepo = new \Datalayer\ExpenseRepository();
		$thisTour = new \Datalayer\Tour();
		$form = new Form();

		//Get the ID query string parameter
		$nThisTourID = $_REQUEST['ID'] ?? null;

		try {
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisTourID) && $nThisTourID !== '')
		{
			$entity = $tourRepo->findById((int) $nThisTourID);

			if ($entity) {
				$thisTour = $entity;
				$aTourRecords = [$entity];
				loadTour($thisTour, $form);

				$form->sMessage = "Update record.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;
			} else {
				$form->sMessage = "Tour record not found.";
				$form->nMessageType = MESSAGE_TYPE_WARNING;
				$form->nFormMode = FORM_MODE_NEW;
			}
		}
		else
		{
			//Based on which button was selected, perform processing necessary 
			//before the page is rendered
			// **************
			// *   ADD      *
			// **************
		 	if (isset($_POST["btnAdd"])) 
			{
				buildTourObject($thisTour);
				
				if ($tourRepo->insert($thisTour))			
				{
					$thisTour = $tourRepo->findById((int) $thisTour->id) ?? $thisTour;
					$aTourRecords = [$thisTour];
					loadTour($thisTour, $form);
					$nThisTourID = $thisTour->id;

					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Tour Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
				buildTourObject($thisTour);
				
				if ($tourRepo->update($thisTour))			
				{
					if (!empty($thisTour->id)) {
						$tourPerformances = $performanceRepo->find(['tourId' => (int) $thisTour->id]);
						foreach ($tourPerformances as $oTourPerformance) {
							$sCheckboxName = "chkPerformance{$oTourPerformance->id}";
							if (!isset($_POST[$sCheckboxName]) || $_POST[$sCheckboxName] != 'PerformanceAssociated') {
								$oTourPerformance->tourId = null;
								$performanceRepo->update($oTourPerformance);
							}
						}

						if (!empty($thisTour->startDate) && !empty($thisTour->endDate)) {
							$potentialPerformances = $performanceRepo->find([
								'startDate' => $thisTour->startDate,
								'endDate' => $thisTour->endDate,
								'excludeTourId' => (int) $thisTour->id,
							]);
							foreach ($potentialPerformances as $oPotentialPerformance) {
								$sCheckboxName = "chkPerformance{$oPotentialPerformance->id}";
								if (isset($_POST[$sCheckboxName]) && $_POST[$sCheckboxName] == 'PerformanceAssociated') {
									$oPotentialPerformance->tourId = (int) $thisTour->id;
									$performanceRepo->update($oPotentialPerformance);
								}
							}
						}
					}

					$thisTour = $tourRepo->findById((int) $thisTour->id) ?? $thisTour;
					$aTourRecords = [$thisTour];
					loadTour($thisTour, $form);
					$nThisTourID = $thisTour->id;

					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Tour Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
				buildTourObject($thisTour);

				$deleteError = null;
				if (!empty($thisTour->id)) {
					$relatedExpenses = $expenseRepo->find(['tourId' => (int) $thisTour->id]);
					if (sizeof($relatedExpenses) > 0) {
						$deleteError = "TOUR013 - Can not delete Tour because it has ";
						$deleteError .= "<A HREF='" . ADMIN_DIR . "/ExpenseMaintenance.php?TOUR_ID={$thisTour->id}'>";
						$deleteError .= sizeof($relatedExpenses) . " expenses.</A>";
					} else {
						$relatedPerformances = $performanceRepo->find(['tourId' => (int) $thisTour->id]);
						if (sizeof($relatedPerformances) > 0) {
							$deleteError = "TOUR018 - Can not delete Tour because it has " . sizeof($relatedPerformances) . " performances.";
						}
					}
				}

				if ($deleteError !== null) {
					$form->sMessage = $deleteError;
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_EDIT;
				} else if (!empty($thisTour->id) && $tourRepo->delete((int) $thisTour->id)) {
					clearFormFields($form);
					
					$form->sMessage = "Tour Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED";
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_EDIT;					
				}
			
			}
			// **************
			// *   SEARCH   *
			// **************
			else if (isset($_POST["btnSearch"])) 
			{
				buildTourObject($thisTour);

				$criteria = [
					'id' => $thisTour->id,
					'name' => $thisTour->name,
					'fuzzyName' => true,
					'notes' => $thisTour->notes,
				];

				$includeDate = getTourDateFromRequest('TourIncludeDate');
				if (!empty($includeDate)) {
					$criteria['includeDate'] = $includeDate;
				}

				$aTourRecords = $tourRepo->find($criteria);

				if (sizeof($aTourRecords) < 1)
				{
					$form->sMessage = "No Tour records found matching search criteria";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}			
				else if (sizeof($aTourRecords) == 1)
				{
					$thisTour = $aTourRecords[0];
					loadTour($thisTour, $form);
					$nThisTourID = $thisTour->id;

					$form->sMessage = "One Tour record found.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else 
				{
					$form->sMessage = "Select Tour record to edit from results list below.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_SELECT;			
				}
			}
			// *************
			// *   CLEAR   *
			// *************
			else if (isset($_POST["btnClear"])) 
			{	
				clearFormFields($form);
				
				$form->sMessage = "Search for records or Add new record";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_NEW;			
			}
			// *************
			// *   COPY  *
			// *************
			else if (isset($_POST["btnCopy"])) 
			{
				$_POST['hdnTourID'] = NULL;
				
				$form->sMessage = "Search for records or Add new record";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_NEW;	
			}			
			// ***************
			// *  1st TIME   *
			// ***************
			else 
			{
				$form->sMessage = "Search for records or Add new record";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_NEW;			
			}	

		}

		if (!empty($thisTour->id)) {
			$aTourPerformances = $performanceRepo->find(['tourId' => (int) $thisTour->id]);
			if (!empty($thisTour->startDate) && !empty($thisTour->endDate)) {
				$aPotentialPerformances = $performanceRepo->find([
					'startDate' => $thisTour->startDate,
					'endDate' => $thisTour->endDate,
					'excludeTourId' => (int) $thisTour->id,
				]);
			}
			$aPerformanceTasks = $performanceTaskRepo->find(['tourId' => (int) $thisTour->id]);
		}

		} catch (\Throwable $e) {
			$form->sMessage = $e->getMessage();
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->nFormMode = FORM_MODE_NEW;
		}

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnTourID" value="<?php echo $_POST['hdnTourID'] ?? ''?>" />	

	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
				Tour Maintenance
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 Buttons">
					<?php
					$form->renderButtons();
					?>
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
	<?php	
	if ($form->nFormMode == FORM_MODE_SELECT)
	{
	 ?>
	
	<div class="row">
	
		<div class="col-xs-12">
		<?php
				
			$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
	
			//Header row for Results
			echo "<div class='row result-header'>";
			echo "	<div class='hidden-xs col-sm-4 result-header'>Tour Name</div>";
			echo "	<div class='hidden-xs col-sm-4 result-header'>Start Date</div>";
			echo "	<div class='hidden-xs col-sm-4 result-header'>End Date</div>";
			echo "	<div class='visible-xs col-xs-12 result-header'>Tours</div>";
			echo "</div>";
	
					
			foreach($aTourRecords as $oTourRecord)
			{
				if ($sResultStyleClass == RESULT_STYLE_CLASS)
				{
					$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
				}
				else
				{
					$sResultStyleClass = RESULT_STYLE_CLASS;
				}
				echo "<div class=\"row {$sResultStyleClass}\">";
				echo "	<div class='col-xs-12 col-sm-4 {$sResultStyleClass}'><A HREF='./TourMaintenance.php?ID={$oTourRecord->id}'>{$oTourRecord->name}</a></div>";
				echo "	<div class='hidden-xs col-sm-4 {$sResultStyleClass}'><A HREF='./TourMaintenance.php?ID={$oTourRecord->id}'>{$oTourRecord->startDate}</a></div>";
				echo "	<div class='hidden-xs col-sm-4 {$sResultStyleClass}'><A HREF='./TourMaintenance.php?ID={$oTourRecord->id}'>{$oTourRecord->endDate}</a></div>";
				echo "</div>";
			}
			?>
			<div class="row" style="height: 20px;"></div>
		
	<?php
	}
	else
	{
	?>
	
		<div class="row"> 
			<div class="col-xs-12">
				<?php
				if (($_POST['hdnTourID'] ?? 0) > 0)
				{
					echo "<a href='". ADMIN_DIR . "/ExpenseMaintenance.php?TOUR_ID={$thisTour->id}";
					echo "' class='secondaryLinkButton'>Edit Expenses</a>";
					echo "<span class='hidden-xs'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/ExpenseMaintenance.php?TOUR_ID={$thisTour->id}";
					echo "&ACTION=ADD_EXPENSE' class='secondaryLinkButton'>Enter Expense</a>";
					echo "<span class='hidden-xs'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/TourEmails.php?TOUR_ID={$thisTour->id}' target='_blank' class='secondaryLinkButton'>";
					echo "Tour Emails</a>";
					echo "<span class='hidden-xs'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/TourReport.php?ID={$thisTour->id}";
					echo "' class='secondaryLinkButton'>Tour Report</a>";
					echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
					echo "<br class='visible-xs'>";
					echo "<a href='" . ADMIN_DIR . "/PerformanceTaskMaintenance.php?TOUR_ID={$thisTour->id}&TOUR_NAME={$thisTour->name}' class='secondaryLinkButton'>Add Task</a>";

				}
				?>
			</div>
				<div class="col-xs-12 FieldGroupTitle">
					DETAILS
				</div>
				<?php
					if (!empty($thisTour->id) && sizeof($aPerformanceTasks) > 0) {
						echo "<div class=\"col-xs-12 FieldGroup\">";
						echo "TASKS:";
						foreach ($aPerformanceTasks as $oPerformanceTaskRecord) {
							if ($oPerformanceTaskRecord->complete) {
								echo "<del> ";
							}
							echo "<div><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->id}'>{$oPerformanceTaskRecord->description}</A></div>";
							if ($oPerformanceTaskRecord->complete) {
								echo "</del> ";
							}
						}
						echo "</div>";
					}
					?>
				<div class="col-xs-12 FieldGroup">
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							START DATE:<BR /> 
							<?php	  
							renderDatePicker("TourStartDate", $_POST['TourStartDate'] ?? ($thisTour->startDate ?? ''));
							?>	
							<br><br>							    
						</div>								
						<div class="col-xs-12 col-sm-4">
							END DATE:<BR /> 
							<?php	  
							renderDatePicker("TourEndDate", $_POST['TourEndDate'] ?? ($thisTour->endDate ?? ''));
							?>		
							<br><br>						  
						</div>								
						<div class="col-xs-12 col-sm-4">
							INCLUDE DATE:<BR /> 
							<?php	  
							renderDatePicker("TourIncludeDate", $_POST['TourIncludeDate'] ?? '');
							?>	
							<br><br>							    
						</div>								
						<div class="col-xs-12">
							TOUR NAME: <input type="text" name="txtTourName" value="<?php echo $_POST['txtTourName'] ?? ''; ?>" size="60" />&nbsp;&nbsp;								
						</div>	
						<div class="col-xs-12">
							TOUR NOTES
							<textarea name="txtNotes" cols=60 rows=5 ><?php echo $_POST['txtNotes'] ?? ''; ?></textarea>
						</div>							
					</div>
				</div>	
				<div class="col-xs-12 FieldGroupTitle">
					PERFORMANCES
				</div>
				<div class="col-xs-12 FieldGroup">
					<div class="row">
						<div class="col-xs-12">
							<?php
							if (!empty($thisTour->id))
							{
								echo "<div class='row result-header'>";
								echo "<div class='hidden-xs hidden-sm col-md-1 result-header'></div>";
								echo "<div class='col-xs-4 col-md-2 result-header'>Name</div>";
								echo "<div class='col-xs-2 col-md-2 result-header'>Date</div>";
								echo "<div class='hidden-xs hidden-sm col-md-1 result-header'>Time</div>";
								echo "<div class='col-xs-6 col-md-3 result-header'>Location</div>";
								echo "<div class='hidden-xs hidden-sm col-md-3 result-header'></div>";
								echo "</div>";

								foreach ($aTourPerformances as $oPerformance)
								{
									$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

									echo "<div class=\"row {$sResultStyleClass}\">";
									echo "<div class='hidden-xs hidden-sm col-md-1 {$sResultStyleClass}'>";
									echo "<input type='checkbox' name='chkPerformance{$oPerformance->id}'";
									echo " value='PerformanceAssociated' checked >";
									echo "</div>";
									echo "<div class='col-xs-4 col-md-2  {$sResultStyleClass}'>";
									echo "<a href='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID={$oPerformance->id}'>";
									echo "{$oPerformance->name}</a></div>";
									echo "<div class='col-xs-2 col-md-2 {$sResultStyleClass}'>{$oPerformance->performanceDate}</div>";
									echo "<div class='hidden-xs hidden-sm col-md-1 {$sResultStyleClass}'>{$oPerformance->performanceTime}</div>";
									echo "<div class='col-xs-6 col-md-3 {$sResultStyleClass}'>{$oPerformance->location}, ";
									echo "{$oPerformance->locationCity}, {$oPerformance->locationState }</div>";
									echo "<div class='hidden-xs hidden-sm col-md-3 {$sResultStyleClass}'>";
									if ($oPerformance->performanceDate > $thisTour->endDate || $oPerformance->performanceDate < $thisTour->startDate)
									{
										echo "<font color='red'>Performance date outside of tour dates</font>";
									}
									echo "</div>";
									echo "</div>";
								}

								foreach ($aPotentialPerformances as $oPerformance)
								{
									$sResultStyleClass = RESULT_STYLE_CLASS;

									echo "<div class=\"row {$sResultStyleClass}\">";
									echo "<div class='hidden-xs hidden-sm col-md-1 {$sResultStyleClass}'>";
									echo "<input type='checkbox' name='chkPerformance{$oPerformance->id}'";
									echo " value='PerformanceAssociated' >";
									echo "</div>";
									echo "<div class='hidden-xs hidden-sm col-md-2  {$sResultStyleClass}'>";
									echo "<a href='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID={$oPerformance->id}'>";
									echo "{$oPerformance->name}</a></div>";
									echo "<div class='hidden-xs hidden-sm col-md-2 {$sResultStyleClass}'>{$oPerformance->performanceDate}</div>";
									echo "<div class='hidden-xs hidden-sm col-md-1 {$sResultStyleClass}'>{$oPerformance->performanceTime}</div>";
									echo "<div class='hidden-xs hidden-sm col-md-3 {$sResultStyleClass}'>{$oPerformance->location}, ";
									echo "{$oPerformance->locationCity}, {$oPerformance->locationState}</div>";
									echo "<div class='hidden-xs hidden-sm col-md-3 {$sResultStyleClass}'>";
									if (!empty($oPerformance->tourId))
									{
										echo "<a href='" . ADMIN_DIR . "/TourMaintenance.php?ID={$oPerformance->tourId}'>";
										echo "<font color='red'>Performance already associated with tour ";
										echo "{$oPerformance->tourId}</font></a>";
									}
									echo "</div>";
									echo "</div>";
								}
							}
						?>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class=" col-xs-3 FormFieldNoEdit">
							ID: <?php echo $_POST['hdnTourID'] ?? ''; ?>						
						</div>
						<div class=" col-xs-9 FormFieldNoEdit">
							LAST UPDATED: <?php echo $_POST['txtLastUpdate'] ?? ''; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 Buttons">
				<?php
				$form->renderButtons();
				?>
			</div>
		</div>
		<div class="row">
			<dic class="col-xs-12 FormMessage">
				<?php
				$form->renderFormMessage();
				?>
			</div>
		</div>
	</div>
	<?php
	}
	?>
	
</form>
</div>
<?php		


/*
 ********************************************************************************
 * buildTourObject
 * 
 * This function loads builds a Tour object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildTourObject(\Datalayer\Tour $Tour)
{
	$id = $_POST['hdnTourID'] ?? null;
	$Tour->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$Tour->name = html_entity_decode($_POST['txtTourName'] ?? '', ENT_QUOTES);
	$Tour->notes = html_entity_decode($_POST['txtNotes'] ?? '', ENT_QUOTES);

	$startDate = getTourDateFromRequest('TourStartDate');
	if (!empty($startDate)) {
		$Tour->startDate = $startDate;
	}

	$endDate = getTourDateFromRequest('TourEndDate');
	if (!empty($endDate)) {
		$Tour->endDate = $endDate;
	}
}


/*
 ********************************************************************************
 * getTourDateFromRequest
 ********************************************************************************
*/
function getTourDateFromRequest(string $fieldName): ?string
{
	$dtValue = $_REQUEST[$fieldName] ?? "";
	if ($dtValue > "0000-00-00") {
		$dtValue = $_POST[$fieldName] ?? "";
	}
	if ($dtValue > "0000-00-00") {
		return $dtValue;
	}
	return null;
}


/*
 ********************************************************************************
 * loadTour
 * 
 * This function loads the form field array from a populated Tour object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadTour(\Datalayer\Tour $Tour, $form)
{
	if (!is_null($Tour->id))
	{
		$_POST['hdnTourID'] = $Tour->id;
		$_POST['txtTourName'] = htmlentities($Tour->name ?? '', ENT_QUOTES);
		$_POST['TourStartDate'] = htmlentities($Tour->startDate ?? '', ENT_QUOTES);
		$_POST['TourEndDate'] = htmlentities($Tour->endDate ?? '', ENT_QUOTES);
		$_POST['txtNotes'] = htmlentities($Tour->notes ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($Tour->lastUpdate ?? '', ENT_QUOTES);
	}
}


/*
 ********************************************************************************
 * clearFormFields
 * 
 * This function clears the form fields
 ********************************************************************************
*/
function clearFormFields($form)
{
	$_POST = array();
}

?>

</body>
<script type="text/javascript">
<!--
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

	//Reason is a required Field
    if ( document.TourMaint.txtTourName.value == "" )
    {
        sErrorMessage += "Tour Name Required\n";
        bValid = false;
    }

	//Tour Start Date is a required Field
    if ( document.TourMaint.TourStartDate.value == "" || document.TourMaint.TourStartDate.value == "0000-00-00" )
    {
        sErrorMessage += "Tour Start Date Required\n";
        bValid = false;
    }

	//Tour End Date is a required Field
    if ( document.TourMaint.TourEndDate.value == "" || document.TourMaint.TourEndDate.value == "0000-00-00" )
    {
        sErrorMessage += "Tour End Date Required\n";
        bValid = false;
    }

	//Tour Start Date can not be greater than the end date
    if (document.TourMaint.TourStartDate.value > document.TourMaint.TourEndDate.value)
    {
        sErrorMessage += "Start Date Can Not Be Greater Than End Date\n";
        bValid = false;
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
