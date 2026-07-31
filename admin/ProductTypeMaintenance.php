<?php

/*

*******************************************************************

ProductTypeMaintenance.php

This PHP file defines the Maintenance page for managing Product Types.

NOTES

Date        Change

-------------------------------------------------------------

2026-07-30	Migrated to new Datalayer ProductType repository

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

include_once(DATALAYER_DIR . "/ProductType.php");

include_once(DATALAYER_DIR . "/ProductTypeRepository.php");



//include Form Class		

include(CLASS_DIR . "/class_Form.php");



//Array of Product Type records from the DB

$aProductTypeRecords = [];



$sActiveMenuItem = REPORTS_ACTIVE;

$sPageName = "PRODUCT REPORT";



?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">



<?php

include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");

?>



<body>



	<!-- DIV used for Image Preview Popup -->

	<div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15" id="preview_div"></div>



<form name="ProductTypeMaint" action="ProductTypeMaintenance.php" method="post">



	<?php



		//Instantiate needed objects

		$productTypeRepo = new \Datalayer\ProductTypeRepository();

		$thisProductType = new \Datalayer\ProductType();

		$form = new Form();



		//Get the ID query string parameter

		$nThisProductTypeID = $_REQUEST['ID'] ?? null;



		try {

		//If an ID was passed to the page, retrieve that record for update		

		if (!is_null($nThisProductTypeID) && $nThisProductTypeID !== '')

		{

			$entity = $productTypeRepo->findById((int) $nThisProductTypeID);



			if ($entity) {

				$thisProductType = $entity;

				$aProductTypeRecords = [$entity];

				loadProductType($thisProductType, $form);



				$form->sMessage = "Update record.";

				$form->nMessageType = MESSAGE_TYPE_INFO;

				$form->nFormMode = FORM_MODE_EDIT;

			} else {

				$form->sMessage = "Product Type record not found.";

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

				buildProductTypeObject($thisProductType);

				

				if ($productTypeRepo->insert($thisProductType))

				{

					$thisProductType = $productTypeRepo->findById((int) $thisProductType->id) ?? $thisProductType;

					$aProductTypeRecords = [$thisProductType];

					loadProductType($thisProductType, $form);

					

					$form->nMessageType = MESSAGE_TYPE_INFO;

					$form->sMessage = "Product Type Added";

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

				buildProductTypeObject($thisProductType);

				

				if ($productTypeRepo->update($thisProductType))

				{

					$thisProductType = $productTypeRepo->findById((int) $thisProductType->id) ?? $thisProductType;

					$aProductTypeRecords = [$thisProductType];

					loadProductType($thisProductType, $form);

				

					$form->nMessageType = MESSAGE_TYPE_INFO;

					$form->sMessage = "Product Type Updated";

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

				buildProductTypeObject($thisProductType);



				if (!empty($thisProductType->id) && $productTypeRepo->delete((int) $thisProductType->id)) {

					clearFormFields($form);

					

					$form->sMessage = "Product Type Deleted";

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

				buildProductTypeObject($thisProductType);



				$aProductTypeRecords = $productTypeRepo->find([

					'id' => $thisProductType->id,

					'name' => $thisProductType->name,

					'fuzzyName' => true,

				]);



				if (sizeof($aProductTypeRecords) < 1)

				{

					$form->sMessage = "No Product Type records found matching search criteria";

					$form->nMessageType = MESSAGE_TYPE_WARNING;

					$form->nFormMode = FORM_MODE_NEW;			

				}			

				else if (sizeof($aProductTypeRecords) == 1)

				{

					$thisProductType = $aProductTypeRecords[0];

					loadProductType($thisProductType, $form);			



					$form->sMessage = "One Product Type record found.";

					$form->nMessageType = MESSAGE_TYPE_INFO;

					$form->nFormMode = FORM_MODE_EDIT;			

				}

				else 

				{

					$form->sMessage = "Select Product Type record to edit from results list below.";

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

				$_POST['hdnProductTypeID'] = NULL;

				

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

	<input type="hidden" name="hdnProductTypeID" value="<?php echo $_POST['hdnProductTypeID'] ?? ''?>" />	



<?php

	include(ADMIN_INCLUDE_DIR . "/AdminHeader.php");

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



				

		foreach($aProductTypeRecords as $oProductTypeRecord)

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

			echo "	<TD class='" , $sResultStyleClass . "'><A HREF='./ProductTypeMaintenance.php?ID=" . $oProductTypeRecord->id . "'>Select</A></TD>";

			

			$i = 1;

		

			echo "	<TD class='" , $sResultStyleClass . "'>" . $oProductTypeRecord->name . "</TD>";

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

										ID: <?php echo $_POST['hdnProductTypeID'] ?? ''; ?>						

									</td>

									<td >

										NAME: <input type="text" name="txtProductTypeName" value="<?php echo $_POST['txtProductTypeName'] ?? ''; ?>" size="60" />

									</td>

								</tr>

							</table>

						</TD>

					</TR>

					<TR>

						<TD CLASS="FormFieldNoEdit">

							LAST UPDATED: <?php echo $_POST['txtLastUpdate'] ?? ''; ?>

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

function buildProductTypeObject(\Datalayer\ProductType $ProductType)

{

	$id = $_POST['hdnProductTypeID'] ?? null;

	$ProductType->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;



	$ProductType->name = html_entity_decode($_POST['txtProductTypeName'] ?? '', ENT_QUOTES);

}





/*

 ********************************************************************************

 * loadProductType

 * 

 * This function loads the form field array from a populated Product Type object

 * so that it will be displayed in the form fields

 ********************************************************************************

*/

function loadProductType(\Datalayer\ProductType $ProductType, $form)

{



	if (!is_null($ProductType->id))

	{

		$_POST['hdnProductTypeID'] = $ProductType->id;

		$_POST['txtProductTypeName'] = htmlentities($ProductType->name ?? '', ENT_QUOTES);

		$_POST['txtLastUpdate'] = htmlentities($ProductType->lastUpdate ?? '', ENT_QUOTES);

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

