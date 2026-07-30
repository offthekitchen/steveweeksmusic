<?php
/*
*******************************************************************
VendorMaintenance.php
This PHP file defines the Maintenance page for managing vendors.
NOTES
Date        Change
-------------------------------------------------------------
2016-02-11	Refactored
2016-12-14	Made Responsive
2016-12-29	Improved Responsivity for phone
2021-08-21 	Updated for PHP 8
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

//include Vendor Class		
include_once (CLASS_DIR . "/class_Vendor.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of Vendor records from the DB
global $aVendorRecords;

$sActiveMenuItem = PRODUCTS_ACTIVE;	
$sPageName = "Vendor Maintenance";

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
<form name="VendorMaint" action="VendorMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$thisVendor = new Vendor();
		$form = new Form();

		//Get the ID query string parameter
		$nThisVendorID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : NULL;
		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisVendorID))
		{
						
			$thisVendor->nVendorID = $nThisVendorID;

			//Search the Database for records matching the search criteria			
			if ($thisVendor->getVendor())
			{
			
				//Records found
				if (sizeof($thisVendor->aVendorRecords) > 0)
				{
									
					//Only One Record should be returned.  Add this to the form field array
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadVendor($thisVendor->aVendorRecords[0], $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				else
				{
					//The record was not found
					$form->sMessage = "Vendor record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisVendor->sErrorMessage;
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
				buildVendorObject($thisVendor);
				
				//Insert record
				if ($thisVendor->insertVendor())			
				{
					//Load the form fields with the newly populated object
					loadVendor($thisVendor, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Vendor Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
				
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: " . $thisVendor->sErrorMessage;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
			
				//Load values from form field array into DB object
				buildVendorObject($thisVendor);
				
				//Update record
				if ($thisVendor->updateVendor())			
				{
				
					//reload Vendor
					$thisVendor->getVendor();
				
					//Load the form fields with the newly populated DB object						
					loadVendor($thisVendor->aVendorRecords[0], $form);
				
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Vendor Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - " . $thisVendor->sErrorMessage;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildVendorObject($thisVendor);				

				//Delete record
				if ($thisVendor->deleteVendor())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "Vendor Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: " . $thisVendor->sErrorMessage;
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
				buildVendorObject($thisVendor);

				//Search the Database for records matching the search criteria			
				if ($thisVendor->getVendor())
				{
					//No records found
					if(sizeof($thisVendor->aVendorRecords) < 1)
					{
						$form->sMessage = "No Vendor records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisVendor->aVendorRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadVendor($thisVendor->aVendorRecords[0], $form);			

						$form->sMessage = "One Vendor record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select Vendor record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisVendor->sErrorMessage;
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
				$_POST['hdnVendorID'] = NULL;
				
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
	<input type="hidden" name="hdnVendorID" value="<?php echo $_POST['hdnVendorID']?>" />	
	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Vendor Maintenance
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
		echo "	<div class='hidden-xs col-sm-12 result-header'>Vendor Name</div>";
		echo "	<div class='visible-xs col-xs-12 result-header'>Vendors</div>";
		echo "</div>";

				
		foreach($thisVendor->aVendorRecords as $oVendorRecord)
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
			
			//The first column is the ID and is used to build a link
			echo "	<div class='col-xs-12 result-selector {$sResultStyleClass}'><A HREF='./VendorMaintenance.php?ID={$oVendorRecord->nVendorID}'>{$oVendorRecord->sVendorName}</A></div>";			
			$i = 1;
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
				NAME: <input type="text" name="txtVendorName" value="<?php echo $_POST['txtVendorName']; ?>" size="60" />
			</div>
			<div class="col-xs-12">		
				<div class="row FormFieldNoEdit">
					<div class ="col-xs-3 FormFieldNoEdit">
						ID: <?php echo $_POST['hdnVendorID']; ?>						
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
 * buildVendorObject
 * 
 * This function loads builds a Vendor object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildVendorObject($vendor)
{

	//Load the Array used to populate the form fields based on the newly loaded object
	$vendor->nVendorID = $_POST['hdnVendorID'];

	$vendor->sVendorName = html_entity_decode($_POST['txtVendorName'], ENT_QUOTES);
	$vendor->bFuzzyNameSearch = TRUE;

}


/*
 ********************************************************************************
 * loadVendor
 * 
 * This function loads the form field array from a populated Vendor object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadVendor(&$vendor, $form)
{

	if (!is_null($vendor->nVendorID))
	{
		//Load Hidden Fields
		$_POST['hdnVendorID'] = $vendor->nVendorID;

		$_POST['txtVendorName'] = htmlentities($vendor->sVendorName, ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($vendor->dtLastUpdate, ENT_QUOTES);
		
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

	//Vendor Name is a required Field
    if ( document.VendorMaint.txtVendorName.value == "" )
    {
        sErrorMessage += "Vendor Name Required\n";
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
