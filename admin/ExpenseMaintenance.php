<?php
/*
*******************************************************************
ExpenseMaintenance.php
This PHP file defines the Maintenance page for 
managing Expenses.
NOTES
Date        Change
-------------------------------------------------------------
2015-05-05	Refactored and added dynamic building of Product Drop-down.
2015-05-24  Corrected problem with Expense Total
2015-10-19	Added PERF_RELATED parm processing
2015-10-23	Added YEAR parm processing<br />
2016-09-15 	Added check for ADD_EXPENSE parm
2016-12-05	Responsified Page
2018-02-22	Added Render Vendor Dropdown call
2021-08-30	Updated for PHP 8
2022-01-25	Added ability for no product search
2024-05-17	Added Colorado Sessions Flag
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

 $sActiveMenuItem = FINANCES_ACTIVE;
 $sPageName = "Expense Maintenance";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Expense Maintenance</title>
	<?php

	?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHead-Responsive.php");

?>

<body>

	<!-- DIV used for Image Preview Popup -->
	<div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15" id="preview_div"></div>

	<?php
	
	//include Expense Class		
 	include_once (CLASS_DIR . "/class_Expense.php");

	//include Category Class		
 	include_once (CLASS_DIR . "/class_Category.php");

	//include Tour Class		
 	include_once (CLASS_DIR . "/class_Tour.php");

	//include Product Class		
 	include_once (CLASS_DIR . "/class_Product.php");

	//include Vendor Class		
 	include_once (CLASS_DIR . "/class_Vendor.php");

	//include TaxCategory Class		
 	include_once (CLASS_DIR . "/class_TaxCategory.php");

 	//include Form Class		
 	include (CLASS_DIR . "/class_Form.php");

	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");

	//Array of Expense records from the DB
	global $aExpenseRecords;
	
	global $nThisExpenseID;
	
	$oExpenseCategories = NULL;
	
	?>
	
<div class="container-fluid"> 	
	
<form name="ExpenseMaint" action="ExpenseMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$thisExpense = new Expense();
		$form = new Form();

		//If a Vendor ID is passed, go ahead and search expenses for that Vendor
		if (isset($_REQUEST['VENDOR_ID']) && $_REQUEST['VENDOR_ID'] != "")
		{
			$_POST['selVendor'] = $_REQUEST['VENDOR_ID'];
			$_POST['btnSearch'] = "Search";
		}

		//If a Product ID is passed, go ahead and search expenses for that Product
		if (isset($_REQUEST['PRODUCT_ID']) && $_REQUEST['PRODUCT_ID'] != "")
		{
			$_POST['selProduct'] = $_REQUEST['PRODUCT_ID'];	
			
			//If the action is not to Add search for Expenses
			if (!isset($_REQUEST['ACTION']) || $_REQUEST['ACTION'] != "ADD_EXPENSE")
			{
				$_POST['btnSearch'] = "Search";
			}
		}

		//If a Tour ID is passed, go ahead and search expenses for that Tour
		if (isset($_REQUEST['TOUR_ID']) && $_REQUEST['TOUR_ID'] != "")
		{
			$_POST['hdnTourID'] = $_REQUEST['TOUR_ID'];
			//If an action is passed to add the expense, go into add mode 
			if (!isset($_REQUEST['ACTION']) || $_REQUEST['ACTION'] != "ADD_EXPENSE")
			{
				
				$_POST['selTour'] = $_REQUEST['TOUR_ID'];
				$_POST['btnSearch'] = "Search";
			}
		}

		//If a Tax Category ID is passed, go ahead and search expenses for that Tax Category
		if (isset($_REQUEST['TAX_CATEGORY_ID']) && $_REQUEST['TAX_CATEGORY_ID'] != "")
		{
			$_POST['selTaxCategory'] = $_REQUEST['TAX_CATEGORY_ID'];
			$_POST['btnSearch'] = "Search";
		}

		//If a Year is passed, go ahead and search expenses for that Year
		if (isset($_REQUEST['YEAR']) && $_REQUEST['YEAR'] != "")
		{
			$_POST['nYear'] = $_REQUEST['YEAR'];
			$_POST['btnSearch'] = "Search";
		}


		//If a Category ID is passed, go ahead and search expenses for that Category
		if (isset($_REQUEST['CATEGORY_ID']) && $_REQUEST['CATEGORY_ID'] != "")
		{
			$_POST['chkCategory' . $_REQUEST['CATEGORY_ID']] = "CategoryAssociated";
			$_POST['btnSearch'] = "Search";
		}

		//If a Start Date and an is passed, go ahead and search revenues for that date
		if (isset($_REQUEST['START_DATE']) && $_REQUEST['START_DATE'] != "")
		{
			$_POST['hdnStartDate'] = $_REQUEST['START_DATE'];
			$_POST['btnSearch'] = "Search";
		}

		//If a End Date and an is passed, go ahead and search revenues for that date
		if (isset($_REQUEST['END_DATE']) && $_REQUEST['END_DATE'] != "")
		{
			$_POST['hdnEndDate'] = $_REQUEST['END_DATE'];
			$_POST['btnSearch'] = "Search";
		}

		//If a Performance Related indicator is passed, go ahead and search revenues for that date
		if (isset($_REQUEST['PERF_RELATED']) && $_REQUEST['PERF_RELATED'] == "TRUE")
		{
			$_POST['bPerformanceRelated'] = TRUE;
			$_POST['btnSearch'] = "Search";
		}

		//If a Colorado Sessions indicator is passed, go ahead and search revenues for Colorado Sessions revenues
		if (isset($_REQUEST['COLORADO_SESSIONS']) && $_REQUEST['COLORADO_SESSIONS'] == "TRUE")
		{
			$sColoradoSessionsFormId = 'chkCategory' . COLORAD_SESSIONS_CAT_ID;
			$_POST[$sColoradoSessionsFormId] = TRUE;
			$_POST['btnSearch'] = "Search";
		}

		//Get the ID query string parameter
		if (isset($_REQUEST['ID']) && $_REQUEST['ID'] !="")
		{
			$nThisExpenseID = $_REQUEST['ID'];
		}
		
		//Get the Expense Categories
		$oExpenseCategories = new Category();
		$oExpenseCategories->bExpenseRelated = TRUE;

		//Get all Expense Categories
		if (!$oExpenseCategories->getCategory())
		{
			$form->sMessage = "Error Retrieving Expense Categories:{$oExpenseCategories->sErrorMessage}";
			$form->nMessageType = MESSAGE_TYPE_INFO;
			$form->nFormMode = FORM_MODE_EDIT;			
		}
		else
		{
			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisExpenseID))
			{
				$thisExpense->nExpenseID = $nThisExpenseID;
	
					//Search the Database for records matching the search criteria			
				if ($thisExpense->getExpense())
				{
				
					//Records found
					if (sizeof($thisExpense->aExpenseRecords) > 0)
					{
						
						//Only One Record should be returned.  Add this to the form field array						
						//so that it displays in the form fields and to the values in the
						//current Object.
						loadExpense($thisExpense->aExpenseRecords[0], $form);			
	
						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
					}
					else
					{
						//The record was not found
						$form->sMessage = "Expense record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}
				}
				else
				{
					//Error
					$form->sMessage = $thisExpense->sErrorMessage;
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
					buildExpenseObject($thisExpense);
					
					//Insert record
					if ($thisExpense->insertExpense())			
					{
	
						//If the insert was successful, update the category associations
						if(updateExpenseCategories($thisExpense))
						{
					
							//Reload Expense newly inserted expense
							$nNewExpenseID = $thisExpense->nExpenseID;
							$thisExpense = new Expense();
							$thisExpense->nExpenseID = $nNewExpenseID;	
	
							if ($thisExpense->getExpense())
							{	
								//Load the form fields with the newly populated object
								loadExpense($thisExpense->aExpenseRecords[0], $form);
								//Store the ID of the performance
								$nThisExpenseID = $thisExpense->aExpenseRecords[0]->nExpenseID;			
								
								//Success
								$form->nMessageType = MESSAGE_TYPE_INFO;
								$form->sMessage = "Expense Added";
								$form->nFormMode = FORM_MODE_EDIT;			
							}
							else
							{
								//Problem reloading screen
								clearFormFields($form);
								$form->nMessageType = MESSAGE_TYPE_ERROR;
								$form->sMessage = "Expense Added, but error occured while reloading the performance data";
								$form->nFormMode = FORM_MODE_NEW;			
							}
						}
						else
						{
							//The update of the category associations failed 
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "ERROR: {$thisExpense->sErrorMessage}";
							$form->nFormMode = FORM_MODE_EDIT;			
						}
					}
					else
					{
					
						//The insert of the new expense failed
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED: {$thisExpense->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
						
				}					
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) 
				{
	
					//Load values from form field array into DB object
					buildExpenseObject($thisExpense);
	
					//Update record
					if ($thisExpense->updateExpense())			
					{
					
						//If the update was successful, update the category associations
						if(updateExpenseCategories($thisExpense))
						{
					
							//reload the updated Expense record
							$thisExpense->getExpense();
						
							//Load the form fields with the newly populated DB object						
							loadExpense($thisExpense->aExpenseRecords[0], $form);
							//Store the ID of the performance
							$nThisExpenseID = $thisExpense->aExpenseRecords[0]->nExpenseID;			
						
							//Success
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Expense Updated";
							$form->nFormMode = FORM_MODE_EDIT;	
						}
						else
						{
							//Failed to updated categories
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "ERROR: {$thisExpense->sErrorMessage}";
							$form->nFormMode = FORM_MODE_EDIT;			
						}
			
					}
					else
					{
						//Failed to update expense record
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed - {$thisExpense->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;			
					}
				}
				// **************
				// *   DELETE   *
				// **************
				else if (isset($_POST["btnDelete"])) 
				{
				
					//Load DB record
					buildExpenseObject($thisExpense);				
	
					//Delete record
					if ($thisExpense->deleteExpense())
					{
						//Clear the form fields
						clearFormFields($form);
						
						//Success
						$form->sMessage = "Expense Deleted";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;					
					}
					else
					{
						$form->sMessage = "DELETE FAILED: {$thisExpense->sErrorMessage}";
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
					buildExpenseObject($thisExpense);
					
					//MAYBE SHOULD CREATE A NONE AND AN ALL
					$thisExpense->bNoProduct = FALSE;

					//Search the Database for records matching the search criteria			
					if ($thisExpense->getExpense())
					{
						//No records found
						if(sizeof($thisExpense->aExpenseRecords) < 1)
						{
							$form->sMessage = "No Expense records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;			
						}			
						else if (sizeof($thisExpense->aExpenseRecords) == 1)
						{
							//Only One Record returned.  Add this to the form field array
							//so that it displays in the form fields
							loadExpense($thisExpense->aExpenseRecords[0], $form);
							//Store the ID of the performance
							$nThisExpenseID = $thisExpense->aExpenseRecords[0]->nExpenseID;			
	
							$form->sMessage = "One Expense record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;			
							
						}
						//If Multiple records found, the array of search reults will be populated
						else 
						{
							//Multiiple records returned
							$form->sMessage = "Select Expense record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;			
						}
	
					}
					else
					{
						//Attempt to get records failed
						$form->sMessage = $thisExpense->sErrorMessage;
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
					$_POST['hdnExpenseID'] = NULL;
					
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
		}

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnExpenseID" value="<?php echo $_POST['hdnExpenseID']?>" />	
	<input type="hidden" id="hdnProductID" name="hdnProductID" value="<?php echo $_POST['hdnProductID']?>" />	
	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Expense Maintenance
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
			echo "<div class=\"row result-header\">";
			echo "	<div class='hidden-xs col-sm-4 col-md-3 result-header'>Date</div>";
			echo "	<div class='hidden-xs col-sm-4 col-md-3 result-header'>Amount</div>";
			echo "	<div class='hidden-xs col-sm-4 col-md-6 result-header'>Description</div>";
			echo "	<div class='visible-xs col-xs-12 result-header'>Expenses</div>";
			echo "</div>";
	
			$nExpenseAmountTotal =0;		
			foreach($thisExpense->aExpenseRecords as $oExpenseRecord)
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
							
				echo "	<div class='col-xs-12 col-sm-4 col-md-3 result-selector {$sResultStyleClass}'><A HREF='./ExpenseMaintenance.php?ID={$oExpenseRecord->nExpenseID}'>{$oExpenseRecord->dtExpenseDate}</A></div>";
				echo "	<div class='col-xs-12 col-sm-4 col-md-3 result-selector {$sResultStyleClass}'><A HREF='./ExpenseMaintenance.php?ID={$oExpenseRecord->nExpenseID}'> \${$oExpenseRecord->nExpenseAmount} </A></div>";
				echo "	<div class='col-xs-12 col-sm-4 col-md-6 result-selector {$sResultStyleClass}'><A HREF='./ExpenseMaintenance.php?ID={$oExpenseRecord->nExpenseID}'>{$oExpenseRecord->sExpenseDescription}</A></div>";
				$nExpenseAmountTotal += $oExpenseRecord->nExpenseAmount;
				echo "</div>";
				
			}
	
			echo "<div class=\"row\">";
			echo "	<div class='col-xs-12 col-sm-4 col-md-3'>Total</div>";
			echo "	<div class='col-xs-12 col-sm-4 col-md-3'> \$ {$nExpenseAmountTotal}</div>";
			echo "	<div class='col-xs-12 col-sm-4 col-md-6'></div>";
			echo "</div>";
			?>
		
		</div>	
		
	<?php
	}
	else
	{
	?>
		<div class="row"> 
			<div class="col-xs-12 col-md-8 FieldGroupTitle">
				DETAILS
			</div>			
			<div class="col-xs-12 col-md-8 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
						DATE:<BR /> 
						<?php	  
							renderDatePicker("ExpenseDate", $_POST['ExpenseDate']);
						?>	
					</div>
					<div class="col-xs-12">
						DESCRIPTION: 
						  <input type="text" name="txtExpenseDescription" value="<?php echo $_POST['txtExpenseDescription']; ?>" size="60" />&nbsp;&nbsp;
					</div>
					<div class="col-xs-12">
						AMOUNT: 
						  <input type="text" name="txtExpenseAmount" value="<?php echo $_POST['txtExpenseAmount']; ?>" size="20" />&nbsp;&nbsp;
					</div>
					<div class="col-xs-12">
						<?php
						renderTourDropDown();
						?>
					</div>
					<div class="col-xs-12">
						<?php
						renderProductDropDown($_POST['selProduct']);
						?>
					</div>
					<div class="col-xs-12">
						<?php
							renderVendorDropDown($_POST['selVendor']);
						?>						
					</div>
					<div class="col-xs-12">
						<?php
							renderTaxCategoryDropDown($_POST['selTaxCategory']);
						?>	
						
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
						CATEGORIES
					</div>
						<?php
						
						foreach ($oExpenseCategories->aCategoryRecords as $oCategory)
						{
							$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
	
							echo "<div class='col-xs-2 category-selector {$sResultStyleClass}'>";
							echo "<input type='checkbox' name='chkCategory{$oCategory->nCategoryID}' class='result-checkbox'";
							echo " value='CategoryAssociated'";
							if (isset($_POST['chkCategory' . $oCategory->nCategoryID]))
							{
								echo " checked ";
							}
							echo ">";
							echo "</div>";
							echo "<div class='col-xs-10 category-name  result-checkbox-text {$sResultStyleClass}'>";
							echo $oCategory->sCategoryName . "</div>";
						}
						?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class ="col-xs-3 FormFieldNoEdit">
				ID: <?php echo $_POST['hdnExpenseID']; ?>						
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
	<?php
	}
	?>	
</form>
</div>
<?php		

/*
 ********************************************************************************
 * buildExpenseObject
 * 
 * This function loads builds a flag object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildExpenseObject($oExpense)
{
	global $oExpenseCategories;
	
	//Load the Array used to populate the form fields based on the newly loaded object
	$oExpense->nExpenseID = $_POST['hdnExpenseID'];
	$oExpense->nTourID = $_POST['hdnTourID'];
	$oExpense->dtStartDate = $_POST['hdnStartDate'];
	$oExpense->dtEndDate = $_POST['hdnEndDate'];
	$oExpense->nExpenseYear = $_POST['nYear'];
	$oExpense->bPerformanceRelated = $_POST['bPerformanceRelated'];

	$oExpense->nExpenseAmount = html_entity_decode($_POST['txtExpenseAmount'], ENT_QUOTES);
	$oExpense->sExpenseDescription = html_entity_decode($_POST['txtExpenseDescription'], ENT_QUOTES);
	$oExpense->bFuzzyNameSearch = TRUE;

	//Expense Date	
	$dtExpenseDate = isset($_REQUEST["ExpenseDate"]) ? $_REQUEST["ExpenseDate"] : "";
	if($dtExpenseDate > "0000-00-00")
	{
		//If no datepicker is displayed, use the hidden field
		$dtExpenseDate = isset($_POST["ExpenseDate"]) ? $_POST["ExpenseDate"] : "";
	}
	if($dtExpenseDate > "0000-00-00")
	{
		$oExpense->dtExpenseDate  = $dtExpenseDate;
	}

	$oExpense->nProductID = $_POST['selProduct'];
	$oExpense->nVendorID = $_POST['selVendor'];
	$oExpense->nTaxCategoryID = $_POST['selTaxCategory'];
	
	$aExpenseCategoryIDs = array();
	$iExpenseIndex = 0;

	//Load an array of all Category IDs for Checked Categories
	foreach($oExpenseCategories->aCategoryRecords as $oExpenseCategory)
	{
		if (isset($_POST['chkCategory' . $oExpenseCategory->nCategoryID]))
		{
			$aExpenseCategoryIDs[$iExpenseIndex] = $oExpenseCategory->nCategoryID;
			$oExpense->aExpenseCategoryIDs = $aExpenseCategoryIDs;
		}
		
		$iExpenseIndex++;
	}

}


/*
 ********************************************************************************
 * loadExpense
 * 
 * This function loads the form field array from a populated Expense object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadExpense(&$oExpense, $form)
{
	global $oExpenseCategories;

	if (!is_null($oExpense->nExpenseID))
	{
		//Load Hidden Fields
		$_POST['hdnExpenseID'] = $oExpense->nExpenseID;
		$_POST['hdnTourID'] = $oExpense->nTourID;

		$_POST['txtExpenseDescription'] = htmlentities($oExpense->sExpenseDescription, ENT_QUOTES);
		$_POST['ExpenseDate'] = htmlentities($oExpense->dtExpenseDate, ENT_QUOTES);
		$_POST['txtExpenseAmount'] = htmlentities($oExpense->nExpenseAmount, ENT_QUOTES);
		if ($oExpense->nProductID > 0)
		{
			$_POST['selProduct'] = $oExpense->nProductID;
		}
		else
		{
			$_POST['selProduct'] = 0;
		}

		if ($oExpense->nVendorID > 0)
		{
			$_POST['selVendor'] = $oExpense->nVendorID;
		}
		else
		{
			$_POST['selVendor'] = 0;
		}

		if ($oExpense->nTaxCategoryID > 0)
		{
			$_POST['selTaxCategory'] = $oExpense->nTaxCategoryID;
		}
		else
		{
			$_POST['selTaxCategory'] = 0;
		}
		
		//Load the form field array with Category IDs assoiated with this Expense
		$aThisExpenseCategories = array();
		$i = 0;
		foreach($oExpenseCategories->aCategoryRecords as $oExpenseCategory)
		{
			if($oExpense->categoryExists($oExpenseCategory->nCategoryID))
			{
				$_POST['chkCategory' . $oExpenseCategory->nCategoryID] = "CategoryAssociated";
			}
		}
		

		$_POST['txtLastUpdate'] = htmlentities($oExpense->dtLastUpdate, ENT_QUOTES);
		
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

	global $oExpenseCategories;
	
	//Load Form field values into array 
	foreach($_POST as $fieldName=>$fieldValue) 
	{
		$_POST[$fieldName]= htmlentities(stripslashes($fieldValue));
		
	}
	
}

/*
 ********************************************************************************
 * updateExpenseCategories()
 * 
 * This function updates the expense categories based on which categories are
 * selected on the page.
 ********************************************************************************
*/
function updateExpenseCategories(&$oExpense)
{
	$oExpenseCategories = new Category();
	$oExpenseCategories->bExpenseRelated = TRUE;
	if($oExpenseCategories->getCategory())
	{

		//First delete all Expense Category Xref records
		if($oExpense->removeExpenseCategory(NULL))
		{
			//Then add back any xrefs for Categories selected
			foreach($oExpenseCategories->aCategoryRecords as $oExpenseCategory)
			{
				$sCheckboxName = "chkCategory{$oExpenseCategory->nCategoryID}";
				if(isset($_POST[$sCheckboxName]) || $_POST[$sCheckboxName] == 'CategoryAssociated')
				{
					if (!$oExpense->addExpenseCategory($oExpenseCategory->nCategoryID))
					{
						//ERRROR
						return FALSE;
					}
				}
			}
			
			return TRUE;
		}
		else
		{
			//ERRROR
			return FALSE;
		}
	}
	else
	{
		//ERROR
		return FALSE;
	}

}

