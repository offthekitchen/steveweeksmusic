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
2026-07-30	Migrated to new Datalayer Artist repository
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
include_once(DATALAYER_DIR . "/Artist.php");
include_once(DATALAYER_DIR . "/ArtistRepository.php");
include_once(DATALAYER_DIR . "/Song.php");
include_once(DATALAYER_DIR . "/SongRepository.php");
include_once(DATALAYER_DIR . "/CD.php");
include_once(DATALAYER_DIR . "/CDRepository.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of Artist records from the DB
$aArtistRecords = [];

$sActiveMenuItem = DISCOGRAPHY_ACTIVE;
$sPageName = "Artist Maintenance";
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
<form name="ArtistMaint" action="ArtistMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$artistRepo = new \Datalayer\ArtistRepository();
		$songRepo = new \Datalayer\SongRepository();
		$cdRepo = new \Datalayer\CDRepository();
		$thisArtist = new \Datalayer\Artist();
		$form = new Form();

		//Get the ID query string parameter
		$nThisArtistID = $_REQUEST['ARTIST_ID'] ?? null;
		
		try {
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisArtistID) && $nThisArtistID !== '')
		{
			$entity = $artistRepo->findById((int) $nThisArtistID);

			if ($entity) {
				$thisArtist = $entity;
				$aArtistRecords = [$entity];
				loadArtist($thisArtist, $form);

				$form->sMessage = "Update record.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;
			} else {
				$form->sMessage = "Artist record not found.";
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
				buildArtistObject($thisArtist);
				
				if ($artistRepo->insert($thisArtist))
				{
					$thisArtist = $artistRepo->findById((int) $thisArtist->id) ?? $thisArtist;
					$aArtistRecords = [$thisArtist];
					loadArtist($thisArtist, $form);
					
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Artist Added";
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
				buildArtistObject($thisArtist);
				
				if ($artistRepo->update($thisArtist))
				{
					$thisArtist = $artistRepo->findById((int) $thisArtist->id) ?? $thisArtist;
					$aArtistRecords = [$thisArtist];
					loadArtist($thisArtist, $form);
				
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "Artist Updated";
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
				buildArtistObject($thisArtist);

				$deleteError = null;
				if (!empty($thisArtist->id)) {
					$relatedSongs = $songRepo->find(['artistId' => (int) $thisArtist->id]);
					$relatedCDs = $cdRepo->find([
						'artistId' => (int) $thisArtist->id,
						'includeSingles' => true,
					]);

					if (sizeof($relatedSongs) > 0 || sizeof($relatedCDs) > 0) {
						$deleteError = "ART0013 - Can not delete Artist because it is associated with ";
						$deleteError .= "<A HREF='" . ADMIN_DIR . "/SongMaintenance.php?ARTIST_ID=" . $thisArtist->id;
						$deleteError .= "'>" . sizeof($relatedSongs) . " songs</A>";
						$deleteError .= " and <A HREF='" . ADMIN_DIR . "/CDMaintenance.php?ARTIST_ID=" . $thisArtist->id;
						$deleteError .= "'>" . sizeof($relatedCDs) . " CDs.</A>";
					}
				}

				if ($deleteError !== null) {
					$form->sMessage = $deleteError;
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_EDIT;
				} else if (!empty($thisArtist->id) && $artistRepo->delete((int) $thisArtist->id)) {
					clearFormFields($form);
					
					$form->sMessage = "Artist Deleted";
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
				buildArtistObject($thisArtist);

				$aArtistRecords = $artistRepo->find([
					'id' => $thisArtist->id,
					'name' => $thisArtist->name,
					'fuzzyName' => true,
					'image' => $thisArtist->image,
				]);

				if (sizeof($aArtistRecords) < 1)
				{
					$form->sMessage = "No Artist records found matching search criteria";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}			
				else if (sizeof($aArtistRecords) == 1)
				{
					$thisArtist = $aArtistRecords[0];
					loadArtist($thisArtist, $form);			

					$form->sMessage = "One Artist record found.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else 
				{
					$form->sMessage = "Select Artist record to edit from results list below.";
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
		} catch (\Throwable $e) {
			$form->sMessage = $e->getMessage();
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->nFormMode = FORM_MODE_NEW;
		}

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnArtistID" value="<?php echo $_POST['hdnArtistID'] ?? ''?>" />	

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

				
		foreach($aArtistRecords as $oArtistRecord)
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
			echo "	<div class='col-xs-12 {$sResultStyleClass}'><A HREF='./ArtistMaintenance.php?ARTIST_ID={$oArtistRecord->id}'>{$oArtistRecord->name}</A></div>";
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
			if (($_POST['hdnArtistID'] ?? 0) > 0)
			{
				echo "<a href='". ADMIN_DIR . "/SongMaintenance.php?ARTIST_ID={$_POST['hdnArtistID']}";
				echo "' class='secondaryLinkButton'>Edit Songs</a>";
				echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
				echo "<br class='visible-xs'>";
				echo "<a href='". ADMIN_DIR . "/CDMaintenance.php?ARTIST_ID={$_POST['hdnArtistID']}";					
				echo "&ACTION=ADD_EXPENSE'  class='secondaryLinkButton'>Edit CDs</a>";
				echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
				echo "<br class='visible-xs'>";
				$reportName = $aArtistRecords[0]->name ?? ($_POST['txtArtistName'] ?? 'Artist');
				echo "<a href='". ADMIN_DIR . "/ArtistReport.php?ARTIST_ID={$_POST['hdnArtistID']}";							
				echo "'  class='secondaryLinkButton'>{$reportName} Report</a>";
			}
			?>
			</div>			
				<div class="col-xs-12 FieldGroup">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							NAME: <input type="text" name="txtArtistName" value="<?php echo $_POST['txtArtistName'] ?? ''; ?>" size="40" />
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
							<input type="text" name="txtImage" id="txtImage" value="<?php echo $_POST['txtImage'] ?? ''; ?>" size="30" /><BR />
							<input type="button" name="btnPreviewImage" onclick="preview_image('txtImage','<?php echo IMG_DIR ?>')" value="Preview" />												
					</div>
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="row FormFieldNoEdit">
					<div class ="col-xs-3">
						ID: <?php echo $_POST['hdnArtistID'] ?? ''; ?>						
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
 * buildArtistObject
 * 
 * This function loads builds a Artist object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildArtistObject(\Datalayer\Artist $Artist)
{
	$id = $_POST['hdnArtistID'] ?? null;
	$Artist->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$Artist->name = html_entity_decode($_POST['txtArtistName'] ?? '', ENT_QUOTES);
	$Artist->image = html_entity_decode($_POST['txtImage'] ?? '', ENT_QUOTES);
}


/*
 ********************************************************************************
 * loadArtist
 * 
 * This function loads the form field array from a populated Artist object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadArtist(\Datalayer\Artist $Artist, $form)
{

	if (!is_null($Artist->id))
	{
		$_POST['hdnArtistID'] = $Artist->id;

		$_POST['txtArtistName'] = htmlentities($Artist->name ?? '', ENT_QUOTES);
		$_POST['txtImage'] = htmlentities($Artist->image ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($Artist->lastUpdate ?? '', ENT_QUOTES);
		
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
