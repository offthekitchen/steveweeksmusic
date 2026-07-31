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
2026-07-30	Migrated to new Datalayer Review repository
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
include_once(DATALAYER_DIR . "/Review.php");
include_once(DATALAYER_DIR . "/ReviewRepository.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//include Form Class
include(CLASS_DIR . "/class_Form.php");

//Array of Review records from the DB
$aReviewRecords = [];

$sActiveMenuItem = DISCOGRAPHY_ACTIVE;
$sPageName = "Review Maintenance";

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
		<form name="ReviewMaint" action="ReviewMaintenance.php" method="post">

			<?php
			$reviewRepo = new \Datalayer\ReviewRepository();
			$thisReview = new \Datalayer\Review();
			$form = new Form();

			$nThisReviewID = $_REQUEST['Review_ID'] ?? null;

			if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "") {
				$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
				$_POST['btnSearch'] = "Search";
			}

			try {
				if (!is_null($nThisReviewID) && $nThisReviewID !== '') {
					$entity = $reviewRepo->findById((int) $nThisReviewID);

					if ($entity) {
						$thisReview = $entity;
						$aReviewRecords = [$entity];
						loadReview($thisReview, $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$form->sMessage = "Review record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					}
				} else {
					if (isset($_POST["btnAdd"])) {
						buildReviewObject($thisReview);

						if ($reviewRepo->insert($thisReview)) {
							$thisReview = $reviewRepo->findById((int) $thisReview->id) ?? $thisReview;
							$aReviewRecords = [$thisReview];
							loadReview($thisReview, $form);

							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Review Added";
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "ADD RECORD FAILED";
							$form->nFormMode = FORM_MODE_EDIT;
						}
					} else if (isset($_POST["btnUpdate"])) {
						buildReviewObject($thisReview);

						if ($reviewRepo->update($thisReview)) {
							$thisReview = $reviewRepo->findById((int) $thisReview->id) ?? $thisReview;
							$aReviewRecords = [$thisReview];
							loadReview($thisReview, $form);

							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Review Updated";
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "ERROR: Update Failed";
							$form->nFormMode = FORM_MODE_EDIT;
						}
					} else if (isset($_POST["btnDelete"])) {
						buildReviewObject($thisReview);

						if (!empty($thisReview->id) && $reviewRepo->delete((int) $thisReview->id)) {
							clearFormFields($form);

							$form->sMessage = "Review Deleted";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_NEW;
						} else {
							$form->sMessage = "DELETE FAILED";
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->nFormMode = FORM_MODE_EDIT;
						}
					} else if (isset($_POST["btnSearch"])) {
						buildReviewObject($thisReview);

						$aReviewRecords = $reviewRepo->find(buildReviewSearchCriteria($thisReview));

						if (sizeof($aReviewRecords) < 1) {
							$form->sMessage = "No Review records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($aReviewRecords) == 1) {
							$thisReview = $aReviewRecords[0];
							loadReview($thisReview, $form);

							$form->sMessage = "One Review record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							$form->sMessage = "Select Review record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
						}
					} else if (isset($_POST["btnClear"])) {
						clearFormFields($form);

						$form->sMessage = "Search for records or Add new record";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else if (isset($_POST["btnCancel"])) {
						clearFormFields($form);
						$form->sMessage = "Search for records or Add new record";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else if (isset($_POST["btnCopy"])) {
						copyFormFields($form);
						$_POST['hdnReviewID'] = NULL;

						$form->sMessage = "Search for records or Add new record";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
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
			<input type="hidden" name="hdnReviewID" value="<?php echo $_POST['hdnReviewID'] ?? '' ?>" />

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

						echo "<div class='row result-header'>";
						echo "	<div class='hidden-xs col-sm-3 result-header'>Review Date</div>";
						echo "	<div class='hidden-xs col-sm-9 result-header'>Reviewer Name</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Awards</div>";
						echo "</div>";

						foreach ($aReviewRecords as $oReviewRecord) {
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";
							echo "	<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./ReviewMaintenance.php?Review_ID={$oReviewRecord->id}'>{$oReviewRecord->reviewDate}</A></div>";
							echo "	<div class='col-xs-12 col-sm-9 {$sResultStyleClass}'><A HREF='./ReviewMaintenance.php?Review_ID={$oReviewRecord->id}'>{$oReviewRecord->author}</A></div>";
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
							if (($_POST['hdnReviewID'] ?? 0) > 0) {
								echo "<i><small><a href='" . ADMIN_DIR . "/ReviewReport.php?REVIEW_ID={$_POST['hdnReviewID']}";
								echo "'>Review Report</a></small></i>";
							}
							?>
						</div>
						<div class="col-xs-12 FieldGroup">
							<div class="row">
								<div class="col-xs-12">
									AUTHOR: <input type="text" name="txtReviewAuthor" value="<?php echo $_POST['txtReviewAuthor'] ?? ''; ?>" size="40" />
								</div>
								<div class="col-xs-12">
									SOURCE: <input type="text" name="txtReviewSource" value="<?php echo $_POST['txtReviewSource'] ?? ''; ?>" size="40" />
								</div>
								<div class="col-xs-12">
									REVIEW DATE:<BR />
									<?php
									renderDatePicker("ReviewDate", $_POST['ReviewDate'] ?? ($thisReview->reviewDate ?? ''));
									?>

								</div>
								<div class="col-xs-12">
									REVIEW URL: <input type="text" name="txtReviewURL" value="<?php echo $_POST['txtReviewURL'] ?? ''; ?>" size="40" />
								</div>
								<div class="col-xs-2 col-sm-1">
									<input type="checkbox" class="result-checkbox" name="chkInternalReviewUrl" value="InternalReviewUrl" <?php if (!empty($_POST['chkInternalReviewUrl'])) {
																																				echo " checked ";
																																			}; ?> />
								</div>
								<div class="col-xs-10 col-sm-3 result-checkbox-text">INTERNAL REVIEW URL</div>
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
								<div class="col-xs-12 col-sm-4">
									RATING:
									<select name="selRating" id="selRating">
										<option value="">ALL</option>
										<?php
										for ($i = 1; $i <= 5; $i++) {
											echo "<option value=\"{$i}\"";
											if (($_POST['selRating'] ?? '') == $i) {
												echo " selected ";
											}
											echo ">{$i}</option>";
										}
										?>

									</select>
								</div>
								<div class="col-xs-2 col-sm-1">
									<input type="checkbox" class="result-checkbox" name="chkPerformanceRelated" value="PerformanceRelated" <?php if (!empty($_POST['chkPerformanceRelated'])) {
																																				echo " checked ";
																																			}; ?> />
								</div>
								<div class="col-xs-10 col-sm-3 result-checkbox-text">PERFORMANCE RELATED</div>
								<div class="col-xs-2 col-sm-1">
									<input type="checkbox" class="result-checkbox" name="chkGeneral" value="General" <?php if (!empty($_POST['chkGeneral'])) {
																															echo " checked ";
																														}; ?> />
								</div>
								<div class="col-xs-10 col-sm-3 result-checkbox-text">GENERAL REVIEW</div>
								<div class="col-xs-12">
									TEXT:<BR />
									<textarea name="txtReviewText" rows="5" cols="100"><?php echo $_POST['txtReviewText'] ?? ''; ?></textarea>
								</div>
								<div class="col-xs-12">
									Excerpt:<BR />
									<textarea name="txtReviewExcerpt" rows="5" cols="100"><?php echo $_POST['txtReviewExcerpt'] ?? ''; ?></textarea>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="row FormFieldNoEdit">
								<div class="col-xs-3">
									ID: <?php echo $_POST['hdnReviewID'] ?? ''; ?>
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
 * buildReviewObject
 ********************************************************************************
*/
	function buildReviewObject(\Datalayer\Review $review): void
	{
		$id = $_POST['hdnReviewID'] ?? null;
		$review->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

		$review->author = html_entity_decode($_POST['txtReviewAuthor'] ?? '', ENT_QUOTES);
		$review->source = html_entity_decode($_POST['txtReviewSource'] ?? '', ENT_QUOTES);
		$review->text = html_entity_decode($_POST['txtReviewText'] ?? '', ENT_QUOTES);
		$review->excerpt = html_entity_decode($_POST['txtReviewExcerpt'] ?? '', ENT_QUOTES);
		$review->url = html_entity_decode($_POST['txtReviewURL'] ?? '', ENT_QUOTES);

		$review->artistId = (int) ($_POST['selArtist'] ?? 0);

		$cdId = $_POST['selCD'] ?? null;
		$review->cdId = ($cdId === '' || $cdId === null) ? null : (int) $cdId;

		$songId = $_POST['selSong'] ?? null;
		$review->songId = ($songId === '' || $songId === null) ? null : (int) $songId;

		$rating = $_POST['selRating'] ?? null;
		$review->rating = ($rating !== '' && $rating !== null && is_numeric($rating)) ? (int) $rating : null;

		$dtReviewDate = $_REQUEST["ReviewDate"] ?? "";
		if ($dtReviewDate > "0000-00-00") {
			$dtReviewDate = $_POST["ReviewDate"] ?? "";
		}
		if ($dtReviewDate > "0000-00-00") {
			$review->reviewDate = $dtReviewDate;
		}

		$review->internalReviewUrl = (($_POST['chkInternalReviewUrl'] ?? '') == "InternalReviewUrl");
		$review->performanceRelated = (($_POST['chkPerformanceRelated'] ?? '') == "PerformanceRelated");
		$review->general = (($_POST['chkGeneral'] ?? '') == "General");
	}


	/*
 ********************************************************************************
 * buildReviewSearchCriteria
 ********************************************************************************
*/
	function buildReviewSearchCriteria(\Datalayer\Review $review): array
	{
		$criteria = [
			'id' => $review->id,
			'orderBy' => 'date',
		];

		if (!empty($review->text)) {
			$criteria['text'] = $review->text;
		}

		if (!empty($review->excerpt)) {
			$criteria['excerpt'] = $review->excerpt;
		}

		if (!empty($review->author)) {
			$criteria['author'] = $review->author;
		}

		if (!empty($review->source)) {
			$criteria['source'] = $review->source;
		}

		if (!empty($review->reviewDate)) {
			$criteria['reviewDate'] = $review->reviewDate;
		}

		if (!empty($review->url)) {
			$criteria['url'] = $review->url;
		}

		if (!empty($review->internalReviewUrl)) {
			$criteria['internalReviewUrl'] = true;
		}

		if (!empty($review->artistId)) {
			$criteria['artistId'] = $review->artistId;
		}

		if (!empty($review->cdId)) {
			$criteria['cdId'] = $review->cdId;
		}

		if (!empty($review->songId)) {
			$criteria['songId'] = $review->songId;
		}

		if (!empty($review->rating)) {
			$criteria['rating'] = $review->rating;
		}

		if (!empty($review->performanceRelated)) {
			$criteria['performanceRelated'] = true;
		}

		if (!empty($review->general)) {
			$criteria['general'] = true;
		}

		return $criteria;
	}


	/*
 ********************************************************************************
 * loadReview
 ********************************************************************************
*/
	function loadReview(\Datalayer\Review $review, $form): void
	{
		if (!is_null($review->id)) {
			$_POST['hdnReviewID'] = $review->id;

			$_POST['txtReviewAuthor'] = htmlentities($review->author ?? '', ENT_QUOTES);
			$_POST['txtReviewSource'] = htmlentities($review->source ?? '', ENT_QUOTES);
			$_POST['txtReviewText'] = $review->text;
			$_POST['txtReviewExcerpt'] = $review->excerpt;
			$_POST['txtReviewURL'] = htmlentities($review->url ?? '', ENT_QUOTES);
			$_POST['ReviewDate'] = htmlentities($review->reviewDate ?? '', ENT_QUOTES);
			$_POST['selArtist'] = $review->artistId;
			$_POST['selCD'] = $review->cdId;
			$_POST['selSong'] = $review->songId;
			$_POST['selRating'] = $review->rating;
			$_POST['chkInternalReviewUrl'] = $review->internalReviewUrl;
			$_POST['chkPerformanceRelated'] = $review->performanceRelated;
			$_POST['chkGeneral'] = $review->general;
			$_POST['txtLastUpdate'] = htmlentities($review->lastUpdate ?? '', ENT_QUOTES);
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
	function clearFormFields($form): void
	{
		$_POST = array();
	}

	/*
 ********************************************************************************
 * copyFormFields
 ********************************************************************************
*/
	function copyFormFields($form): void
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

			if (document.ReviewMaint.txtReviewText.value == "") {
				sErrorMessage += "Review Text Required\n";
				bValid = false;
			}

			if (document.ReviewMaint.txtReviewAuthor.value == "") {
				sErrorMessage += "Author Required\n";
				bValid = false;
			}

			if (!bValid) {
				alert(sErrorMessage);
			}

			return bValid;
		}
		//
		-->
	</script>
</html>
