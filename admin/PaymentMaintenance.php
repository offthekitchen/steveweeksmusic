<?php
/*
*******************************************************************
PaymentMaintenance.php
This PHP file defines the Maintenance page for 
managing Payments.
NOTES
Date        Change
-------------------------------------------------------------
2015-05-12  Added Purchase Order field
2015-09-13  Added link to Product Maintenanace for Product names and 
            refactored 
2015-09-27  Fixed formatting problems in revenue list
2016-12-31	Made Responsive
2017-04-30	Improved Layout
2017-07-03	Styled Add Revenue Button
2017-08-19	Improved Responsivity
2018-04-14	Changed revenue links
2019-05-09	Added Numeric check for Invoice
2019-07-19	Added renderVendorDropDown() call
2020-03-06	Added renderDatePicker
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
 
//inlcude Common Functions
include_once (ADMIN_INCLUDE_DIR . "/CommonFunctions.php");

//include Payment Class		
include_once (CLASS_DIR . "/class_Payment.php");

//include Revenue Class		
 include_once (CLASS_DIR . "/class_Revenue.php");

//include Vendor Class		
 include_once (CLASS_DIR . "/class_Vendor.php");

//include Product Class		
 include_once (CLASS_DIR . "/class_Product.php");

 //include Form Class		
 include (CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
 require_once (CLASS_DIR . "/tc_calendar.php");

//Array of Payment records from the DB
global $aPaymentRecords;

$sActiveMenuItem = FINANCES_ACTIVE;
$sPageName = "Payment Maintenance";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<?php
		
	//Instantiate needed objects
	$thisPayment = new Payment();
	$form = new Form();

	//Get the ID query string parameter
	$nThisPaymentID = $_REQUEST['ID'];
	
	//If a Vendor ID is passed, go ahead and search payments for that vendor
	if (isset($_REQUEST['VENDOR_ID']) && $_REQUEST['VENDOR_ID'] != "")
	{
		$_POST['selVendorType'] = $_REQUEST['VENDOR_ID'];
		$_POST['btnSearch'] = "Search";
	}

	//If an ID was passed to the page, retrieve that record for update		
	if (!is_null($nThisPaymentID))
	{
					
		$thisPayment->nPaymentID = $nThisPaymentID;

		//Search the Database for records matching the search criteria			
		if ($thisPayment->getPayment())
		{
		
			//Records found
			if (sizeof($thisPayment->aPaymentRecords) > 0)
			{
								
				//Only One Record should be returned.  Add this to the form field array
				//so that it displays in the form fields and to the values in the
				//current Object.
				loadPayment($thisPayment->aPaymentRecords[0], $form);

				$form->sMessage = "Update record.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;			
				
			}
			else
			{
				//The record was not found
				$form->sMessage = "Payment record not found.";
				$form->nMessageType = MESSAGE_TYPE_WARNING;
				$form->nFormMode = FORM_MODE_NEW;			
			}
		}
		else
		{
			//Error
			$form->sMessage = $thisPayment->sErrorMessage;
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
			buildPaymentObject($thisPayment);
			
			//Insert record
			if ($thisPayment->insertPayment())			
			{

				//reload Payment
				$nNewPaymentID = $thisPayment->nPaymentID;
				$thisPayment = new Payment();
				$thisPayment->nPaymentID = $nNewPaymentID;

				if ($thisPayment->getPayment())
				{	
					//Load the form fields with the newly populated object
					loadPayment($thisPayment->aPaymentRecords[0], $form);
					//Store the ID of the performance
					$nThisPaymentID = $thisPayment->aPaymentRecords[0]->nPaymentID;			
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Payment Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Problem reloading screen
					clearFormFields($form);
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "Payment Added, but error occured while reloading the data";
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
			
				//Failure
				$form->nMessageType = MESSAGE_TYPE_ERROR;
				$form->sMessage = "ADD RECORD FAILED: {$thisPayment->sErrorMessage}";
				$form->nFormMode = FORM_MODE_EDIT;			
				
			}
				
		}					
		// **************
		// *   UPDATE   *
		// **************
		else if (isset($_POST["btnUpdate"])) 
		{
		
			//Load values from form field array into DB object
			buildPaymentObject($thisPayment);
			
			//Update record
			if ($thisPayment->updatePayment())			
			{
			
				//Update potential Revenues
				if($thisPayment->getPotentialRevenues())
				{
					foreach($thisPayment->oPotentialRevenues->aRevenueRecords as $oPotentialRevenue)
					{
						$sCheckboxName = "chkRevenue{$oPotentialRevenue->nRevenueID}";
						if(isset($_POST[$sCheckboxName]) && $_POST[$sCheckboxName] == 'RevenueAssociated')
						{
							$oPotentialRevenue->nPaymentID = $thisPayment->nPaymentID;
							if(!$oPotentialRevenue->updateRevenue())
							{
								//Attempt to update revenue records failed
								echo "ERROR:  FAILED TO UPDATE REVENUE:{$oPotentialRevenue->nRevenueID}<BR>";
							}
						}  
					}
				}
			
				//reload Payment
				$thisPayment->getPayment();
			
				//Load the form fields with the newly populated DB object						
				loadPayment($thisPayment->aPaymentRecords[0], $form);
				//Store the ID of the payment
				$nThisPaymentID = $thisPayment->aPaymentRecords[0]->nPaymentID;			
			
				//Success
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->sMessage = "Payment Updated";
				$form->nFormMode = FORM_MODE_EDIT;			
			}
			else
			{
				//Failure
				$form->nMessageType = MESSAGE_TYPE_ERROR;
				$form->sMessage = "ERROR: Update Failed - {$thisPayment->sErrorMessage}";
				$form->nFormMode = FORM_MODE_EDIT;			
			}
		}
		// **************
		// *   DELETE   *
		// **************
		else if (isset($_POST["btnDelete"])) 
		{
		
			//Load DB record
			buildPaymentObject($thisPayment);				

			//Delete record
			if ($thisPayment->deletePayment())
			{
				//Clear the form fields
				clearFormFields($form);
				
				//Success
				$form->sMessage = "Payment Deleted";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_NEW;					
			}
			else
			{
				$form->sMessage = "DELETE FAILED: {$thisPayment->sErrorMessage}";
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
			buildPaymentObject($thisPayment);

			//Search the Database for records matching the search criteria			
			if ($thisPayment->getPayment())
			{
				//No records found
				if(sizeof($thisPayment->aPaymentRecords) < 1)
				{
					$form->sMessage = "No Payment records found matching search criteria";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}			
				else if (sizeof($thisPayment->aPaymentRecords) == 1)
				{
					//Only One Record returned.  Add this to the form field array
					//so that it displays in the form fields
					loadPayment($thisPayment->aPaymentRecords[0], $form);
					//Store the ID of the performance
					$nThisPaymentID = $thisPayment->aPaymentRecords[0]->nPaymentID;			

					$form->sMessage = "One Payment record found.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				//If Multiple records found, the array of search reults will be populated
				else 
				{
					//Multiiple records returned
					$form->sMessage = "Select Payment record to edit from results list below.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_SELECT;			
				}

			}
			else
			{
				//Attempt to get records failed
				$form->sMessage = $thisPayment->sErrorMessage;
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
		// *   COPY  *
		// *************
		else if (isset($_POST["btnCopy"])) 
		{
			//copyFormFields($form);
			$_POST['hdnPaymentID'] = NULL;
			
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
<div class="container-fluid">		
<form name="PaymentMaint" action="PaymentMaintenance.php" method="post">
	
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnPaymentID" value="<?php echo $_POST['hdnPaymentID']?>" />	

	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
				Payment Maintenance
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
		echo "	<div class='hidden-xs col-sm-3 result-header'>Payment Date</div>";
		echo "	<div class='hidden-xs col-sm-2 result-header'>Amount</div>";
		echo "	<div class='hidden-xs col-sm-7 result-header'>Payment Description</div>";
		echo "	<div class='visible-xs col-xs-12 col-sm-3 result-header'>Payments</div>";
		echo "</div>";

		$nPaymentAmountTotal =0;		
				
		foreach($thisPayment->aPaymentRecords as $oPaymentRecord)
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
				
			echo "	<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./PaymentMaintenance.php?ID={$oPaymentRecord->nPaymentID}'>{$oPaymentRecord->dtPaymentDate}</a></div>";
			echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./PaymentMaintenance.php?ID={$oPaymentRecord->nPaymentID}'> $ {$oPaymentRecord->nPaymentAmount}</a></div>";
			echo "	<div class='col-xs-12 col-sm-7 {$sResultStyleClass}'><A HREF='./PaymentMaintenance.php?ID={$oPaymentRecord->nPaymentID}'>{$oPaymentRecord->sPaymentDescription}</a></div>";
			$nPaymentAmountTotal += $oPaymentRecord->nPaymentAmount;
			echo "</div>";
		}

		echo "<div class='row'><div class='col-xs-12'><HR></div></div>";
		echo "<div class='row {$sResultStyleClass}'>";
		echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'></div>";
		echo "	<div class='col-xs-6 col-sm-5 {$sResultStyleClass}'>Total</div>";
		echo "	<div class='col-xs-6 col-sm-5 {$sResultStyleClass}'> $ {$nPaymentAmountTotal}</div>";
		echo "</div>";
		echo "<div class='row'><div class='col-xs-12'><HR></div></div>";
		
		?>
		
	<?php
	}
	else
	{
	?>
		<div class="row"> 
			<div class="col-xs-12 FieldGroupTitle">
				DETAILS
			</div>
			<div class="col-xs-12 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
						DATE:<BR /> 
						<?php	  
							renderDatePicker("PaymentDate", $_POST['PaymentDate']);
						?>		    
					</div>								
					<div class="col-xs-12 col-md-3">
						AMOUNT: <input type="text" name="txtPaymentAmount" value="<?php echo $_POST['txtPaymentAmount']; ?>" size="20" />								
					</div>								
					<div class="col-xs-12 col-md-9">
						DESCRIPTION: <input type="text" name="txtPaymentDescription" value="<?php echo $_POST['txtPaymentDescription']; ?>" size="60" />								
					</div>								
					<div class="col-xs-12 col-sm-6 col-md-3">
						<?php
						renderVendorDropDown($_POST['selVendor']);
						?>							
					</div>								
					<div class="col-xs-12 col-sm-6 col-md-3">
						PO: <input type="text" name="txtPurchaseOrder" value="<?php echo $_POST['txtPurchaseOrder']; ?>" size="15" />								
					</div>								
					<div class="col-xs-12 col-sm-6 col-md-3">
						CHECK #: <input type="text" name="txtCheckNumber" value="<?php echo $_POST['txtCheckNumber']; ?>" size="15" />								
					</div>								
					<div class="col-xs-12 col-sm-6 col-md-3">
						INVOICE: <input type="text" name="txtInvoice" value="<?php echo $_POST['txtInvoice']; ?>" size="15" />&nbsp;&nbsp;								
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-2 FieldGroupTitle">
					REVENUES
			</div>			<div class="col-xs-12 col-sm-10">				<?php				if ($_POST['hdnPaymentID'] > 0)				{					echo "<a href='". ADMIN_DIR . "/RevenueMaintenance.php?PAYMENT_ID={$thisPayment->aPaymentRecords[0]->nPaymentID}";					echo "&ACTION=ADD_REVENUE' class='secondaryLinkButton'>Add Revenue</a>";				}				?>						</div>
			<div class="col-xs-12 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
					<?php
						if($thisPayment->aPaymentRecords[0]->nPaymentID > 0)
						{
							echo "<div class='row result-header'>";
							echo "<div class='hidden-xs col-sm-1 col-md-1 result-header'></div>";
							echo "<div class='hidden-xs col-sm-3 col-md-2 result-header'>Date</div>";
							echo "<div class='hidden-xs col-sm-4 col-md-2 result-header'>Description</div>";
							echo "<div class='hidden-xs col-sm-4 col-md-2 result-header'>Amount</div>";
							echo "<div class='hidden-xs hidden-sm col-md-2 result-header'>Product</div>";
							echo "<div class='hidden-xs hidden-sm col-md-1 result-header'>Quantity</div>";
							echo "<div class='hidden-xs col-md-2 result-header'></div>";
							echo "<div class='visible-xs col-xs-12 result-header'>REVENUES</div>";
							echo "</div>";

							$nRevenueAmountTotal = 0;
							$nProductQtyTotal= 0;
							if($thisPayment->aPaymentRecords[0]->getPaymentRevenues())
							{
								foreach($thisPayment->aPaymentRecords[0]->oPaymentRevenues->aRevenueRecords as $oRevenue)
								{
									$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
									
									//Get the Product Name
									$oRevenueProduct = new Product();
									$oRevenueProduct->nProductID = $oRevenue->nProductID;
									if($oRevenue->nProductID > 0)
									{
										if(!$oRevenueProduct->getProduct())
										{
											//Attempt to get records failed
											$form->sMessage = $oRevenueProduct->sErrorMessage;
											$form->nMessageType = MESSAGE_TYPE_ERROR;
											$form->nFormMode = FORM_MODE_NEW;
										}
									}

									echo "<div class='row {$sResultStyleClass}'>";
									echo "<div class='col-xs-12 col-sm-1 col-md-1'>";
									echo "<input class='result-checkbox' type='checkbox' name='chkRevenue{$oRevenue->nRevenueID}'";
									echo " value='RevenueAssociated' checked disabled>";
									echo "</div>";
									echo "<div class='col-xs-12 col-sm-3 col-md-2'>";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->nRevenueID}'>";
									echo "{$oRevenue->dtRevenueDate}</a></div>";
									echo "<div class='col-xs-12 col-sm-4 col-md-2'>";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->nRevenueID}'>";
									echo "{$oRevenue->sRevenueDescription}</a></div>";
									echo "<div class='col-xs-12 col-sm-4 col-md-2'>";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->nRevenueID}'>";
									echo " $ {$oRevenue->nRevenueAmount}</a></div>";
									echo "<div class='hidden-xs hidden-sm col-md-2'>";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->nRevenueID}'>";
									echo "{$oRevenueProduct->aProductRecords[0]->sProductName}</a></div>";
									echo "<div class='hidden-xs hidden-sm col-md-1'>";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oRevenue->nRevenueID}'>";
									echo "{$oRevenue->nProductQty}</a></div>";
									echo "<div class='col-xs-12 col-md-2'></div>";
									echo "</div>";
									$nRevenueAmountTotal += $oRevenue->nRevenueAmount;
									$nProductQtyTotal += $oRevenue->nProductQty;
								}

							}
							else
							{
								echo "ERROR RETRIEIVING REVENUES";
							}
							
							if($thisPayment->aPaymentRecords[0]->getPotentialRevenues())
							{
								foreach($thisPayment->aPaymentRecords[0]->oPotentialRevenues->aRevenueRecords as $oPotentialRevenue)
								{
									$sResultStyleClass = RESULT_STYLE_CLASS;


									//Get the Product Name
									if ($oPotentialRevenue->nProductID > 0)
									{
										$oRevenueProduct = new Product();
										$oRevenueProduct->nProductID = $oPotentialRevenue->nProductID;
										if(!$oRevenueProduct->getProduct())
										{
											//ERROR
										}
									}
									
									echo "<div class='row {$sResultStyleClass}'>";
									echo "<div class='col-xs-12 col-sm-1 col-md-1'>";
									echo "<input class='result-checkbox' type='checkbox' name='chkRevenue{$oPotentialRevenue->nRevenueID}'";
									echo " value='RevenueAssociated' >";
									echo "</div>";
									echo "<div class='col-xs-12 col-sm-3 col-md-1'>";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->nRevenueID}'>{$oPotentialRevenue->dtRevenueDate}</a></div>";
									echo "<div class='col-xs-12 col-sm-4 col-md-2'>";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->nRevenueID}'>";
									echo "{$oPotentialRevenue->sRevenueDescription}</a></div>";
									echo "<div class='col-xs-12 col-sm-4 col-md-2'>";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->nRevenueID}'> $ {$oPotentialRevenue->nRevenueAmount}</a></div>";
									echo "<div class='hidden-xs hidden-sm col-md-2'>";
									echo "<a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->nRevenueID}'>";
									echo "{$oRevenueProduct->aProductRecords[0]->sProductName}</a></div>";
									echo "<div class='hidden-xs hidden-sm col-md-2'><a href='" . ADMIN_DIR . "/RevenueMaintenance.php?ID={$oPotentialRevenue->nRevenueID}'>{$oPotentialRevenue->nProductQty}</a></div>";
									echo "<div class='col-xs-12 col-md-2'>";
									if($oPotentialRevenue->nPaymentID > 0)
									{
										echo "<font color='red'>Revenue already associated with ";
										echo "<a href='" . ADMIN_DIR . "/PaymentMaintenance.php?ID={$oPotentialRevenue->nPaymentID}";
										echo "'>Payment {$oPotentialRevenue->nPaymentID}</a></font>";
									}
									echo "</div>";
									echo "</div>";
									
								}
							}
							
							echo "<div class='row'><div class='col-xs-12'><HR></div></div>";
							echo "<div class='row'>";
							echo "<div class='hidden-xs col-sm-2'></div>";
							echo "<div class='col-xs-2'> Total </div>";
							echo "<div class='col-xs-2'> $" . number_format((float)$nRevenueAmountTotal, 2, '.', '') . "</div>";
							if (number_format((float)$nRevenueAmountTotal, 2, '.', '') <> number_format((float)$_POST['txtPaymentAmount'], 2, '.', ''))
							{
								echo "<div class='col-xs-12 col-sm-6'>";
								echo "<font color='red'>Revenue Total does not equal Payment Amount.</font>";
								echo "</div>";
							}
							echo "</div>";
							
						}
					?>
					</div>
				</div>
			</div>
				<div class="col-xs-12">
					<div class="row">
						<div class=" col-xs-3 FormFieldNoEdit">
							ID: <?php echo $_POST['hdnPaymentID']; ?>						
						</div>
						<div class=" col-xs-9 FormFieldNoEdit">
							LAST UPDATED: <?php echo $_POST['txtLastUpdate']; ?>
					</div>
				</div>
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
	<?php
	}
	?>
	
</form>
</div>
<?php		


/*
 ********************************************************************************
 * buildPaymentObject
 * 
 * This function loads builds a flag object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildPaymentObject($oPayment)
{

	//Load the Array used to populate the form fields based on the newly loaded object
	$oPayment->nPaymentID = $_POST['hdnPaymentID'];

	$oPayment->sPaymentDescription = html_entity_decode($_POST['txtPaymentDescription'], ENT_QUOTES);
	$oPayment->nPaymentAmount = html_entity_decode($_POST['txtPaymentAmount'], ENT_QUOTES);
	$oPayment->nCheckNumber = html_entity_decode($_POST['txtCheckNumber'], ENT_QUOTES);
	$oPayment->nInvoice = html_entity_decode($_POST['txtInvoice'], ENT_QUOTES);
	$oPayment->sPurchaseOrder = html_entity_decode($_POST['txtPurchaseOrder'], ENT_QUOTES);

	$oPayment->nVendorID = $_POST['selVendor'];

	$oPayment->bFuzzyNameSearch = TRUE;

	//Payment Date	
	$dtPaymentDate = isset($_REQUEST["PaymentDate"]) ? $_REQUEST["PaymentDate"] : "";
	if($dtPaymentDate > "0000-00-00")
	{
		//If no datepicker is displayed, use the hidden field
		$dtPaymentDate = isset($_POST["PaymentDate"]) ? $_POST["PaymentDate"] : "";
	}

	if($dtPaymentDate > "0000-00-00")
	{
		$oPayment->dtPaymentDate  	= $dtPaymentDate;
	}


}


/*
 ********************************************************************************
 * loadPayment
 * 
 * This function loads the form field array from a populated Payment object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadPayment(&$oPayment, $form)
{

	if (!is_null($oPayment->nPaymentID))
	{
		//Load Hidden Fields
		$_POST['hdnPaymentID'] = $oPayment->nPaymentID;
		
		$_POST['txtPaymentDescription'] = htmlentities($oPayment->sPaymentDescription, ENT_QUOTES);
		$_POST['txtPaymentAmount'] = htmlentities($oPayment->nPaymentAmount, ENT_QUOTES);
		$_POST['txtCheckNumber'] = htmlentities($oPayment->nCheckNumber ?? '', ENT_QUOTES);
		$_POST['txtInvoice'] = htmlentities($oPayment->nInvoice ?? '', ENT_QUOTES);
		$_POST['txtPurchaseOrder'] = htmlentities($oPayment->sPurchaseOrder ?? '', ENT_QUOTES);
		
		$_POST['PaymentDate'] = htmlentities($oPayment->dtPaymentDate, ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($oPayment->dtLastUpdate, ENT_QUOTES);

		if ($oPayment->nVendorID > 0)
		{
			$_POST['selVendor'] = $oPayment->nVendorID;
		}
		else
		{
			$_POST['selVendor'] = 0;
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
//******************************************
// validate_form
// This script checks the user imnput for
// errors or missing data 
//******************************************
function validate_form ( )
{
    bValid = true;
	sErrorMessage = "";

	//Reason is a required Field
    if ( document.PaymentMaint.txtPaymentDescription.value == "" )
    {
        sErrorMessage += "Payment Name Required\n";
        bValid = false;
    }

	//Payment Date is a required Field
    if ( document.PaymentMaint.PaymentDate.value == "" || document.PaymentMaint.PaymentDate.value == "0000-00-00" )
    {
        sErrorMessage += "Payment Date Required\n";
        bValid = false;
    }

	//Payment Amount is a required, numeric, positive Field
    if ( document.PaymentMaint.txtPaymentAmount.value == "" )
    {
        sErrorMessage += "Payment Amount Required\n";
        bValid = false;
    }
	else if(!isNumeric(document.PaymentMaint.txtPaymentAmount.value)) 
	{
        sErrorMessage += "Payment Amount Must be Numeric\n";
        bValid = false;
	}
	else if(Number(document.PaymentMaint.txtPaymentAmount.value) <= 0) 
	{
        sErrorMessage += "Payment Amount must be greater than 0\n";
        bValid = false;
	}
	 
	if(!isNumeric(document.PaymentMaint.txtInvoice.value) && document.PaymentMaint.txtInvoice.value != "") 
	{
        sErrorMessage += "Invoice Must be Numeric\n";
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
