<?php
/*
*******************************************************************
PerformanceMaintenance.php
This PHP file defines the Maintenance page for 
managing Performances.
NOTES
Date        Change
-------------------------------------------------------------
2016-11-23	Created Responsive version
2016-12-29	Improved Responsivity for phone
2017-03-08	Added Parm for EventID to expand performance details
2017-07-31	Small responsive improvements
2019-01-23	Cleared Booked Date on Copy
2019-11-05	Changed Datepickers to use common function
2021-03-05	Added Pre-recorded flag
2021-03-05	Added adult show flag
2022-03-15	Added Tasks Button and Tasks
2022-07-04	Added strikeout for complete tasks
2024-05-09	Added Colorado Sessions Flag
2026-07-30	Migrated Performance data access to new Datalayer repository
2026-07-30	Migrated PerformanceTask list to Datalayer; removed legacy Tour/Artist includes
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
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");
include_once(DATALAYER_DIR . "/PerformanceTask.php");
include_once(DATALAYER_DIR . "/PerformanceTaskRepository.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of Performance records from the DB
$aPerformanceRecords = [];

$sActiveMenuItem = PERFORMANCES_ACTIVE;	
$sPageName = "Performance Maintenance";	

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
		<form name="PerformanceMaint" action="PerformanceMaintenance.php" method="post">

			<?php

			//Instantiate needed objects
			$performanceRepo = new \Datalayer\PerformanceRepository();
			$performanceTaskRepo = new \Datalayer\PerformanceTaskRepository();
			$thisPerformance = new \Datalayer\Performance();
			$aPerformanceTaskRecords = [];
			$form = new Form();

			//Get the ID query string parameter
			$nThisPerformanceID = $_REQUEST['ID'] ?? null;

			//If an Artist ID is passed, go ahead and search performances for that artist
			if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "")
			{
				$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
				$_POST['btnSearch'] = "Search";
			}

			try {
				//If an ID was passed to the page, retrieve that record for update		
				if (!is_null($nThisPerformanceID) && $nThisPerformanceID !== '') {

					$entity = $performanceRepo->findById((int) $nThisPerformanceID);

					if ($entity) {
						$thisPerformance = $entity;
						$aPerformanceRecords = [$entity];
						loadPerformance($thisPerformance, $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						$form->sMessage = "Performance record not found.";
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
						buildPerformanceObject($thisPerformance);

						if ($performanceRepo->insert($thisPerformance)) {
							$thisPerformance = $performanceRepo->findById((int) $thisPerformance->id) ?? $thisPerformance;
							$aPerformanceRecords = [$thisPerformance];
							loadPerformance($thisPerformance, $form);
							$nThisPerformanceID = $thisPerformance->id;

							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Performance Added";
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

						buildPerformanceObject($thisPerformance);

						if ($performanceRepo->update($thisPerformance)) {
							$thisPerformance = $performanceRepo->findById((int) $thisPerformance->id) ?? $thisPerformance;
							$aPerformanceRecords = [$thisPerformance];
							loadPerformance($thisPerformance, $form);
							$nThisPerformanceID = $thisPerformance->id;

							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Performance Updated";
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

						buildPerformanceObject($thisPerformance);

						if (!empty($thisPerformance->id) && $performanceRepo->delete((int) $thisPerformance->id)) {
							clearFormFields($form);

							$form->sMessage = "Performance Deleted";
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
						buildPerformanceObject($thisPerformance);

						$criteria = [
							'id' => $thisPerformance->id,
							'name' => $thisPerformance->name,
							'fuzzyName' => true,
							'location' => $thisPerformance->location,
							'fuzzyLocation' => true,
							'locationCity' => $thisPerformance->locationCity,
							'locationState' => $thisPerformance->locationState,
							'locationZip' => $thisPerformance->locationZip,
							'artistId' => $thisPerformance->artistId,
							'tourId' => $thisPerformance->tourId,
							'performanceDate' => $thisPerformance->performanceDate,
							'performanceTime' => $thisPerformance->performanceTime,
							'notes' => $thisPerformance->notes,
						];

						if ($thisPerformance->preRecorded !== null) {
							$criteria['preRecorded'] = $thisPerformance->preRecorded;
						}
						if ($thisPerformance->adultShow !== null) {
							$criteria['adultShow'] = $thisPerformance->adultShow;
						}
						if ($thisPerformance->coloradoSessions !== null) {
							$criteria['coloradoSessions'] = $thisPerformance->coloradoSessions;
						}

						$aPerformanceRecords = $performanceRepo->find($criteria);

						if (sizeof($aPerformanceRecords) < 1) {
							$form->sMessage = "No Performance records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($aPerformanceRecords) == 1) {
							$thisPerformance = $aPerformanceRecords[0];
							loadPerformance($thisPerformance, $form);
							$nThisPerformanceID = $thisPerformance->id;

							$form->sMessage = "One Performance record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							$form->sMessage = "Select Performance record to edit from results list below.";
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
					// *   COPY  *
					// *************
					else if (isset($_POST["btnCopy"])) {
						$_POST['hdnPerformanceID'] = NULL;
						$_POST['BookedDate'] = "0000-00-00";

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
			} catch (Exception $e) {
				error_log('PerformanceMaintenance error: ' . $e->getMessage());
				$form->sMessage = "ERROR: " . $e->getMessage();
				$form->nMessageType = MESSAGE_TYPE_ERROR;
				$form->nFormMode = FORM_MODE_NEW;
			}

			?>
			<!-- Hidden Fields -->
			<input type="hidden" name="hdnPerformanceID" value="<?php echo $_POST['hdnPerformanceID'] ?>" />

			<div class="row">
				<?php
				include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 Title">
							Performance Maintenance
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
			//Parse Performance Date
			if ($_POST['PerformanceDate'] > "0000-00-00") {
				//Must parse out date parts to set the date on the calendar picker and schedule links
				$aPerformanceDateParts = explode("-", $_POST['PerformanceDate']);
			}

			if ($form->nFormMode == FORM_MODE_SELECT) {
			?>

				<div class="row">

					<div class="col-xs-12">
						<?php

						$sResultStyleClass = RESULT_STYLE_CLASS_ALT;

						//Header row for Results
						echo "<div class='row result-header'>";
						echo "	<div class='hidden-xs col-sm-2 col-md-2  result-header'>Performance Name</div>";
						echo "	<div class='hidden-xs col-sm-3 col-md-2  result-header'>Date</div>";
						echo "	<div class='hidden-xs hidden-sm col-md-2  result-header'>Time</div>";
						echo "	<div class='hidden-xs hidden-sm col-md-2  result-header'>Location</div>";
						echo "	<div class='hidden-xs col-sm-5 col-md-2 result-header'>City</div>";
						echo "	<div class='hidden-xs col-sm-2 col-md-2 result-header'>State</div>";
						echo "	<div class='visible-xs col-xs-12 result-header'>Performances</div>";
						echo "</div>";


						foreach ($aPerformanceRecords as $oPerformanceRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							//The first column is the ID and is used to build a link
							$i = 1;

							echo "	<div class='col-xs-12 col-sm-2 col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->id}'>{$oPerformanceRecord->name}</A></div>";
							echo "	<div class='col-xs-12 col-sm-3 col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->id}'>{$oPerformanceRecord->performanceDate}</A></div>";
							echo "	<div class='hidden-xs hidden-sm col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->id}'>{$oPerformanceRecord->performanceTime}</A></div>";
							echo "	<div class='hidden-xs hidden-sm col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->id}'>{$oPerformanceRecord->location}</A></div>";
							echo "	<div class='col-xs-12 col-sm-5 col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->id}'>{$oPerformanceRecord->locationCity}</A></div>";
							echo "	<div class='col-xs-12 col-sm-2 col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->id}'>{$oPerformanceRecord->locationState}</A></div>";
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
					<div class="col-xs-12">
						<?php
						if ($_POST['hdnPerformanceID'] > 0) {
							//Parse Performance Date
							if ($_POST['PerformanceDate'] > "0000-00-00") {
								//Must parse out date parts to set the date on the calendar picker
								$aPerformanceDateParts = explode("-", $_POST['PerformanceDate']);
							}

							echo "<a href='{$ROOT}/schedule.php?year={$aPerformanceDateParts[0]}";
							echo "&eventID={$nThisPerformanceID}";
							echo "#performance-{$nThisPerformanceID}' class='secondaryLinkButton'";
							echo " target='_blank'>Page</a>";
							echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
							echo "<br class='visible-xs'>";
							echo "<a href='" . ADMIN_DIR . "/PerformanceEmails.php?ID={$nThisPerformanceID}' class='secondaryLinkButton' target='_blank'>Emails</a>";
							echo "<span class='hidden-xs hidden-sm'>&nbsp;&nbsp;&nbsp;&nbsp;&#8226;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
							echo "<br class='visible-xs'>";
							echo "<a href='" . ADMIN_DIR . "/PerformanceTaskMaintenance.php?PERFORMANCE_ID={$nThisPerformanceID}&PERFORMANCE_NAME=" . urlencode($thisPerformance->name ?? '') . "' class='secondaryLinkButton'>Add Task</a>";
						}
						?>

					</div>
					<div class="col-xs-12 FieldGroupTitle">DETAILS</div>
					<?php
					// The performance object will have info if a search returned one record.  It will have the record in its array
					// if a performance ID was passed in the query string
					if(empty($thisPerformance->id) && sizeof($aPerformanceRecords) > 0) {
						$thisPerformance->id = $aPerformanceRecords[0]->id;
					}
					if(!empty($thisPerformance->id)){
						$aPerformanceTaskRecords = $performanceTaskRepo->find([
							'performanceId' => (int) $thisPerformance->id,
						]);
						if (sizeof($aPerformanceTaskRecords) > 0) {
							echo "<div class=\"col-xs-12 FieldGroup\">";
							echo "TASKS:";
							foreach ($aPerformanceTaskRecords as $oPerformanceTaskRecord) {
								if (!empty($oPerformanceTaskRecord->complete)) {
									echo "<del> ";
								}
								echo "<div><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->id}'>{$oPerformanceTaskRecord->description}</A></div>";
								if (!empty($oPerformanceTaskRecord->complete)) {
									echo "</del> ";
								}
							}
							echo "</div>";
						}
					}
					?>
					<div class="col-xs-12 FieldGroup">
						<div class="row">
							<div class="col-xs-12 col-md-4">
								NAME: <input type="text" name="txtPerformanceName" value="<?php echo $_POST['txtPerformanceName']; ?>" />
							</div>
							<div class="col-xs-12 col-md-4">
								PERFORMANCE DATE:<BR />
								<?php
								renderDatePicker("PerformanceDate", $thisPerformance->performanceDate ?? $_POST['PerformanceDate'] ?? '');
								?>
							</div>

							<div class="col-xs-12 col-md-4">
								TIME: <input type="text" name="txtPerformanceTime" value="<?php echo $_POST['txtPerformanceTime']; ?>" />
							</div>
							<div class="col-xs-12 col-md-4">

								<div class="row">
									<div class="col-xs-12">
										PERFORMANCE DECSRIPTION<BR />
										<textarea name="txtDescription" rows=10><?php echo $_POST['txtDescription']; ?></textarea>
									</div>	
									<div class="col-xs-12">
										<div style="float: left; height: 30px; width: 30px;">
											<input type="checkbox" name="chkPreRecorded" style="float: left;" class="result-checkbox" value="PreRecorded" <?php if ($_POST['chkPreRecorded']) {
																																								echo " checked ";
																																							}; ?> />
										</div>
										<div style="float: left;">
											Pre-Recorded
										</div>
									</div>
									<div class="col-xs-12">
										<div style="float: left; height: 30px; width: 30px;">
											<input type="checkbox" name="chkAdultShow" style="float: left;" class="result-checkbox" value="AdultShow" <?php if ($_POST['chkAdultShow']) {
																																								echo " checked ";
																																							}; ?> />
										</div>
										<div style="float: left;">
											Adult Show
										</div>
									</div>
									<div class="col-xs-12">
										<div style="float: left; height: 30px; width: 30px;">
											<input type="checkbox" name="chkColoradoSessions" style="float: left;" class="result-checkbox" value="ColoradoSessions" <?php if ($_POST['chkColoradoSessions']) {
																																								echo " checked ";
																																							}; ?> />
										</div>
										<div style="float: left;">
											Colorado Sessions
										</div>
									</div>
								</div>
							</div>

							<div class="col-xs-12 col-md-4">
								LOCATION: <input type="text" name="txtLocation" value="<?php echo $_POST['txtLocation']; ?>" />
							</div>
							<div class="col-xs-12 col-md-4">
								LOCATION URL: <input type="text" name="txtLocationWebsite" value="<?php echo $_POST['txtLocationWebsite']; ?>" />
							</div>
							<div class="col-xs-12 col-md-4">
								ADDRESS 1: <input type="text" name="txtLocationAddr1" value="<?php echo $_POST['txtLocationAddr1']; ?>" />
							</div>
							<div class="col-xs-12 col-md-4">
								ADDRESS 2: <input type="text" name="txtLocationAddr2" value="<?php echo $_POST['txtLocationAddr2']; ?>" />									
							</div>
							<div class="col-xs-12 col-md-4">
								CITY: <input type="text" name="txtLocationCity" value="<?php echo $_POST['txtLocationCity']; ?>" />
								STATE: <input type="text" name="txtLocationState" value="<?php echo $_POST['txtLocationState']; ?>" />
								ZIP: <input type="text" name="txtLocationZip" value="<?php echo $_POST['txtLocationZip']; ?>" />
							</div>
							<div class="col-xs-12 col-md-4">
								EVENT WEBSITE: <input type="text" name="txtPerformanceWebsite" value="<?php echo $_POST['txtPerformanceWebsite']; ?>" />
							</div>
							<div class="col-xs-12 col-md-4">
								ADMISSION: <input type="text" name="txtAdmission" value="<?php echo $_POST['txtAdmission']; ?>" />
							</div>
							<div class="col-xs-12 col-md-4">
								<?php 
								renderTourDropDown();
								?>
							</div>
							<div class="col-xs-12 col-md-4">
								<?php
									renderArtistDropDown($_POST['selArtist']);
								?>
							</div>
						</div>
					</div>
					<div class="col-xs-12 FieldGroupTitle">CONTACT INFORMATION</div>
					<div class="col-xs-12 FieldGroup">
						<div class="row">
							<div class="col-xs-12 col-md-4">
								CONTACT: <input type="text" name="txtContactName" value="<?php echo $_POST['txtContactName']; ?>" size="60" />
							</div>
							<div class="col-xs-12 col-md-4">
								CONTACT PHONE: <input type="text" name="txtContactPhone" value="<?php echo $_POST['txtContactPhone']; ?>" size="20" />
							</div>
							<div class="col-xs-12 col-md-4">
								CONTACT EMAIL: <input type="text" name="txtContactEmail" value="<?php echo $_POST['txtContactEmail']; ?>" size="60" />
							</div>
						</div>
					</div>

					<div class="col-xs-12 FieldGroupTitle">BOOKING INFORMATION</div>
					<div class="col-xs-12 FieldGroup">
						<div class="row">
							<div class="col-xs-12 col-md-2">
								BOOKED DATE:<br />
								<?php
								renderDatePicker("BookedDate", $thisPerformance->bookedDate ?? $_POST['BookedDate'] ?? '');

								?>
							</div>
							<div class="col-xs-12 col-md-10">
								BOOKED_AMOUNT: <input type="text" name="txtBookedAmount" value="<?php echo $_POST['txtBookedAmount']; ?>" size="20" />
							</div>
							<div class="col-xs-12">
								PERFORMANCE NOTES
								<textarea name="txtNotes" cols=60 rows=5><?php echo $_POST['txtNotes']; ?></textarea>
							</div>
							<div class="col-xs-12 col-md-3">
								CONTRACT:
								<SELECT ID="selContract" NAME="selContract">
									<OPTION <?php if (is_null($_POST['selContract'])) {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="">N/A</OPTION>
									<OPTION <?php if ($_POST['selContract'] == "Y") {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="Y">YES</OPTION>
									<OPTION <?php if ($_POST['selContract'] == "N") {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="N">NO</OPTION>
								</SELECT>
							</div>
							<div class="col-xs-12 col-md-3">
								AIRFARE BOOKED:
								<SELECT ID="selAirfare" NAME="selAirfare">
									<OPTION <?php if (is_null($_POST['selAirfare'])) {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="">N/A</OPTION>
									<OPTION <?php if ($_POST['selAirfare'] == "Y") {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="Y">YES</OPTION>
									<OPTION <?php if ($_POST['selAirfare'] == "N") {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="N">NO</OPTION>
								</SELECT>
							</div>
							<div class="col-xs-12 col-md-3">
								HOTEL BOOKED:
								<SELECT ID="selHotel" NAME="selHotel">
									<OPTION <?php if (is_null($_POST['selHotel'])) {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="">N/A</OPTION>
									<OPTION <?php if ($_POST['selHotel'] == "Y") {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="Y">YES</OPTION>
									<OPTION <?php if ($_POST['selHotel'] == "N") {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="N">NO</OPTION>
								</SELECT>
							</div>
							<div class="col-xs-12 col-md-3">
								RENTAL CAR BOOKED:
								<SELECT ID="selRentalCar" NAME="selRentalCar">
									<OPTION <?php if (is_null($_POST['selRentalCar'])) {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="">N/A</OPTION>
									<OPTION <?php if ($_POST['selRentalCar'] == "Y") {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="Y">YES</OPTION>
									<OPTION <?php if ($_POST['selRentalCar'] == "N") {
												echo " SELECTED='SELECTED' ";
											} ?> VALUE="N">NO</OPTION>
								</SELECT>
							</div>
						</div>
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
			<?php
			}
			?>
		</form>

	</div>
	<?php


	/*
 ********************************************************************************
 * buildPerformanceObject
 * 
 * This function loads builds a Performance object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
	function buildPerformanceObject(\Datalayer\Performance $oPerformance)
	{
		$id = $_POST['hdnPerformanceID'] ?? null;
		$oPerformance->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

		$tourId = $_POST['hdnTourID'] ?? null;
		$oPerformance->tourId = (!empty($tourId) && is_numeric($tourId)) ? (int) $tourId : null;
		$oPerformance->artistId = (int) ($_POST['selArtist'] ?? 0);

		$oPerformance->name = html_entity_decode($_POST['txtPerformanceName'] ?? '', ENT_QUOTES);
		$oPerformance->website = html_entity_decode($_POST['txtPerformanceWebsite'] ?? '', ENT_QUOTES);
		$oPerformance->performanceTime = html_entity_decode($_POST['txtPerformanceTime'] ?? '', ENT_QUOTES);
		$oPerformance->location = html_entity_decode($_POST['txtLocation'] ?? '', ENT_QUOTES);
		$oPerformance->locationWebsite = html_entity_decode($_POST['txtLocationWebsite'] ?? '', ENT_QUOTES);
		$oPerformance->locationAddr1 = html_entity_decode($_POST['txtLocationAddr1'] ?? '', ENT_QUOTES);
		$oPerformance->locationAddr2 = html_entity_decode($_POST['txtLocationAddr2'] ?? '', ENT_QUOTES);
		$oPerformance->locationCity = html_entity_decode($_POST['txtLocationCity'] ?? '', ENT_QUOTES);
		$oPerformance->locationState = html_entity_decode($_POST['txtLocationState'] ?? '', ENT_QUOTES);
		$oPerformance->locationZip = html_entity_decode($_POST['txtLocationZip'] ?? '', ENT_QUOTES);
		$oPerformance->description = html_entity_decode($_POST['txtDescription'] ?? '', ENT_QUOTES);
		$oPerformance->admission = html_entity_decode($_POST['txtAdmission'] ?? '', ENT_QUOTES);
		$oPerformance->contactName = html_entity_decode($_POST['txtContactName'] ?? '', ENT_QUOTES);
		$oPerformance->contactEmail = html_entity_decode($_POST['txtContactEmail'] ?? '', ENT_QUOTES);
		$oPerformance->contactPhone = html_entity_decode($_POST['txtContactPhone'] ?? '', ENT_QUOTES);
		$oPerformance->notes = html_entity_decode($_POST['txtNotes'] ?? '', ENT_QUOTES);
		$oPerformance->contract = $_POST['selContract'] ?? null;
		$oPerformance->airfare = $_POST['selAirfare'] ?? null;
		$oPerformance->hotel = $_POST['selHotel'] ?? null;
		$oPerformance->rentalCar = $_POST['selRentalCar'] ?? null;

		$bookedAmount = html_entity_decode($_POST['txtBookedAmount'] ?? '', ENT_QUOTES);
		$oPerformance->bookedAmount = is_numeric($bookedAmount) ? (float) $bookedAmount : null;

		//Pre-Recorded Flag
		if (($_POST['chkPreRecorded'] ?? '') == "PreRecorded") {
			$oPerformance->preRecorded = TRUE;
		}

		//Adult Show Flag
		if (($_POST['chkAdultShow'] ?? '') == "AdultShow") {
			$oPerformance->adultShow = TRUE;
		}

		// Colorado Sessions Flag
		if (($_POST['chkColoradoSessions'] ?? '') == "ColoradoSessions") {
			$oPerformance->coloradoSessions = TRUE;
		}

		//Performance Date	
		$dtPerformanceDate = isset($_REQUEST["PerformanceDate"]) ? $_REQUEST["PerformanceDate"] : "";
		if ($dtPerformanceDate > "0000-00-00") {
			$dtPerformanceDate = isset($_POST["PerformanceDate"]) ? $_POST["PerformanceDate"] : "";
		}

		if ($dtPerformanceDate > "0000-00-00") {
			$oPerformance->performanceDate = $dtPerformanceDate;
		}

		//Booked Date	
		$dtBookedDate = isset($_REQUEST["BookedDate"]) ? $_REQUEST["BookedDate"] : "";
		if ($dtBookedDate > "0000-00-00") {
			$dtBookedDate = isset($_POST["BookedDate"]) ? $_POST["BookedDate"] : "";
		}

		if ($dtBookedDate > "0000-00-00") {
			$oPerformance->bookedDate = $dtBookedDate;
		}
	}


	/*
 ********************************************************************************
 * loadPerformance
 * 
 * This function loads the form field array from a populated Performance object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
	function loadPerformance(\Datalayer\Performance $oPerformance, $form)
	{

		if (!is_null($oPerformance->id)) {
			//Load Hidden Fields
			$_POST['hdnPerformanceID'] = $oPerformance->id;
			$_POST['hdnTourID'] = $oPerformance->tourId;
			$_POST['selArtist'] = $oPerformance->artistId;

			$_POST['txtPerformanceName'] = htmlentities($oPerformance->name ?? '', ENT_QUOTES);
			$_POST['txtPerformanceWebsite'] = htmlentities($oPerformance->website ?? "", ENT_QUOTES);
			$_POST['PerformanceDate'] = htmlentities($oPerformance->performanceDate ?? '', ENT_QUOTES);
			$_POST['txtPerformanceTime'] = htmlentities($oPerformance->performanceTime ?? '', ENT_QUOTES);
			$_POST['txtLocation'] = htmlentities($oPerformance->location ?? '', ENT_QUOTES);
			$_POST['txtLocationWebsite'] = htmlentities($oPerformance->locationWebsite ?? "", ENT_QUOTES);
			$_POST['txtLocationAddr1'] = htmlentities($oPerformance->locationAddr1 ?? '', ENT_QUOTES);
			$_POST['txtLocationAddr2'] = htmlentities($oPerformance->locationAddr2 ?? '', ENT_QUOTES);
			$_POST['txtLocationCity'] = htmlentities($oPerformance->locationCity ?? '', ENT_QUOTES);
			$_POST['txtLocationState'] = htmlentities($oPerformance->locationState ?? '', ENT_QUOTES);
			$_POST['txtLocationZip'] = htmlentities($oPerformance->locationZip ?? '', ENT_QUOTES);
			$_POST['txtDescription'] = htmlentities($oPerformance->description ?? '', ENT_QUOTES);
			$_POST['txtAdmission'] = htmlentities($oPerformance->admission ?? '', ENT_QUOTES);
			$_POST['txtContactName'] = htmlentities($oPerformance->contactName ?? '', ENT_QUOTES);
			$_POST['txtContactEmail'] = htmlentities($oPerformance->contactEmail ?? '', ENT_QUOTES);
			$_POST['txtContactPhone'] = htmlentities($oPerformance->contactPhone ?? '', ENT_QUOTES);
			$_POST['txtBookedAmount'] = htmlentities((string) ($oPerformance->bookedAmount ?? '0'), ENT_QUOTES);
			$_POST['BookedDate'] = htmlentities($oPerformance->bookedDate ?? '0000-00-00', ENT_QUOTES);
			$_POST['txtNotes'] = htmlentities($oPerformance->notes ?? "", ENT_QUOTES);
			$_POST['selContract'] = htmlentities($oPerformance->contract ?? "", ENT_QUOTES);
			$_POST['selAirfare'] = htmlentities($oPerformance->airfare ?? "", ENT_QUOTES);
			$_POST['selHotel'] = htmlentities($oPerformance->hotel ?? "", ENT_QUOTES);
			$_POST['selRentalCar'] = htmlentities($oPerformance->rentalCar ?? "", ENT_QUOTES);
			$_POST['txtLastUpdate'] = htmlentities($oPerformance->lastUpdate ?? '', ENT_QUOTES);

			$_POST['chkPreRecorded'] = $oPerformance->preRecorded;
			$_POST['chkAdultShow'] = $oPerformance->adultShow;
			$_POST['chkColoradoSessions'] = $oPerformance->coloradoSessions;
		} else {
			foreach ($_POST as $fieldName => $fieldValue) {
				$_POST[$fieldName] = $fieldValue;
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

	?>

</body>
<script type="text/javascript">
		<!--
		function validate_form() {
			bValid = true;
			sErrorMessage = "";

			//Performance Name is a required Field
			if (document.PerformanceMaint.txtPerformanceName.value == "") {
				sErrorMessage += "Performance Name Required\n";
				bValid = false;
			}

			//Performance Name is a required Field
			if (document.PerformanceMaint.PerformanceDate.value == "" || document.PerformanceMaint.PerformanceDate.value == "0000-00-00") {
				sErrorMessage += "Performance Date Required\n";
				bValid = false;
			}

			//Artist is a required Field
			if ( document.PerformanceMaint.selArtist.value == "0" )
			{
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
	<script>
		function buildTourLink() {
			setTourID();
			if (document.getElementById("selTour").value > 0) {
				sTourMaintLink = "<A HREF='./TourMaintenance.php?ID=" + document.getElementById("selTour").value + "' class='secondaryLinkButton'>Edit Tour</A>";
				document.getElementById("lnkTourMaint").innerHTML = sTourMaintLink;
			} else {
				document.getElementById("lnkTourMaint").innerHTML = "";
			}
		}

		function getTours() {
			sDate = document.getElementById("PerformanceDate").value;

			if (sDate == "" || sDate == "0000-00-00") {
				document.getElementById("selTour").innerHTML = "<OPTION Value='0'>NONE</OPTION>";
				document.getElementById("hdnTourID").value = 0;
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
						document.getElementById("selTour").innerHTML = xmlhttp.responseText;
						//alert("VALUE is " + document.getElementById("selTour").value);
						if (document.getElementById("selTour").value == 0) {
							document.getElementById("hdnTourID").value = 0;
						}
					}
				}
				//alert("CALLING GetPotentialTours.php?date="+sDate);		
				xmlhttp.open("GET", "GetPotentialTours.php?date=" + sDate, true);
				xmlhttp.send();
			}

		}

		function setTourID() {

			//alert("Setting TourID to " + document.getElementById("selTour").value);
			document.getElementById("hdnTourID").value = document.getElementById("selTour").value;
		}
	</script>
	</html>