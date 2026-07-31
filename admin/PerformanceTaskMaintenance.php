<?php
/*
*******************************************************************
PerformanceTaskMaintenance.php
This PHP file defines the Maintenance page for managing PerformanceTask.
NOTES
Date        Change
-------------------------------------------------------------
2020-03-15	Created
2026-07-30	Migrated to new Datalayer PerformanceTask repository
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
include_once(DATALAYER_DIR . "/PerformanceTask.php");
include_once(DATALAYER_DIR . "/PerformanceTaskRepository.php");
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");
include_once(DATALAYER_DIR . "/Tour.php");
include_once(DATALAYER_DIR . "/TourRepository.php");

//Require the Class for the calendar picker
require_once (CLASS_DIR . "/tc_calendar.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of PerformanceTask records from the DB
$aPerformanceTaskRecords = [];

$sActiveMenuItem = PERFORMANCES_ACTIVE;	
$sPageName = "Performance Task Maiintenance";
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
<form name="PerformanceTaskMaint" action="PerformanceTaskMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$performanceTaskRepo = new \Datalayer\PerformanceTaskRepository();
		$performanceRepo = new \Datalayer\PerformanceRepository();
		$tourRepo = new \Datalayer\TourRepository();
		$thisPerformanceTask = new \Datalayer\PerformanceTask();
		$form = new Form();

		//Get the ID query string parameter
		$nThisPerformanceTaskID = $_REQUEST['PERFORMANCE_TASK_ID'] ?? null;
        $nPerformanceID = $_REQUEST['PERFORMANCE_ID'] ?? null;
		$sPerformanceName = $_REQUEST['PERFORMANCE_NAME'] ?? null;

		$nTourID = $_REQUEST['TOUR_ID'] ?? null;
        $sTourName = $_REQUEST['TOUR_NAME'] ?? null;
       		
		try {
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisPerformanceTaskID) && $nThisPerformanceTaskID !== '')
		{
			$entity = $performanceTaskRepo->findById((int) $nThisPerformanceTaskID);

			if ($entity) {
				$thisPerformanceTask = $entity;
				$aPerformanceTaskRecords = [$entity];
				loadPerformanceTask($thisPerformanceTask, $form, $performanceRepo, $tourRepo);

				$form->sMessage = "Update record.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;
			} else {
				$form->sMessage = "PerformanceTask record not found.";
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
				buildPerformanceTaskObject($thisPerformanceTask);
				
				if ($performanceTaskRepo->insert($thisPerformanceTask))			
				{
					$thisPerformanceTask = $performanceTaskRepo->findById((int) $thisPerformanceTask->id) ?? $thisPerformanceTask;
					$aPerformanceTaskRecords = [$thisPerformanceTask];
					loadPerformanceTask($thisPerformanceTask, $form, $performanceRepo, $tourRepo);
					
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "PerformanceTask Added";
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
				buildPerformanceTaskObject($thisPerformanceTask);
				
				if ($performanceTaskRepo->update($thisPerformanceTask))			
				{
					$thisPerformanceTask = $performanceTaskRepo->findById((int) $thisPerformanceTask->id) ?? $thisPerformanceTask;
					$aPerformanceTaskRecords = [$thisPerformanceTask];
					loadPerformanceTask($thisPerformanceTask, $form, $performanceRepo, $tourRepo);
				
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "PerformanceTask Updated";
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
				buildPerformanceTaskObject($thisPerformanceTask);

				if (!empty($thisPerformanceTask->id) && $performanceTaskRepo->delete((int) $thisPerformanceTask->id))
				{
					clearFormFields($form);
					
					$form->sMessage = "PerformanceTask Deleted";
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
				buildPerformanceTaskObject($thisPerformanceTask);

				$criteria = [
					'id' => $thisPerformanceTask->id,
					'description' => $thisPerformanceTask->description,
					'fuzzyDescription' => true,
				];

				if (!empty($thisPerformanceTask->tourId)) {
					$criteria['tourId'] = $thisPerformanceTask->tourId;
				}
				if (!empty($thisPerformanceTask->performanceId)) {
					$criteria['performanceId'] = $thisPerformanceTask->performanceId;
				}

				$aPerformanceTaskRecords = $performanceTaskRepo->find($criteria);

				if (sizeof($aPerformanceTaskRecords) < 1)
				{
					$form->sMessage = "No PerformanceTask records found matching search criteria";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}			
				else if (sizeof($aPerformanceTaskRecords) == 1)
				{
					$thisPerformanceTask = $aPerformanceTaskRecords[0];
					loadPerformanceTask($thisPerformanceTask, $form, $performanceRepo, $tourRepo);

					$form->sMessage = "One PerformanceTask record found.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else 
				{
					$form->sMessage = "Select PerformanceTask record to edit from results list below.";
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
			// *   CANCEL  *
			// *************
			else if (isset($_POST["btnCancel"])) 
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
				copyFormFields($form);
				$_POST['hdnPerformanceTaskID'] = NULL;
				
				$form->sMessage = "Search for records or Add new record";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_NEW;	
			}				
			// ***************
			// *  1st TIME   *
			// ***************
			else 
			{
                if(!empty($nPerformanceID)){
                    $_POST['hdnPerformanceID'] = $nPerformanceID;
					if (empty($sPerformanceName)) {
						$performance = $performanceRepo->findById((int) $nPerformanceID);
						$sPerformanceName = $performance->name ?? '';
					}
                }
                if(!empty($nTourID)){
                    $_POST['hdnTourID'] = $nTourID;
					if (empty($sTourName)) {
						$tour = $tourRepo->findById((int) $nTourID);
						$sTourName = $tour->name ?? '';
					}
                }
				if(!empty($sPerformanceName)){
                    $_POST['hdnPerformanceName'] = $sPerformanceName;
                }
                if(!empty($sTourName)){
                    $_POST['hdnTourName'] = $sTourName;
                }

				$form->sMessage = "Search for records or Add new record";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_NEW;			
			}	

		}
		} catch (\Throwable $e) {
			$form->sMessage = $e->getMessage();
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->nFormMode = FORM_MODE_NEW;
		}

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnPerformanceTaskID" value="<?php echo $_POST['hdnPerformanceTaskID'] ?? ''?>" />	
    <input type="hidden" name="hdnPerformanceID" value="<?php echo $_POST['hdnPerformanceID'] ?? ''?>" />	
	<input type="hidden" name="hdnPerformanceName" value="<?php echo $_POST['hdnPerformanceName'] ?? ''?>" />		
    <input type="hidden" name="hdnTourID" value="<?php echo $_POST['hdnTourID'] ?? ''?>" />
	<input type="hidden" name="hdnTourName" value="<?php echo $_POST['hdnTourName'] ?? ''?>" />	

	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Performance Task Maintenance
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
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Description</div>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Performance</div>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Tour</div>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Complete</div>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Last Update</div>";
		echo "</div>";

				
		foreach($aPerformanceTaskRecords as $oPerformanceTaskRecord)
		{
			list($sPerformanceName, $sTourName) = getPerformanceTaskRelatedNames(
				$oPerformanceTaskRecord,
				$performanceRepo,
				$tourRepo
			);

			//Alternate the result style
			if ($sResultStyleClass == RESULT_STYLE_CLASS)
			{
				$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
			}
			else
			{
				$sResultStyleClass = RESULT_STYLE_CLASS;
			}
			echo "<div class='row {$sResultStyleClass}'>";	
			echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->id}'>{$oPerformanceTaskRecord->description}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->id}'>{$sPerformanceName}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->id}'>{$sTourName}</A></div>";
            if($oPerformanceTaskRecord->complete){
                echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->id}'>YES</A></div>";
            }
            else {
                echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->id}'>NO</A></div>";
            }
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->id}'>{$oPerformanceTaskRecord->lastUpdate}</A></div>";			
			echo "</div>";
		}
		?>
	
		<div class="row" style="height: 20px;"></div>
		</div>	
		
	<?php
	}
	else
	{
	?>
	
	<div class="row"> 
		<div class="col-xs-12">	
				<div class="col-xs-12 FieldGroup">
					<div class="row">
						<div class="col-xs-12 col-md-4">
								DESCRIPTION: <input type="text" name="txtDescription" value="<?php echo $_POST['txtDescription'] ?? ''; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-4">
								PERFORMANCE: <?php echo $_POST['hdnPerformanceName'] ?? ''; ?> 
						</div>
						<div class="col-xs-12 col-md-4">
								TOUR: <?php echo $_POST['hdnTourName'] ?? ''; ?>
						</div>
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkComplete" class="result-checkbox" value="COMPLETE"<?php if( !empty($_POST['chkComplete'])) {echo " checked ";}; ?> />
										</div>
										<div class="col-xs-9 col-sm-10 result-checkbox-text">
											COMPLETE
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>	
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="row FormFieldNoEdit">
					<div class ="col-xs-3">
						ID: <?php echo $_POST['hdnPerformanceTaskID'] ?? ''; ?>						
					</div>
					<div class ="col-xs-9 FormFieldNoEdit">
						LAST UPDATED: <?php echo $_POST['txtLastUpdate'] ?? ''; ?>
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
		</div>	
	
	<?php
	}
	?>
	
</form>
</div>
<?php		


/*
 ********************************************************************************
 * buildPerformanceTaskObject
 * 
 * This function loads builds a PerformanceTask object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildPerformanceTaskObject(\Datalayer\PerformanceTask $PerformanceTask)
{
	$id = $_POST['hdnPerformanceTaskID'] ?? null;
	$PerformanceTask->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$performanceId = $_POST['hdnPerformanceID'] ?? null;
	$PerformanceTask->performanceId = (!empty($performanceId) && is_numeric($performanceId)) ? (int) $performanceId : null;

	$tourId = $_POST['hdnTourID'] ?? null;
	$PerformanceTask->tourId = (!empty($tourId) && is_numeric($tourId)) ? (int) $tourId : null;

	$PerformanceTask->description = html_entity_decode($_POST['txtDescription'] ?? '', ENT_QUOTES);
	$PerformanceTask->complete = (($_POST['chkComplete'] ?? '') == "COMPLETE");
}


/*
 ********************************************************************************
 * loadPerformanceTask
 * 
 * This function loads the form field array from a populated PerformanceTask object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadPerformanceTask(
	\Datalayer\PerformanceTask $PerformanceTask,
	$form,
	\Datalayer\PerformanceRepository $performanceRepo,
	\Datalayer\TourRepository $tourRepo
) {
	if (!is_null($PerformanceTask->id))
	{
		$_POST['hdnPerformanceTaskID'] = $PerformanceTask->id;
        $_POST['hdnPerformanceID'] = $PerformanceTask->performanceId;
        $_POST['hdnTourID'] = $PerformanceTask->tourId;

		list($sPerformanceName, $sTourName) = getPerformanceTaskRelatedNames(
			$PerformanceTask,
			$performanceRepo,
			$tourRepo
		);
		$_POST['hdnPerformanceName'] = htmlentities($sPerformanceName, ENT_QUOTES);
		$_POST['hdnTourName'] = htmlentities($sTourName, ENT_QUOTES);

		$_POST['txtDescription'] = htmlentities($PerformanceTask->description ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($PerformanceTask->lastUpdate ?? '', ENT_QUOTES);
		$_POST['chkComplete'] = $PerformanceTask->complete;
	}
	else
	{
		foreach($_POST as $fieldName=>$fieldValue) {
			$_POST[$fieldName]= htmlentities(stripslashes($fieldValue));
		}
	}
}


/*
 ********************************************************************************
 * getPerformanceTaskRelatedNames
 ********************************************************************************
*/
function getPerformanceTaskRelatedNames(
	\Datalayer\PerformanceTask $PerformanceTask,
	\Datalayer\PerformanceRepository $performanceRepo,
	\Datalayer\TourRepository $tourRepo
): array {
	$sPerformanceName = '';
	$sTourName = '';

	if (!empty($PerformanceTask->performanceId)) {
		$performance = $performanceRepo->findById((int) $PerformanceTask->performanceId);
		$sPerformanceName = $performance->name ?? '';
	}
	if (!empty($PerformanceTask->tourId)) {
		$tour = $tourRepo->findById((int) $PerformanceTask->tourId);
		$sTourName = $tour->name ?? '';
	}

	return [$sPerformanceName, $sTourName];
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

/*
 ********************************************************************************
 * copyFormFields
 * 
 * This function copies the data from the form back into the form field array
 ********************************************************************************
*/
function copyFormFields($form)
{
	foreach($_POST as $fieldName=>$fieldValue) 
	{
		$_POST[$fieldName]= htmlentities(stripslashes($fieldValue));
	}
}

?>

</body>
<script type="text/javascript">
<!--
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";


	// Description is a required Field
    if ( document.PerformanceTaskMaint.txtDescription.value == '' )
    {
        sErrorMessage += "Description Required\n";
        bValid = false;
	}

    if ( document.PerformanceTaskMaint.hdnPerformanceID.value == '' && document.PerformanceTaskMaint.hdnTourID.value == '')
    {
        sErrorMessage += "Performance or Tour Required\n";
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
