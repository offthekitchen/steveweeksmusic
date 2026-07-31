<?php
/*
*******************************************************************
ProductMaintenance.php
This PHP file defines the Maintenance page for 
managing Products.
NOTES
Date        Change
-------------------------------------------------------------
2015-06-04	Removed Product Type
2016-12-11	Made Responsive
2016-12-29	Improved Responsivity for phone
2019-08-14  Added Artist Dropdown
2026-07-30	Migrated to new Datalayer Product repository
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

//inlcude Common Functions
include_once(ADMIN_INCLUDE_DIR . "/CommonFunctions.php");

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Product.php");
include_once(DATALAYER_DIR . "/ProductRepository.php");
include_once(DATALAYER_DIR . "/Expense.php");
include_once(DATALAYER_DIR . "/ExpenseRepository.php");
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of Product records from the DB
$aProductRecords = [];

$sActiveMenuItem = PRODUCTS_ACTIVE;
$sPageName = "Product Maintenance";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<!-- DIV used for Image Preview Popup -->
	<div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15" id="preview_div"></div>

<div class="container-fluid">	
<form name="ProductMaint" action="ProductMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$productRepo = new \Datalayer\ProductRepository();
		$expenseRepo = new \Datalayer\ExpenseRepository();
		$revenueRepo = new \Datalayer\RevenueRepository();
		$thisProduct = new \Datalayer\Product();
		$form = new Form();

		//Get the ID query string parameter
		$nThisProductID = $_REQUEST['ID'] ?? null;

		//If an Artist ID is passed, go ahead and search products for that artist
		if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "")
		{
			$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
			$_POST['btnSearch'] = "Search";
		}

		try {
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisProductID) && $nThisProductID !== '')
		{
			$entity = $productRepo->findById((int) $nThisProductID);

			if ($entity) {
				$thisProduct = $entity;
				$aProductRecords = [$entity];
				loadProduct($thisProduct, $form);

				$form->sMessage = "Update record.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;
			} else {
				$form->sMessage = "Product record not found.";
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
				buildProductObject($thisProduct);
				
				if ($productRepo->insert($thisProduct))
				{
					$thisProduct = $productRepo->findById((int) $thisProduct->id) ?? $thisProduct;
					$aProductRecords = [$thisProduct];
					loadProduct($thisProduct, $form);
					
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Product Added";
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
				buildProductObject($thisProduct);
				
				if ($productRepo->update($thisProduct))
				{
					$thisProduct = $productRepo->findById((int) $thisProduct->id) ?? $thisProduct;
					$aProductRecords = [$thisProduct];
					loadProduct($thisProduct, $form);
				
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Product Updated";
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
				buildProductObject($thisProduct);

				$deleteError = null;
				if (!empty($thisProduct->id)) {
					$relatedExpenses = $expenseRepo->find(['productId' => (int) $thisProduct->id]);
					$relatedRevenues = $revenueRepo->find(['productId' => (int) $thisProduct->id]);

					if (sizeof($relatedExpenses) > 0) {
						$deleteError = "PRD012 - Can not delete Product because it has ";
						$deleteError .= "<A HREF='" . ADMIN_DIR . "/ExpenseMaintenance.php?PRODUCT_ID=" . $thisProduct->id . "'>";
						$deleteError .= sizeof($relatedExpenses) . " expenses.</A>";
					} elseif (sizeof($relatedRevenues) > 0) {
						$deleteError = "PRD013 - Can not delete Product because it has ";
						$deleteError .= "<A HREF='" . ADMIN_DIR . "/RevenueMaintenance.php?PRODUCT_ID=" . $thisProduct->id . "'>";
						$deleteError .= sizeof($relatedRevenues) . " revenues.</A>";
					}
				}

				if ($deleteError !== null) {
					$form->sMessage = $deleteError;
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_EDIT;
				} else if (!empty($thisProduct->id) && $productRepo->delete((int) $thisProduct->id)) {
					clearFormFields($form);
					
					$form->sMessage = "Product Deleted";
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
				buildProductObject($thisProduct);

				$criteria = [
					'id' => $thisProduct->id,
					'name' => $thisProduct->name,
					'fuzzyName' => true,
					'image' => $thisProduct->image,
					'thumbnail' => $thisProduct->thumbnail,
				];

				if (!empty($thisProduct->artistId)) {
					$criteria['artistId'] = $thisProduct->artistId;
				}

				$aProductRecords = $productRepo->find($criteria);

				if (sizeof($aProductRecords) < 1)
				{
					$form->sMessage = "No Product records found matching search criteria";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}			
				else if (sizeof($aProductRecords) == 1)
				{
					$thisProduct = $aProductRecords[0];
					loadProduct($thisProduct, $form);			

					$form->sMessage = "One Product record found.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else 
				{
					$form->sMessage = "Select Product record to edit from results list below.";
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
				$_POST['hdnProductID'] = NULL;
				
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
	<input type="hidden" name="hdnProductID" value="<?php echo $_POST['hdnProductID'] ?? ''?>" />	
	<div class="row">
<?php
	include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Product Maintenance
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
			echo "	<div class='hidden-xs col-sm-12 result-header'>Product Name</div>";
			echo "	<div class='visible-xs col-xs-12 result-header'>Products</div>";
			echo "</div>";

				
		foreach($aProductRecords as $oProductRecord)
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
			echo "<div class='col-xs-12 result-selector {$sResultStyleClass}'>";
			echo "<A HREF='./ProductMaintenance.php?ID={$oProductRecord->id}'>{$oProductRecord->name}</A>";
			echo "</div>";
			
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
			<div class="col-xs-12">
				<?php
				if (($_POST['hdnProductID'] ?? 0) > 0)
				{
					$nProductId = $_POST['hdnProductID'];
					$sProductName = $aProductRecords[0]->name ?? ($_POST['txtProductName'] ?? 'Product');
					echo "<a href='". ADMIN_DIR . "/ExpenseMaintenance.php?PRODUCT_ID={$nProductId}";					
					echo "'  class='secondaryLinkButton'>Edit Expenses</a>";					
					echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/ExpenseMaintenance.php?PRODUCT_ID={$nProductId}";										
					echo "&ACTION=ADD_EXPENSE'  class='secondaryLinkButton'>Enter Expense</a>";					
					echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";					
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/ProductReport.php?PRODUCT_ID={$nProductId}";												
					echo "'  class='secondaryLinkButton'>\"{$sProductName}\" Report</a>";
				}
				?>
			</div>			
			<div class="col-xs-12 col-md-6 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
						NAME: <input type="text" name="txtProductName" value="<?php echo $_POST['txtProductName'] ?? ''; ?>" placeholder="Product Name" size="60" />
					</div>
					<div class="col-xs-12">
						<?php
							renderArtistDropDown($_POST['selArtist'] ?? 0);
						?>
					</div>
					
				</div>
			</div>
			<div class="col-xs-12 col-md-3 FieldGroup">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-12 image-preview">
						<span class="Subtitle"> Image</span><br />												
						<?php
						if (!empty($_POST['txtImage']))
						{
							$sFullImagePath =IMG_DIR . "/{$_POST['txtImage']}";
						}
						else
						{
							$sFullImagePath = NULL;
						}
						$form->renderImagePreview($sFullImagePath, ""); 
						?>												
						<input type="text" name="txtImage" id="txtImage" value="<?php echo $_POST['txtImage'] ?? ''; ?>" size="30" /><BR />
						<input type="button" name="btnPreviewImage" class="btnPreviewImage" onclick="preview_image('txtImage','<?php echo IMG_DIR ?>')" value="Preview" />												
					</div>
					<div class="col-xs-12 col-sm-6 col-md-12 thumbnail-preview">
						<span class="Subtitle">Thumbnail Image</span><br />												
						<?php
						if (!empty($_POST['txtThumbnail']))
						{
							$sFullImagePath = IMG_DIR . "/{$_POST['txtThumbnail']}";
						}
						else
						{
							$sFullImagePath = NULL;
						}
						$form->renderImagePreview($sFullImagePath, ""); 
						?>												
						<input type="text" name="txtThumbnail" id="txtThumbnail" value="<?php echo $_POST['txtThumbnail'] ?? ''; ?>" size="30" /><BR />
						<input type="button" name="btnPreviewThumbnail" class="btnPreviewImage" onclick="preview_image('txtThumbnail','<?php echo IMG_DIR ?>')" value="Preview" />												
					</div>
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="row FormFieldNoEdit">
					<div class ="col-xs-3 FormFieldNoEdit">
						ID: <?php echo $_POST['hdnProductID'] ?? ''; ?>						
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
 * buildProductObject
 * 
 * This function loads builds a product object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildProductObject(\Datalayer\Product $Product)
{
	$id = $_POST['hdnProductID'] ?? null;
	$Product->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$Product->name = html_entity_decode($_POST['txtProductName'] ?? '', ENT_QUOTES);
	$Product->artistId = (int) ($_POST['selArtist'] ?? 0);
	$Product->image = html_entity_decode($_POST['txtImage'] ?? '', ENT_QUOTES);
	$Product->thumbnail = html_entity_decode($_POST['txtThumbnail'] ?? '', ENT_QUOTES);
}


/*
 ********************************************************************************
 * loadProduct
 * 
 * This function loads the form field array from a populated Product object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadProduct(\Datalayer\Product $Product, $form)
{

	if (!is_null($Product->id))
	{
		$_POST['hdnProductID'] = $Product->id;
		
		$_POST['txtProductName'] = htmlentities($Product->name ?? '', ENT_QUOTES);
		$_POST['selArtist'] = $Product->artistId;
		$_POST['txtImage'] = htmlentities($Product->image ?? '', ENT_QUOTES);
		$_POST['txtThumbnail'] = htmlentities($Product->thumbnail ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($Product->lastUpdate ?? '', ENT_QUOTES);
		
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

	//Product Name is a required Field
    if ( document.ProductMaint.txtProductName.value == "" )
    {
        sErrorMessage += "Product Name Required\n";
        bValid = false;
    }

	//Artist is a required Field
	if ( document.ProductMaint.selArtist.value == "0" )
    {
        sErrorMessage += "Artist Required\n";
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
