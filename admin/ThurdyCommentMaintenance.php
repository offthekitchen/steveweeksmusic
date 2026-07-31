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
2026-07-30	Migrated to new Datalayer ThurdyComment repository
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
include_once(DATALAYER_DIR . "/ThurdyComment.php");
include_once(DATALAYER_DIR . "/ThurdyCommentRepository.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//Array of Comment records from the DB
$aCommentRecords = [];

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
			$commentRepo = new \Datalayer\ThurdyCommentRepository();
			$thisComment = new \Datalayer\ThurdyComment();
			$form = new Form();

			//Get the ID query string parameter
			$nThisCommentID = $_REQUEST['ID'] ?? null;

			try {
			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisCommentID) && $nThisCommentID !== '') {

				$entity = $commentRepo->findById((int) $nThisCommentID);

				if ($entity) {
					$thisComment = $entity;
					$aCommentRecords = [$entity];
					loadComment($thisComment, $form);

					$form->sMessage = "Update record.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;
				} else {
					$form->sMessage = "Comment record not found.";
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
					buildCommentObject($thisComment);

					if ($commentRepo->insert($thisComment)) {
						$thisComment = $commentRepo->findById((int) $thisComment->id) ?? $thisComment;
						$aCommentRecords = [$thisComment];
						loadComment($thisComment, $form);

						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Comment Added";
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

					buildCommentObject($thisComment);

					if ($commentRepo->update($thisComment)) {
						$thisComment = $commentRepo->findById((int) $thisComment->id) ?? $thisComment;
						$aCommentRecords = [$thisComment];
						loadComment($thisComment, $form);

						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Comment Updated";
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

					buildCommentObject($thisComment);

					if (!empty($thisComment->id) && $commentRepo->delete((int) $thisComment->id)) {
						clearFormFields($form);

						$form->sMessage = "Comment Deleted";
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
					$searchApproved = null;
					buildCommentObject($thisComment, $searchApproved);

					$criteria = [
						'id' => $thisComment->id,
						'comment' => $thisComment->comment,
						'answer' => $thisComment->answer,
						'fuzzyName' => true,
						'commentDate' => $thisComment->commentDate,
					];
					if ($searchApproved !== null) {
						$criteria['approved'] = $searchApproved;
					}

					$aCommentRecords = $commentRepo->find($criteria);

					if (sizeof($aCommentRecords) < 1) {
						$form->sMessage = "No Comment records found matching search criteria";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					} else if (sizeof($aCommentRecords) == 1) {
						$thisComment = $aCommentRecords[0];
						loadComment($thisComment, $form);

						$form->sMessage = "One Comment record found.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$form->sMessage = "Select Comment record to edit from results list below.";
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
			} catch (\Throwable $e) {
				$form->sMessage = $e->getMessage();
				$form->nMessageType = MESSAGE_TYPE_ERROR;
				$form->nFormMode = FORM_MODE_NEW;
			}

			?>
			<!-- Hidden Fields -->
			<input type="hidden" name="hdnCommentID" value="<?php echo $_POST['hdnCommentID'] ?? '' ?>" />
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


						foreach ($aCommentRecords as $oCommentRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							echo "<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ThurdyCommentMaintenance.php?ID=" . $oCommentRecord->id . "'>" . htmlentities($oCommentRecord->commentDate ?? '', ENT_QUOTES) . "</a></div>";
							echo "<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ThurdyCommentMaintenance.php?ID=" . $oCommentRecord->id . "'>" . htmlentities($oCommentRecord->comment ?? '', ENT_QUOTES) . "</a></div>";
							echo "<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ThurdyCommentMaintenance.php?ID=" . $oCommentRecord->id . "'>" . htmlentities($oCommentRecord->answer ?? '', ENT_QUOTES) . "</a></div>";
							echo "<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ThurdyCommentMaintenance.php?ID=" . $oCommentRecord->id . "'>";
							if ($oCommentRecord->approved) {
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
								COMMENT: <input type="text" name="txtComment" value="<?php echo $_POST['txtComment'] ?? ''; ?>" size="60" />&nbsp;&nbsp;
							</div>
							<div class="col-xs-12 col-md-2">
								DATE:<BR />
								<?php
								renderDatePicker("CommentDate", $_POST['CommentDate'] ?? ($thisComment->commentDate ?? ''));
								?>
							</div>
							<div class="col-xs-12 col-md-1">
								<label for="chkApproved">Approved
									<input type="checkbox" id="chkApproved" name="chkApproved" value="approved" <?php if (!empty($_POST['chkApproved'])) {
																													echo " checked ";
																												} ?> />
								</label>
							</div>
							<div class="col-xs-12 col-md-4">
								ANSWER: <input type="text" name="txtAnswer" value="<?php echo $_POST['txtAnswer'] ?? ''; ?>" size="60" />&nbsp;&nbsp;
							</div>
							<div class="col-xs-12">
								<div class="row">
									<div class=" col-xs-3 FormFieldNoEdit">
										ID: <?php echo $_POST['hdnCommentID'] ?? ''; ?>
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
 * buildCommentObject
 * 
 * This function loads builds a comment object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
	function buildCommentObject(\Datalayer\ThurdyComment $Comment, ?bool &$searchApproved = null)
	{
		$id = $_POST['hdnCommentID'] ?? null;
		$Comment->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

		$Comment->comment = html_entity_decode($_POST['txtComment'] ?? '', ENT_QUOTES);
		$Comment->answer = html_entity_decode($_POST['txtAnswer'] ?? '', ENT_QUOTES);

		//Comment Date	
		$dtCommentDate = isset($_REQUEST["CommentDate"]) ? $_REQUEST["CommentDate"] : "";
		if ($dtCommentDate > "0000-00-00") {
			$dtCommentDate = isset($_POST["CommentDate"]) ? $_POST["CommentDate"] : "";
		}
		if ($dtCommentDate > "0000-00-00") {
			$Comment->commentDate = $dtCommentDate;
		}

		//Load Approved Boolean only if checked (don't search for this field if not checked)
		if (isset($_POST['chkApproved']) && $_POST['chkApproved'] == 'approved') {
			$Comment->approved = true;
			$searchApproved = true;
		} else if (!isset($_POST["btnSearch"])) {
			$Comment->approved = false;
		} else {
			$searchApproved = null;
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
	function loadComment(\Datalayer\ThurdyComment $Comment, $form)
	{

		if (!is_null($Comment->id)) {
			$_POST['hdnCommentID'] = $Comment->id;

			$_POST['CommentDate'] = htmlentities($Comment->commentDate ?? '', ENT_QUOTES);
			$_POST['txtComment'] = htmlentities($Comment->comment ?? '', ENT_QUOTES);
			$_POST['txtAnswer'] = htmlentities($Comment->answer ?? '', ENT_QUOTES);
			$_POST['txtLastUpdate'] = htmlentities($Comment->lastUpdate ?? '', ENT_QUOTES);

			if ($Comment->approved) {
				$_POST['chkApproved'] = TRUE;
			} else {
				$_POST['chkApproved'] = FALSE;
			}
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
