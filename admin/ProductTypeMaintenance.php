<?php
error_reporting(E_ALL ^ E_NOTICE);

//This include defines the relative path to the root directory from this sub-directory
include_once ("root.inc.php");

//inlcude web site settings
include_once ($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//inlcude admin settings
include_once (ADMIN_DIR . "/includes/AdminSettings.php");

//include Product Type Class		
include_once (CLASS_DIR . "/class_ProductType.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of Product Type records from the DB
global $aProductTypeRecords;

$sActiveMenuItem = REPORTS_ACTIVE;
$sPageName = "PRODUCT REPORT";

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
	

	
	?>
<form name="ProductTypeMaint" action="ProductTypeMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$thisProductType = new ProductType();
		$form = new Form();

		//Get the ID query string parameter
		$nThisProductTypeID = $_REQUEST['ID'];
		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisProductTypeID))
		{
						
			$thisProductType->nProductTypeID = $nThisProductTypeID;

			//Search the Database for records matching the search criteria			
			if ($thisProductType->getProductType())
			{
			
				//Records found
				if (sizeof($thisProductType->aProductTypeRecords) > 0)
				{
									
					//Only One Record should be returned.  Add this to the form field array
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadProductType($thisProductType->aProductTypeRecords[0], $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				else
				{
					//The record was not found
					$form->sMessage = "Product Type record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisProductType->sErrorMessage;
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
				buildProductTypeObject($thisProductType);
				
				//Insert record
				if ($thisProductType->insertProductType())			
				{
					//Load the form fields with the newly populated object
					loadProductType($thisProductType, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Product Type Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
				
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: " . $thisProductType->sErrorMessage;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
			
				//Load values from form field array into DB object
				buildProductTypeObject($thisProductType);
				
				//Update record
				if ($thisProductType->updateProductType())			
				{
				
					//reload Product Type
					$thisProductType->getProductType();
				
					//Load the form fields with the newly populated DB object						
					loadProductType($thisProductType->aProductTypeRecords[0], $form);
				
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Product Type Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - " . $thisProductType->sErrorMessage;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildProductTypeObject($thisProductType);				

				//Delete record
				if ($thisProductType->deleteProductType())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "Product Type Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: " . $thisProductType->sErrorMessage;
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
				buildProductTypeObject($thisProductType);

				//Search the Database for records matching the search criteria			
				if ($thisProductType->getProductType())
				{
					//No records found
					if(sizeof($thisProductType->aProductTypeRecords) < 1)
					{
						$form->sMessage = "No Product Type records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisProductType->aProductTypeRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadProductType($thisProductType->aProductTypeRecords[0], $form);			

						$form->sMessage = "One Product Type record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select Product Type record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisProductType->sErrorMessage;
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
				$form->aFormFieldValues['hdnProductTypeID'] = NULL;
				
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
	<input type="hidden" name="hdnProductTypeID" value="<?php echo $form->aFormFieldValues['hdnProductTypeID']?>" />	

<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader.php");
?>	
	<table border="0" width="90%">
		<tr>
			<td colspan="2" class="Title">
				Product Type Maintenance
			</td>
			<td align="right">
			</td>
		</tr>
		<tr>
			<td colspan="3" class="Buttons">
				<?php
				$form->renderButtons();
				?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="FormMessage">
				<?php
				$form->renderFormMessage();
				?>
			</td>
		</tr>
	</table>
	<?php	
	if ($form->nFormMode == FORM_MODE_SELECT)
	{
	 ?>
	
	<table border="0"> 
		<?php
			
		$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

		//Header row for Results
		echo "<TR>";
		echo "	<TH class='" . RESULT_STYLE_CLASS . "'></TH>";
		echo "	<TH class='" . RESULT_STYLE_CLASS . "'>Product Type Name</TH>";
		echo "</TR>";

				
		foreach($thisProductType->aProductTypeRecords as $oProductTypeRecord)
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
			echo "<TR>";
			
			//The first column is the ID and is used to build a link
			echo "	<TD class='" , $sResultStyleClass . "'><A HREF='./ProductTypeMaintenance.php?ID=" . $oProductTypeRecord->nProductTypeID . "'>Select</A></TD>";
			
			$i = 1;
		
			echo "	<TD class='" , $sResultStyleClass . "'>" . $oProductTypeRecord->sProductTypeName . "</TD>";
			echo "</TR>";
		}
		?>
	
	</table>	
		
	<?php
	}
	else
	{
	?>
	
	<table border="0">
		<tr>
			<td width="1094" colspan="3">
				 <TABLE width="1094">
				 	<TR>
						<TD colspan="3">
							<table>
								<tr>					
									<td CLASS="FormFieldNoEdit" width="50">
										ID: <?php echo $form->aFormFieldValues['hdnProductTypeID']; ?>						
									</td>
									<td >
										NAME: <input type="text" name="txtProductTypeName" value="<?php echo $form->aFormFieldValues['txtProductTypeName']; ?>" size="60" />
									</td>
								</tr>
							</table>
						</TD>
					</TR>
					<TR>
						<TD CLASS="FormFieldNoEdit">
							LAST UPDATED: <?php echo $form->aFormFieldValues['txtLastUpdate']; ?>
						</TD>
					</TR>
			  </TABLE>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<TABLE>
					<TR>
						<TD>
						</TD>
						<TD>
							
						</TD>
					</TR>
				</TABLE>
			</td>
		</tr>
		<tr>
			<td colspan="3">
			</td>
		</tr>
		<tr>
			<td colspan="3">
			</td>
		</tr>
		<tr>
			<td colspan="3" class="Buttons">
				<?php
				$form->renderButtons();
				?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="FormMessage">
				<?php
				$form->renderFormMessage();
				?>
			</td>
		</tr>
	</table>
	
	<?php
	}
	?>
	
</form>

<?php		


/*
 ********************************************************************************
 * buildProductTypeObject
 * 
 * This function loads builds a Product Type object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildProductTypeObject($productType)
{

	//Load the Array used to populate the form fields based on the newly loaded object
	$productType->nProductTypeID = $_POST['hdnProductTypeID'];

	$productType->sProductTypeName = html_entity_decode($_POST['txtProductTypeName'], ENT_QUOTES);
	$productType->bFuzzyNameSearch = TRUE;

}


/*
 ********************************************************************************
 * loadProductType
 * 
 * This function loads the form field array from a populated Product Type object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadProductType(&$productType, $form)
{

	if (!is_null($productType->nProductTypeID))
	{
		//Load Hidden Fields
		$form->aFormFieldValues['hdnProductTypeID'] = $productType->nProductTypeID;

		$form->aFormFieldValues['txtProductTypeName'] = htmlentities($productType->sProductTypeName, ENT_QUOTES);
		$form->aFormFieldValues['txtLastUpdate'] = htmlentities($productType->dtLastUpdate, ENT_QUOTES);
		
	}
	else
	{
		//Load Form field values into array 
		foreach($_POST as $fieldName=>$fieldValue) {
	
			$form->aFormFieldValues[$fieldName]= htmlentities(stripslashes($fieldValue));
	
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
	$form->aFormFieldValues = array();
	$form->aFormFieldValues['hdnProductTypeID'] = 0;


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
		$form->aFormFieldValues[$fieldName]= htmlentities(stripslashes($fieldValue));
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

	//Product Type Name is a required Field
    if ( document.ProductTypeMaint.txtProductTypeName.value == "" )
    {
        sErrorMessage += "Product Type Name Required\n";
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
