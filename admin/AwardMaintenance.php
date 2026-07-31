<?php
/*
*******************************************************************
AwardMaintenance.php
This PHP file defines the Maintenance page for managing Awards.
NOTES
Date        Change
-------------------------------------------------------------
2017-03-20	Made responsive
2017-09-02	Improved Responsivity
2021-08-30	Updated for PHP 8
2026-07-30	Migrated to new Datalayer Award repository
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
include_once(DATALAYER_DIR . "/Award.php");
include_once(DATALAYER_DIR . "/AwardRepository.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of Award records from the DB
$aAwardRecords = [];

$sActiveMenuItem = DISCOGRAPHY_ACTIVE;
$sPageName = "Award Maintenance";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
include(ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>
	<!-- Javascript required for Calendar picker -->
	<script language="javascript" src="./javascript/calendar.js"></script>

	<script type="text/javascript">
		<!--
		function validate_form() {
			bValid = true;
			sErrorMessage = "";

			//Award Name is a required Field
			if (document.AwardMaint.txtAwardName.value == "") {
				sErrorMessage += "Award Name Required\n";
				bValid = false;
			}

			//Artist is a required Field
			if (document.AwardMaint.selArtist.value == "0") {
				sErrorMessage += "Artist Required\n";
				bValid = false;
			}

			//If any errors, alert
			if (!bValid) {
				alert(sErrorMessage);
			}

			return bValid;
		}
		//
		-->
	</script>

</head>

<body>

	<!-- DIV used for Image Preview Popup -->
	<div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15" id="preview_div"></div>

	<div class="container-fluid">
		<form name="AwardMaint" action="AwardMaintenance.php" method="post">

			<?php
			//Instantiate needed objects
			$awardRepo = new \Datalayer\AwardRepository();
			$thisAward = new \Datalayer\Award();
			$form = new Form();

			//Get the ID query string parameter
			$nThisAwardID = $_REQUEST['Award_ID'] ?? null;

			//If an artist ID is passed, go ahead and search Awards for that artist
			if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "") {
				$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
				$_POST['btnSearch'] = "Search";
			}

			try {
			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisAwardID) && $nThisAwardID !== '') {

				$entity = $awardRepo->findById((int) $nThisAwardID);

				if ($entity) {
					$thisAward = $entity;
					$aAwardRecords = [$entity];
					loadAward($thisAward, $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;
				} else {
					$form->sMessage = "Award record not found.";
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
					buildAwardObject($thisAward);

					if ($awardRepo->insert($thisAward)) {
						$thisAward = $awardRepo->findById((int) $thisAward->id) ?? $thisAward;
						$aAwardRecords = [$thisAward];
						loadAward($thisAward, $form);

						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Award Added";
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

					buildAwardObject($thisAward);

					if ($awardRepo->update($thisAward)) {
						$thisAward = $awardRepo->findById((int) $thisAward->id) ?? $thisAward;
						$aAwardRecords = [$thisAward];
						loadAward($thisAward, $form);

						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Award Updated";
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

					buildAwardObject($thisAward);

					if (!empty($thisAward->id) && $awardRepo->delete((int) $thisAward->id)) {
						clearFormFields($form);

						$form->sMessage = "Award Deleted";
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
					buildAwardObject($thisAward);

					$criteria = [
						'id' => $thisAward->id,
						'name' => $thisAward->name,
						'fuzzyName' => true,
						'description' => $thisAward->description,
						'awardDate' => $thisAward->awardDate,
						'url' => $thisAward->url,
						'orderBy' => 'date',
					];

					if (!empty($thisAward->artistId)) {
						$criteria['artistId'] = $thisAward->artistId;
					}
					if ($thisAward->cdId !== null && $thisAward->cdId !== '') {
						$criteria['cdId'] = $thisAward->cdId;
					}
					if (!empty($thisAward->songId)) {
						$criteria['songId'] = $thisAward->songId;
					}
					if (!empty($thisAward->performanceRelated)) {
						$criteria['performanceRelated'] = true;
					}

					$aAwardRecords = $awardRepo->find($criteria);

					if (sizeof($aAwardRecords) < 1) {
						$form->sMessage = "No Award records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					} else if (sizeof($aAwardRecords) == 1) {
						$thisAward = $aAwardRecords[0];
						loadAward($thisAward, $form);

						$form->sMessage = "One Award record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$form->sMessage = "Select Award record to edit from results list below.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_SELECT;
					}
				}
				// *************
				// *   CLEAR   *
				// *************
				else if (isset($_POST["btnClear"])) {

					clearFormFields($form);

					$form->sMessage = "Search for records or Add new record";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_NEW;
				}
				// *************
				// *   CANCEL  *
				// *************
				else if (isset($_POST["btnCancel"])) {
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
					$_POST['hdnAwardID'] = NULL;

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
			<input type="hidden" name="hdnAwardID" value="<?php echo $_POST['hdnAwardID'] ?? '' ?>" />

			<div class="row">
				<?php
				include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Award Maintenance
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
						echo "	<div class='hidden-xs col-sm-12 col-sm-3 result-header'>Award Date</div>";
						echo "	<div class='hidden-xs col-sm-12 col-sm-9 result-header'>Award Name</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Awards</div>";
						echo "</div>";


						foreach ($aAwardRecords as $oAwardRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";
							echo "	<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./AwardMaintenance.php?Award_ID={$oAwardRecord->id}'>{$oAwardRecord->awardDate}</a></div>";
							echo "	<div class='col-xs-12 col-sm-9 {$sResultStyleClass}'><A HREF='./AwardMaintenance.php?Award_ID={$oAwardRecord->id}'>{$oAwardRecord->name}</a></div>";
							echo "</div>";
						}
						?>

						<div class="row" style="height: 20px;"></div>
					</div>

				<?php
			} else {
				?>

					<div class="row">
						<div class="col-xs-12 col-md-6 FieldGroup">
							<div class="row">
								<div class="col-xs-12">
									NAME: <input type="text" name="txtAwardName" value="<?php echo $_POST['txtAwardName'] ?? ''; ?>" size="40" />
								</div>
								<div class="col-xs-12">
									AWARD URL: <input type="text" name="txtAwardURL" value="<?php echo $_POST['txtAwardURL'] ?? ''; ?>" size="30" />
								</div>
								<div class="col-xs-2 category-selector">
									<input type="checkbox" name="chkPerformanceRelated" class="result-checkbox" value="PerformanceRelated" <?php if (!empty($_POST['chkPerformanceRelated'])) {
																																				echo " checked ";
																																			}; ?> />
								</div>
								<div class="col-xs-10 result-checkbox-text">
									PERFORMANCE RELATED
								</div>
								<div class="col-xs-12">
									<?php
									renderArtistDropDown($_POST['selArtist'] ?? 0);
									?>
								</div>
								<div class="col-xs-12">
									<?php
									renderCDDropDown($_POST['selCD'] ?? '');
									?>
								</div>
								<div class="col-xs-12">
									<?php
									renderSongDropDown($_POST['selSong'] ?? '');
									?>
								</div>
								<div class="col-xs-12">
									AWARD DATE: <br>
									<?php
									renderDatePicker("AwardDate", $_POST['AwardDate'] ?? ($thisAward->awardDate ?? ''));
									?>
								</div>
								<div class="col-xs-12">
									DESCRIPTION: <textarea name="txtAwardDescription" rows="4" cols="50"><?php echo $_POST['txtAwardDescription'] ?? ''; ?></textarea>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-md-3 FieldGroup">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-12 thumbnail-preview">
									<span class="Subtitle">Image</span><br />
									<?php
									if (!empty($_POST['txtAwardImage'])) {
										$sFullImagePath = IMG_DIR . "/{$_POST['txtAwardImage']}";
									} else {
										$sFullImagePath = NULL;
									}
									$form->renderImagePreview($sFullImagePath, "");
									?>
									<br /><br />
									<input type="text" name="txtAwardImage" id="txtAwardImage" value="<?php echo $_POST['txtAwardImage'] ?? ''; ?>" size="30" /><BR />
									<input type="button" name="btnPreviewImage" onclick="preview_image('txtAwardImage','<?php echo IMG_DIR ?>')" value="Preview" />
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="row FormFieldNoEdit">
								<div class="col-xs-3">
									ID: <?php echo $_POST['hdnAwardID'] ?? ''; ?>
								</div>
								<div class="col-xs-9 FormFieldNoEdit">
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
 * buildAwardObject
 * 
 * This function loads builds a Award object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
	function buildAwardObject(\Datalayer\Award $Award)
	{
		$id = $_POST['hdnAwardID'] ?? null;
		$Award->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

		$Award->name = html_entity_decode($_POST['txtAwardName'] ?? '', ENT_QUOTES);
		$Award->description = html_entity_decode($_POST['txtAwardDescription'] ?? '', ENT_QUOTES);
		$Award->image = html_entity_decode($_POST['txtAwardImage'] ?? '', ENT_QUOTES);
		$Award->url = html_entity_decode($_POST['txtAwardURL'] ?? '', ENT_QUOTES);
		$Award->artistId = (int) ($_POST['selArtist'] ?? 0);

		$cdId = $_POST['selCD'] ?? null;
		if ($cdId === '' || $cdId === null) {
			$Award->cdId = null;
		} else {
			$Award->cdId = (int) $cdId;
		}

		$songId = $_POST['selSong'] ?? null;
		if ($songId === '' || $songId === null) {
			$Award->songId = null;
		} else {
			$Award->songId = (int) $songId;
		}

		//Award Date	
		$dtAwardDate = isset($_REQUEST["AwardDate"]) ? $_REQUEST["AwardDate"] : "";
		if ($dtAwardDate > "0000-00-00") {
			$dtAwardDate = isset($_POST["AwardDate"]) ? $_POST["AwardDate"] : "";
		}
		if ($dtAwardDate > "0000-00-00") {
			$Award->awardDate = $dtAwardDate;
		}

		$Award->performanceRelated = (($_POST['chkPerformanceRelated'] ?? '') == "PerformanceRelated");
	}


	/*
 ********************************************************************************
 * loadAward
 * 
 * This function loads the form field array from a populated Award object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
	function loadAward(\Datalayer\Award $Award, $form)
	{

		if (!is_null($Award->id)) {
			$_POST['hdnAwardID'] = $Award->id;
			$_POST['txtAwardName'] = htmlentities($Award->name ?? '', ENT_QUOTES);
			$_POST['txtAwardDescription'] = $Award->description;
			$_POST['txtAwardImage'] = htmlentities($Award->image ?? '', ENT_QUOTES);
			$_POST['txtAwardURL'] = htmlentities($Award->url ?? '', ENT_QUOTES);
			$_POST['AwardDate'] = htmlentities($Award->awardDate ?? '', ENT_QUOTES);
			$_POST['selArtist'] = $Award->artistId;
			$_POST['selCD'] = $Award->cdId;
			$_POST['selSong'] = $Award->songId;
			$_POST['chkPerformanceRelated'] = $Award->performanceRelated;
			$_POST['txtLastUpdate'] = htmlentities($Award->lastUpdate ?? '', ENT_QUOTES);
		} else {
			foreach ($_POST as $fieldName => $fieldValue) {
				$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
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
		foreach ($_POST as $fieldName => $fieldValue) {
			$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
		}
	}

	?>

</body>

</html>
