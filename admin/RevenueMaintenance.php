<?php
/*
*******************************************************************
RevenueMaintenance.php
This PHP file defines the Maintenance page for 
managing Revenues.
NOTES
Date        Change
-------------------------------------------------------------
2015-04-29	Refactored and Added product info to search results
2015-05-05  Added Dynamic Building of Drop-down
2015-06-02  Changed formvalidation to allow negative amount
2015-10-19	Added PERF_RELATED parm processing
2015-10-23	Added YEAR parm processing
2016-02-11	Refactored<br />
2017-04-30	Improved Layout
2019-08-14  Added Artist Dropdown
2020-03-14	Added logic to return revenues for no product (ID=0)
2020-09-30	Added Logic to use hidden date field for update if no datepicker value found
2021-08-30	Updated for PHP 8
2022-01-25	Added ability for no product search
2024-05-09	Fixed error with Performance Dropdown
2026-07-30	Migrated to new Datalayer Revenue repository
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
include_once(DATALAYER_DIR . "/Revenue.php");
include_once(DATALAYER_DIR . "/RevenueRepository.php");
include_once(DATALAYER_DIR . "/Category.php");
include_once(DATALAYER_DIR . "/CategoryRepository.php");
include_once(DATALAYER_DIR . "/Payment.php");
include_once(DATALAYER_DIR . "/PaymentRepository.php");
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");
include_once(DATALAYER_DIR . "/RevenueType.php");
include_once(DATALAYER_DIR . "/RevenueTypeRepository.php");

//include Form Class
include(CLASS_DIR . "/class_Form.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//Array of Revenue records from the DB
$aRevenueRecords = [];

$sActiveMenuItem = FINANCES_ACTIVE;
$sPageName = "Revenue Maintenance";

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

		<form name="RevenueMaint" action="RevenueMaintenance.php" method="post">

			<?php

			$revenueRepo = new \Datalayer\RevenueRepository();
			$categoryRepo = new \Datalayer\CategoryRepository();
			$paymentRepo = new \Datalayer\PaymentRepository();
			$performanceRepo = new \Datalayer\PerformanceRepository();
			$revenueTypeRepo = new \Datalayer\RevenueTypeRepository();
			$thisRevenue = new \Datalayer\Revenue();
			$form = new Form();
			$aRevenueCategories = [];
			$aRevenueTypes = [];

			if (isset($_REQUEST['PRODUCT_ID']) && $_REQUEST['PRODUCT_ID'] != "") {
				$_POST['selProduct'] = $_REQUEST['PRODUCT_ID'];
				$_POST['btnSearch'] = "Search";
			} else {
				$_POST['selProduct'] = $_POST['selProduct'] ?? null;
			}

			if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "") {
				$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
				$_POST['btnSearch'] = "Search";
			} else {
				$_POST['selArtist'] = $_POST['selArtist'] ?? 0;
			}

			if (isset($_REQUEST['PAYMENT_ID']) && $_REQUEST['PAYMENT_ID'] != "") {
				$_POST['hdnPaymentID'] = $_REQUEST['PAYMENT_ID'];
				if (!isset($_REQUEST['ACTION']) || $_REQUEST['ACTION'] != "ADD_REVENUE") {
					$_POST['selPayment'] = $_REQUEST['PAYMENT_ID'];
					$_POST['btnSearch'] = "Search";
				} else {
					$payment = $paymentRepo->findById((int) $_REQUEST['PAYMENT_ID']);
					if ($payment) {
						$_POST['txtRevenueAmount'] = $payment->amount;
						$_POST['txtRevenueDescription'] = htmlentities($payment->description ?? '', ENT_QUOTES);
						$_POST['PaidDate'] = htmlentities($payment->paymentDate ?? '', ENT_QUOTES);
						$_POST['RevenueDate'] = htmlentities($payment->paymentDate ?? '', ENT_QUOTES);
					}
				}
			} else {
				$_POST['hdnPaymentID'] = $_POST['hdnPaymentID'] ?? 0;
			}

			if (isset($_REQUEST['PERFORMANCE_ID']) && $_REQUEST['PERFORMANCE_ID'] != "") {
				$_POST['hdnPerformanceID'] = $_REQUEST['PERFORMANCE_ID'];
				if (!isset($_REQUEST['ACTION']) || $_REQUEST['ACTION'] != "ADD_REVENUE") {
					$_POST['selPerformance'] = $_REQUEST['PERFORMANCE_ID'];
					$_POST['btnSearch'] = "Search";
				}
			} else {
				$_POST['hdnPerformanceID'] = $_POST['hdnPerformanceID'] ?? 0;
			}

			if (isset($_REQUEST['REVENUE_TYPE_ID']) && $_REQUEST['REVENUE_TYPE_ID'] != "") {
				$_POST['selRevenueType'] = $_REQUEST['REVENUE_TYPE_ID'];
				$_POST['btnSearch'] = "Search";
			} else {
				$_POST['selRevenueType'] = $_POST['selRevenueType'] ?? 0;
			}

			if (isset($_REQUEST['CATEGORY_ID']) && $_REQUEST['CATEGORY_ID'] != "") {
				$_POST['chkCategory' . $_REQUEST['CATEGORY_ID']] = "CategoryAssociated";
				$_POST['btnSearch'] = "Search";
			}

			if (isset($_REQUEST['START_DATE']) && $_REQUEST['START_DATE'] != "") {
				$_POST['hdnStartDate'] = $_REQUEST['START_DATE'];
				$_POST['btnSearch'] = "Search";
			}

			if (isset($_REQUEST['END_DATE']) && $_REQUEST['END_DATE'] != "") {
				$_POST['hdnEndDate'] = $_REQUEST['END_DATE'];
				$_POST['btnSearch'] = "Search";
			}

			if (isset($_REQUEST['YEAR']) && $_REQUEST['YEAR'] != "") {
				$_POST['nYear'] = $_REQUEST['YEAR'];
				$_POST['btnSearch'] = "Search";
			}

			if (isset($_REQUEST['PERF_RELATED']) && $_REQUEST['PERF_RELATED'] == "TRUE") {
				$_POST['bPerformanceRelated'] = TRUE;
				$_POST['btnSearch'] = "Search";
			}

			if (isset($_REQUEST['COLORADO_SESSIONS']) && $_REQUEST['COLORADO_SESSIONS'] == "TRUE") {
				$sColoradoSessionsFormId = 'chkCategory' . COLORAD_SESSIONS_CAT_ID;
				$_POST[$sColoradoSessionsFormId] = TRUE;
				$_POST['btnSearch'] = "Search";
			}

			$nThisRevenueID = $_REQUEST['ID'] ?? null;

			try {
				$aRevenueCategories = $categoryRepo->find(['revenueRelated' => true]);
				$aRevenueTypes = $revenueTypeRepo->find();

				if (empty($aRevenueCategories)) {
					$form->sMessage = "Error Retrieving Revenue Categories.";
					$form->nMessageType = MESSAGE_TYPE_INFO;
					$form->nFormMode = FORM_MODE_EDIT;
				} else {
					if (!is_null($nThisRevenueID) && $nThisRevenueID !== '') {
						$entity = $revenueRepo->findById((int) $nThisRevenueID);

						if ($entity) {
							$thisRevenue = $entity;
							$aRevenueRecords = [$entity];
							loadRevenue($thisRevenue, $form, $aRevenueCategories);

							$form->sMessage = "Update record.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							$form->sMessage = "Revenue record not found.";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						}
					} else {
						if (isset($_POST["btnAdd"])) {
							buildRevenueObject($thisRevenue);

							if ($revenueRepo->insert($thisRevenue)) {
								if (updateRevenueCategories((int) $thisRevenue->id, $aRevenueCategories)) {
									$thisRevenue = $revenueRepo->findById((int) $thisRevenue->id) ?? $thisRevenue;
									$aRevenueRecords = [$thisRevenue];
									loadRevenue($thisRevenue, $form, $aRevenueCategories);

									$form->nMessageType = MESSAGE_TYPE_INFO;
									$form->sMessage = "Revenue Added";
									$form->nFormMode = FORM_MODE_EDIT;
								} else {
									$form->nMessageType = MESSAGE_TYPE_ERROR;
									$form->sMessage = "ERROR: Failed to update revenue category associations";
									$form->nFormMode = FORM_MODE_EDIT;
								}
							} else {
								$form->nMessageType = MESSAGE_TYPE_ERROR;
								$form->sMessage = "ADD RECORD FAILED";
								$form->nFormMode = FORM_MODE_EDIT;
							}
						} else if (isset($_POST["btnUpdate"])) {
							buildRevenueObject($thisRevenue);

							if ($revenueRepo->update($thisRevenue)) {
								if (updateRevenueCategories((int) $thisRevenue->id, $aRevenueCategories)) {
									$thisRevenue = $revenueRepo->findById((int) $thisRevenue->id) ?? $thisRevenue;
									$aRevenueRecords = [$thisRevenue];
									loadRevenue($thisRevenue, $form, $aRevenueCategories);

									$form->nMessageType = MESSAGE_TYPE_INFO;
									$form->sMessage = "Revenue Updated";
									$form->nFormMode = FORM_MODE_EDIT;
								} else {
									$form->nMessageType = MESSAGE_TYPE_ERROR;
									$form->sMessage = "ERROR: Failed to update revenue category associations";
									$form->nFormMode = FORM_MODE_EDIT;
								}
							} else {
								$form->nMessageType = MESSAGE_TYPE_ERROR;
								$form->sMessage = "ERROR: Update Failed";
								$form->nFormMode = FORM_MODE_EDIT;
							}
						} else if (isset($_POST["btnDelete"])) {
							buildRevenueObject($thisRevenue);

							if (!empty($thisRevenue->id) && $revenueRepo->delete((int) $thisRevenue->id)) {
								if (removeRevenueCategories((int) $thisRevenue->id)) {
									clearFormFields($form);

									$form->sMessage = "Revenue Deleted";
									$form->nMessageType = MESSAGE_TYPE_INFO;
									$form->nFormMode = FORM_MODE_NEW;
								} else {
									$form->sMessage = "DELETED REVENUE BUT FAILED TO DELETE CATEGORY ASSOCIATIONS";
									$form->nMessageType = MESSAGE_TYPE_ERROR;
									$form->nFormMode = FORM_MODE_EDIT;
								}
							} else {
								$form->sMessage = "DELETE FAILED";
								$form->nMessageType = MESSAGE_TYPE_ERROR;
								$form->nFormMode = FORM_MODE_EDIT;
							}
						} else if (isset($_POST["btnSearch"])) {
							buildRevenueObject($thisRevenue);

							$aRevenueRecords = $revenueRepo->find(
								buildRevenueSearchCriteria($thisRevenue, $aRevenueCategories)
							);

							if (sizeof($aRevenueRecords) < 1) {
								$form->sMessage = "No Revenue records found matching search criteria";
								$form->nMessageType = MESSAGE_TYPE_WARNING;
								$form->nFormMode = FORM_MODE_NEW;
							} else if (sizeof($aRevenueRecords) == 1) {
								$thisRevenue = $aRevenueRecords[0];
								loadRevenue($thisRevenue, $form, $aRevenueCategories);

								$form->sMessage = "One Revenue record found.";
								$form->nMessageType = MESSAGE_TYPE_INFO;
								$form->nFormMode = FORM_MODE_EDIT;
							} else {
								$form->sMessage = "Select Revenue record to edit from results list below.";
								$form->nMessageType = MESSAGE_TYPE_INFO;
								$form->nFormMode = FORM_MODE_SELECT;
							}
						} else if (isset($_POST["btnClear"]) || isset($_POST["btnCancel"])) {
							clearFormFields($form);

							$form->sMessage = "Search for records or Add new record";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (isset($_POST["btnCopy"])) {
							$_POST['hdnRevenueID'] = NULL;

							$form->sMessage = "Search for records or Add new record";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_NEW;
						} else {
							$form->sMessage = "Search for records or Add new record";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_NEW;
						}
					}
				}
			} catch (\Throwable $e) {
				$form->sMessage = $e->getMessage();
				$form->nMessageType = MESSAGE_TYPE_ERROR;
				$form->nFormMode = FORM_MODE_NEW;
			}

			?>
			<!-- Hidden Fields -->
			<input type="hidden" name="hdnRevenueID" value="<?php echo $_POST['hdnRevenueID'] ?? '' ?>" />
			<input type="hidden" id="hdnPerformanceID" name="hdnPerformanceID"
				value="<?php echo $_POST['hdnPerformanceID'] ?? '' ?>" />
			<input type="hidden" id="hdnPaymentID" name="hdnPaymentID" value="<?php echo $_POST['hdnPaymentID'] ?? '' ?>" />
			<div class="row">
				<?php
				include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Revenue Maintenance
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
						echo "	<div class='hidden-xs col-sm-3 result-header'>Date</div>";
						echo "	<div class='hidden-xs col-sm-3 result-header'>Amount</div>";
						echo "	<div class='hidden-xs col-sm-3 result-header'>Description</div>";
						echo "	<div class='hidden-xs col-sm-2 result-header'>Product</div>";
						echo "	<div class='hidden-xs col-sm-1 result-header'>Quantity</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Revenues</div>";
						echo "</div>";

						$nRevenueAmountTotal = 0;
						$nQuantityTotal = 0;
						foreach ($aRevenueRecords as $oRevenueRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class=\"row {$sResultStyleClass}\">";

							echo "	<div class='col-xs-12 col-sm-3'><A HREF='./RevenueMaintenance.php?ID={$oRevenueRecord->id}'>{$oRevenueRecord->revenueDate}</div>";
							echo "	<div class='col-xs-12 col-sm-3'><A HREF='./RevenueMaintenance.php?ID={$oRevenueRecord->id}'> $ {$oRevenueRecord->amount}</div>";
							echo "	<div class='col-xs-12 col-sm-3'><A HREF='./RevenueMaintenance.php?ID={$oRevenueRecord->id}'>{$oRevenueRecord->description}</div>";
							$nRevenueAmountTotal += $oRevenueRecord->amount ?? 0;
							echo "	<div class='col-xs-12 col-sm-2'><A HREF='./RevenueMaintenance.php?ID={$oRevenueRecord->id}'>{$oRevenueRecord->productName}</div>";
							echo "	<div class='col-xs-12 col-sm-1'><A HREF='./RevenueMaintenance.php?ID={$oRevenueRecord->id}'>{$oRevenueRecord->productQty}</div>";
							$nQuantityTotal += $oRevenueRecord->productQty ?? 0;
							echo "</A>";
							echo "</div>";
						}

						echo "<div class=\"row\">";
						echo "	<div class='col-xs-12' style='border-top: 1px solid #e3e3e3'></div>";
						echo "</div>";

						echo "<div class=\"row\">";
						echo "	<div class='col-xs-12 col-sm-3'>Total</div>";
						echo "	<div class='col-xs-12 col-sm-3'><span class='visible-xs'>Revenue:</span> \$ {$nRevenueAmountTotal}</div>";
						echo "	<div class='col-xs-12 col-sm-5'></div>";
						echo "	<div class='col-xs-12 col-sm-1'><span class='visible-xs'>Quantity:</span>{$nQuantityTotal}</div>";
						echo "</div>";
						?>

					</div>

					<?php
			} else {
				?>

					<div class="row">
						<div class="col-xs-12 col-md-8 FieldGroupTitle">
							DETAILS
						</div>
						<div class="col-xs-12 col-md-8 FieldGroup">
							<div class="row">
								<div class="col-xs-6">
									<div class="date-selector">
										PAID DATE:<BR />
										<?php
										renderDatePicker("PaidDate", $_POST['PaidDate'] ?? ($thisRevenue->paidDate ?? ''));
										?>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="date-selector">
										REVENUE DATE:<BR />
										<?php
										renderDatePicker("RevenueDate", $_POST['RevenueDate'] ?? ($thisRevenue->revenueDate ?? ''));
										?>
									</div>
								</div>
								<div class="col-xs-12">
									DESCRIPTION:
									<input type="text" name="txtRevenueDescription"
										value="<?php echo $_POST['txtRevenueDescription'] ?? ''; ?>" size="60" />
								</div>
								<div class="col-xs-12 col-sm-6">
									AMOUNT:
									<input type="text" name="txtRevenueAmount"
										value="<?php echo $_POST['txtRevenueAmount'] ?? ''; ?>" size="20" />
								</div>
								<div class="col-xs-12 col-sm-6">
									REVENUE TYPE:
									<SELECT NAME="selRevenueType" ID="selRevenueType">
										<OPTION VALUE="0">NONE</OPTION>
										<?php
										foreach ($aRevenueTypes as $oRevenueType) {
											echo "<OPTION VALUE=\"{$oRevenueType->id}\" ";
											if ($oRevenueType->id == ($_POST['selRevenueType'] ?? 0)) {
												echo " SELECTED ";
											}
											echo ">{$oRevenueType->name}</OPTION>";
										}
										?>
									</SELECT>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-6">
									<?php
									renderProductDropDown($_POST['selProduct'] ?? null);
									?>
									<BR>
									QUANTITY:
									<input type="text" name="txtProductQty" value="<?php echo $_POST['txtProductQty'] ?? ''; ?>"
										size="4" />
								</div>
								<div class="col-xs-12 col-sm-6">
									PAYMENT:
									<SELECT NAME="selPayment" ID="selPayment" onchange="buildPaymentLink()">
										<OPTION VALUE="0">NONE</OPTION>
										<?php
										if (!empty($_POST['hdnPaymentID']) && (int) $_POST['hdnPaymentID'] > 0) {
											$thisPayment = $paymentRepo->findById((int) $_POST['hdnPaymentID']);
											if ($thisPayment) {
												echo "<OPTION VALUE={$thisPayment->id} SELECTED>{$thisPayment->description} - {$thisPayment->amount}</OPTION>";
											}
										}

										?>
									</SELECT>
									<INPUT type="button" ID="btnAssignPayment" value="Assign Payment"
										onclick="getPayments()" />

									<?php
									if (($_POST['hdnPaymentID'] ?? 0) > 0) {
										echo "<SPAN ID=\"lnkPaymentMaint\" name=\"lnkPaymentMaint\">";
										echo "<A HREF='" . ADMIN_DIR . "/PaymentMaintenance.php?ID={$_POST['hdnPaymentID']}' class='secondaryLinkButton'>Edit Payment</A>";
										echo "</SPAN>";
									}
									?>

								</div>
							</div>
							<div class="row">
								<?php if ($form->nFormMode == FORM_MODE_SEARCH || $form->nFormMode == FORM_MODE_NEW) {
									?>
									<div class="col-xs-12">
										<?php
										renderArtistDropDown($_POST['selArtist'] ?? 0);
										?>
									</div>
									<?php
								}
								?>

								<div class="col-xs-12">
									PERFORMANCE:
									<SELECT NAME="selPerformance" ID="selPerformance" onchange="buildPerformanceLink()">
										<OPTION VALUE="0">NONE</OPTION>
										<?php
										if (!empty($_POST['hdnPerformanceID']) && (int) $_POST['hdnPerformanceID'] > 0) {
											$thisPerformance = $performanceRepo->findById((int) $_POST['hdnPerformanceID']);
											if ($thisPerformance) {
												echo "<OPTION VALUE={$thisPerformance->id} SELECTED>{$thisPerformance->name} - {$thisPerformance->location}</OPTION>";
											}
										}

										?>
									</SELECT>
									<INPUT type="button" ID="btnAssignPerformance" value="Assign Performance"
										onclick="getPerformances()" />
									<SPAN ID="lnkPerformanceMaint" name="lnkPerformanceMaint">
										<?php
										if (($_POST['hdnPerformanceID'] ?? 0) > 0) {
											echo "<A HREF='" . ADMIN_DIR . "/PerformanceMaintenance.php?ID={$_POST['hdnPerformanceID']}' class='secondaryLinkButton'>Edit Performance</A>";
										}
										?>
									</SPAN>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div>TAX CATEGORIES</div>
									<div class="FieldGroup">
										<div class="row">
											<div class="col-xs-3 col-sm-2">
												<input type="checkbox" name="chkColoradoRevenue" class="result-checkbox"
													value="ColoradoRevenue" <?php if (!empty($_POST['chkColoradoRevenue'])) {
														echo " checked ";
													}
													; ?> />
											</div>
											<div class="col-xs-9 col-sm-4 result-checkbox-text">
												COLORADO REVENUE
											</div>
											<div class="col-xs-3 col-sm-2">
												<input type="checkbox" name="chkElPasoRevenue" class="result-checkbox"
													value="ElPasoRevenue" <?php if (!empty($_POST['chkElPasoRevenue'])) {
														echo " checked ";
													}
													; ?> />
											</div>
											<div class="col-xs-9 col-sm-4 result-checkbox-text">
												EL PASO REVENUE
											</div>
											<div class="col-xs-3 col-sm-2">
												<input type="checkbox" name="chkCharitable" class="result-checkbox"
													value="Charitable" <?php if (!empty($_POST['chkCharitable'])) {
														echo " checked ";
													}
													; ?> />
											</div>
											<div class="col-xs-9 col-sm-4 result-checkbox-text">
												CHARITABLE
											</div>
											<div class="col-xs-3 col-sm-2">
												<input type="checkbox" name="chkResale" class="result-checkbox"
													value="Resale" <?php if (!empty($_POST['chkResale'])) {
														echo " checked ";
													}
													; ?> />
											</div>
											<div class="col-xs-9 col-sm-4 result-checkbox-text">
												RESALE
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-md-4 FieldGroup">
							<div class="row">
								<div class="col-xs-12">
									CATEGORIES
								</div>
								<?php

								foreach ($aRevenueCategories as $oCategory) {
									$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

									echo "<div class='col-xs-2 category-selector {$sResultStyleClass}'>";
									echo "<input type='checkbox' name='chkCategory{$oCategory->id}' class='result-checkbox'";
									echo " value='CategoryAssociated'";
									if (isset($_POST['chkCategory' . $oCategory->id])) {
										echo " checked ";
									}
									echo ">";
									echo "</div>";
									echo "<div class='col-xs-10 category-name result-checkbox-text {$sResultStyleClass}'>";
									echo $oCategory->name . "</div>";
								}
								?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-3 FormFieldNoEdit">
							ID: <?php echo $_POST['hdnRevenueID'] ?? ''; ?>
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

				<?php
			}
			?>
		</form>
	</div>
	<?php


	/*
	 ********************************************************************************
	 * buildRevenueObject
	 ********************************************************************************
	 */
	function buildRevenueObject(\Datalayer\Revenue $revenue): void
	{
		$id = $_POST['hdnRevenueID'] ?? null;
		$revenue->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

		$performanceId = $_POST['hdnPerformanceID'] ?? null;
		$revenue->performanceId = (is_numeric($performanceId) && (int) $performanceId > 0) ? (int) $performanceId : null;

		$paymentId = $_POST['hdnPaymentID'] ?? null;
		$revenue->paymentId = (is_numeric($paymentId) && (int) $paymentId > 0) ? (int) $paymentId : null;

		$amount = html_entity_decode($_POST['txtRevenueAmount'] ?? '', ENT_QUOTES);
		$revenue->amount = ($amount !== '' && is_numeric($amount)) ? (float) $amount : null;

		if (empty($_POST['txtRevenueDescription'])) {
			$_POST['txtRevenueDescription'] = " ";
		}
		$revenue->description = html_entity_decode($_POST['txtRevenueDescription'] ?? '', ENT_QUOTES);

		$revenue->coloradoRevenue = (($_POST['chkColoradoRevenue'] ?? '') == "ColoradoRevenue");
		$revenue->elPasoRevenue = (($_POST['chkElPasoRevenue'] ?? '') == "ElPasoRevenue");
		$revenue->charitable = (($_POST['chkCharitable'] ?? '') == "Charitable");
		$revenue->resale = (($_POST['chkResale'] ?? '') == "Resale");

		$dtRevenueDate = $_REQUEST["RevenueDate"] ?? "";
		if ($dtRevenueDate == "0000-00-00") {
			$dtRevenueDate = $_POST["RevenueDate"] ?? "";
		}
		if ($dtRevenueDate > "0000-00-00") {
			$revenue->revenueDate = $dtRevenueDate;
		}

		$dtPaidDate = $_REQUEST["PaidDate"] ?? "";
		if ($dtPaidDate == "0000-00-00") {
			$dtPaidDate = $_POST["PaidDate"] ?? "";
		}
		if ($dtPaidDate > "0000-00-00") {
			$revenue->paidDate = $dtPaidDate;
		}

		$revenueTypeId = $_POST['selRevenueType'] ?? null;
		$revenue->revenueTypeId = (is_numeric($revenueTypeId) && (int) $revenueTypeId > 0) ? (int) $revenueTypeId : null;

		$productId = $_POST['selProduct'] ?? null;
		$revenue->productId = (is_numeric($productId) && (int) $productId > 0) ? (int) $productId : null;

		$productQty = $_POST['txtProductQty'] ?? null;
		$revenue->productQty = (is_numeric($productQty) && $productQty !== '') ? (int) $productQty : null;
	}


	/*
	 ********************************************************************************
	 * buildRevenueSearchCriteria
	 ********************************************************************************
	 */
	function buildRevenueSearchCriteria(\Datalayer\Revenue $revenue, array $revenueCategories): array
	{
		$criteria = [
			'id' => $revenue->id,
			'description' => $revenue->description,
			'fuzzyName' => true,
			'includeProductInfo' => true,
		];

		if (!empty($revenue->revenueDate)) {
			$criteria['revenueDate'] = $revenue->revenueDate;
		}

		if (!empty($revenue->paidDate)) {
			$criteria['paidDate'] = $revenue->paidDate;
		}

		if (!empty($revenue->amount) && $revenue->amount > 0) {
			$criteria['amount'] = $revenue->amount;
		}

		$paidYear = $_POST['nYear'] ?? null;
		if (!empty($paidYear)) {
			$criteria['paidYear'] = (int) $paidYear;
		}

		$startDate = $_POST['hdnStartDate'] ?? null;
		if (!empty($startDate)) {
			$criteria['startDate'] = $startDate;
		}

		$endDate = $_POST['hdnEndDate'] ?? null;
		if (!empty($endDate)) {
			$criteria['endDate'] = $endDate;
		}

		if (!empty($_POST['bPerformanceRelated'])) {
			$criteria['performanceRelated'] = true;
		}

		if (!empty($revenue->paymentId)) {
			$criteria['paymentId'] = $revenue->paymentId;
		}

		if (!empty($revenue->performanceId)) {
			$criteria['performanceId'] = $revenue->performanceId;
		}

		if (!empty($revenue->revenueTypeId)) {
			$criteria['revenueTypeId'] = $revenue->revenueTypeId;
		}

		if ($revenue->coloradoRevenue) {
			$criteria['coloradoRevenue'] = true;
		}

		if ($revenue->elPasoRevenue) {
			$criteria['elPasoRevenue'] = true;
		}

		if ($revenue->charitable) {
			$criteria['charitable'] = true;
		}

		if ($revenue->resale) {
			$criteria['resale'] = true;
		}

		if (array_key_exists('selProduct', $_POST)) {
			$criteria['productId'] = $_POST['selProduct'];
		}

		if (!empty($revenue->productQty)) {
			$criteria['productQty'] = $revenue->productQty;
		}

		$artistId = $_POST['selArtist'] ?? null;
		if (!empty($artistId)) {
			$criteria['artistId'] = (int) $artistId;
		}

		$categoryIds = [];
		foreach ($revenueCategories as $revenueCategory) {
			if (isset($_POST['chkCategory' . $revenueCategory->id])) {
				$categoryIds[] = $revenueCategory->id;
			}
		}
		if (!empty($categoryIds)) {
			$criteria['categoryIds'] = $categoryIds;
		}

		return $criteria;
	}


	/*
	 ********************************************************************************
	 * loadRevenue
	 ********************************************************************************
	 */
	function loadRevenue(\Datalayer\Revenue $revenue, $form, array $revenueCategories): void
	{
		if (!is_null($revenue->id)) {
			$_POST['hdnRevenueID'] = $revenue->id;
			$_POST['hdnPerformanceID'] = $revenue->performanceId;
			$_POST['hdnPaymentID'] = $revenue->paymentId;

			$_POST['txtRevenueDescription'] = htmlentities($revenue->description ?? '', ENT_QUOTES);
			$_POST['RevenueDate'] = htmlentities($revenue->revenueDate ?? '', ENT_QUOTES);
			$_POST['PaidDate'] = htmlentities($revenue->paidDate ?? '', ENT_QUOTES);
			$_POST['txtRevenueAmount'] = htmlentities($revenue->amount ?? '', ENT_QUOTES);
			$_POST['txtProductQty'] = htmlentities($revenue->productQty ?? '', ENT_QUOTES);

			$_POST['chkColoradoRevenue'] = $revenue->coloradoRevenue;
			$_POST['chkElPasoRevenue'] = $revenue->elPasoRevenue;
			$_POST['chkCharitable'] = $revenue->charitable;
			$_POST['chkResale'] = $revenue->resale;

			$_POST['selProduct'] = (!empty($revenue->productId)) ? $revenue->productId : 0;
			$_POST['selRevenueType'] = (!empty($revenue->revenueTypeId)) ? $revenue->revenueTypeId : 0;

			$associatedCategoryIds = getRevenueCategoryIds((int) $revenue->id);
			foreach ($revenueCategories as $revenueCategory) {
				if (in_array((int) $revenueCategory->id, $associatedCategoryIds, true)) {
					$_POST['chkCategory' . $revenueCategory->id] = "CategoryAssociated";
				}
			}

			$_POST['txtLastUpdate'] = htmlentities($revenue->lastUpdate ?? '', ENT_QUOTES);
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


	/*
	 ********************************************************************************
	 * getRevenueCategoryIds
	 ********************************************************************************
	 */
	function getRevenueCategoryIds(int $revenueId): array
	{
		$db = \Datalayer\Connection::getPdo();
		$stmt = $db->prepare('SELECT CATEGORY_ID FROM REVENUE_CATEGORY_XREF WHERE REVENUE_ID = :revenueId');
		$stmt->execute([':revenueId' => $revenueId]);
		return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
	}


	/*
	 ********************************************************************************
	 * updateRevenueCategories
	 ********************************************************************************
	 */
	function updateRevenueCategories(int $revenueId, array $revenueCategories): bool
	{
		if ($revenueId <= 0) {
			return false;
		}

		if (!removeRevenueCategories($revenueId)) {
			return false;
		}

		$db = \Datalayer\Connection::getPdo();
		$stmt = $db->prepare('INSERT INTO REVENUE_CATEGORY_XREF (REVENUE_ID, CATEGORY_ID) VALUES (:revenueId, :categoryId)');

		foreach ($revenueCategories as $revenueCategory) {
			$checkboxName = "chkCategory{$revenueCategory->id}";
			if (isset($_POST[$checkboxName]) && $_POST[$checkboxName] == 'CategoryAssociated') {
				if (!$stmt->execute([
					':revenueId' => $revenueId,
					':categoryId' => (int) $revenueCategory->id,
				])) {
					return false;
				}
			}
		}

		return true;
	}


	/*
	 ********************************************************************************
	 * removeRevenueCategories
	 ********************************************************************************
	 */
	function removeRevenueCategories(int $revenueId): bool
	{
		if ($revenueId <= 0) {
			return false;
		}

		$db = \Datalayer\Connection::getPdo();
		$stmt = $db->prepare('DELETE FROM REVENUE_CATEGORY_XREF WHERE REVENUE_ID = :revenueId');
		return $stmt->execute([':revenueId' => $revenueId]);
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

	//Revenue Name is a required Field
	if ( document.RevenueMaint.txtRevenueDescription.value == "" )
	{
		sErrorMessage += "Revenue Description Required\n";
		bValid = false;
	}

	//Revenue Date is a required Field
	if ( document.RevenueMaint.RevenueDate.value == "" || document.RevenueMaint.RevenueDate.value == "0000-00-00" )
	{
		sErrorMessage += "Revenue Date Required\n";
		bValid = false;
	}

	//Paid Date is a required Field
	if ( document.RevenueMaint.PaidDate.value == "" || document.RevenueMaint.PaidDate.value == "0000-00-00" )
	{
		sErrorMessage += "Paid Date Required\n";
		bValid = false;
	}

	//Revenue Type is a required Field
	if ( document.RevenueMaint.selRevenueType.value == "" || document.RevenueMaint.selRevenueType.value == 0 )
	{
		sErrorMessage += "Revenue Type Required\n";
		bValid = false;
	}

	//Revenue Amount is a required, numeric, positive Field
	if ( document.RevenueMaint.txtRevenueAmount.value == "" )
	{
		sErrorMessage += "Revenue Amount Required\n";
		bValid = false;
	}
	else if(!isNumeric(document.RevenueMaint.txtRevenueAmount.value)) 
	{
		sErrorMessage += "Revenue Amount Must be Numeric\n";
		bValid = false;
	}
	
	//If any errors, alert
	if (!bValid)
	{
		alert(sErrorMessage);
	}

	return bValid;
}

//*********************************************
// buildPerformanceLink
// This script builds a link to the Performance
// Maintenance page based on the performance
// chosen in the drop-down list
//*********************************************
function buildPerformanceLink() 
{
	setPerformanceID();
	if(document.getElementById("selPerformance").value > 0)
	{
		sPerformanceMaintLink = "<A CLASS='secondaryLinkButton' HREF='./PerformanceMaintenance.php?ID=" + document.getElementById("selPerformance").value + "'>Edit Performance</A>";  
		document.getElementById("lnkPerformanceMaint").innerHTML = sPerformanceMaintLink;
	}
	else
	{
		document.getElementById("lnkPerformanceMaint").innerHTML = "";
	}
}

//*********************************************
// buildPaymenteLink
// This script builds a link to the Payment
// Maintenance page based on the payment
// chosen in the drop-down list
//*********************************************
function buildPaymentLink() 
{
	setPaymentID();
	if(document.getElementById("selPayment").value > 0)
	{
		sPaymentMaintLink = "<SMALL><I><A HREF='./PaymentMaintenance.php?ID=" + document.getElementById("selPayment").value + "'>Edit Payment</A></I></SMALL>";  
		document.getElementById("lnkPaymentMaint").innerHTML = sPaymentMaintLink;
	}
	else
	{
		document.getElementById("lnkPaymentMaint").innerHTML = "";
	}
}

//*********************************************
// getPerformances
// This script makes an AJAX call to retrieve
// potential performances based on the revenue 
// date chosen in the calender and updates the
// performance dropdown with the returned HTML. 
//*********************************************
function getPerformances() {
	sDate = document.getElementById("RevenueDate").value;

	if (sDate == "" || sDate == "0000-00-00") {
		document.getElementById("selPerformance").innerHTML = "<OPTION Value='0'>NONE</OPTION>";
		document.getElementById("hdnPerformanceID").value = 0;
		return;
	} else { 
		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else {
			// code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("selPerformance").innerHTML = xmlhttp.responseText;
//alert("VALUE is " + document.getElementById("selPerformance").value);
				if(document.getElementById("selPerformance").value == 0)
				{	
					document.getElementById("hdnPerformanceID").value = 0;
				}
			}
		}
//alert("CALLING GetPotentialPerformances.php?date="+sDate);		
		xmlhttp.open("GET","GetPotentialPerformances.php?date="+sDate,true);
		xmlhttp.send();
	}
	
}

//*********************************************
// getPayments
// This script makes an AJAX call to retrieve
// potential payments based on the paid 
// date chosen in the calender and updates the
// payment dropdown with the returned HTML. 
//*********************************************
function getPayments() {
	sDate = document.getElementById("PaidDate").value;

	if (sDate == "" || sDate == "0000-00-00") {
		document.getElementById("selPayment").innerHTML = "<OPTION Value='0'>NONE</OPTION>";
		document.getElementById("hdnPaymentID").value = 0;
		return;
	} else { 
		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else {
			// code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("selPayment").innerHTML = xmlhttp.responseText;
//alert("VALUE is " + document.getElementById("selPayment").value);
				if(document.getElementById("selPayment").value == 0)
				{	
					document.getElementById("hdnPaymentID").value = 0;
				}
			}
		}
//alert("CALLING GetPotentialPerformances.php?date="+sDate);		
		xmlhttp.open("GET","GetPotentialPayments.php?date="+sDate,true);
		xmlhttp.send();
	}
	
}

//*********************************************
// setPerformanceID
// This script sets the Performance ID hidden 
// form field based on the performance chosen 
// in the performance dropdown. 
//*********************************************
function setPerformanceID() {

//alert("Setting PerformanceID to " + document.getElementById("selPerformance").value);
	document.getElementById("hdnPerformanceID").value = document.getElementById("selPerformance").value;
}

//*********************************************
// setPaymentID
// This script sets the Payment ID hidden 
// form field based on the payment chosen 
// in the payment dropdown. 
//*********************************************
function setPaymentID() {

//alert("Setting PaymentID to " + document.getElementById("selPayment").value);
	document.getElementById("hdnPaymentID").value = document.getElementById("selPayment").value;
}
</script>
</html>