<?php
/*
*******************************************************************
ThurdySongDataMaintenance.php
This PHP file defines the Maintenance page for 
managing Songs.
NOTES
Date        Change
-------------------------------------------------------------
2018-11-21	Created.
2021-08-30	Updated for PHP 8
2026-07-30	Migrated to new Datalayer ThurdySongData repository
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
include_once(DATALAYER_DIR . "/ThurdySongData.php");
include_once(DATALAYER_DIR . "/ThurdySongDataRepository.php");
include_once(DATALAYER_DIR . "/Song.php");
include_once(DATALAYER_DIR . "/SongRepository.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of Song records from the DB
$aThurdySongDataRecords = [];

$sActiveMenuItem = MISC_ACTIVE;
$sPageName = "Thurdy Song Data Maintenance";

?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<!-- DIV used for Image Preview Popup -->
	<div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15"
		id="preview_div"></div>

	<div class="container-fluid">
		<form name="ThurdySongDataMaint" action="ThurdySongDataMaintenance.php" method="post">

			<?php

			//Instantiate needed objects
			$songDataRepo = new \Datalayer\ThurdySongDataRepository();
			$songRepo = new \Datalayer\SongRepository();
			$thisSong = new \Datalayer\ThurdySongData();
			$form = new Form();

			//Get the Song ID query string parameter
			$nThisSongId = $_REQUEST['SONGID'] ?? null;
			if (isset($nThisSongId) && $nThisSongId !== '' && !is_numeric($nThisSongId)) {
				error_log('ThurdySongDataMaintenance.php: INVALID SONG ID: ' . $nThisSongId);
			}

			try {
			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisSongId) && $nThisSongId !== '') {

				$aThurdySongDataRecords = $songDataRepo->find(['songId' => (int) $nThisSongId]);

				if (sizeof($aThurdySongDataRecords) > 0) {
					$thisSong = $aThurdySongDataRecords[0];
					loadData($thisSong, $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;
				} else {
					$form->sMessage = "Song record not found.";
					$form->nMessageType = MESSAGE_TYPE_WARNING;
					$form->nFormMode = FORM_MODE_NEW;
				}
			} else {
				//Based on which button was selected, perform processing necessary 
				//before the page is rendered
				// **************
				// *   ADD      *
				// **************
				if (isset($_POST["btnAdd"])) {
					buildObject($thisSong);

					if ($songDataRepo->insert($thisSong)) {
						$thisSong = $songDataRepo->findById((int) $thisSong->id) ?? $thisSong;
						$aThurdySongDataRecords = [$thisSong];
						loadData($thisSong, $form);

						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Song Added";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED";
						$form->nFormMode = FORM_MODE_EDIT;
					}

				}
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) {

					buildObject($thisSong);

					if ($songDataRepo->update($thisSong)) {
						$thisSong = $songDataRepo->findById((int) $thisSong->id) ?? $thisSong;
						$aThurdySongDataRecords = [$thisSong];
						loadData($thisSong, $form);

						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Song Updated";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   DELETE   *
				// **************
				else if (isset($_POST["btnDelete"])) {

					buildObject($thisSong);

					if (!empty($thisSong->id) && $songDataRepo->delete((int) $thisSong->id)) {
						clearFormFields($form);

						$form->sMessage = "Song Data Deleted";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
						$form->sMessage = "DELETE FAILED";
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_EDIT;
					}

				}
				// **************
				// *   SEARCH   *
				// **************
				else if (isset($_POST["btnSearch"])) {
					buildObject($thisSong);

					$aThurdySongDataRecords = $songDataRepo->find([
						'id' => $thisSong->id,
						'songId' => $thisSong->songId,
						'videoLink' => $thisSong->videoLink,
						'videoSubmittedBy' => $thisSong->videoSubmittedBy,
						'liveVersion' => $thisSong->liveVersion,
						'dropId' => $thisSong->dropId,
						'artImage' => $thisSong->artImage,
						'orderBy' => 'SONG_ID',
					]);

					if (sizeof($aThurdySongDataRecords) < 1) {
						$form->sMessage = "No Song Data records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					} else if (sizeof($aThurdySongDataRecords) == 1) {
						$thisSong = $aThurdySongDataRecords[0];
						loadData($thisSong, $form);

						$form->sMessage = "One Song record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;

					} else {
						$form->sMessage = "Select Song record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;
					}
				}
				// *************
				// *   CLEAR   *
				// *************
				else if (isset($_POST["btnClear"]) || isset($_POST["btnCancel"])) {

					clearFormFields($form);

					$form->sMessage = "Search for records or Add new record";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;
				}
				// *************
				// *   COPY  *
				// *************
				else if (isset($_POST["btnCopy"])) {
					copyFormFields($form);
					$_POST['hdnThurdySongDataID'] = NULL;

					$form->sMessage = "Search for records or Add new record";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;
				}
				// ***************
				// *  1st TIME   *
				// ***************
				else {
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
			<input type="hidden" name="hdnThurdySongDataID" value="<?php echo $_POST['hdnThurdySongDataID'] ?? '' ?>" />

			<div class="row">
				<?php
				include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Thurdy Song Data Maintenance
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
			if ($form->nFormMode == FORM_MODE_SELECT) {
				?>
				<div class="row">

					<div class="col-xs-12">
						<?php

						$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

						//Header row for Results
						echo "<div class='row result-header'>";
						echo "<div class='hidden-xs col-sm-6 result-header'>Song<BR>ID</div>";
						echo "<div class='hidden-xs col-sm-6 result-header'>Title</div>";
						echo "<div class='visible-xs col-xs-12 result-header'>Songs</div>";
						echo "</div>";

						foreach ($aThurdySongDataRecords as $oThurdySongDataRecord) {

							$sSongTitle = '';
							if (!empty($oThurdySongDataRecord->songId)) {
								$oSong = $songRepo->findById((int) $oThurdySongDataRecord->songId);
								$sSongTitle = htmlentities($oSong->name ?? '', ENT_QUOTES);
							}

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							echo "<div class='col-xs-12 col-sm-6 {$sResultStyleClass}'>";
							echo "<A HREF='./ThurdySongDataMaintenance.php?SONGID={$oThurdySongDataRecord->songId}'>{$oThurdySongDataRecord->songId}</A></div>";

							echo "<div class='col-xs-12 col-sm-6 {$sResultStyleClass}'>";
							echo "<A HREF='./ThurdySongDataMaintenance.php?SONGID={$oThurdySongDataRecord->songId}'>{$sSongTitle}</A></div>";
							echo "</div>";

						}
						?>
					</div>
				</div>
				<?php
			} else {
				?>
				<div class="row">
					<div class="col-xs-12 FieldGroupTitle">
						DETAILS
					</div>
					<div class="col-xs-12 FieldGroup">
						<div class="row">
							<div class="col-xs-12 col-md-5">
								SONG:
								<?php renderArtistSongDropDown($_POST['selSong'] ?? null, THURDY_ARTIST_ID); ?>
							</div>
							<div class="col-xs-12 col-md-5">
								DROP:
								<?php renderDropDropDown($_POST['selDrop'] ?? null); ?>
							</div>
							<div class="col-xs-12 col-md-5">
								VIDEO LINK:
								<input type="text" name="txtVideoLink" value="<?php echo $_POST['txtVideoLink'] ?? ''; ?>"
									size="40" />
							</div>

							<div class="col-xs-12 col-md-5">
								SUBMITTED BY:
								<input type="text" name="txtVideoSubmittedBy"
									value="<?php echo $_POST['txtVideoSubmittedBy'] ?? ''; ?>" size="30" />
							</div>
							<div class="col-xs-12 col-md-5">
								LIVE VERSION:
								<input type="text" name="txtLiveVersion" value="<?php echo $_POST['txtLiveVersion'] ?? ''; ?>"
									size="40" />
							</div>
							<div class="col-xs-12 col-md-5">
								<div class="row">
									<div class="col-xs-12">
										Art Image
									</div>
									<div class="col-xs-12">
										<?php
										if (!empty($_POST['txtArtImage'])) {
											$sFullImagePath = DROP_IMG_DIR . "/{$_POST['txtArtImage']}";
										} else {
											$sFullImagePath = NULL;
										}
										echo "<div style='padding: 15px;'><IMG SRC='" . $sFullImagePath . "' BORDER=0 Height=100 ></div>";
										?>
									</div>
									<div class="col-xs-12">
										<input type="text" name="txtArtImage" id="txtArtImage"
											value="<?php echo $_POST['txtArtImage'] ?? ''; ?>" size="30" /><BR />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 FieldGroup">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class=" col-xs-3 FormFieldNoEdit">
										ID: <?php echo $_POST['hdnThurdySongDataID'] ?? ''; ?>
									</div>
									<div class=" col-xs-9 FormFieldNoEdit">
										LAST UPDATED: <?php echo $_POST['txtLastUpdate'] ?? ''; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div>
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
			}
			?>

		</form>
	</div>
	<?php


	/*
	 ********************************************************************************
	 * buildObject
	 * 
	 * This function loads builds a song object from the data typed into the 
	 * form fields.
	 ********************************************************************************
	 */
	function buildObject(\Datalayer\ThurdySongData $Song)
	{
		$id = $_POST['hdnThurdySongDataID'] ?? null;
		$Song->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

		$songId = $_POST['selSong'] ?? '';
		$Song->songId = ($songId !== '' && is_numeric($songId)) ? (int) $songId : null;

		$Song->videoLink = html_entity_decode($_POST['txtVideoLink'] ?? '', ENT_QUOTES);
		$Song->videoSubmittedBy = html_entity_decode($_POST['txtVideoSubmittedBy'] ?? '', ENT_QUOTES);
		$Song->liveVersion = html_entity_decode($_POST['txtLiveVersion'] ?? '', ENT_QUOTES);

		$dropId = $_POST['selDrop'] ?? '';
		$Song->dropId = ($dropId !== '' && is_numeric($dropId) && (int) $dropId > 0) ? (int) $dropId : null;

		$Song->artImage = html_entity_decode($_POST['txtArtImage'] ?? '', ENT_QUOTES);
	}


	/*
	 ********************************************************************************
	 * loadData
	 * 
	 * This function loads the form field array from a populated Song object
	 * so that it will be displayed in the form fields
	 ********************************************************************************
	 */
	function loadData(\Datalayer\ThurdySongData $Song, $form)
	{
		if (!is_null($Song->id)) {
			$_POST['hdnThurdySongDataID'] = $Song->id;

			$_POST['selSong'] = $Song->songId;
			$_POST['txtVideoLink'] = htmlentities($Song->videoLink ?? '', ENT_QUOTES);
			$_POST['txtVideoSubmittedBy'] = htmlentities($Song->videoSubmittedBy ?? '', ENT_QUOTES);
			$_POST['txtLiveVersion'] = htmlentities($Song->liveVersion ?? '', ENT_QUOTES);
			$_POST['selDrop'] = $Song->dropId;
			$_POST['txtArtImage'] = htmlentities($Song->artImage ?? '', ENT_QUOTES);
			$_POST['txtLastUpdate'] = htmlentities($Song->lastUpdate ?? '', ENT_QUOTES);
		} else {
			foreach ($_POST as $fieldName => $fieldValue) {
				$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
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
		foreach ($_POST as $fieldName => $fieldValue) {
			$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
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
	
		//Revenue Type is a required Field
		if ( document.ThurdySongDataMaint.selSong.value == "" || document.ThurdySongDataMaint.selSong.value == 0 )
		{
			sErrorMessage += "Song Required\n";
			bValid = false;
		}
	
		if (!bValid)
		{
			alert(sErrorMessage);
		}
	
		return bValid;
	}
	//-->
</script>

</html>
