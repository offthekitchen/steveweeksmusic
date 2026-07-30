<?php
/*
*******************************************************************
ThurdyDropMaintenance.php
This PHP file defines the Maintenance page for 
managing Drops.
NOTES
Date        Change
-------------------------------------------------------------
2015-05-21	Created.
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

//include Drop Class		
include_once (CLASS_DIR . "/class_ThurdyDrop.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of Drop records from the DB
global $aDropRecords;

global $nThisDropID;

$sActiveMenuItem = MISC_ACTIVE;	
$sPageName = "Thurdy Drop Maintenance";
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
<form name="DropMaint" action="ThurdyDropMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$thisDrop = new ThurdyDrop();
		$thisDrop->sOrderBy = DATE_ORDER;
		$form = new Form();

		//Get the ID query string parameter
		if (isset($_REQUEST['DROP_ID']) && $_REQUEST['DROP_ID'] !="")
		{
			$nThisDropID = $_REQUEST['DROP_ID'];
		}
		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisDropID))
		{
			$thisDrop->nDropID = $nThisDropID;

				//Search the Database for records matching the search criteria			
			if ($thisDrop->getThurdyDrop())
			{
			
				//Records found
				if (sizeof($thisDrop->aDropRecords) > 0)
				{
					
					//Only One Record should be returned.  Add this to the form field array						
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadDrop($thisDrop->aDropRecords[0], $form);			

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//The record was not found
					$form->sMessage = "Drop record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisDrop->sErrorMessage;
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
				buildDropObject($thisDrop);
				
				//Insert record
				if ($thisDrop->insertDrop())			
				{
					//Load the form fields with the newly populated object
					loadDrop($thisDrop, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Drop Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
				
					//The insert of the new expense failed
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: {$thisDrop->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{

				//Load values from form field array into DB object
				buildDropObject($thisDrop);

				//Update record
				if ($thisDrop->updateDrop())
				{
					//Load the form fields with the newly populated object
					loadDrop($thisDrop, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Drop Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else			
				{
					//Failed to update expense record
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - {$thisDrop->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildDropObject($thisDrop);				

				//Delete record
				if ($thisDrop->deleteDrop())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "Drop Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: {$thisDrop->sErrorMessage}";
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
				buildDropObject($thisDrop);
				$thisDrop->sOrderByField = "DROP_ID";

				//Search the Database for records matching the search criteria			
				if ($thisDrop->getThurdyDrop())
				{
					//No records found
					if(sizeof($thisDrop->aDropRecords) < 1)
					{
						$form->sMessage = "No Drop records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisDrop->aDropRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadDrop($thisDrop->aDropRecords[0], $form);
						//Store the ID of the performance
						$nThisDropID = $thisDrop->aDropRecords[0]->nDropID;			

						$form->sMessage = "One Drop record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select Drop record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisDrop->sErrorMessage;
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_NEW;
				}
			}
			// *************
			// *   CLEAR   *
			// *************
			else if (isset($_POST["btnClear"]) || isset($_POST["btnCancel"])) 
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
				$_POST['hdnDropID'] = NULL;
				
				$form->sMessage = "Search for records or Add new record";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_NEW;	
			}			
			// ***************
			// *  1st TIME   *
			// ***************
			else 
			{
				copyFormFields($form);
				$form->sMessage = "Search for records or Add new record";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_NEW;			
			}	

		}	

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnDropID" value="<?php echo $_POST['hdnDropID']?>" />	

	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
				Drop Maintenance
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
		echo "<div class='hidden-xs col-sm-3 result-header'>Drop Date</div>";
		echo "<div class='hidden-xs col-sm-6 result-header'>Location</div>";
		echo "<div class='visible-xs col-xs-12 result-header'>Drops</div>";		
		echo "</div>";

		foreach($thisDrop->aDropRecords as $oDropRecord)
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

			echo "<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'>";
			echo "<A HREF='./ThurdyDropMaintenance.php?DROP_ID={$oDropRecord->nDropID}'>{$oDropRecord->dtDropDate}</A></div>";
			
			echo "<div class='col-xs-12 col-sm-6 {$sResultStyleClass}'>";
			echo "<A HREF='./ThurdyDropMaintenance.php?DROP_ID={$oDropRecord->nDropID}'>{$oDropRecord->sDropLocation}</A></div>";
			echo "</div>";
			
		}
		?>
		<div class="row" style="height: 20px;"></div>
		</div>	
	</div>
		
	<?php
	}
	else
	{
	?>
	<div class="row"> 
		<div class="col-xs-12 FieldGroupTitle">
			DETAILS
		</div>
		<div class="col-xs-12 FieldGroup">
			<div class="row">
				<div class="col-xs-12 col-md-5">
					LOCATION: 
					<input type="text" name="txtDropLocation" value="<?php echo $_POST['txtDropLocation']; ?>" size="60" />
				</div>
				<div class="col-xs-12 col-md-5">
					DESCRIPTION: 
					<input type="text" name="txtDropDesc" value="<?php echo $_POST['txtDropDesc']; ?>" size="40" />
				</div>
				<div class="col-xs-12 col-md-2">
					DROP DATE:<BR /> 
					<?php	  
							renderDatePicker("DropDate", $_POST['DropDate']);
					?>
				</div>
			</div>
		</div>
		<div class="col-xs-12 FieldGroup">
			<div class="row">
				<div class="col-xs-12 col-md-5">
					<div class="row">
						<div class="col-xs-12">
							Drop Image												
						</div>
						<div class="col-xs-12">				
							<?php
							if (!empty($_POST['txtDropImage']))
							{
								$sFullImagePath = DROP_IMG_DIR . "/{$_POST['txtDropImage']}";
							}
							else
							{
								$sFullImagePath = NULL;
							}
							echo "<div style='padding: 15px;'><IMG SRC='" . $sFullImagePath . "' BORDER=0 Height=100 ></div>";
							?>												
						</div>
						<div class="col-xs-12">
							<input type="text" name="txtDropImage" id="txtDropImage" value="<?php echo $_POST['txtDropImage']; ?>" size="30" /><BR />
						</div>
					</div>					
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class=" col-xs-3 FormFieldNoEdit">
							ID: <?php echo $_POST['hdnPerformanceID']; ?>						
						</div>
						<div class=" col-xs-9 FormFieldNoEdit">
							LAST UPDATED: <?php echo $_POST['txtLastUpdate']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div>
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
	}
	?>
	
</form>

</div>
<?php		


/*
 ********************************************************************************
 * buildDropObject
 * 
 * This function loads builds a song object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildDropObject($oDrop)
{
	
	//Load the Array used to populate the form fields based on the newly loaded object
	$oDrop->nDropID = $_POST['hdnDropID'];
	$oDrop->sDropLocation = $_POST['txtDropLocation'];
	$oDrop->sDropDesc = $_POST['txtDropDesc'];
	$oDrop->sVideoLink = $_POST['txtVideoLink'];
	$oDrop->sVideoSubmittedBy = $_POST['txtVideoSubmittedBy'];
	$oDrop->sDropImage = $_POST['txtDropImage'];

	$oDrop->bFuzzyNameSearch = TRUE;

	//Drop Date	
	$dtDropDate = isset($_REQUEST["DropDate"]) ? $_REQUEST["DropDate"] : "";
	if($dtDropDate > "0000-00-00")
	{
		//If no datepicker is displayed, use the hidden field
		$dtDropDate = isset($_POST["DropDate"]) ? $_POST["DropDate"] : "";
	}
	if($dtDropDate > "0000-00-00")
	{
		$oDrop->dtDropDate  = $dtDropDate;
	}

}


/*
 ********************************************************************************
 * loadDrop
 * 
 * This function loads the form field array from a populated Drop object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadDrop(&$oDrop, $form)
{
	if (!is_null($oDrop->nDropID))
	{
		//Load Hidden Fields
		$_POST['hdnDropID'] = $oDrop->nDropID;
		$_POST['txtDropLocation'] = htmlentities($oDrop->sDropLocation, ENT_QUOTES);
		$_POST['txtDropDesc'] = htmlentities($oDrop->sDropDesc, ENT_QUOTES);
		$_POST['DropDate'] = htmlentities($oDrop->dtDropDate, ENT_QUOTES);
		$_POST['txtDropImage'] = htmlentities($oDrop->sDropImage, ENT_QUOTES);
	}
	else
	{
		//Load Form field values into array 
		foreach($_POST as $fieldName=>$fieldValue) {
	
			//$_POST[$fieldName]= htmlentities(stripslashes($fieldValue));
			$_POST[$fieldName]= $fieldValue;
	
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
	//Hidden Fileds must be set to 0
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

//******************************************
// validate_form
// This script checks the user imnput for
// errors or missing data 
//******************************************
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

	//Drop Name is a required Field
    if ( document.DropMaint.txtDropLocation.value == "" )
    {
        sErrorMessage += "Drop Location Required\n";
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
