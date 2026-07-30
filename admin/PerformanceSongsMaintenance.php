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

	<?php
	
	//include PerformanceSongs Class		
 	include_once (CLASS_DIR . "/class_PerformanceSongs.php");

	
	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");

 	//include Form Class		
 	include (CLASS_DIR . "/class_Form.php");

	//Array of PerformanceSongs records from the DB
	global $aPerformanceSongsRecords;
	
	?>
	
<div class="container-fluid">		
<form name="PerformanceSongsMaint" action="PerformanceSongsMaintenance.php" method="post">

	<?php

		//Instantiate needed objects
		$thisPerformanceSongs = new PerformanceSongs();
		$form = new Form();

		//Get the ID query string parameter
		$nThisPerformanceSongsID = $_REQUEST['PERFORMANCE_SONGS_ID'];
		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisPerformanceSongsID))
		{
						
			$thisPerformanceSongs->nPerformanceSongID = $nThisPerformanceSongsID;

			//Search the Database for records matching the search criteria			
			if ($thisPerformanceSongs->getPerformanceSongs())
			{
			
				//Records found
				if (sizeof($thisPerformanceSongs->aPerformanceSongsRecords) > 0)
				{
									
					//Only One Record should be returned.  Add this to the form field array
					//so that it displays in the form fields and to the values in the
					//current Object.
					loadPerformanceSongs($thisPerformanceSongs->aPerformanceSongsRecords[0], $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
				else
				{
					//The record was not found
					$form->sMessage = "PerformanceSongs record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;			
				}
			}
			else
			{
				//Error
				$form->sMessage = $thisPerformanceSongs->sErrorMessage;
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
				buildPerformanceSongsObject($thisPerformanceSongs);
				
				//Insert record
				if ($thisPerformanceSongs->insertPerformanceSongs())			
				{
					//Load the form fields with the newly populated object
					loadPerformanceSongs($thisPerformanceSongs, $form);
					
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "PerformanceSongs Added";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
				
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ADD RECORD FAILED: {$thisPerformanceSongs->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
					
				}
					
			}					
			// **************
			// *   UPDATE   *
			// **************
			else if (isset($_POST["btnUpdate"])) 
			{
			
				//Load values from form field array into DB object
				buildPerformanceSongsObject($thisPerformanceSongs);
				
				//Update record
				if ($thisPerformanceSongs->updatePerformanceSongs())			
				{
				
					//reload PerformanceSongs
					$thisPerformanceSongs->getPerformanceSongs();
				
					//Load the form fields with the newly populated DB object						
					loadPerformanceSongs($thisPerformanceSongs->aPerformanceSongsRecords[0], $form);
				
					//Success
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->sMessage = "PerformanceSongs Updated";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
				else
				{
					//Failure
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->sMessage = "ERROR: Update Failed - {$thisPerformanceSongs->sErrorMessage}";
					$form->nFormMode = FORM_MODE_EDIT;			
				}
			}
			// **************
			// *   DELETE   *
			// **************
			else if (isset($_POST["btnDelete"])) 
			{
			
				//Load DB record
				buildPerformanceSongsObject($thisPerformanceSongs);				

				//Delete record
				if ($thisPerformanceSongs->deletePerformanceSongs())
				{
					//Clear the form fields
					clearFormFields($form);
					
					//Success
					$form->sMessage = "PerformanceSongs Deleted";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;					
				}
				else
				{
					$form->sMessage = "DELETE FAILED: {$thisPerformanceSongs->sErrorMessage}";
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
				buildPerformanceSongsObject($thisPerformanceSongs);

				//Search the Database for records matching the search criteria			
				if ($thisPerformanceSongs->getPerformanceSongs())
				{
					//No records found
					if(sizeof($thisPerformanceSongs->aPerformanceSongsRecords) < 1)
					{
						$form->sMessage = "No PerformanceSongs records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;			
					}			
					else if (sizeof($thisPerformanceSongs->aPerformanceSongsRecords) == 1)
					{
						//Only One Record returned.  Add this to the form field array
						//so that it displays in the form fields
						loadPerformanceSongs($thisPerformanceSongs->aPerformanceSongsRecords[0], $form);			

						$form->sMessage = "One PerformanceSongs record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;			
						
					}
					//If Multiple records found, the array of search reults will be populated
					else 
					{
						//Multiiple records returned
						$form->sMessage = "Select PerformanceSongs record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;			
					}

				}
				else
				{
					//Attempt to get records failed
					$form->sMessage = $thisPerformanceSongs->sErrorMessage;
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

	?>
	<!-- Hidden Fields -->
	<input type="hidden" name="hdnPerformanceSongID" value="<?php echo $_POST['hdnPerformanceSongID']?>" />	
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

		$totalEstimatedTimeFormated = calculateSongTimeTotal($thisPerformanceSongs->aPerformanceSongsRecords); 
		
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

				
		foreach($thisPerformanceSongs->aPerformanceSongsRecords as $oPerformanceSongsRecord)
		{
			$sTimeClass='';
			if($oPerformanceSongsRecord->tEstimatedTime == '00:00:00'){
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
			echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->nPerformanceSongID}'>{$oPerformanceSongsRecord->sTitle}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->nPerformanceSongID}'>{$oPerformanceSongsRecord->sArtist}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->nPerformanceSongID}'>{$oPerformanceSongsRecord->sTuning}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->nPerformanceSongID}'>{$oPerformanceSongsRecord->sCapo}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->nPerformanceSongID}'>{$oPerformanceSongsRecord->nRating}</A></div>";
			echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass} {$sTimeClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->nPerformanceSongID}'>{$oPerformanceSongsRecord->tEstimatedTime}</A></div>";			
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
								TITLE: <input type="text" name="txtTitle" value="<?php echo $_POST['txtTitle']; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-4">
								ARTIST: <input type="text" name="txtArtist" value="<?php echo $_POST['txtArtist']; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-4">
								TUNING: <input type="text" name="txtTuning" value="<?php echo $_POST['txtTuning']; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-3">
								CAPO: <input type="text" name="txtCapo" value="<?php echo $_POST['txtCapo']; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-3">
								ESTIMATED TIME <i>(hh:mm:ss)</i>: <input type="text" name="txtEstimatedTime" value="<?php echo $_POST['txtEstimatedTime']; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-3">
								EFFECT: <input type="text" name="txtEffect" value="<?php echo $_POST['txtEffect']; ?>" size="40" />
						</div>
						<div class="col-xs-12 col-md-3">
							RATING:
							<SELECT ID ="selRating" NAME ="selRating">
								<OPTION <?php if(is_null($_POST['selRating']) ){ echo " SELECTED='SELECTED' "; } ?> VALUE="" >All</OPTION>
								<OPTION <?php if($_POST['selRating'] == "1"){ echo " SELECTED='SELECTED' ";  } ?> VALUE="1">1</OPTION>
								<OPTION <?php if($_POST['selRating'] == "2"){ echo " SELECTED='SELECTED' "; } ?> VALUE="2">2</OPTION>
								<OPTION <?php if($_POST['selRating'] == "3"){ echo " SELECTED='SELECTED' "; } ?> VALUE="3">3</OPTION>
								<OPTION <?php if($_POST['selRating'] == "4"){ echo " SELECTED='SELECTED' "; } ?> VALUE="4">4</OPTION>
								<OPTION <?php if($_POST['selRating'] == "5"){ echo " SELECTED='SELECTED' "; } ?> VALUE="5">5</OPTION>						
							</SELECT>
						</div>
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkLearned" class="result-checkbox" value="LEARNED"<?php if( $_POST['chkLearned']) {echo " checked ";}; ?> />
										</div>
										<div class="col-xs-9 col-sm-10 result-checkbox-text">
											LEARNED
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkClean" class="result-checkbox" value="CLEAN"<?php if( $_POST['chkClean']) {echo " checked ";}; ?> />
										</div>
										<div class="col-xs-9 col-sm-10 result-checkbox-text">
											CLEAN
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkPopular" class="result-checkbox" value="POPULAR"<?php if( $_POST['chkPopular']) {echo " checked ";}; ?> />
										</div>
										<div class="col-xs-9 col-sm-10 result-checkbox-text">
											POPULAR
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									<div class="row">
										<div class="col-xs-3 col-sm-2">
											<input type="checkbox" name="chkOriginal" class="result-checkbox" value="ORIGINAL"<?php if( $_POST['chkOriginal']) {echo " checked ";}; ?> />
										</div>
										<div class="col-xs-9 col-sm-10 result-checkbox-text">
											ORIGINAL
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									TABS:
									<SELECT ID ="selTabs" NAME ="selTabs">
										<OPTION <?php if(is_null($_POST['selTabs'])){ echo " SELECTED='SELECTED' "; } ?> VALUE="" >N/A</OPTION>
										<OPTION <?php if($_POST['selTabs'] == "Y"){ echo " SELECTED='SELECTED' ";  } ?> VALUE="Y">YES</OPTION>
										<OPTION <?php if($_POST['selTabs'] == "N"){ echo " SELECTED='SELECTED' "; } ?> VALUE="N">NO</OPTION>
									</SELECT>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-3">
									DEMO:
									<SELECT ID ="selDemo" NAME ="selDemo">
										<OPTION <?php if(is_null($_POST['selDemo'])){ echo " SELECTED='SELECTED' "; } ?> VALUE="" >N/A</OPTION>
										<OPTION <?php if($_POST['selDemo'] == "Y"){ echo " SELECTED='SELECTED' ";  } ?> VALUE="Y">YES</OPTION>
										<OPTION <?php if($_POST['selDemo'] == "N"){ echo " SELECTED='SELECTED' "; } ?> VALUE="N">NO</OPTION>
									</SELECT>
								</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							NOTES:
							<textarea name="txtNotes" cols=60 rows=5 ><?php echo $_POST['txtNotes']; ?></textarea>
						</div>
					</div>	
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="row FormFieldNoEdit">
					<div class ="col-xs-3">
						ID: <?php echo $_POST['hdnPerformanceSongID']; ?>						
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
 * buildPerformanceSongsObject
 * 
 * This function loads builds a PerformanceSongs object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildPerformanceSongsObject($PerformanceSongs)
{
	//Load the Array used to populate the form fields based on the newly loaded object
	$PerformanceSongs->nPerformanceSongID = $_POST['hdnPerformanceSongID'];

	$PerformanceSongs->sTitle = html_entity_decode($_POST['txtTitle'], ENT_QUOTES);
	$PerformanceSongs->sArtist = html_entity_decode($_POST['txtArtist'], ENT_QUOTES);
	$PerformanceSongs->sTuning = html_entity_decode($_POST['txtTuning'], ENT_QUOTES);
	$PerformanceSongs->sCapo = html_entity_decode($_POST['txtCapo'], ENT_QUOTES);
	$PerformanceSongs->tEstimatedTime = html_entity_decode($_POST['txtEstimatedTime'], ENT_QUOTES);
	$PerformanceSongs->sEffect = html_entity_decode($_POST['txtEffect'], ENT_QUOTES);
	$PerformanceSongs->sNotes = html_entity_decode($_POST['txtNotes'], ENT_QUOTES);

	if($_POST['chkLearned'] == "LEARNED")
	{
		$PerformanceSongs->bLearned = TRUE;
	}

	if($_POST['chkClean'] == "CLEAN")
	{
		$PerformanceSongs->bClean = TRUE;
	}
	if($_POST['chkPopular'] == "POPULAR")
	{
		$PerformanceSongs->bPopular = TRUE;
	}
	if($_POST['chkOriginal'] == "ORIGINAL")
	{
		$PerformanceSongs->bOriginal = TRUE;
	}

	$PerformanceSongs->nRating = $_POST['selRating'];
	$PerformanceSongs->bTabs = $_POST['selTabs'];
	$PerformanceSongs->bDemo = $_POST['selDemo'];

	$PerformanceSongs->bFuzzyTitleSearch = TRUE;
}


/*
 ********************************************************************************
 * loadPerformanceSongs
 * 
 * This function loads the form field array from a populated PerformanceSongs object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
function loadPerformanceSongs(&$PerformanceSongs, $form)
{

	if (!is_null($PerformanceSongs->nPerformanceSongID))
	{

		//Load Hidden Fields
		$_POST['hdnPerformanceSongID'] = $PerformanceSongs->nPerformanceSongID;

		$_POST['txtTitle'] = htmlentities($PerformanceSongs->sTitle, ENT_QUOTES);
		$_POST['txtArtist'] = htmlentities($PerformanceSongs->sArtist, ENT_QUOTES);
		$_POST['txtTuning'] = htmlentities($PerformanceSongs->sTuning, ENT_QUOTES);
		$_POST['txtCapo'] = htmlentities($PerformanceSongs->sCapo, ENT_QUOTES);
		$_POST['txtEstimatedTime'] = htmlentities($PerformanceSongs->tEstimatedTime, ENT_QUOTES);
		$_POST['txtEffect'] = htmlentities($PerformanceSongs->sEffect, ENT_QUOTES);
		$_POST['txtNotes'] = htmlentities($PerformanceSongs->sNotes, ENT_QUOTES);
		$_POST['txtLastUpdate'] = htmlentities($PerformanceSongs->dtLastUpdate, ENT_QUOTES);

		$_POST['chkLearned'] = $PerformanceSongs->bLearned;
		$_POST['chkClean'] = $PerformanceSongs->bClean;
		$_POST['chkPopular'] = $PerformanceSongs->bPopular;
		$_POST['chkOriginal'] = $PerformanceSongs->bOriginal;

		$_POST['selTabs'] = $PerformanceSongs->bTabs;
		$_POST['selDemo'] = $PerformanceSongs->bDemo;
		$_POST['selRating'] = $PerformanceSongs->nRating;

		
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