?>

</body>

<script type="text/javascript">

//******************************************
// validate_form
// This script checks the user imnput for
// errors or missing data 
//******************************************
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

	//Expense Name is a required Field
    if ( document.ExpenseMaint.txtExpenseDescription.value == "" )
    {
        sErrorMessage += "Expense Description Required\n";
        bValid = false;
    }

	//Expense Date is a required Field
    if ( document.ExpenseMaint.ExpenseDate.value == "" || document.ExpenseMaint.ExpenseDate.value == "0000-00-00" )
    {
        sErrorMessage += "Expense Date Required\n";
        bValid = false;
    }

	//Tax Category is a required Field
    if ( document.ExpenseMaint.selTaxCategory.value == "" || document.ExpenseMaint.selTaxCategory.value == 0 )
    {
        sErrorMessage += "Tax Category Required\n";
        bValid = false;
    }

	//Expense Amount is a required, numeric, positive Field
    if ( document.ExpenseMaint.txtExpenseAmount.value == "" )
    {
        sErrorMessage += "Expense Amount Required\n";
        bValid = false;
    }
	else if(!isNumeric(document.ExpenseMaint.txtExpenseAmount.value)) 
	{
        sErrorMessage += "Expense Amount Must be Numeric\n";
        bValid = false;
	}
	else if(Number(document.ExpenseMaint.txtExpenseAmount.value) <= 0) 
	{
        sErrorMessage += "Expense Amount must be greater than 0\n";
        bValid = false;
	}

	//If any errors, alert
	if (!bValid)
	{
		alert(sErrorMessage);
	}

    return bValid;
}

