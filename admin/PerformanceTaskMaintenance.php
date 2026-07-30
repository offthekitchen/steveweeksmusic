<?php
/*
*******************************************************************
PerformanceTaskMaintenance.php
This PHP file defines the Maintenance page for managing PerformanceTask.
NOTES
Date        Change
-------------------------------------------------------------
2020-03-15	Created
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

	<?php
	
	//include PerformanceTask Class		
 	include_once (CLASS_DIR . "/class_PerformanceTask.php");

	
	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");

 	//include Form Class		
 	include (CLASS_DIR . "/class_Form.php");

	//Array of PerformanceTask records from the DB
	global $aPerformanceTaskRecords;
	
	?>
	
<div class="container-fluid">		
<form name="PerformanceTaskMaint" action="PerformanceTaskMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$thisPerformanceTask = new PerformanceTask();
		$form = new Form();

		//Get the ID query string parameter
		$nThisPerformanceTaskID = $_REQUEST['PERFORMANCE_TASK_ID'];
        $nPerformanceID = $_REQUEST['PERFORMANCE_ID'];
		if(isset($_REQUEST['PERFORMANCE_NAME'])){
			$sPerformanceName = $_REQUEST['PERFORMANCE_NAME'];
		}

		$nTourID = $_REQUEST['TOUR_ID'];
        if(isset($_REQUEST['TOUR_NAME'])){
			$sTourName = $_REQUEST['TOUR_NAME'];
		}
       		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisPerformanceTaskID))
		{
						
			$thisPerformanceTask->nPerformanceTaskID = $nThisPerformanceTaskID;

			//Search the Database for records matching the search criteria			
			if ($thisPerformanceTask->getPerformanceTask())
			{
			
				//Records found
				if (sizeof($thisPerformanceTask->aPerformanceTaskRecords) > 0)
				{
									
					//Only One Record should be returned.  Add this to the form field array
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadPerformanceTask($thisPerformanceTask->aPerformanceTaskRecords[0], $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				else
				{
					//The record was not found
					$form->sMessage = "PerformanceTask record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisPerformanceTask->sErrorMessage;
				$form->nMessageType = MESSAGE_TYPE_ERROR;
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
				//Load values into DB array
				buildPerformanceTaskObject($thisPerformanceTask);
				
				//Insert record
				if ($thisPerformanceTask->insertPerformanceTask())			
				{

					//Load the form fields with the newly populated object
					loadPerformanceTask($thisPerformanceTask, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "PerformanceTask Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
				
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: {$thisPerformanceTask->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
			
				//Load values from form field array into DB object
				buildPerformanceTaskObject($thisPerformanceTask);
				
				//Update record
				if ($thisPerformanceTask->updatePerformanceTask())			
				{
				
					//reload PerformanceTask
					$thisPerformanceTask->getPerformanceTask();
				
					//Load the form fields with the newly populated DB object						
					loadPerformanceTask($thisPerformanceTask->aPerformanceTaskRecords[0], $form);
				
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "PerformanceTask Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - {$thisPerformanceTask->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildPerformanceTaskObject($thisPerformanceTask);				

				//Delete record
				if ($thisPerformanceTask->deletePerformanceTask())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "PerformanceTask Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: {$thisPerformanceTask->sErrorMessage}";
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_EDIT;					
				}
			
			}
			// **************
			// *   SEARCH   *
			// **************
			else if (isset($_POST["btnSearch"])) 
			{
				//Load Array of Search Values
				buildPerformanceTaskObject($thisPerformanceTask);

				//Search the Database for records matching the search criteria			
				if ($thisPerformanceTask->getPerformanceTask())
				{
					//No records found
					if(sizeof($thisPerformanceTask->aPerformanceTaskRecords) < 1)
					{
						$form->sMessage = "No PerformanceTask records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisPerformanceTask->aPerformanceTaskRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadPerformanceTask($thisPerformanceTask->aPerformanceTaskRecords[0], $form);			

						$form->sMessage = "One PerformanceTask record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select PerformanceTask record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisPerformanceTask->sErrorMessage;
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_NEW;
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
                }
                if(!empty($nTourID)){
                    $_POST['hdnTourID'] = $nTourID;
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

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnPerformanceTaskID" value="<?php echo $_POST['hdnPerformanceTaskID']?>" />	
    <input type="hidden" name="hdnPerformanceID" value="<?php echo $_POST['hdnPerformanceID']?>" />	
	<input type="hidden" name="hdnPerformanceName" value="<?php echo $_POST['hdnPerformanceName']?>" />		
    <input type="hidden" name="hdnTourID" value="<?php echo $_POST['hdnTourID']?>" />
	<input type="hidden" name="hdnTourName" value="<?php echo $_POST['hdnTourName']?>" />	

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

				
		foreach($thisPerformanceTask->aPerformanceTaskRecords as $oPerformanceTaskRecord)
		{
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
			echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->nPerformanceTaskID}'>{$oPerformanceTaskRecord->sDescription}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->nPerformanceTaskID}'>{$oPerformanceTaskRecord->sPerformanceName}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->nPerformanceTaskID}'>{$oPerformanceTaskRecord->sTourName}</A></div>";
            if($oPerformanceTaskRecord->bComplete){
                echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->nPerformanceTaskID}'>YES</A></div>";
            }
            else {
                echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->nPerformanceTaskID}'>NO</A></div>";
            }
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->nPerformanceTaskID}'>{$oPerformanceTaskRecord->dtLastUpdate}</A></div>";			
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
								DESCRIPTION: <input type="text" name="txtDescription" value="<?php echo $_POST['txtDescription']; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-4">
								PERFORMANCE: <?php echo $_POST['hdnPerformanceName']; ?> 
						</div>
						<div class="col-xs-12 col-md-4">
								TOUR: <?php echo $_POST['hdnTourName']; ?>
						</div>
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkComplete" class="result-checkbox" value="COMPLETE"<?php if( $_POST['chkComplete']) {echo " checked ";}; ?> />
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
						ID: <?php echo $_POST['hdnPerformanceTaskID']; ?>						
					</div>
					<div class ="col-xs-9 FormFieldNoEdit">
						LAST UPDATED: <?php echo $_POST['txtLastUpdate']; ?>
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
function buildPerformanceTaskObject($PerformanceTask)
{
	//Load the Array used to populate the form fields based on the newly loaded object
	$PerformanceTask->nPerformanceTaskID = $_POST['hdnPerformanceTaskID'];
	$PerformanceTask->nPerformanceID =$_POST['hdnPerformanceID'];
	$PerformanceTask->nTourID = $_POST['hdnTourID'];

	$PerformanceTask->sDescription = html_entity_decode($_POST['txtDescription'], ENT_QUOTES);
    $PerformanceTask->sPerformanceName = html_entity_decode($_POST['hdnPerformanceName'], ENT_QUOTES);
    $PerformanceTask->sTourName = html_entity_decode($_POST['hdnTourName'], ENT_QUOTES);

	if($_POST['chkComplete'] == "COMPLETE")
	{
		$PerformanceTask->bComplete = TRUE;
	}

    $PerformanceTask->bFuzzyDescriptionSearch = TRUE;
}


/*
 ********************************************************************************
 * loadPerformanceTask
 * 
 * This function loads the form field array from a populated PerformanceTask object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadPerformanceTask(&$PerformanceTask, $form)
{

	if (!is_null($PerformanceTask->nPerformanceTaskID))
	{

		//Load Hidden Fields
		$_POST['hdnPerformanceTaskID'] = $PerformanceTask->nPerformanceTaskID;
        $_POST['hdnPerformanceID'] = $PerformanceTask->nPerformanceID;
        $_POST['hdnTourID'] = $PerformanceTask->nTourID;
		$_POST['hdnPerformanceName'] = isset($PerformanceTask->sPerformanceName) ? htmlentities($PerformanceTask->sPerformanceName, ENT_QUOTES) : '';
		$_POST['hdnTourName'] = isset($PerformanceTask->sTourName) ? htmlentities($PerformanceTask->sTourName, ENT_QUOTES) : '';
		$_POST['txtDescription'] = isset($PerformanceTask->sDescription) ? htmlentities($PerformanceTask->sDescription, ENT_QUOTES) : '';
		$_POST['txtLastUpdate'] = isset($PerformanceTask->dtLastUpdate) ? htmlentities($PerformanceTask->dtLastUpdate, ENT_QUOTES) : '';
		$_POST['chkComplete'] = isset($PerformanceTask->bComplete) ? htmlentities($PerformanceTask->bComplete, ENT_QUOTES) : '';	
	}
	else
	{
		//Load Form field values into array 
		foreach($_POST as $fieldName=>$fieldValue) {
	
			$_POST[$fieldName]= htmlentities(stripslashes($fieldValue));
	
		}

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

/*
 ********************************************************************************
 * copyFormFields
 * 
 * This function copies the data from the form back into the form field array
 ********************************************************************************
*/
function copyFormFields($form)
{

	//Load Form field values into array 
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
