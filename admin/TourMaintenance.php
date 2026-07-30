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
	
//include Tour Class		
include_once (CLASS_DIR . "/class_Tour.php");

//include Performance Task Class		
include_once(CLASS_DIR . "/class_PerformanceTask.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of Tour records from the DB
global $aTourRecords;
	 
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
		$thisTour = new Tour();
		$thisPerformanceTasks = new PerformanceTask();
		$form = new Form();

		//Get the ID query string parameter
		$nThisTourID = $_REQUEST['ID'];
		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisTourID))
		{
						
			$thisTour->nTourID = $nThisTourID;

			//Search the Database for records matching the search criteria			
			if ($thisTour->getTour())
			{
			
				//Records found
				if (sizeof($thisTour->aTourRecords) > 0)
				{
									
					//Only One Record should be returned.  Add this to the form field array
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadTour($thisTour->aTourRecords[0], $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				else
				{
					//The record was not found
					$form->sMessage = "Tour record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisTour->sErrorMessage;
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
				buildTourObject($thisTour);
				
				//Insert record
				if ($thisTour->insertTour())			
				{

					//reload Tour
					$nNewTourID = $thisTour->nTourID;
					$thisTour = new Tour();
					$thisTour->nTourID = $nNewTourID;

					if ($thisTour->getTour())
					{	
						//Load the form fields with the newly populated object
						loadTour($thisTour->aTourRecords[0], $form);
						//Store the ID of the performance
						$nThisTourID = $thisTour->aTourRecords[0]->nTourID;			
						
						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Tour Added";
						$form->nFormMode = FORM_MODE_EDIT;			
					}
					else
					{
						//Problem reloading screen
						clearFormFields($form);
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "Tour Added, but error occured while reloading the data";
						$form->nFormMode = FORM_MODE_NEW;			
					}
				}
				else
				{
				
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: {$thisTour->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
			
				//Load values from form field array into DB object
				buildTourObject($thisTour);
				
				//Update record
				if ($thisTour->updateTour())			
				{
				
					//Update tour performances
					if($thisTour->getTourPerformances())
					{
						foreach($thisTour->oTourPerformances->aPerformanceRecords as $oTourPerformance)
						{
							$sCheckboxName = "chkPerformance{$oTourPerformance->nPerformanceID}";
							if(!isset($_POST[$sCheckboxName]) || $_POST[$sCheckboxName] != 'PerformanceAssociated')
							{
								$oTourPerformance->nTourID = NULL;
								if(!$oTourPerformance->updatePerformance())
								{
									//ERROR NEEDED
								}
							}  
						}
					}

					//Update potential performances
					if($thisTour->getPotentialPerformances())
					{
						foreach($thisTour->oPotentialPerformances->aPerformanceRecords as $oPotentialPerformance)
						{
							$sCheckboxName = "chkPerformance{$oPotentialPerformance->nPerformanceID}";
							if(isset($_POST[$sCheckboxName]) && $_POST[$sCheckboxName] == 'PerformanceAssociated')
							{
								$oPotentialPerformance->nTourID = $thisTour->nTourID;
								if(!$oPotentialPerformance->updatePerformance())
								{
									//ERROR NEEDED
								}
							}  
						}
					}
				
					//reload Tour
					$thisTour->getTour();
				
					//Load the form fields with the newly populated DB object						
					loadTour($thisTour->aTourRecords[0], $form);
					//Store the ID of the tour
					$nThisTourID = $thisTour->aTourRecords[0]->nTourID;			
				
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Tour Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - {$thisTour->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildTourObject($thisTour);				

				//Delete record
				if ($thisTour->deleteTour())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "Tour Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: {$thisTour->sErrorMessage}";
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
				buildTourObject($thisTour);

				//Search the Database for records matching the search criteria			
				if ($thisTour->getTour())
				{
					//No records found
					if(sizeof($thisTour->aTourRecords) < 1)
					{
						$form->sMessage = "No Tour records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisTour->aTourRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadTour($thisTour->aTourRecords[0], $form);
						//Store the ID of the performance
						$nThisTourID = $thisTour->aTourRecords[0]->nTourID;			

						$form->sMessage = "One Tour record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select Tour record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisTour->sErrorMessage;
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

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnTourID" value="<?php echo $_POST['hdnTourID']?>" />	

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
	
					
			foreach($thisTour->aTourRecords as $oTourRecord)
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
				echo "<div class=\"row {$sResultStyleClass}\">";
				
				$i = 1;
			
				echo "	<div class='col-xs-12 col-sm-4 {$sResultStyleClass}'><A HREF='./TourMaintenance.php?ID={$oTourRecord->nTourID}'>{$oTourRecord->sTourName}</a></div>";
				echo "	<div class='hidden-xs col-sm-4 {$sResultStyleClass}'><A HREF='./TourMaintenance.php?ID={$oTourRecord->nTourID}'>{$oTourRecord->dtTourStartDate}</a></div>";
				echo "	<div class='hidden-xs col-sm-4 {$sResultStyleClass}'><A HREF='./TourMaintenance.php?ID={$oTourRecord->nTourID}'>{$oTourRecord->dtTourEndDate}</a></div>";
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
				if ($_POST['hdnTourID'] > 0)
				{
					echo "<a href='". ADMIN_DIR . "/ExpenseMaintenance.php?TOUR_ID={$thisTour->aTourRecords[0]->nTourID}";
					echo "' class='secondaryLinkButton'>Edit Expenses</a>";
					echo "<span class='hidden-xs'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/ExpenseMaintenance.php?TOUR_ID={$thisTour->aTourRecords[0]->nTourID}";
					echo "&ACTION=ADD_EXPENSE' class='secondaryLinkButton'>Enter Expense</a>";
					echo "<span class='hidden-xs'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/TourEmails.php?TOUR_ID={$thisTour->aTourRecords[0]->nTourID}' target='_blank' class='secondaryLinkButton'>";
					echo "Tour Emails</a>";
					echo "<span class='hidden-xs'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/TourReport.php?ID={$thisTour->aTourRecords[0]->nTourID}";
					echo "' class='secondaryLinkButton'>Tour Report</a>";
					echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
					echo "<br class='visible-xs'>";
					echo "<a href='" . ADMIN_DIR . "/PerformanceTaskMaintenance.php?TOUR_ID={$thisTour->aTourRecords[0]->nTourID}&TOUR_NAME={$thisTour->aTourRecords[0]->sTourName}' class='secondaryLinkButton'>Add Task</a>";

				}
				?>
			</div>
				<div class="col-xs-12 FieldGroupTitle">
					DETAILS
				</div>
				<?php
					// The tour object will have info if a search returned one record.  It will have the record in its array
					// if a Tour ID was passed in the query string
					if(empty($thisTour->nTourID)) {
						$thisTour->nTourID = $thisTour->aTourRecords[0]->nTourID;
					}
					if(!empty($thisTour->nTourID)){
						$thisPerformanceTasks->nTourID = $thisTour->nTourID;
						if ($thisPerformanceTasks->getPerformanceTask()) {
							if(sizeof($thisPerformanceTasks->aPerformanceTaskRecords) > 0){
								echo "<div class=\"col-xs-12 FieldGroup\">";
								echo "TASKS:";
								foreach ($thisPerformanceTasks->aPerformanceTaskRecords as $oPerformanceTaskRecord) {
									if($oPerformanceTaskRecord->bComplete){
										echo "<del> ";
									}
									echo "<div><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->nPerformanceTaskID}'>{$oPerformanceTaskRecord->sDescription}</A></div>";
									if($oPerformanceTaskRecord->bComplete){
										echo "</del> ";
									}
								}
								echo "</div>";
							}
						}
					}
					?>
				<div class="col-xs-12 FieldGroup">
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							START DATE:<BR /> 
							<?php	  
							renderDatePicker("TourStartDate", $thisTour->aTourRecords[0]->dtTourStartDate);
							?>	
							<br><br>							    
						</div>								
						<div class="col-xs-12 col-sm-4">
							END DATE:<BR /> 
							<?php	  
							renderDatePicker("TourEndDate", $thisTour->aTourRecords[0]->dtTourEndDate);
							?>		
							<br><br>						  
						</div>								
						<div class="col-xs-12 col-sm-4">
							INCLUDE DATE:<BR /> 
							<?php	  
							renderDatePicker("TourIncludeDate", $thisTour->aTourRecords[0]->dtTourIncludeDate);
							?>	
							<br><br>							    
						</div>								
						<div class="col-xs-12">
							TOUR NAME: <input type="text" name="txtTourName" value="<?php echo $_POST['txtTourName']; ?>" size="60" />&nbsp;&nbsp;								
						</div>	
						<div class="col-xs-12">
							TOUR NOTES
							<textarea name="txtNotes" cols=60 rows=5 ><?php echo $_POST['txtNotes']; ?></textarea>
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
							if($thisTour->aTourRecords[0]->nTourID > 0)
							{
								echo "<div class='row result-header'>";
								echo "<div class='hidden-xs hidden-sm col-md-1 result-header'></div>";
								echo "<div class='col-xs-4 col-md-2 result-header'>Name</div>";
								echo "<div class='col-xs-2 col-md-2 result-header'>Date</div>";
								echo "<div class='hidden-xs hidden-sm col-md-1 result-header'>Time</div>";
								echo "<div class='col-xs-6 col-md-3 result-header'>Location</div>";
								echo "<div class='hidden-xs hidden-sm col-md-3 result-header'></div>";
								echo "</div>";

								if($thisTour->aTourRecords[0]->getTourPerformances())
								{
									foreach($thisTour->aTourRecords[0]->oTourPerformances->aPerformanceRecords as $oPerformance)
									{
										$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

										echo "<div class=\"row {$sResultStyleClass}\">";
										echo "<div class='hidden-xs hidden-sm col-md-1 {$sResultStyleClass}'>";
										echo "<input type='checkbox' name='chkPerformance{$oPerformance->nPerformanceID}'";
										echo " value='PerformanceAssociated' checked >";
										echo "</div>";
										echo "<div class='col-xs-4 col-md-2  {$sResultStyleClass}'>";
										echo "<a href='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID={$oPerformance->nPerformanceID}'>";
										echo "{$oPerformance->sPerformanceName}</a></div>";
										echo "<div class='col-xs-2 col-md-2 {$sResultStyleClass}'>{$oPerformance->dtPerformanceDate}</div>";
										echo "<div class='hidden-xs hidden-sm col-md-1 {$sResultStyleClass}'>{$oPerformance->sPerformanceTime}</div>";
										echo "<div class='col-xs-6 col-md-3 {$sResultStyleClass}'>{$oPerformance->sLocation}, ";
										echo "{$oPerformance->sLocationCity}, {$oPerformance->sLocationState }</div>";
										echo "<div class='hidden-xs hidden-sm col-md-3 {$sResultStyleClass}'>";
										if($oPerformance->dtPerformanceDate > $thisTour->aTourRecords[0]->dtTourEndDate || $oPerformance->dtPerformanceDate < $thisTour->aTourRecords[0]->dtTourStartDate)
										{
											echo "<font color='red'>Performance date outside of tour dates</font>";
										}
										echo "</div>";
										echo "</div>";
										
									}
								}
								else
								{
									echo "ERROR RETRIEIVING PERORMANCES";
								}
								
								if($thisTour->aTourRecords[0]->getPotentialPerformances())
								{
									foreach($thisTour->aTourRecords[0]->oPotentialPerformances->aPerformanceRecords as $oPerformance)
									{
										$sResultStyleClass = RESULT_STYLE_CLASS;

										echo "<div class=\"row {$sResultStyleClass}\">";
										echo "<div class='hidden-xs hidden-sm col-md-1 {$sResultStyleClass}'>";
										echo "<input type='checkbox' name='chkPerformance{$oPerformance->nPerformanceID}'";
										echo " value='PerformanceAssociated' >";
										echo "</div>";
										echo "<div class='hidden-xs hidden-sm col-md-2  {$sResultStyleClass}'>";
										echo "<a href='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID={$oPerformance->nPerformanceID}'>";
										echo "{$oPerformance->sPerformanceName}</a></div>";
										echo "<div class='hidden-xs hidden-sm col-md-2 {$sResultStyleClass}'>{$oPerformance->dtPerformanceDate}</div>";
										echo "<div class='hidden-xs hidden-sm col-md-1 {$sResultStyleClass}'>{$oPerformance->sPerformanceTime}</div>";
										echo "<div class='hidden-xs hidden-sm col-md-3 {$sResultStyleClass}'>{$oPerformance->sLocation}, ";
										echo "{$oPerformance->sLocationCity}, {$oPerformance->sLocationState}</div>";
										echo "<div class='hidden-xs hidden-sm col-md-3 {$sResultStyleClass}'>";
										if($oPerformance->nTourID > 0)
										{
											echo "<a href='" . ADMIN_DIR . "/TourMaintenance.php?ID={$oPerformance->nTourID}'>";
											echo "<font color='red'>Performance already associated with tour ";
											echo "{$oPerformance->nTourID}</font></a>";
										}
										echo "</div>";
										echo "</div>";
										
									}
								}
								
							}
						?>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class=" col-xs-3 FormFieldNoEdit">
							ID: <?php echo $_POST['hdnTourID']; ?>						
						</div>
						<div class=" col-xs-9 FormFieldNoEdit">
							LAST UPDATED: <?php echo $_POST['txtLastUpdate']; ?>
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
 * This function loads builds a flag object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildTourObject($oTour)
{

	//Load the Array used to populate the form fields based on the newly loaded object
	$oTour->nTourID = $_POST['hdnTourID'];

	$oTour->sTourName = html_entity_decode($_POST['txtTourName'], ENT_QUOTES);
	$oTour->bFuzzyNameSearch = TRUE;

	//Tour Inlcude Date	
	$dtTourIncludeDate = isset($_REQUEST["TourIncludeDate"]) ? $_REQUEST["TourIncludeDate"] : "";
	if($dtTourIncludeDate > "0000-00-00")
	{
		//If no datepicker is displayed, use the hidden field
		$dtTourIncludeDate = isset($_POST["TourIncludeDate"]) ? $_POST["TourIncludeDate"] : "";
	}
	if($dtTourIncludeDate > "0000-00-00")
	{
		$oTour->dtTourIncludeDate  	= $dtTourIncludeDate;
	}

	//Tour Start Date	
	$dtTourStartDate = isset($_REQUEST["TourStartDate"]) ? $_REQUEST["TourStartDate"] : "";
	if($dtTourStartDate > "0000-00-00")
	{
		//If no datepicker is displayed, use the hidden field
		$dtTourStartDate = isset($_POST["TourStartDate"]) ? $_POST["TourStartDate"] : "";
	}
	if($dtTourStartDate > "0000-00-00")
	{
		$oTour->dtTourStartDate  	= $dtTourStartDate;
	}

	//Tour End Date	
	$dtTourEndDate = isset($_REQUEST["TourEndDate"]) ? $_REQUEST["TourEndDate"] : "";
	if($dtTourEndDate > "0000-00-00")
	{
		//If no datepicker is displayed, use the hidden field
		$dtTourEndDate = isset($_POST["TourEndDate"]) ? $_POST["TourEndDate"] : "";
	}
	if($dtTourEndDate > "0000-00-00")
	{
		$oTour->dtTourEndDate  = $dtTourEndDate;
	}

	$oTour->sNotes = html_entity_decode($_POST['txtNotes'], ENT_QUOTES);

}


/*
 ********************************************************************************
 * loadTour
 * 
 * This function loads the form field array from a populated Tour object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadTour(&$oTour, $form)
{

	if (!is_null($oTour->nTourID))
	{
		//Load Hidden Fields
		$_POST['hdnTourID'] = $oTour->nTourID;
		

		$_POST['txtTourName'] = htmlentities($oTour->sTourName, ENT_QUOTES);
		
		$_POST['TourStartDate'] = htmlentities($oTour->dtTourStartDate, ENT_QUOTES);
		$_POST['TourEndDate'] = htmlentities($oTour->dtTourEndDate, ENT_QUOTES);
		$_POST['txtNotes'] = htmlentities($oTour->sNotes, ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($oTour->dtLastUpdate, ENT_QUOTES);
		
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