//*********************************************
// buildTourLink
// This function builds a link to the Tour
// Maintenance page based on the Tour
// chosen in the drop-down list
//*********************************************
function buildTourLink() 
{
	setTourID();
	if(document.getElementById("selTour").value > 0)
	{
		sTourMaintLink = "<SMALL><I><A HREF='./TourMaintenance.php?ID=" + document.getElementById("selTour").value + "'>Edit Tour</A></I></SMALL>";  
		document.getElementById("lnkTourMaint").innerHTML = sTourMaintLink;
	}
	else
	{
		document.getElementById("lnkTourMaint").innerHTML = "";
	}
}

//*********************************************
// getTours
// This function builds the tour drop-down list
//*********************************************
function getTours() {
	sDate = document.getElementById("ExpenseDate").value;

    if (sDate == "" || sDate == "0000-00-00") {
        document.getElementById("selTour").innerHTML = "<OPTION Value='0'>NONE</OPTION>";
		document.getElementById("hdnTourID").value = 0;
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("selTour").innerHTML = xmlhttp.responseText;
//alert("VALUE is " + document.getElementById("selTour").value);
				if(document.getElementById("selTour").value == 0)
				{	
					document.getElementById("hdnTourID").value = 0;
				}
            }
        }
//alert("CALLING GetPotentialTours.php?date="+sDate);		
        xmlhttp.open("GET","GetPotentialTours.php?date="+sDate,true);
        xmlhttp.send();
    }
	
}

//*********************************************
// getTours
// This function sets the Tour ID hidden form 
// field to that of the toru selected in the 
// drop-down list
//*********************************************
function setTourID() {

//alert("Setting TourID to " + document.getElementById("selTour").value);
	document.getElementById("hdnTourID").value = document.getElementById("selTour").value;
}
</script>

</html>
