<?php
/*
*******************************************************************
PerformanceSongsMaintenance.php
This PHP file defines the Maintenance page for managing PerformanceSongs.
NOTES
Date        Change
-------------------------------------------------------------
2020-04-27	Created
2020-11-07	Added Popular Flag
2023-11-24	Added Original flag
2026-07-30	Migrated to new Datalayer PerformanceSongs repository
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

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/PerformanceSongs.php");
include_once(DATALAYER_DIR . "/PerformanceSongsRepository.php");

//Require the Class for the calendar picker
require_once (CLASS_DIR . "/tc_calendar.php");

//include Form Class		
include (CLASS_DIR . "/class_Form.php");

//Array of PerformanceSongs records from the DB
$aPerformanceSongsRecords = [];

$sActiveMenuItem = PERFORMANCES_ACTIVE;	
$sPageName = "Performance Songs Maiintenance";
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
<form name="PerformanceSongsMaint" action="PerformanceSongsMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$performanceSongsRepo = new \Datalayer\PerformanceSongsRepository();
		$thisPerformanceSongs = new \Datalayer\PerformanceSongs();
		$form = new Form();

		//Get the ID query string parameter
		$nThisPerformanceSongsID = $_REQUEST['PERFORMANCE_SONGS_ID'] ?? null;

		try {
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisPerformanceSongsID) && $nThisPerformanceSongsID !== '')
		{
			$entity = $performanceSongsRepo->findById((int) $nThisPerformanceSongsID);

			if ($entity) {
				$thisPerformanceSongs = $entity;
				$aPerformanceSongsRecords = [$entity];
				loadPerformanceSongs($thisPerformanceSongs, $form);

				$form->sMessage = "Update record.";
				$form->nMessageType = MESSAGE_TYPE_INFO;
				$form->nFormMode = FORM_MODE_EDIT;
			} else {
				$form->sMessage = "PerformanceSongs record not found.";
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
				buildPerformanceSongsObject($thisPerformanceSongs);
				
				if ($performanceSongsRepo->insert($thisPerformanceSongs))
				{
					$thisPerformanceSongs = $performanceSongsRepo->findById((int) $thisPerformanceSongs->id) ?? $thisPerformanceSongs;
					$aPerformanceSongsRecords = [$thisPerformanceSongs];
					loadPerformanceSongs($thisPerformanceSongs, $form);
					
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "PerformanceSongs Added";
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
				buildPerformanceSongsObject($thisPerformanceSongs);
				
				if ($performanceSongsRepo->update($thisPerformanceSongs))
				{
					$thisPerformanceSongs = $performanceSongsRepo->findById((int) $thisPerformanceSongs->id) ?? $thisPerformanceSongs;
					$aPerformanceSongsRecords = [$thisPerformanceSongs];
					loadPerformanceSongs($thisPerformanceSongs, $form);
				
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "PerformanceSongs Updated";
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
				buildPerformanceSongsObject($thisPerformanceSongs);

				if (!empty($thisPerformanceSongs->id) && $performanceSongsRepo->delete((int) $thisPerformanceSongs->id))
				{
					clearFormFields($form);
					
					$form->sMessage = "PerformanceSongs Deleted";
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
				buildPerformanceSongsObject($thisPerformanceSongs);

				$criteria = [
					'id' => $thisPerformanceSongs->id,
					'title' => $thisPerformanceSongs->title,
					'fuzzyTitle' => true,
					'artist' => $thisPerformanceSongs->artist,
					'tuning' => $thisPerformanceSongs->tuning,
					'capo' => $thisPerformanceSongs->capo,
					'effect' => $thisPerformanceSongs->effect,
					'notes' => $thisPerformanceSongs->notes,
					'estimatedTime' => $thisPerformanceSongs->estimatedTime,
				];

				if ($thisPerformanceSongs->rating !== null && $thisPerformanceSongs->rating !== '') {
					$criteria['rating'] = $thisPerformanceSongs->rating;
				}
				if (!empty($thisPerformanceSongs->tabs)) {
					$criteria['tabs'] = $thisPerformanceSongs->tabs;
				}
				if (!empty($thisPerformanceSongs->demo)) {
					$criteria['demo'] = $thisPerformanceSongs->demo;
				}
				if ($thisPerformanceSongs->learned) {
					$criteria['learned'] = true;
				}
				if ($thisPerformanceSongs->clean) {
					$criteria['clean'] = true;
				}
				if ($thisPerformanceSongs->popular) {
					$criteria['popular'] = true;
				}
				if ($thisPerformanceSongs->original) {
					$criteria['original'] = true;
				}

				$aPerformanceSongsRecords = $performanceSongsRepo->find($criteria);

				if (sizeof($aPerformanceSongsRecords) < 1)
				{
					$form->sMessage = "No PerformanceSongs records found matching search criteria";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}			
				else if (sizeof($aPerformanceSongsRecords) == 1)
				{
					$thisPerformanceSongs = $aPerformanceSongsRecords[0];
					loadPerformanceSongs($thisPerformanceSongs, $form);			

					$form->sMessage = "One PerformanceSongs record found.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else 
				{
					$form->sMessage = "Select PerformanceSongs record to edit from results list below.";
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
				$_POST['hdnPerformanceSongID'] = NULL;
				
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
	<input type="hidden" name="hdnPerformanceSongID" value="<?php echo $_POST['hdnPerformanceSongID'] ?? ''?>" />	
	<div class="row">
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	</div>
	<div class="row">
		<div class="col-xs-12"> 
			<div class="row">
				<div class="col-xs-12 Title">
					Performance Songs Maintenance
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

		$totalEstimatedTimeFormated = calculateSongTimeTotal($aPerformanceSongsRecords); 
		
		$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

		//Header row for Results
		echo "<div class='row result-header'>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Title</div>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Artist</div>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Tuning</div>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Capo</div>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Rating</div>";
		echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>Time</div>";
		echo "	<div class='visible-xs col-xs-12 result-header'>Performance Songs</div>";
		echo "</div>";

				
		foreach($aPerformanceSongsRecords as $oPerformanceSongsRecord)
		{
			$sTimeClass='';
			if(($oPerformanceSongsRecord->estimatedTime ?? '') == '00:00:00'){
				$sTimeClass = 'missing-time';
			}

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
			echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->title}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->artist}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->tuning}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->capo}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->rating}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass} {$sTimeClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->estimatedTime}</A></div>";			
			echo "</div>";
		}

		echo "<div class='hidden-xs row'>";
		echo "	<div class='col-xs-12' ><hr></div>";
		echo "</div>";

		echo "<div class='row hidden-xs'>";
		echo "	<div class='col-sm-2' ></div>";
		echo "	<div class='col-sm-2' ></div>";
		echo "	<div class='col-sm-2' ></div>";
		echo "	<div class='col-sm-2' ></div>";
		echo "	<div class='col-sm-2' ><b>TOTAL:</b></div>";
		echo "	<div class='col-sm-2' col-md-2'><b>{$totalEstimatedTimeFormated}</b></A></div>";
		echo "</div>";
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
				<div class="col-xs-12 FieldGroup">
					<div class="row">
						<div class="col-xs-12 col-md-4">
								TITLE: <input type="text" name="txtTitle" value="<?php echo $_POST['txtTitle'] ?? ''; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-4">
								ARTIST: <input type="text" name="txtArtist" value="<?php echo $_POST['txtArtist'] ?? ''; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-4">
								TUNING: <input type="text" name="txtTuning" value="<?php echo $_POST['txtTuning'] ?? ''; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-3">
								CAPO: <input type="text" name="txtCapo" value="<?php echo $_POST['txtCapo'] ?? ''; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-3">
								ESTIMATED TIME <i>(hh:mm:ss)</i>: <input type="text" name="txtEstimatedTime" value="<?php echo $_POST['txtEstimatedTime'] ?? ''; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-3">
								EFFECT: <input type="text" name="txtEffect" value="<?php echo $_POST['txtEffect'] ?? ''; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-3">
							RATING:
							<SELECT ID ="selRating" NAME ="selRating">
								<OPTION <?php if(is_null($_POST['selRating'] ?? null) ){ echo " SELECTED='SELECTED' "; } ?> VALUE="" >All</OPTION>
								<OPTION <?php if(($_POST['selRating'] ?? '') == "1"){ echo " SELECTED='SELECTED' ";  } ?> VALUE="1">1</OPTION>
								<OPTION <?php if(($_POST['selRating'] ?? '') == "2"){ echo " SELECTED='SELECTED' "; } ?> VALUE="2">2</OPTION>
								<OPTION <?php if(($_POST['selRating'] ?? '') == "3"){ echo " SELECTED='SELECTED' "; } ?> VALUE="3">3</OPTION>
								<OPTION <?php if(($_POST['selRating'] ?? '') == "4"){ echo " SELECTED='SELECTED' "; } ?> VALUE="4">4</OPTION>
								<OPTION <?php if(($_POST['selRating'] ?? '') == "5"){ echo " SELECTED='SELECTED' "; } ?> VALUE="5">5</OPTION>						
							</SELECT>
						</div>
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkLearned" class="result-checkbox" value="LEARNED"<?php if( !empty($_POST['chkLearned'])) {echo " checked ";}; ?> />
										</div>
										<div class="col-xs-9 col-sm-10 result-checkbox-text">
											LEARNED
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkClean" class="result-checkbox" value="CLEAN"<?php if( !empty($_POST['chkClean'])) {echo " checked ";}; ?> />
										</div>
										<div class="col-xs-9 col-sm-10 result-checkbox-text">
											CLEAN
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkPopular" class="result-checkbox" value="POPULAR"<?php if( !empty($_POST['chkPopular'])) {echo " checked ";}; ?> />
										</div>
										<div class="col-xs-9 col-sm-10 result-checkbox-text">
											POPULAR
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkOriginal" class="result-checkbox" value="ORIGINAL"<?php if( !empty($_POST['chkOriginal'])) {echo " checked ";}; ?> />
										</div>
										<div class="col-xs-9 col-sm-10 result-checkbox-text">
											ORIGINAL
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									TABS:
									<SELECT ID ="selTabs" NAME ="selTabs">
										<OPTION <?php if(is_null($_POST['selTabs'] ?? null)){ echo " SELECTED='SELECTED' "; } ?> VALUE="" >N/A</OPTION>
										<OPTION <?php if(($_POST['selTabs'] ?? '') == "Y"){ echo " SELECTED='SELECTED' ";  } ?> VALUE="Y">YES</OPTION>
										<OPTION <?php if(($_POST['selTabs'] ?? '') == "N"){ echo " SELECTED='SELECTED' "; } ?> VALUE="N">NO</OPTION>
									</SELECT>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									DEMO:
									<SELECT ID ="selDemo" NAME ="selDemo">
										<OPTION <?php if(is_null($_POST['selDemo'] ?? null)){ echo " SELECTED='SELECTED' "; } ?> VALUE="" >N/A</OPTION>
										<OPTION <?php if(($_POST['selDemo'] ?? '') == "Y"){ echo " SELECTED='SELECTED' ";  } ?> VALUE="Y">YES</OPTION>
										<OPTION <?php if(($_POST['selDemo'] ?? '') == "N"){ echo " SELECTED='SELECTED' "; } ?> VALUE="N">NO</OPTION>
									</SELECT>
								</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							NOTES:
							<textarea name="txtNotes" cols=60 rows=5 ><?php echo $_POST['txtNotes'] ?? ''; ?></textarea>
						</div>
					</div>	
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="row FormFieldNoEdit">
					<div class ="col-xs-3">
						ID: <?php echo $_POST['hdnPerformanceSongID'] ?? ''; ?>						
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
 * buildPerformanceSongsObject
 * 
 * This function loads builds a PerformanceSongs object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildPerformanceSongsObject(\Datalayer\PerformanceSongs $PerformanceSongs)
{
	$id = $_POST['hdnPerformanceSongID'] ?? null;
	$PerformanceSongs->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

	$PerformanceSongs->title = html_entity_decode($_POST['txtTitle'] ?? '', ENT_QUOTES);
	$PerformanceSongs->artist = html_entity_decode($_POST['txtArtist'] ?? '', ENT_QUOTES);
	$PerformanceSongs->tuning = html_entity_decode($_POST['txtTuning'] ?? '', ENT_QUOTES);
	$PerformanceSongs->capo = html_entity_decode($_POST['txtCapo'] ?? '', ENT_QUOTES);
	$PerformanceSongs->estimatedTime = html_entity_decode($_POST['txtEstimatedTime'] ?? '', ENT_QUOTES);
	$PerformanceSongs->effect = html_entity_decode($_POST['txtEffect'] ?? '', ENT_QUOTES);
	$PerformanceSongs->notes = html_entity_decode($_POST['txtNotes'] ?? '', ENT_QUOTES);

	$PerformanceSongs->learned = (($_POST['chkLearned'] ?? '') == "LEARNED");
	$PerformanceSongs->clean = (($_POST['chkClean'] ?? '') == "CLEAN");
	$PerformanceSongs->popular = (($_POST['chkPopular'] ?? '') == "POPULAR");
	$PerformanceSongs->original = (($_POST['chkOriginal'] ?? '') == "ORIGINAL");

	$rating = $_POST['selRating'] ?? '';
	$PerformanceSongs->rating = ($rating !== '' && is_numeric($rating)) ? (int) $rating : null;

	$tabs = $_POST['selTabs'] ?? '';
	$PerformanceSongs->tabs = ($tabs !== '') ? $tabs : null;

	$demo = $_POST['selDemo'] ?? '';
	$PerformanceSongs->demo = ($demo !== '') ? $demo : null;
}


/*
 ********************************************************************************
 * loadPerformanceSongs
 * 
 * This function loads the form field array from a populated PerformanceSongs object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadPerformanceSongs(\Datalayer\PerformanceSongs $PerformanceSongs, $form)
{

	if (!is_null($PerformanceSongs->id))
	{
		$_POST['hdnPerformanceSongID'] = $PerformanceSongs->id;

		$_POST['txtTitle'] = htmlentities($PerformanceSongs->title ?? '', ENT_QUOTES);
		$_POST['txtArtist'] = htmlentities($PerformanceSongs->artist ?? '', ENT_QUOTES);
		$_POST['txtTuning'] = htmlentities($PerformanceSongs->tuning ?? '', ENT_QUOTES);
		$_POST['txtCapo'] = htmlentities($PerformanceSongs->capo ?? '', ENT_QUOTES);
		$_POST['txtEstimatedTime'] = htmlentities($PerformanceSongs->estimatedTime ?? '', ENT_QUOTES);
		$_POST['txtEffect'] = htmlentities($PerformanceSongs->effect ?? '', ENT_QUOTES);
		$_POST['txtNotes'] = htmlentities($PerformanceSongs->notes ?? '', ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($PerformanceSongs->lastUpdate ?? '', ENT_QUOTES);

		$_POST['chkLearned'] = $PerformanceSongs->learned;
		$_POST['chkClean'] = $PerformanceSongs->clean;
		$_POST['chkPopular'] = $PerformanceSongs->popular;
		$_POST['chkOriginal'] = $PerformanceSongs->original;

		$_POST['selTabs'] = $PerformanceSongs->tabs;
		$_POST['selDemo'] = $PerformanceSongs->demo;
		$_POST['selRating'] = $PerformanceSongs->rating;
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

	// Title is a required Field
    if ( document.PerformanceSongsMaint.txtTitle.value == "" )
    {
        sErrorMessage += "Title Required\n";
        bValid = false;
	}
	
	// Artist is a required Field
	if ( document.PerformanceSongsMaint.txtArtist.value == "" )
    {
        sErrorMessage += "Artist Required\n";
        bValid = false;
    }

	// Tuning is a required Field
	if ( document.PerformanceSongsMaint.txtTuning.value == "" )
    {
        sErrorMessage += "Tuning Required\n";
        bValid = false;
	}

	//Rating is a required Field
    if ( document.PerformanceSongsMaint.selRating.value == "" || document.PerformanceSongsMaint.selRating.value == 0 )
    {
        sErrorMessage += "Rating Required\n";
        bValid = false;
	}

	//Demo is a required Field
    if ( document.PerformanceSongsMaint.selDemo.value == "" || document.PerformanceSongsMaint.selDemo.value == 0 )
    {
        sErrorMessage += "Demo Required\n";
        bValid = false;
	}

	//Tabs is a required Field
    if ( document.PerformanceSongsMaint.selTabs.value == "" || document.PerformanceSongsMaint.selTabs.value == 0 )
    {
        sErrorMessage += "Tabs Required\n";
        bValid = false;
	}

	//RaEstimated Time must match hh:mm:ss format
    if ( document.PerformanceSongsMaint.txtEstimatedTime.value != "" )
    {
		console.log(document.PerformanceSongsMaint.txtEstimatedTime.value)

		let match = /^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/.test(document.PerformanceSongsMaint.txtEstimatedTime.value)
		if(!match){
			sErrorMessage += "Estiamted Time must be hh:mm:ss format\n";
        	bValid = false;
		}
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
