<?php
/*
*******************************************************************
RevenueTypeMaintenance.php
This PHP file defines the Maintenance page for managing revenue types.
NOTES
Date        Change
-------------------------------------------------------------
2016-02-11	Refactored
2017-02-19	Made Responsive
2021-08-30	Updated for PHP 8
2026-07-30	Migrated to new Datalayer RevenueType repository
*******************************************************************
*/	

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	
//This include defines the relative path to the root directory from this sub-directory
include_once("root.inc.php");

//inlcude web site settings
include_once($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once(SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//inlcude admin settings
include_once(ADMIN_DIR . "/includes/AdminSettings.php");

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/RevenueType.php");
include_once(DATALAYER_DIR . "/RevenueTypeRepository.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of RevenueType records from the DB
$aRevenueTypeRecords = [];

$sActiveMenuItem = FINANCES_ACTIVE;
$sPageName = "Revenue Type Maintenance";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

<div class="container-fluid">	
<form name="RevenueTypeMaint" action="RevenueTypeMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$revenueTypeRepo = new \Datalayer\RevenueTypeRepository();
		$thisRevenueType = new \Datalayer\RevenueType();
		$form = new Form();

		//Get the ID query string parameter
		$nThisRevenueTypeID = $_REQUEST['ID'] ?? null;
		
		try {
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisRevenueTypeID) && $nThisRevenueTypeID !== '')
		{
			$entity = $revenueTypeRepo->findById((int) $nThisRevenueTypeID);

			if ($entity) {
				$thisRevenueType = $entity;
				$aRevenueTypeRecords = [$entity];
				loadRevenueType($thisRevenueType, $form);

				$form->sMessage = "Update record.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;
			} else {
				$form->sMessage = "Revenue Type record not found.";
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
				buildRevenueTypeObject($thisRevenueType);
				
				if ($revenueTypeRepo->insert($thisRevenueType))
				{
					$thisRevenueType = $revenueTypeRepo->findById((int) $thisRevenueType->id) ?? $thisRevenueType;
					$aRevenueTypeRecords = [$thisRevenueType];
					loadRevenueType($thisRevenueType, $form);
					
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Revenue Type Added";
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
				buildRevenueTypeObject($thisRevenueType);
				
				if ($revenueTypeRepo->update($thisRevenueType))
				{
					$thisRevenueType = $revenueTypeRepo->findById((int) $thisRevenueType->id) ?? $thisRevenueType;
					$aRevenueTypeRecords = [$thisRevenueType];
					loadRevenueType($thisRevenueType, $form);
				
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Revenue Type Updated";
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
				buildRevenueTypeObject($thisRevenueType);

				if (!empty($thisRevenueType->id) && $revenueTypeRepo->delete((int) $thisRevenueType->id)) {
					clearFormFields($form);
					
					$form->sMessage = "Revenue Type Deleted";
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
				buildRevenueTypeObject($thisRevenueType);

				$aRevenueTypeRecords = $revenueTypeRepo->find([
					'id' => $thisRevenueType->id,
					'name' => $thisRevenueType->name,
					'fuzzyName' => true,
				]);

				if (sizeof($aRevenueTypeRecords) < 1)
				{
					$form->sMessage = "No Revenue Type records found matching search criteria";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}			
				else if (sizeof($aRevenueTypeRecords) == 1)
				{
					$thisRevenueType = $aRevenueTypeRecords[0];
					loadRevenueType($thisRevenueType, $form);			

					$form->sMessage = "One RevenueType record found.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else 
				{
					$form->sMessage = "Select Revenue Type record to edit from results list below.";
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
				$_POST['hdnRevenueTypeID'] = NULL;
				
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
		} catch (\Throwable $e) {
			$form->sMessage = $e->getMessage();
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->nFormMode = FORM_MODE_NEW;
		}

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnRevenueTypeID" value="<?php echo $_POST['hdnRevenueTypeID'] ?? '' ?>" />	

	<div class="row">
<?php
include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Revenue Type Maintenance
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
		echo "	<div class='hidden-xs col-sm-12 result-header'>Revenue Type Name</div>";
		echo "	<div class='visible-xs col-xs-12 result-header'>RevenueTypes</div>";
		echo "</div>";

				
		foreach($aRevenueTypeRecords as $oRevenueTypeRecord)
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
		
			echo "	<div class='col-xs-12 result-selector {$sResultStyleClass}'><A HREF='./RevenueTypeMaintenance.php?ID={$oRevenueTypeRecord->id}'>{$oRevenueTypeRecord->name}</a></div>";
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
			<div class="col-xs-12  FieldGroup">
				NAME: <input type="text" name="txtRevenueTypeName" value="<?php echo $_POST['txtRevenueTypeName'] ?? ''; ?>" size="60" />
			</div>
			<div class="col-xs-12">		
				<div class="row">
					<div class ="col-xs-3 FormFieldNoEdit">
						ID: <?php echo $_POST['hdnRevenueTypeID'] ?? ''; ?>						
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
 * buildRevenueTypeObject
 * 
 * This function loads builds a RevenueType object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildRevenueTypeObject(\Datalayer\RevenueType $revenueType)
{
	$id = $_POST['hdnRevenueTypeID'] ?? null;
	$revenueType->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$revenueType->name = html_entity_decode($_POST['txtRevenueTypeName'] ?? '', ENT_QUOTES);
}


/*
 ********************************************************************************
 * loadRevenueType
 * 
 * This function loads the form field array from a populated RevenueType object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadRevenueType(\Datalayer\RevenueType $revenueType, $form)
{
	if (!is_null($revenueType->id))
	{
		$_POST['hdnRevenueTypeID'] = $revenueType->id;

		$_POST['txtRevenueTypeName'] = htmlentities($revenueType->name ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($revenueType->lastUpdate ?? '', ENT_QUOTES);
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

	//RevenueType Name is a required Field
    if ( document.RevenueTypeMaint.txtRevenueTypeName.value == "" )
    {
        sErrorMessage += "RevenueType Name Required\n";
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
