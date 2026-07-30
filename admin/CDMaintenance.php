<?php
/*
*******************************************************************
CDMaintenance.php
This PHP file defines the Maintenance page for managing CDs.
NOTES
Date        Change
-------------------------------------------------------------
2015-12-18	Added Artist 
2016-02-11	Added renderArtistDropDown()
2016-07-04	Added Thumbnail, Purcahse Link, SHoprt Name and Desc
2017-02-21	Made Responsive
2017-08-28	Improved Responsivity
2021-08-30	Updated for PHP 8
2026-07-30	Migrated to new Datalayer CD repository
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

$sActiveMenuItem = DISCOGRAPHY_ACTIVE;	
$sPageName = "CD Maintenance";
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
	
	//include new datalayer
	include_once (DATALAYER_DIR . "/Connection.php");
	include_once (DATALAYER_DIR . "/CD.php");
	include_once (DATALAYER_DIR . "/CDRepository.php");
	include_once (DATALAYER_DIR . "/Song.php");
	include_once (DATALAYER_DIR . "/SongRepository.php");

	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");

 	//include Form Class		
 	include (CLASS_DIR . "/class_Form.php");

	//Array of CD records from the DB
	$aCDRecords = [];
	
	?>
<div class="container-fluid">	
<form name="CDMaint" action="CDMaintenance.php" method="post">

	<?php
		//Instantiate needed objects
		$cdRepo = new \Datalayer\CDRepository();
		$songRepo = new \Datalayer\SongRepository();
		$thisCD = new \Datalayer\CD();
		$form = new Form();

		//Get the ID query string parameter
		$nThisCDID = $_REQUEST['CD_ID'] ?? null;

		//If an artist ID is passed, go ahead and search CDs for that artist
		if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "")
		{
			$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
			$_POST['btnSearch'] = "Search";
		}

		try {
			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisCDID) && $nThisCDID !== '')
			{
				$entity = $cdRepo->findById((int) $nThisCDID);

				if ($entity)
				{
					$thisCD = $entity;
					$aCDRecords = [$entity];
					loadCD($thisCD, $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					$form->sMessage = "CD record not found.";
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
					buildCDObject($thisCD);
					
					if ($cdRepo->insert($thisCD))			
					{
						$thisCD = $cdRepo->findById((int) $thisCD->id) ?? $thisCD;
						$aCDRecords = [$thisCD];
						loadCD($thisCD, $form);
						
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "CD Added";
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
					buildCDObject($thisCD);
					
					if ($cdRepo->update($thisCD))			
					{
						$thisCD = $cdRepo->findById((int) $thisCD->id) ?? $thisCD;
						$aCDRecords = [$thisCD];
						loadCD($thisCD, $form);
					
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "CD Updated";
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
					buildCDObject($thisCD);				

					if (!empty($thisCD->id)) {
						$relatedSongs = $songRepo->findByCdId((int) $thisCD->id);
						if (sizeof($relatedSongs) > 0) {
							$form->sMessage = "Can not delete CD because it has ";
							$form->sMessage .= "<A HREF='" . ADMIN_DIR . "/SongMaintenance.php?CD_ID=" . $thisCD->id;
							$form->sMessage .= "'>" . sizeof($relatedSongs) . " songs.</A>.";
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->nFormMode = FORM_MODE_EDIT;
						} else if ($cdRepo->delete((int) $thisCD->id)) {
							clearFormFields($form);
							
							$form->sMessage = "CD Deleted";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_NEW;					
						} else {
							$form->sMessage = "DELETE FAILED";
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->nFormMode = FORM_MODE_EDIT;					
						}
					} else {
						$form->sMessage = "DELETE FAILED: No CD ID supplied.";
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				
				}
				// **************
				// *   SEARCH   *
				// **************
				else if (isset($_POST["btnSearch"])) 
				{
					buildCDObject($thisCD);

					$aCDRecords = $cdRepo->find([
						'id' => $thisCD->id,
						'name' => $thisCD->name,
						'fuzzyName' => true,
						'shortName' => $thisCD->shortName,
						'artistId' => $thisCD->artistId,
						'upc' => $thisCD->upc,
						'includeSingles' => false,
						'orderBy' => 'release',
					]);

					if(sizeof($aCDRecords) < 1)
					{
						$form->sMessage = "No CD records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($aCDRecords) == 1)
					{
						$thisCD = $aCDRecords[0];
						loadCD($thisCD, $form);			

						$form->sMessage = "One CD record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
					}
					else 
					{
						$form->sMessage = "Select CD record to edit from results list below.";
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
					$_POST['hdnCDID'] = NULL;
					
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
		} catch (Exception $e) {
			error_log('CDMaintenance error: ' . $e->getMessage());
			$form->sMessage = "ERROR: " . $e->getMessage();
			$form->nMessageType = MESSAGE_TYPE_ERROR;
			$form->nFormMode = FORM_MODE_NEW;
		}

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnCDID" value="<?php echo $_POST['hdnCDID']?>" />	

	<div class="row">
		<?php
		include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
		?>
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					CD Maintenance
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
			echo "	<div class='hidden-xs col-sm-12 result-header'>CD Name</div>";
			echo "	<div class='visible-xs col-xs-12 result-header'>CDs</div>";
			echo "</div>";
				
		foreach($aCDRecords as $oCDRecord)
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

			echo "	<div class='col-xs-12 {$sResultStyleClass}'><A HREF='./CDMaintenance.php?CD_ID={$oCDRecord->id}'>{$oCDRecord->name}</A></div>";
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
				if ($_POST['hdnCDID'] > 0)
				{
					echo "<a href='". ADMIN_DIR . "/SongReport.php?CD_ID={$thisCD->id}";
					echo "' class='secondaryLinkButton'>CD Report</a>";
				}
				?>
			</div>			
			<div class="col-xs-12 col-md-6 FieldGroup">
				<div class="row">
					<div class="col-xs-12">
						NAME: <input type="text" name="txtCDName" value="<?php echo $_POST['txtCDName']; ?>" size="40" />
					</div>
					<div class="col-xs-12">
						SHORT NAME: <input type="text" name="txtCDShortName" value="<?php echo $_POST['txtCDShortName']; ?>" size="40" />
					</div>
					<div class="col-xs-12">
						UPC: <input type="text" name="txtUPC" value="<?php echo $_POST['txtUPC']; ?>" size="40" />
					</div>
					<div class="col-xs-12">
						RUN TIME: <input type="text" name="txtRunTime" value="<?php echo $_POST['txtRunTime']; ?>" size="30" />
					</div>
					<div class="col-xs-12">
						PURCHASE LINK: <input type="text" name="txtPurchaseLink" value="<?php echo $_POST['txtPurchaseLink']; ?>" size="30" />
					</div>
					<div class="col-xs-12">
						<?php
						renderArtistDropDown($_POST['selArtist']);
						?>
					</div>
					<div class="col-xs-12">
						DESCRIPTION: <textarea name="txtCDDescription"  rows="4" cols="50"><?php echo $_POST['txtCDDescription']; ?></textarea>
					</div>
					<div class="col-xs-12">
						RELEASE DATE: <BR /> 
						<?php	  
							renderDatePicker("ReleaseDate", $_POST['ReleaseDate']);
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
						<br /><br />		
						<input type="text" name="txtImage" id="txtImage" value="<?php echo $_POST['txtImage']; ?>" size="30" /><BR />
						<input type="button" name="btnPreviewImage" onclick="preview_image('txtImage','<?php echo IMG_DIR ?>')" value="Preview" />												
					</div>
					<div class="col-xs-12 col-sm-6 col-md-12 thumbnail-preview">
						<span class="Subtitle">Thumbnail Image</span><br />												
						<?php
						if (!empty($_POST['txtThumbnail']))
						{
							$sFullImagePath =IMG_DIR . "/{$_POST['txtThumbnail']}";
						}
						else
						{
							$sFullImagePath = NULL;
						}
						$form->renderImagePreview($sFullImagePath, ""); 
						?>			
						<br /><br />											
						<input type="text" name="txtThumbnail" id="txtThumbnail" value="<?php echo $_POST['txtThumbnail']; ?>" size="30" /><BR />
						<input type="button" name="btnPreviewThumbnail" onclick="preview_image('txtThumbnail','<?php echo IMG_DIR ?>')" value="Preview" />												
					</div>
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="row">
					<div class ="col-xs-3 FormFieldNoEdit">
						ID: <?php echo $_POST['hdnCDID']; ?>						
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
 * buildCDObject
 * 
 * This function loads builds a CD object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildCDObject(\Datalayer\CD $CD)
{
	$id = $_POST['hdnCDID'] ?? null;
	$CD->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$CD->name = html_entity_decode($_POST['txtCDName'] ?? '', ENT_QUOTES);
	$CD->shortName = html_entity_decode($_POST['txtCDShortName'] ?? '', ENT_QUOTES);
	$CD->description = html_entity_decode($_POST['txtCDDescription'] ?? '', ENT_QUOTES);
	$CD->image = html_entity_decode($_POST['txtImage'] ?? '', ENT_QUOTES);
	$CD->thumbnail = html_entity_decode($_POST['txtThumbnail'] ?? '', ENT_QUOTES);
	$CD->upc = html_entity_decode($_POST['txtUPC'] ?? '', ENT_QUOTES);
	$CD->runTime = html_entity_decode($_POST['txtRunTime'] ?? '', ENT_QUOTES);
	$CD->purchaseLink = html_entity_decode($_POST['txtPurchaseLink'] ?? '', ENT_QUOTES);
	$CD->artistId = (int) ($_POST['selArtist'] ?? 0);

	//Release Date	
	$dtReleaseDate = isset($_REQUEST["ReleaseDate"]) ? $_REQUEST["ReleaseDate"] : "";
	if($dtReleaseDate > "0000-00-00")
	{
		$dtReleaseDate = isset($_POST["ReleaseDate"]) ? $_POST["ReleaseDate"] : "";
	}
	if($dtReleaseDate > "0000-00-00")
	{
		$CD->releaseDate = $dtReleaseDate;
	}

}


/*
 ********************************************************************************
 * loadCD
 * 
 * This function loads the form field array from a populated CD object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadCD(\Datalayer\CD $CD, $form)
{
	if (!is_null($CD->id))
	{
		$_POST['hdnCDID'] = $CD->id;

		$_POST['txtCDName'] = htmlentities($CD->name ?? '', ENT_QUOTES);
		$_POST['txtCDShortName'] = htmlentities($CD->shortName ?? '', ENT_QUOTES);
		$_POST['txtCDDescription'] = $CD->description;
		$_POST['txtImage'] = htmlentities($CD->image ?? '', ENT_QUOTES);
		$_POST['txtThumbnail'] = htmlentities($CD->thumbnail ?? '', ENT_QUOTES);
		$_POST['txtUPC'] = htmlentities($CD->upc ?? '', ENT_QUOTES);
		$_POST['txtRunTime'] = htmlentities($CD->runTime ?? '', ENT_QUOTES);
		$_POST['txtPurchaseLink'] = htmlentities($CD->purchaseLink ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($CD->lastUpdate ?? '', ENT_QUOTES);
		$_POST['ReleaseDate'] = htmlentities($CD->releaseDate ?? '', ENT_QUOTES);
		$_POST['selArtist'] = $CD->artistId;
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
 ********************************************************************************
*/
function clearFormFields($form)
{
	$_POST = array();
}

/*
 ********************************************************************************
 * copyFormFields
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

	//CD Name is a required Field
    if ( document.CDMaint.txtCDName.value == "" )
    {
        sErrorMessage += "CD Name Required\n";
        bValid = false;
    }
	
	//Artist is a required Field
    if ( document.CDMaint.selArtist.value == "0" )
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
