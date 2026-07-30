<?php
/*
*******************************************************************
ReviewMaintenance.php
This PHP file defines the Maintenance page for managing Artists.
NOTES
Date        Change
-------------------------------------------------------------
2017-03-25	Made Responsive
2017-09-03	Improved Responsivity
2017-09-06	Added Internal Review Flag
2021-08-30	Updated for PHP 8
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

//include Review Class		
include_once(CLASS_DIR . "/class_Review.php");

include_once(CLASS_DIR . "/class_Artist.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of Review records from the DB
global $aReviewRecords;

$sActiveMenuItem = DISCOGRAPHY_ACTIVE;
$sPageName = "Review Maintenance";

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


	?>
	<div class="container-fluid">
		<form name="ReviewMaint" action="ReviewMaintenance.php" method="post">

			<?php
			//Get Artists for drop-down list
			$oArtists = new Artist();
			if (!$oArtists->getArtist()) {
				//ERROR
			}

			//Instantiate needed objects
			$thisReview = new Review();
			$thisReview->sOrderBy = DATE_ORDER;
			$form = new Form();

			//Get the ID query string parameter
			$nThisReviewID = $_REQUEST['Review_ID'];

			//If an artist ID is passed, go ahead and search Reviews for that artist
			if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "") {
				$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
				$_POST['btnSearch'] = "Search";
			}

			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisReviewID)) {

				$thisReview->nReviewID = $nThisReviewID;

				//Search the Database for records matching the search criteria			
				if ($thisReview->getReview()) {

					//Records found
					if (sizeof($thisReview->aReviewRecords) > 0) {

						//Only One Record should be returned.  Add this to the form field array
						//so that it displays in the form fields and to the values in the
						//current Object.
						loadReview($thisReview->aReviewRecords[0], $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//The record was not found
						$form->sMessage = "Review record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					}
				} else {
					//Error
					$form->sMessage = $thisReview->sErrorMessage;
					$form->nMessageType = MESSAGE_TYPE_ERROR;
					$form->nFormMode = FORM_MODE_NEW;
				}
			} else {
				//Based on which button was selected, perform processing necessary 
				//before the page is rendered
				// **************
				// *   ADD      *
				// **************
				if (isset($_POST["btnAdd"])) {
					//Load values into DB array
					buildReviewObject($thisReview);

					//Insert record
					if ($thisReview->insertReview()) {
						//reload Review
						$insertedReview = new Review();
						$insertedReview->nReviewID = $thisReview->nReviewID;
						$insertedReview->getReview();
						$thisReview = $insertedReview->aReviewRecords[0];

						//Load the form fields with the newly populated object
						loadReview($thisReview, $form);

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Review Added";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {

						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED: {$thisReview->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) {

					//Load values from form field array into DB object
					buildReviewObject($thisReview);

					//Update record
					if ($thisReview->updateReview()) {

						//reload Review
						$thisReview->getReview();

						//Load the form fields with the newly populated DB object						
						loadReview($thisReview->aReviewRecords[0], $form);

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Review Updated";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed - {$thisReview->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   DELETE   *
				// **************
				else if (isset($_POST["btnDelete"])) {

					//Load DB record
					buildReviewObject($thisReview);

					//Delete record
					if ($thisReview->deleteReview()) {
						//Clear the form fields
						clearFormFields($form);

						//Success
						$form->sMessage = "Review Deleted";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
						$form->sMessage = "DELETE FAILED: {$thisReview->sErrorMessage}";
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   SEARCH   *
				// **************
				else if (isset($_POST["btnSearch"])) {
					//Load Array of Search Values
					buildReviewObject($thisReview);

					//Search the Database for records matching the search criteria			
					if ($thisReview->getReview()) {
						//No records found
						if (sizeof($thisReview->aReviewRecords) < 1) {
							$form->sMessage = "No Review records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($thisReview->aReviewRecords) == 1) {
							//Only One Record returned.  Add this to the form field array
							//so that it displays in the form fields
							loadReview($thisReview->aReviewRecords[0], $form);

							$form->sMessage = "One Review record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						}
						//If Multiple records found, the array of search reults will be populated
						else {
							//Multiiple records returned
							$form->sMessage = "Select Review record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
						}
					} else {
						//Attempt to get records failed
						$form->sMessage = $thisReview->sErrorMessage;
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_NEW;
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
					$_POST['hdnReviewID'] = NULL;

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

			?>
			<!-- Hidden Fields -->
			<input type="hidden" name="hdnReviewID" value="<?php echo $_POST['hdnReviewID'] ?>" />

			<div class="row">
			<?php
				include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Review Maintenance
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
						echo "	<div class='hidden-xs col-sm-3 result-header'>Review Date</div>";
						echo "	<div class='hidden-xs col-sm-9 result-header'>Reviewer Name</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Awards</div>";
						echo "</div>";


						foreach ($thisReview->aReviewRecords as $oReviewRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";
							echo "	<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ReviewMaintenance.php?Review_ID={$oReviewRecord->nReviewID}'>{$oReviewRecord->dtReviewDate}</A></div>";
							echo "	<div class='col-xs-12 col-sm-9 {$sResultStyleClass}'><A HREF='./ReviewMaintenance.php?Review_ID={$oReviewRecord->nReviewID}'>{$oReviewRecord->sReviewAuthor}</A></div>";
							echo "</div>";
						}
						?>

						<div class="row" style="height: 20px;"></div>
					</div>
				<?php
			} else {
				?>

					<div class="row">
						<div class="col-xs-12">

							<?php
							if ($_POST['hdnReviewID'] > 0) {
								echo "<i><small><a href='" . ADMIN_DIR . "/ReviewReport.php?REVIEW_ID={$thisREview->aReviewRecords[0]->nReviewID}";
								echo "'>Review Report</a></small></i>";
							}
							?>
						</div>
						<div class="col-xs-12 FieldGroup">
							<div class="row">
								<div class="col-xs-12">
									AUTHOR: <input type="text" name="txtReviewAuthor" value="<?php echo $_POST['txtReviewAuthor']; ?>" size="40" />
								</div>
								<div class="col-xs-12">
									SOURCE: <input type="text" name="txtReviewSource" value="<?php echo $_POST['txtReviewSource']; ?>" size="40" />
								</div>
								<div class="col-xs-12">
									REVIEW DATE:<BR />
									<?php
									renderDatePicker("ReviewDate", $thisReview->aReviewRecords[0]->dtReviewDate);
									?>

								</div>
								<div class="col-xs-12">
									REVIEW URL: <input type="text" name="txtReviewURL" value="<?php echo $_POST['txtReviewURL']; ?>" size="40" />
								</div>
								<div class="col-xs-2 col-sm-1">
									<input type="checkbox" class="result-checkbox" name="chkInternalReviewUrl" value="InternalReviewUrl" <?php if ($_POST['chkInternalReviewUrl']) {
																																				echo " checked ";
																																			}; ?> />
								</div>
								<div class="col-xs-10 col-sm-3 result-checkbox-text">INTERNAL REVIEW URL</div>
								<div class="col-xs-12">
									<?php
									renderArtistDropDown($_POST['selArtist']);
									?>
								</div>
								<div class="col-xs-12">
									<?php
									renderCDDropDown($_POST['selCD']);
									?>
								</div>
								<div class="col-xs-12">
									<?php
									renderSongDropDown($_POST['selSong']);
									?>
								</div>
								<div class="col-xs-12 col-sm-4">
									RATING:
									<select name="selRating" id="selRating">
										<option value="">ALL</option>
										<?php
										for ($i = 1; $i <= 5; $i++) {
											echo "<option value=\"{$i}\"";
											if ($_POST['selRating'] == $i) {
												echo " selected ";
											}
											echo ">{$i}</option>";
										}
										?>

									</select>
								</div>
								<div class="col-xs-2 col-sm-1">
									<input type="checkbox" class="result-checkbox" name="chkPerformanceRelated" value="PerformanceRelated" <?php if ($_POST['chkPerformanceRelated']) {
																																				echo " checked ";
																																			}; ?> />
								</div>
								<div class="col-xs-10 col-sm-3 result-checkbox-text">PERFORMANCE RELATED</div>
								<div class="col-xs-2 col-sm-1">
									<input type="checkbox" class="result-checkbox" name="chkGeneral" value="General" <?php if ($_POST['chkGeneral']) {
																															echo " checked ";
																														}; ?> />
								</div>
								<div class="col-xs-10 col-sm-3 result-checkbox-text">GENERAL REVIEW</div>
								<div class="col-xs-12">
									TEXT:<BR />
									<textarea name="txtReviewText" rows="5" cols="100"><?php echo $_POST['txtReviewText']; ?></textarea>
								</div>
								<div class="col-xs-12">
									Excerpt:<BR />
									<textarea name="txtReviewExcerpt" rows="5" cols="100"><?php echo $_POST['txtReviewExcerpt']; ?></textarea>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="row FormFieldNoEdit">
								<div class="col-xs-3">
									ID: <?php echo $_POST['hdnReviewID']; ?>
								</div>
								<div class="col-xs-9 FormFieldNoEdit">
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
 * buildReviewObject
 * 
 * This function loads builds a Review object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
	function buildReviewObject($Review)
	{

		//Load the Array used to populate the form fields based on the newly loaded object
		$Review->nReviewID = $_POST['hdnReviewID'];

		$Review->sReviewAuthor = html_entity_decode($_POST['txtReviewAuthor'], ENT_QUOTES);
		$Review->sReviewSource = html_entity_decode($_POST['txtReviewSource'], ENT_QUOTES);
		$Review->sReviewText = html_entity_decode($_POST['txtReviewText'], ENT_QUOTES);
		$Review->sReviewExcerpt = html_entity_decode($_POST['txtReviewExcerpt'], ENT_QUOTES);
		$Review->sReviewURL = html_entity_decode($_POST['txtReviewURL'], ENT_QUOTES);
		$Review->bFuzzyNameSearch = TRUE;
		$Review->nArtistID = $_POST['selArtist'];
		$Review->nCDID = $_POST['selCD'];
		$Review->nSongID = $_POST['selSong'];
		$Review->nRating = $_POST['selRating'];

		//Review Date	
		$dtReviewDate = isset($_REQUEST["ReviewDate"]) ? $_REQUEST["ReviewDate"] : "";
		if ($dtReviewDate > "0000-00-00") {
			//If no datepicker is displayed, use the hidden field
			$dtReviewDate = isset($_POST["ReviewDate"]) ? $_POST["ReviewDate"] : "";
		}
		if ($dtReviewDate > "0000-00-00") {
			$Review->dtReviewDate  	= $dtReviewDate;
		}

		if ($_POST['chkInternalReviewUrl'] == "InternalReviewUrl") {
			$Review->bInternalReviewUrl = TRUE;
		}

		if ($_POST['chkPerformanceRelated'] == "PerformanceRelated") {
			$Review->bPerformanceRelated = TRUE;
		}

		if ($_POST['chkGeneral'] == "General") {
			$Review->bGeneral = TRUE;
		}
	}


	/*
 ********************************************************************************
 * loadReview
 * 
 * This function loads the form field array from a populated Review object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
	function loadReview(&$Review, $form)
	{

		if (!is_null($Review->nReviewID)) {
			//Load Hidden Fields
			$_POST['hdnReviewID'] = $Review->nReviewID;

			$_POST['txtReviewAuthor'] = htmlentities($Review->sReviewAuthor, ENT_QUOTES);
			$_POST['txtReviewSource'] = htmlentities($Review->sReviewSource, ENT_QUOTES);
			$_POST['txtReviewText'] = $Review->sReviewText;
			$_POST['txtReviewExcerpt'] = $Review->sReviewExcerpt;
			$_POST['txtReviewURL'] = htmlentities($Review->sReviewURL, ENT_QUOTES);
			$_POST['ReviewDate'] = htmlentities($Review->dtReviewDate, ENT_QUOTES);
			$_POST['selArtist'] = $Review->nArtistID;
			$_POST['selCD'] = $Review->nCDID;
			$_POST['selSong'] = $Review->nSongID;
			$_POST['selRating'] = $Review->nRating;
			$_POST['chkInternalReviewUrl'] = $Review->bInternalReviewUrl;
			$_POST['chkPerformanceRelated'] = $Review->bPerformanceRelated;
			$_POST['chkGeneral'] = $Review->bGeneral;
			$_POST['txtLastUpdate'] = htmlentities($Review->dtLastUpdate, ENT_QUOTES);
		} else {
			//Load Form field values into array 
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

		//Load Form field values into array 
		foreach ($_POST as $fieldName => $fieldValue) {
			$_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
		}
	}

	?>

</body>
<script type="text/javascript">
		<!--
		function validate_form() {
			bValid = true;
			sErrorMessage = "";

			//Review Text is a required Field
			if (document.ReviewMaint.txtReviewText.value == "") {
				sErrorMessage += "Review Text Required\n";
				bValid = false;
			}

			//Author is a required Field
			if (document.ReviewMaint.txtReviewAuthor.value == "") {
				sErrorMessage += "Author Required\n";
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
</html>