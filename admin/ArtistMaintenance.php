<?php
/*
*******************************************************************
ArtistMaintenance.php
This PHP file defines the Maintenance page for managing Artists.
NOTES
Date        Change
-------------------------------------------------------------
2015-12-18	Created
2016-02-11	Added renderArtistDropDown(), secondary nav and 
			changed query parm to ARTIST_ID
2017-03-28	Made Responsive
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

 	
//include Artist Class		
include_once (CLASS_DIR . "/class_Artist.php");


//Require the Class for the calendar picker
require_once (CLASS_DIR . "/tc_calendar.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of Artist records from the DB
global $aArtistRecords;

$sActiveMenuItem = DISCOGRAPHY_ACTIVE;
$sPageName = "Artist Maintenance";
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
<form name="ArtistMaint" action="ArtistMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$thisArtist = new Artist();
		$form = new Form();

		//Get the ID query string parameter
		$nThisArtistID = $_REQUEST['ARTIST_ID'];
		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisArtistID))
		{
						
			$thisArtist->nArtistID = $nThisArtistID;

			//Search the Database for records matching the search criteria			
			if ($thisArtist->getArtist())
			{
			
				//Records found
				if (sizeof($thisArtist->aArtistRecords) > 0)
				{
									
					//Only One Record should be returned.  Add this to the form field array
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadArtist($thisArtist->aArtistRecords[0], $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				else
				{
					//The record was not found
					$form->sMessage = "Artist record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisArtist->sErrorMessage;
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
				buildArtistObject($thisArtist);
				
				//Insert record
				if ($thisArtist->insertArtist())			
				{
					//Load the form fields with the newly populated object
					loadArtist($thisArtist, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Artist Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
				
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: {$thisArtist->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
			
				//Load values from form field array into DB object
				buildArtistObject($thisArtist);
				
				//Update record
				if ($thisArtist->updateArtist())			
				{
				
					//reload Artist
					$thisArtist->getArtist();
				
					//Load the form fields with the newly populated DB object						
					loadArtist($thisArtist->aArtistRecords[0], $form);
				
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Artist Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - {$thisArtist->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildArtistObject($thisArtist);				

				//Delete record
				if ($thisArtist->deleteArtist())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "Artist Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: {$thisArtist->sErrorMessage}";
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
				buildArtistObject($thisArtist);

				//Search the Database for records matching the search criteria			
				if ($thisArtist->getArtist())
				{
					//No records found
					if(sizeof($thisArtist->aArtistRecords) < 1)
					{
						$form->sMessage = "No Artist records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisArtist->aArtistRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadArtist($thisArtist->aArtistRecords[0], $form);			

						$form->sMessage = "One Artist record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select Artist record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisArtist->sErrorMessage;
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
				$_POST['hdnArtistID'] = NULL;
				
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
	<input type="hidden" name="hdnArtistID" value="<?php echo $_POST['hdnArtistID']?>" />	

	<div class="row">
		<?php
		include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
		?>
	</div>	
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Artist Maintenance
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
		echo "	<div class='hidden-xs col-sm-12 result-header'>Artist Name</div>";
		echo "	<div class='visible-xs col-xs-12 result-header'>Awards</div>";
		echo "</div>";

				
		foreach($thisArtist->aArtistRecords as $oArtistRecord)
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
			echo "	<div class='col-xs-12 {$sResultStyleClass}'><A HREF='./ArtistMaintenance.php?ARTIST_ID={$oArtistRecord->nArtistID}'>{$oArtistRecord->sArtistName}</A></div>";
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
			if ($_POST['hdnArtistID'] > 0)
			{
				echo "<a href='". ADMIN_DIR . "/SongMaintenance.php?ARTIST_ID={$_POST['hdnArtistID']}";
				echo "' class='secondaryLinkButton'>Edit Songs</a>";
				echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
				echo "<br class='visible-xs'>";
				echo "<a href='". ADMIN_DIR . "/CDMaintenance.php?ARTIST_ID={$_POST['hdnArtistID']}";					
				echo "&ACTION=ADD_EXPENSE'  class='secondaryLinkButton'>Edit CDs</a>";
				echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
				echo "<br class='visible-xs'>";
				echo "<a href='". ADMIN_DIR . "/ArtistReport.php?ARTIST_ID={$_POST['hdnArtistID']}";							
				echo "'  class='secondaryLinkButton'>{$thisArtist->aArtistRecords[0]->sArtistName} Report</a>";
			}
			?>
			</div>			
				<div class="col-xs-12 FieldGroup">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							NAME: <input type="text" name="txtArtistName" value="<?php echo $_POST['txtArtistName']; ?>" size="40" />
							</div>
						<div class="col-xs-12 col-sm-3 image-preview">
							<span class="Subtitle">Image</span><br />												
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
						<br /><br />
							<input type="text" name="txtImage" id="txtImage" value="<?php echo $_POST['txtImage']; ?>" size="30" /><BR />
							<input type="button" name="btnPreviewImage" onclick="preview_image('txtImage','<?php echo IMG_DIR ?>')" value="Preview" />												
					</div>
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="row FormFieldNoEdit">
					<div class ="col-xs-3">
						ID: <?php echo $_POST['hdnArtistID']; ?>						
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
 * buildArtistObject
 * 
 * This function loads builds a Artist object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildArtistObject($Artist)
{
	//Load the Array used to populate the form fields based on the newly loaded object
	$Artist->nArtistID = $_POST['hdnArtistID'];

	$Artist->sArtistName = html_entity_decode($_POST['txtArtistName'], ENT_QUOTES);
	$Artist->sArtistImage = html_entity_decode($_POST['txtImage'], ENT_QUOTES);
	$Artist->bFuzzyNameSearch = TRUE;
}


/*
 ********************************************************************************
 * loadArtist
 * 
 * This function loads the form field array from a populated Artist object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadArtist(&$Artist, $form)
{

	if (!is_null($Artist->nArtistID))
	{
		//Load Hidden Fields
		$_POST['hdnArtistID'] = $Artist->nArtistID;

		$_POST['txtArtistName'] = htmlentities($Artist->sArtistName, ENT_QUOTES);
		$_POST['txtImage'] = htmlentities($Artist->sArtistImage, ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($Artist->dtLastUpdate, ENT_QUOTES);
		
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

	//Artist Name is a required Field
    if ( document.ArtistMaint.txtArtistName.value == "" )
    {
        sErrorMessage += "Artist Name Required\n";
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
