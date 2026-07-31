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
2026-07-30	Migrated to new Datalayer TaxCategory repository
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
include_once(DATALAYER_DIR . "/TaxCategory.php");
include_once(DATALAYER_DIR . "/TaxCategoryRepository.php");
include_once(DATALAYER_DIR . "/Expense.php");
include_once(DATALAYER_DIR . "/ExpenseRepository.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of TaxCategory records from the DB
$aTaxCategoryRecords = [];

$sActiveMenuItem = FINANCES_ACTIVE;
$sPageName = "Tax Category Maintenance";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

<div class="container-fluid">	
<form name="TaxCategoryMaint" action="TaxCategoryMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$taxCategoryRepo = new \Datalayer\TaxCategoryRepository();
		$expenseRepo = new \Datalayer\ExpenseRepository();
		$thisTaxCategory = new \Datalayer\TaxCategory();
		$form = new Form();

		//Get the ID query string parameter
		$nThisTaxCategoryID = $_REQUEST['ID'] ?? null;
		
		try {
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisTaxCategoryID) && $nThisTaxCategoryID !== '')
		{
			$entity = $taxCategoryRepo->findById((int) $nThisTaxCategoryID);

			if ($entity) {
				$thisTaxCategory = $entity;
				$aTaxCategoryRecords = [$entity];
				loadTaxCategory($thisTaxCategory, $form);

				$form->sMessage = "Update record.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;
			} else {
				$form->sMessage = "TaxCategory record not found.";
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
				buildTaxCategoryObject($thisTaxCategory);
				
				if ($taxCategoryRepo->insert($thisTaxCategory))
				{
					$thisTaxCategory = $taxCategoryRepo->findById((int) $thisTaxCategory->id) ?? $thisTaxCategory;
					$aTaxCategoryRecords = [$thisTaxCategory];
					loadTaxCategory($thisTaxCategory, $form);
					
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Tax Category Added";
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
				buildTaxCategoryObject($thisTaxCategory);
				
				if ($taxCategoryRepo->update($thisTaxCategory))
				{
					$thisTaxCategory = $taxCategoryRepo->findById((int) $thisTaxCategory->id) ?? $thisTaxCategory;
					$aTaxCategoryRecords = [$thisTaxCategory];
					loadTaxCategory($thisTaxCategory, $form);
				
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Tax Category Updated";
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
				buildTaxCategoryObject($thisTaxCategory);

				$deleteError = null;
				if (!empty($thisTaxCategory->id)) {
					$relatedExpenses = $expenseRepo->find(['taxCategoryId' => (int) $thisTaxCategory->id]);

					if (sizeof($relatedExpenses) > 0) {
						$deleteError = "TXC011 - Can not delete Tax Category because it has ";
						$deleteError .= "<A HREF='" . ADMIN_DIR . "/ExpenseMaintenance.php?TAX_CATEGORY_ID=" . $thisTaxCategory->id;
						$deleteError .= "'>" . sizeof($relatedExpenses) . " expenses.</A>";
					}
				}

				if ($deleteError !== null) {
					$form->sMessage = $deleteError;
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_EDIT;
				} else if (!empty($thisTaxCategory->id) && $taxCategoryRepo->delete((int) $thisTaxCategory->id)) {
					clearFormFields($form);
					
					$form->sMessage = "Tax Category Deleted";
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
				buildTaxCategoryObject($thisTaxCategory);

				$aTaxCategoryRecords = $taxCategoryRepo->find([
					'id' => $thisTaxCategory->id,
					'name' => $thisTaxCategory->name,
					'fuzzyName' => true,
				]);

				if (sizeof($aTaxCategoryRecords) < 1)
				{
					$form->sMessage = "No Tax Category records found matching search criteria";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}			
				else if (sizeof($aTaxCategoryRecords) == 1)
				{
					$thisTaxCategory = $aTaxCategoryRecords[0];
					loadTaxCategory($thisTaxCategory, $form);			

					$form->sMessage = "One Tax Category record found.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else 
				{
					$form->sMessage = "Select Tax Category record to edit from results list below.";
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
		} catch (\Throwable $e) {
			$form->sMessage = $e->getMessage();
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->nFormMode = FORM_MODE_NEW;
		}

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnTaxCategoryID" value="<?php echo $_POST['hdnTaxCategoryID'] ?? '' ?>" />	

	<div class="row">
<?php
include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
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

				
		foreach($aTaxCategoryRecords as $oTaxCategoryRecord)
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
				
			echo "	<div class='col-xs-12 result-selector {$sResultStyleClass}'><A HREF='./TaxCategoryMaintenance.php?ID={$oTaxCategoryRecord->id}'>{$oTaxCategoryRecord->name}</a></div>";
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
				NAME: <input type="text" name="txtTaxCategoryName" value="<?php echo $_POST['txtTaxCategoryName'] ?? ''; ?>" size="60" />
			</div>
			<div class="col-xs-12">		
				<div class="row">
					<div class ="col-xs-3 FormFieldNoEdit">
						ID: <?php echo $_POST['hdnTaxCategoryID'] ?? ''; ?>						
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
 * buildTaxCategoryObject
 * 
 * This function loads builds a TaxCategory object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildTaxCategoryObject(\Datalayer\TaxCategory $taxCategory)
{
	$id = $_POST['hdnTaxCategoryID'] ?? null;
	$taxCategory->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$taxCategory->name = html_entity_decode($_POST['txtTaxCategoryName'] ?? '', ENT_QUOTES);
}


/*
 ********************************************************************************
 * loadTaxCategory
 * 
 * This function loads the form field array from a populated TaxCategory object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadTaxCategory(\Datalayer\TaxCategory $taxCategory, $form)
{
	if (!is_null($taxCategory->id))
	{
		$_POST['hdnTaxCategoryID'] = $taxCategory->id;

		$_POST['txtTaxCategoryName'] = htmlentities($taxCategory->name ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($taxCategory->lastUpdate ?? '', ENT_QUOTES);
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
