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

 $sActiveMenuItem = PRODUCTS_ACTIVE;	
 $sPageName = "Product Maintenance";
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
	
	//include Product Class		
	 include_once (CLASS_DIR . "/class_Product.php");
	//Include Artist Class 
	 include_once (CLASS_DIR . "/class_Artist.php");

 	//include Form Class		
 	include (CLASS_DIR . "/class_Form.php");

	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");

	//Array of Product records from the DB
	global $aProductRecords;
	
	?>
<div class="container-fluid">	
<form name="ProductMaint" action="ProductMaintenance.php" method="post">

	<?php

		//Get Artists for drop-down list
		$oArtists = new Artist();
		if (!$oArtists->getArtist())
		{
			//ERROR
		}

		//Instantiate needed objects
		$thisProduct = new Product();
		$form = new Form();

		//Get the ID query string parameter
		$nThisProductID = $_REQUEST['ID'];

		//If an Artist ID is passed, go ahead and search products for that artist
		if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "")
		{
			$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
			$_POST['btnSearch'] = "Search";
		}

		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisProductID))
		{
						
			$thisProduct->nProductID = $nThisProductID;

			//Search the Database for records matching the search criteria			
			if ($thisProduct->getProduct())
			{
			
				//Records found
				if (sizeof($thisProduct->aProductRecords) > 0)
				{
									
					//Only One Record should be returned.  Add this to the form field array
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadProduct($thisProduct->aProductRecords[0], $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				else
				{
					//The record was not found
					$form->sMessage = "Product record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisProduct->sErrorMessage;
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
				buildProductObject($thisProduct);
				
				//Insert record
				if ($thisProduct->insertProduct())			
				{
					//Load the form fields with the newly populated object
					loadProduct($thisProduct, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Product Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
				
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: {$thisProduct->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
			
				//Load values from form field array into DB object
				buildProductObject($thisProduct);
				
				//Update record
				if ($thisProduct->updateProduct())			
				{
				
					//reload Product
					if (!$thisProduct->getProduct())
					{
						echo "Product was updated, but error was encountered retrieving product data {$thisProduct->sErrorMessage}";
					}
				
					//Load the form fields with the newly populated DB object						
					loadProduct($thisProduct->aProductRecords[0], $form);
				
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Product Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - {$thisProduct->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildProductObject($thisProduct);				

				//Delete record
				if ($thisProduct->deleteProduct())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "Product Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: {$thisProduct->sErrorMessage}";
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
				buildProductObject($thisProduct);

				//Search the Database for records matching the search criteria			
				if ($thisProduct->getProduct())
				{
					//No records found
					if(sizeof($thisProduct->aProductRecords) < 1)
					{
						$form->sMessage = "No Product records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisProduct->aProductRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadProduct($thisProduct->aProductRecords[0], $form);			

						$form->sMessage = "One Product record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select Product record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisProduct->sErrorMessage;
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

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnProductID" value="<?php echo $_POST['hdnProductID']?>" />	
	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
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

				
		foreach($thisProduct->aProductRecords as $oProductRecord)
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
			echo "<A HREF='./ProductMaintenance.php?ID={$oProductRecord->nProductID}'>{$oProductRecord->sProductName}</A>";
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
				if ($_POST['hdnProductID'] > 0)
				{					
					echo "<a href='". ADMIN_DIR . "/ExpenseMaintenance.php?PRODUCT_ID={$thisProduct->aProductRecords[0]->nProductID}";					
					echo "'  class='secondaryLinkButton'>Edit Expenses</a>";					
					echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/ExpenseMaintenance.php?PRODUCT_ID={$thisProduct->aProductRecords[0]->nProductID}";										
					echo "&ACTION=ADD_EXPENSE'  class='secondaryLinkButton'>Enter Expense</a>";					
					echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";					
					echo "<br class='visible-xs'>";
					echo "<a href='". ADMIN_DIR . "/ProductReport.php?PRODUCT_ID={$thisProduct->aProductRecords[0]->nProductID}";												
					echo "'  class='secondaryLinkButton'>\"{$thisProduct->aProductRecords[0]->sProductName}\" Report</a>";
				}
				?>
			</div>			
			<div class="col-xs-12 col-md-6 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
						NAME: <input type="text" name="txtProductName" value="<?php echo $_POST['txtProductName']; ?>" placeholder="Product Name" size="60" />
					</div>
					<div class="col-xs-12">
						<?php
							renderArtistDropDown($_POST['selArtist']);
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
						<input type="text" name="txtImage" id="txtImage" value="<?php echo $_POST['txtImage']; ?>" size="30" /><BR />
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
						<input type="text" name="txtThumbnail" id="txtThumbnail" value="<?php echo $_POST['txtThumbnail']; ?>" size="30" /><BR />
						<input type="button" name="btnPreviewThumbnail" class="btnPreviewImage" onclick="preview_image('txtThumbnail','<?php echo IMG_DIR ?>')" value="Preview" />												
					</div>
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="row FormFieldNoEdit">
					<div class ="col-xs-3 FormFieldNoEdit">
						ID: <?php echo $_POST['hdnProductID']; ?>						
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
 * buildProductObject
 * 
 * This function loads builds a product object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildProductObject($product)
{

	//Load the Array used to populate the form fields based on the newly loaded object
	$product->nProductID = $_POST['hdnProductID'];

	$product->sProductName = html_entity_decode($_POST['txtProductName'], ENT_QUOTES);
	$product->bFuzzyNameSearch = TRUE;
	$product->nArtistID = $_POST['selArtist'];
	
	$product->sImage = html_entity_decode($_POST['txtImage'], ENT_QUOTES);
	$product->sThumbnail = html_entity_decode($_POST['txtThumbnail'], ENT_QUOTES);


}


/*
 ********************************************************************************
 * loadProduct
 * 
 * This function loads the form field array from a populated Product object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadProduct(&$product, $form)
{

	if (!is_null($product->nProductID))
	{
		//Load Hidden Fields
		$_POST['hdnProductID'] = $product->nProductID;
		
		$_POST['txtProductName'] = htmlentities($product->sProductName, ENT_QUOTES);
		$_POST['selArtist'] = $product->nArtistID;
		$_POST['txtImage'] = htmlentities($product->sImage, ENT_QUOTES);
		$_POST['txtThumbnail'] = htmlentities($product->sThumbnail, ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($product->dtLastUpdate, ENT_QUOTES);
		
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
