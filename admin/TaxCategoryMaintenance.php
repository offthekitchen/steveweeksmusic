<?php
/*
*******************************************************************
TaxCategoryMaintenance.php
This PHP file defines the Maintenance page for managing tax categories.
NOTES
Date        Change
-------------------------------------------------------------
2016-02-11	Refactored
2017-02-19	Make Responsive
2017-08-25	Improved Responsivity
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

 //include TaxCategory Class		
include_once (CLASS_DIR . "/class_TaxCategory.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of TaxCategory records from the DB
global $aTaxCategoryRecords;
 $sActiveMenuItem = FINANCES_ACTIVE;
 $sPageName = "Tax Category Maintenance";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

<div class="container-fluid">	
<form name="TaxCategoryMaint" action="TaxCategoryMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$thisTaxCategory = new TaxCategory();
		$form = new Form();

		//Get the ID query string parameter
		$nThisTaxCategoryID = $_REQUEST['ID'];
		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisTaxCategoryID))
		{
						
			$thisTaxCategory->nTaxCategoryID = $nThisTaxCategoryID;

			//Search the Database for records matching the search criteria			
			if ($thisTaxCategory->getTaxCategory())
			{
			
				//Records found
				if (sizeof($thisTaxCategory->aTaxCategoryRecords) > 0)
				{
									
					//Only One Record should be returned.  Add this to the form field array
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadTaxCategory($thisTaxCategory->aTaxCategoryRecords[0], $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				else
				{
					//The record was not found
					$form->sMessage = "TaxCategory record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisTaxCategory->sErrorMessage;
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
				buildTaxCategoryObject($thisTaxCategory);
				
				//Insert record
				if ($thisTaxCategory->insertTaxCategory())			
				{
					//Load the form fields with the newly populated object
					loadTaxCategory($thisTaxCategory, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Tax Category Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
				
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: " . $thisTaxCategory->sErrorMessage;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
			
				//Load values from form field array into DB object
				buildTaxCategoryObject($thisTaxCategory);
				
				//Update record
				if ($thisTaxCategory->updateTaxCategory())			
				{
				
					//reload TaxCategory
					$thisTaxCategory->getTaxCategory();
				
					//Load the form fields with the newly populated DB object						
					loadTaxCategory($thisTaxCategory->aTaxCategoryRecords[0], $form);
				
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Tax Category Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - " . $thisTaxCategory->sErrorMessage;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildTaxCategoryObject($thisTaxCategory);				

				//Delete record
				if ($thisTaxCategory->deleteTaxCategory())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "Tax Category Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: " . $thisTaxCategory->sErrorMessage;
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
				buildTaxCategoryObject($thisTaxCategory);

				//Search the Database for records matching the search criteria			
				if ($thisTaxCategory->getTaxCategory())
				{
					//No records found
					if(sizeof($thisTaxCategory->aTaxCategoryRecords) < 1)
					{
						$form->sMessage = "No Tax Category records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisTaxCategory->aTaxCategoryRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadTaxCategory($thisTaxCategory->aTaxCategoryRecords[0], $form);			

						$form->sMessage = "One Tax Category record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select Tax Category record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisTaxCategory->sErrorMessage;
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
				$_POST['hdnTaxCategoryID'] = NULL;
				
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
	<input type="hidden" name="hdnTaxCategoryID" value="<?php echo $_POST['hdnTaxCategoryID']?>" />	

	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Tax Category Maintenance
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
		echo "	<div class='hidden-xs col-sm-12 result-header'>Tax Category Name</div>";
		echo "	<div class='visible-xs col-xs-12 result-header'>TaxCategories</div>";
		echo "</div>";

				
		foreach($thisTaxCategory->aTaxCategoryRecords as $oTaxCategoryRecord)
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
				
			echo "	<div class='col-xs-12 result-selector {$sResultStyleClass}'><A HREF='./TaxCategoryMaintenance.php?ID={$oTaxCategoryRecord->nTaxCategoryID}'>{$oTaxCategoryRecord->sTaxCategoryName}</a></div>";
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
				NAME: <input type="text" name="txtTaxCategoryName" value="<?php echo $_POST['txtTaxCategoryName']; ?>" size="60" />
			</div>
			<div class="col-xs-12">		
				<div class="row">
					<div class ="col-xs-3 FormFieldNoEdit">
						ID: <?php echo $_POST['hdnTaxCategoryID']; ?>						
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
 * buildTaxCategoryObject
 * 
 * This function loads builds a TaxCategory object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildTaxCategoryObject($taxCategory)
{

	//Load the Array used to populate the form fields based on the newly loaded object
	$taxCategory->nTaxCategoryID = $_POST['hdnTaxCategoryID'];

	$taxCategory->sTaxCategoryName = html_entity_decode($_POST['txtTaxCategoryName'], ENT_QUOTES);
	$taxCategory->bFuzzyNameSearch = TRUE;

}


/*
 ********************************************************************************
 * loadTaxCategory
 * 
 * This function loads the form field array from a populated TaxCategory object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadTaxCategory(&$taxCategory, $form)
{

	if (!is_null($taxCategory->nTaxCategoryID))
	{
		//Load Hidden Fields
		$_POST['hdnTaxCategoryID'] = $taxCategory->nTaxCategoryID;

		$_POST['txtTaxCategoryName'] = htmlentities($taxCategory->sTaxCategoryName, ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($taxCategory->dtLastUpdate, ENT_QUOTES);
		
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

	//TaxCategory Name is a required Field
    if ( document.TaxCategoryMaint.txtTaxCategoryName.value == "" )
    {
        sErrorMessage += "Tax Category Name Required\n";
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
