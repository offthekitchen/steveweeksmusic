<?php
/*
*******************************************************************
CategoryMaintenance.php
This PHP file defines the Maintenance page for managing categories.
NOTES
Date        Change
-------------------------------------------------------------
2016-02-12 - Refactored 
2017-02-11	Made Responsive
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
	
//include Category Class		
include_once (CLASS_DIR . "/class_Category.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of Category records from the DB
global $aCategoryRecords;
$sActiveMenuItem = FINANCES_ACTIVE;	
$sPageName = "Category Maintenance";
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
<form name="CategoryMaint" action="CategoryMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$thisCategory = new Category();
		$form = new Form();

		//Get the ID query string parameter
		$nThisCategoryID = $_REQUEST['CATEGORY_ID'];
		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisCategoryID))
		{
						
			$thisCategory->nCategoryID = $nThisCategoryID;

			//Search the Database for records matching the search criteria			
			if ($thisCategory->getCategory())
			{
			
				//Records found
				if (sizeof($thisCategory->aCategoryRecords) > 0)
				{
									
					//Only One Record should be returned.  Add this to the form field array
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadCategory($thisCategory->aCategoryRecords[0], $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				else
				{
					//The record was not found
					$form->sMessage = "Category record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisCategory->sErrorMessage;
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
				buildCategoryObject($thisCategory);
				
				//Insert record
				if ($thisCategory->insertCategory())			
				{
					//Load the form fields with the newly populated object
					loadCategory($thisCategory, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Category Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
				
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: {$thisCategory->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
			
				//Load values from form field array into DB object
				buildCategoryObject($thisCategory);
				
				//Update record
				if ($thisCategory->updateCategory())			
				{
				
					//reload Category
					$thisCategory->getCategory();
				
					//Load the form fields with the newly populated DB object						
					loadCategory($thisCategory->aCategoryRecords[0], $form);
				
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Category Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - {$thisCategory->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildCategoryObject($thisCategory);				

				//Delete record
				if ($thisCategory->deleteCategory())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "Category Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: {$thisCategory->sErrorMessage}";
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
				buildCategoryObject($thisCategory);

				//Search the Database for records matching the search criteria			
				if ($thisCategory->getCategory())
				{
					//No records found
					if(sizeof($thisCategory->aCategoryRecords) < 1)
					{
						$form->sMessage = "No Category records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisCategory->aCategoryRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadCategory($thisCategory->aCategoryRecords[0], $form);			

						$form->sMessage = "One Category record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select Category record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisCategory->sErrorMessage;
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
				$_POST['hdnCategoryID'] = NULL;
				
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
	<input type="hidden" name="hdnCategoryID" value="<?php echo $_POST['hdnCategoryID']?>" />	
	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Category Maintenance
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
		echo "	<div class='hidden-xs col-sm-10 col-md-11 result-header'>Category Name</div>";
		echo "	<div class='visible-xs col-xs-12 result-header'>Categories</div>";
		echo "</div>";

				
		foreach($thisCategory->aCategoryRecords as $oCategoryRecord)
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
		
			echo "	<div class='col-xs-12 {$sResultStyleClass}'><A HREF='./CategoryMaintenance.php?CATEGORY_ID={$oCategoryRecord->nCategoryID}'>{$oCategoryRecord->sCategoryName}</a></div>";
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
				<?php
				if ($_POST['hdnCategoryID'] > 0)
				{
					echo "<a href='". ADMIN_DIR . "/RevenueMaintenance.php?CATEGORY_ID={$_POST['hdnCategoryID']}'";
					echo " class='secondaryLinkButton'>Edit Revenues</a>";
					echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
					echo "<a href='". ADMIN_DIR . "/ExpenseMaintenance.php?CATEGORY_ID={$_POST['hdnCategoryID']}'";					
					echo " class='secondaryLinkButton'>Edit Expenses</a>";
					echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
					echo "<a href='". ADMIN_DIR . "/ProductReport.php?CATEGORY_ID={$_POST['hdnCategoryID']}'";							
					echo " class='secondaryLinkButton'>{$thisCategory->aCategoryRecords[0]->sCategoryName} - Product Report</a>";
				}
				?>
			</div>			
			<div class="col-xs-12 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
							NAME: <input type="text" name="txtCategoryName" value="<?php echo $_POST['txtCategoryName']; ?>" size="60" />
						</div>
					</div>
				<div class="row">
					<div class="col-xs-2">
						<input class="result-checkbox" type="checkbox" name="chkExpenseRelated" value="ExpenseRelated"<?php if( $_POST['chkExpenseRelated']) {echo " checked ";}; ?> />
					</div> 
					<div class="col-xs-10 result-checkbox-text">EXPENSE RELATED</div>
					<div class="col-xs-2">
						<input class="result-checkbox" type="checkbox" name="chkRevenueRelated" value="RevenueRelated"<?php if( $_POST['chkRevenueRelated']) {echo " checked ";}; ?> /> 
					</div>
					<div class="col-xs-10 result-checkbox-text">REVENUE RELATED
				  	</div>
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="row">
					<div class ="col-xs-3 FormFieldNoEdit">
						ID: <?php echo $_POST['hdnCategoryID']; ?>						
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
 * buildCategoryObject
 * 
 * This function loads builds a Category object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildCategoryObject($category)
{

	//Load the Array used to populate the form fields based on the newly loaded object
	$category->nCategoryID = $_POST['hdnCategoryID'];

	$category->sCategoryName = html_entity_decode($_POST['txtCategoryName'], ENT_QUOTES);
	$category->bFuzzyNameSearch = TRUE;
	if($_POST['chkExpenseRelated'] == "ExpenseRelated")
	{
		$category->bExpenseRelated = TRUE;
	}
	else
	{
		$category->bExpenseRelated = FALSE;
	}
 
 	if($_POST['chkRevenueRelated'] == "RevenueRelated")
	{
		$category->bRevenueRelated = TRUE;
	}
	else
	{
		$category->bRevenueRelated = FALSE;
	}

}


/*
 ********************************************************************************
 * loadCategory
 * 
 * This function loads the form field array from a populated Category object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadCategory(&$category, $form)
{

	if (!is_null($category->nCategoryID))
	{
		//Load Hidden Fields
		$_POST['hdnCategoryID'] = $category->nCategoryID;

		$_POST['txtCategoryName'] = htmlentities($category->sCategoryName, ENT_QUOTES);
		$_POST['chkExpenseRelated'] = $category->bExpenseRelated;
		$_POST['chkRevenueRelated'] = $category->bRevenueRelated;
		$_POST['txtLastUpdate'] = htmlentities($category->dtLastUpdate, ENT_QUOTES);
		
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
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

	//Artist Name is a required Field
    if ( document.CategoryMaint.txtCategoryName.value == "" )
    {
        sErrorMessage += "Category Name Required\n";
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
