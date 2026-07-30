<?php
/*
*******************************************************************
ThurdyCommentMaintenance.php
This PHP file defines the Maintenance page for 
managing Thurdy Comments.
NOTES
Date        Change
-------------------------------------------------------------
2017-09-07	Small responsive improvements
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

//include Comment Class		
include_once(CLASS_DIR . "/class_ThurdyComment.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//Array of Comment records from the DB
global $aCommentRecords;

$sActiveMenuItem = MISC_ACTIVE;	
$sPageName = "Thurdy Comment Maintenance";
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
		<form name="CommentMaint" action="ThurdyCommentMaintenance.php" method="post">

			<?php

			//Instantiate needed objects
			$thisComment = new ThurdyComment();
			$thisComment->sOrderBy = DATE_ORDER;
			$form = new Form();

			//Get the ID query string parameter
			$nThisCommentID = $_REQUEST['ID'];

			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisCommentID)) {

				$thisComment->nCommentID = $nThisCommentID;

				//Search the Database for records matching the search criteria			
				if ($thisComment->getThurdyComment()) {

					//Records found
					if (sizeof($thisComment->aCommentRecords) > 0) {

						//Only One Record should be returned.  Add this to the form field array
						//so that it displays in the form fields and to the values in the
						//current Object.
						loadComment($thisComment->aCommentRecords[0], $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//The record was not found
						$form->sMessage = "Comment record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					}
				} else {
					//Error
					$form->sMessage = $thisComment->sErrorMessage;
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
					buildCommentObject($thisComment);

					//Insert record
					if ($thisComment->insertComment()) {

						//reload Comment
						$nNewCommentID = $thisComment->nCommentID;
						$thisComment = new ThurdyComment();
						$thisComment->nCommentID = $nNewCommentID;

						if ($thisComment->getThurdyComment()) {
							//Load the form fields with the newly populated object
							loadComment($thisComment->aCommentRecords[0], $form);
							//Store the ID of the performance
							$nThisCommentID = $thisComment->aCommentRecords[0]->nCommentID;

							//Success
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Comment Added";
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							//Problem reloading screen
							clearFormFields($form);
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "Comment Added, but error occured while reloading the performance data";
							$form->nFormMode = FORM_MODE_NEW;
						}
					} else {

						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED: " . $thisComment->sErrorMessage;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) {

					//Load values from form field array into DB object
					buildCommentObject($thisComment);

					//Update record
					if ($thisComment->updateComment()) {

						//reload Comment
						$thisComment->getThurdyComment();

						//Load the form fields with the newly populated DB object						
						loadComment($thisComment->aCommentRecords[0], $form);
						//Store the ID of the performance
						$nThisCommentID = $thisComment->aCommentRecords[0]->nCommentID;

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Comment Updated";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed - " . $thisComment->sErrorMessage;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   DELETE   *
				// **************
				else if (isset($_POST["btnDelete"])) {

					//Load DB record
					buildCommentObject($thisComment);

					//Delete record
					if ($thisComment->deleteComment()) {
						//Clear the form fields
						clearFormFields($form);

						//Success
						$form->sMessage = "Comment Deleted";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
						$form->sMessage = "DELETE FAILED: " . $thisComment->sErrorMessage;
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   SEARCH   *
				// **************
				else if (isset($_POST["btnSearch"])) {
					//Load Array of Search Values
					buildCommentObject($thisComment);

					//Search the Database for records matching the search criteria			
					if ($thisComment->getThurdyComment()) {
						//No records found
						if (sizeof($thisComment->aCommentRecords) < 1) {
							$form->sMessage = "No Comment records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($thisComment->aCommentRecords) == 1) {
							//Only One Record returned.  Add this to the form field array
							//so that it displays in the form fields
							loadComment($thisComment->aCommentRecords[0], $form);
							//Store the ID of the performance
							$nThisCommentID = $thisComment->aCommentRecords[0]->nCommentID;

							$form->sMessage = "One Comment record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						}
						//If Multiple records found, the array of search reults will be populated
						else {
							//Multiiple records returned
							$form->sMessage = "Select Comment record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
						}
					} else {
						//Attempt to get records failed
						$form->sMessage = $thisComment->sErrorMessage;
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
					$_POST['hdnCommentID'] = NULL;

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
			<input type="hidden" name="hdnCommentID" value="<?php echo $_POST['hdnCommentID'] ?>" />
			<div class="row">
				<?php
				include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Thurdy Comment Maintenance
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
						echo "<div class='hidden-xs col-sm-3 result-header'>Date</div>";
						echo "<div class='hidden-xs col-sm-3 result-header'>Comment</div>";
						echo "<div class='hidden-xs col-sm-3 result-header'>Answer</div>";
						echo "<div class='hidden-xs col-sm-3 result-header'>Approved</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Comments</div>";
						echo "</div>";


						foreach ($thisComment->aCommentRecords as $oCommentRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							echo "<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ThurdyCommentMaintenance.php?ID=" . $oCommentRecord->nCommentID . "'>" . $oCommentRecord->dtCommentDate . "</a></div>";
							echo "<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ThurdyCommentMaintenance.php?ID=" . $oCommentRecord->nCommentID . "'>" . $oCommentRecord->sComment . "</a></div>";
							echo "<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ThurdyCommentMaintenance.php?ID=" . $oCommentRecord->nCommentID . "'>" . $oCommentRecord->sAnswer . "</a></div>";
							echo "<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ThurdyCommentMaintenance.php?ID=" . $oCommentRecord->nCommentID . "'>";
							if ($oCommentRecord->bApproved) {
								echo "YES";
							} else {
								echo "NO";
							}
							echo "</div>";
							echo "</div>";
						}
						?>
						<div class="row" style="height: 20px;"></div>
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
								COMMENT: <input type="text" name="txtComment" value="<?php echo $_POST['txtComment']; ?>" size="60" />&nbsp;&nbsp;
							</div>
							<div class="col-xs-12 col-md-2">
								DATE:<BR />
								<?php
								renderDatePicker("CommentDate", $_POST['CommentDate']);
								?>
							</div>
							<div class="col-xs-12 col-md-1">
								<label for="chkApproved">Approved
									<input type="checkbox" id="chkApproved" name="chkApproved" value="approved" <?php if ($_POST['chkApproved']) {
																													echo " checked ";
																												} ?> />
								</label>
							</div>
							<div class="col-xs-12 col-md-4">
								ANSWER: <input type="text" name="txtAnswer" value="<?php echo $_POST['txtAnswer']; ?>" size="60" />&nbsp;&nbsp;
							</div>
							<div class="col-xs-12">
								<div class="row">
									<div class=" col-xs-3 FormFieldNoEdit">
										ID: <?php echo $_POST['hdnPerformanceID']; ?>
									</div>
									<div class=" col-xs-9 FormFieldNoEdit">
										LAST UPDATED: <?php echo $_POST['txtLastUpdate']; ?>
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
 * buildCommentObject
 * 
 * This function loads builds a comment object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
	function buildCommentObject($oComment)
	{

		//Load the Array used to populate the form fields based on the newly loaded object
		$oComment->nCommentID = $_POST['hdnCommentID'];

		$oComment->sComment = html_entity_decode($_POST['txtComment'], ENT_QUOTES);
		$oComment->sAnswer = html_entity_decode($_POST['txtAnswer'], ENT_QUOTES);

		//Comment Date	
		$dtCommentDate = isset($_REQUEST["CommentDate"]) ? $_REQUEST["CommentDate"] : "";
		if ($dtCommentDate > "0000-00-00") {
			//If no datepicker is displayed, use the hidden field
			$dtCommentDate = isset($_POST["CommentDate"]) ? $_POST["CommentDate"] : "";
		}
		if ($dtCommentDate > "0000-00-00") {
			$oComment->dtCommentDate  = $dtCommentDate;
		} else {
			/* if (!isset($_POST["btnSearch"])) {

				$oComment->dtCommentDate  = date("Y-m-d");
			} */
		}

		//Load Approved Boolean only if checked (don't search for this field if not checked)
		if (isset($_POST['chkApproved']) && $_POST['chkApproved'] == 'approved') {
			$oComment->bApproved = TRUE;
		}
		//If we are searching and the Approved checkbox isn't checked, don't use it in the search
		else if (!isset($_POST["btnSearch"])) {
			$oComment->bApproved = FALSE;
		}
	}


	/*
 ********************************************************************************
 * loadComment
 * 
 * This function loads the form field array from a populated Comment object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
	function loadComment(&$oComment, $form)
	{

		if (!is_null($oComment->nCommentID)) {
			//Load Hidden Fields
			$_POST['hdnCommentID'] = htmlentities($oComment->nCommentID, ENT_QUOTES);

			$_POST['CommentDate'] = htmlentities($oComment->dtCommentDate, ENT_QUOTES);
			$_POST['txtComment'] = htmlentities($oComment->sComment, ENT_QUOTES);
			$_POST['txtAnswer'] = htmlentities($oComment->sAnswer, ENT_QUOTES);

			if ($oComment->bApproved) {
				$_POST['chkApproved'] = TRUE;
			} else {
				$_POST['chkApproved'] = FALSE;
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