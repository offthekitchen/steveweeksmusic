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

//include Performance Class		
include_once(CLASS_DIR . "/class_Performance.php");
//include Performance Task Class		
include_once(CLASS_DIR . "/class_PerformanceTask.php");

//include Tour Class		
include_once(CLASS_DIR . "/class_Tour.php");

//Include Artist Class 
include_once (CLASS_DIR . "/class_Artist.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of Performance records from the DB
global $aPerformanceRecords;

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
			$thisPerformance = new Performance();
			$thisPerformanceTasks = new PerformanceTask();
			$form = new Form();

			//Get the ID query string parameter
			$nThisPerformanceID = $_REQUEST['ID'];

			//Get Artists for drop-down list
			$oArtists = new Artist();
			if (!$oArtists->getArtist())
			{
				//ERROR
			}

			//If an Artist ID is passed, go ahead and search products for that artist
			if (isset($_REQUEST['ARTIST_ID']) && $_REQUEST['ARTIST_ID'] != "")
			{
				$_POST['selArtist'] = $_REQUEST['ARTIST_ID'];
				$_POST['btnSearch'] = "Search";
			}

			//If an ID was passed to the page, retrieve that record for update		
			if (!is_null($nThisPerformanceID)) {

				$thisPerformance->nPerformanceID = $nThisPerformanceID;

				//Search the Database for records matching the search criteria			
				if ($thisPerformance->getPerformance()) {

					//Records found
					if (sizeof($thisPerformance->aPerformanceRecords) > 0) {

						//Only One Record should be returned.  Add this to the form field array
						//so that it displays in the form fields and to the values in the
						//current Object.
						loadPerformance($thisPerformance->aPerformanceRecords[0], $form);

						$form->sMessage = "Update record.";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//The record was not found
						$form->sMessage = "Performance record not found.";
						$form->nMessageType = MESSAGE_TYPE_WARNING;
						$form->nFormMode = FORM_MODE_NEW;
					}
				} else {
					//Error
					$form->sMessage = $thisPerformance->sErrorMessage;
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
					buildPerformanceObject($thisPerformance);

					//Insert record
					if ($thisPerformance->insertPerformance()) {

						//reload Performance
						$nNewPerformanceID = $thisPerformance->nPerformanceID;
						$thisPerformance = new Performance();
						$thisPerformance->nPerformanceID = $nNewPerformanceID;

						if ($thisPerformance->getPerformance()) {
							//Load the form fields with the newly populated object
							loadPerformance($thisPerformance->aPerformanceRecords[0], $form);
							//Store the ID of the performance
							$nThisPerformanceID = $thisPerformance->aPerformanceRecords[0]->nPerformanceID;

							//Success
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->sMessage = "Performance Added";
							$form->nFormMode = FORM_MODE_EDIT;
						} else {
							//Problem reloading screen
							clearFormFields($form);
							$form->nMessageType = MESSAGE_TYPE_ERROR;
							$form->sMessage = "Performance Added, but error occured while reloading the performance data";
							$form->nFormMode = FORM_MODE_NEW;
						}
					} else {

						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ADD RECORD FAILED: {$thisPerformance->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   UPDATE   *
				// **************
				else if (isset($_POST["btnUpdate"])) {

					//Load values from form field array into DB object
					buildPerformanceObject($thisPerformance);

					//Update record
					if ($thisPerformance->updatePerformance()) {

						//reload Performance
						$thisPerformance->getPerformance();

						//Load the form fields with the newly populated DB object						
						loadPerformance($thisPerformance->aPerformanceRecords[0], $form);
						//Store the ID of the performance
						$nThisPerformanceID = $thisPerformance->aPerformanceRecords[0]->nPerformanceID;

						//Success
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->sMessage = "Performance Updated";
						$form->nFormMode = FORM_MODE_EDIT;
					} else {
						//Failure
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->sMessage = "ERROR: Update Failed - {$thisPerformance->sErrorMessage}";
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   DELETE   *
				// **************
				else if (isset($_POST["btnDelete"])) {

					//Load DB record
					buildPerformanceObject($thisPerformance);

					//Delete record
					if ($thisPerformance->deletePerformance()) {
						//Clear the form fields
						clearFormFields($form);

						//Success
						$form->sMessage = "Performance Deleted";
						$form->nMessageType = MESSAGE_TYPE_INFO;
						$form->nFormMode = FORM_MODE_NEW;
					} else {
						$form->sMessage = "DELETE FAILED: {$thisPerformance->sErrorMessage}";
						$form->nMessageType = MESSAGE_TYPE_ERROR;
						$form->nFormMode = FORM_MODE_EDIT;
					}
				}
				// **************
				// *   SEARCH   *
				// **************
				else if (isset($_POST["btnSearch"])) {
					//Load Array of Search Values
					buildPerformanceObject($thisPerformance);

					//Search the Database for records matching the search criteria			
					if ($thisPerformance->getPerformance()) {
						//No records found
						if (sizeof($thisPerformance->aPerformanceRecords) < 1) {
							$form->sMessage = "No Performance records found matching search criteria";
							$form->nMessageType = MESSAGE_TYPE_WARNING;
							$form->nFormMode = FORM_MODE_NEW;
						} else if (sizeof($thisPerformance->aPerformanceRecords) == 1) {
							//Only One Record returned.  Add this to the form field array
							//so that it displays in the form fields
							loadPerformance($thisPerformance->aPerformanceRecords[0], $form);
							//Store the ID of the performance
							$nThisPerformanceID = $thisPerformance->aPerformanceRecords[0]->nPerformanceID;

							$form->sMessage = "One Performance record found.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_EDIT;
						}
						//If Multiple records found, the array of search reults will be populated
						else {
							//Multiiple records returned
							$form->sMessage = "Select Performance record to edit from results list below.";
							$form->nMessageType = MESSAGE_TYPE_INFO;
							$form->nFormMode = FORM_MODE_SELECT;
						}
					} else {
						//Attempt to get records failed
						$form->sMessage = $thisPerformance->sErrorMessage;
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


						foreach ($thisPerformance->aPerformanceRecords as $oPerformanceRecord) {

							//Alternate the result style
							if ($sResultStyleClass == RESULT_STYLE_CLASS) {
								$sResultStyleClass = RESULT_STYLE_CLASS_ALT;
							} else {
								$sResultStyleClass = RESULT_STYLE_CLASS;
							}
							echo "<div class='row {$sResultStyleClass}'>";

							//The first column is the ID and is used to build a link
							$i = 1;

							echo "	<div class='col-xs-12 col-sm-2 col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->nPerformanceID}'>{$oPerformanceRecord->sPerformanceName}</A></div>";
							echo "	<div class='col-xs-12 col-sm-3 col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->nPerformanceID}'>{$oPerformanceRecord->dtPerformanceDate}</A></div>";
							echo "	<div class='hidden-xs hidden-sm col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->nPerformanceID}'>{$oPerformanceRecord->sPerformanceTime}</A></div>";
							echo "	<div class='hidden-xs hidden-sm col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->nPerformanceID}'>{$oPerformanceRecord->sLocation}</A></div>";
							echo "	<div class='col-xs-12 col-sm-5 col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->nPerformanceID}'>{$oPerformanceRecord->sLocationCity}</A></div>";
							echo "	<div class='col-xs-12 col-sm-2 col-md-2 {$sResultStyleClass}'><A HREF='./PerformanceMaintenance.php?ID={$oPerformanceRecord->nPerformanceID}'>{$oPerformanceRecord->sLocationState}</A></div>";
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
							echo "<a href='" . ADMIN_DIR . "/PerformanceTaskMaintenance.php?PERFORMANCE_ID={$nThisPerformanceID}&PERFORMANCE_NAME={$thisPerformance->aPerformanceRecords[0]->sPerformanceName}' class='secondaryLinkButton'>Add Task</a>";
						}
						?>

					</div>
					<div class="col-xs-12 FieldGroupTitle">DETAILS</div>
					<?php
					// The performance object will have info if a search returned one record.  It will have the record in its array
					// if a performance ID was passed in the query string
					if(empty($thisPerformance->nPerformanceID)) {
						$thisPerformance->nPerformanceID = $thisPerformance->aPerformanceRecords[0]->nPerformanceID;
					}
					if(!empty($thisPerformance->nPerformanceID)){
						$thisPerformanceTasks->nPerformanceID = $thisPerformance->nPerformanceID;
						if ($thisPerformanceTasks->getPerformanceTask()) {
							if(sizeof($thisPerformanceTasks->aPerformanceTaskRecords) > 0){
								if($oPerformanceTask->bComplete){
									echo "<del> ";
								}
								echo "<div class=\"col-xs-12 FieldGroup\">";
								echo "TASKS:";
								foreach ($thisPerformanceTasks->aPerformanceTaskRecords as $oPerformanceTaskRecord) {
									if($oPerformanceTaskRecord->bComplete){
										echo "<del> ";
									}
									echo "<div><A HREF='./PerformanceTaskMaintenance.php?PERFORMANCE_TASK_ID={$oPerformanceTaskRecord->nPerformanceTaskID}'>{$oPerformanceTaskRecord->sDescription}</A></div>";
									if($oPerformanceTaskRecord->bComplete){
										echo "</del> ";
									}
								}
								echo "</div>";
							}
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
								renderDatePicker("PerformanceDate", $thisPerformance->aPerformanceRecords[0]->dtPerformanceDate);
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
								renderDatePicker("BookedDate", $thisPerformance->aPerformanceRecords[0]->dtBookedDate);

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
	function buildPerformanceObject($oPerformance)
	{

		//Load the Array used to populate the form fields based on the newly loaded object
		$oPerformance->nPerformanceID = $_POST['hdnPerformanceID'];
		$oPerformance->nTourID = $_POST['hdnTourID'];
		$oPerformance->nArtistID = $_POST['selArtist'];

		$oPerformance->sPerformanceName = html_entity_decode($_POST['txtPerformanceName'], ENT_QUOTES);
		$oPerformance->bFuzzyNameSearch = TRUE;
		$oPerformance->sPerformanceWebsite = html_entity_decode($_POST['txtPerformanceWebsite'], ENT_QUOTES);
		$oPerformance->sPerformanceTime = html_entity_decode($_POST['txtPerformanceTime'], ENT_QUOTES);
		$oPerformance->sLocation = html_entity_decode($_POST['txtLocation'], ENT_QUOTES);
		$oPerformance->bFuzzyLocationSearch = TRUE;
		$oPerformance->sLocationWebsite = html_entity_decode($_POST['txtLocationWebsite'], ENT_QUOTES);
		$oPerformance->sLocationAddr1 = html_entity_decode($_POST['txtLocationAddr1'], ENT_QUOTES);
		$oPerformance->sLocationAddr2 = html_entity_decode($_POST['txtLocationAddr2'], ENT_QUOTES);
		$oPerformance->sLocationCity = html_entity_decode($_POST['txtLocationCity'], ENT_QUOTES);
		$oPerformance->sLocationState = html_entity_decode($_POST['txtLocationState'], ENT_QUOTES);
		$oPerformance->sLocationZip = html_entity_decode($_POST['txtLocationZip'], ENT_QUOTES);
		$oPerformance->sDescription = html_entity_decode($_POST['txtDescription'], ENT_QUOTES);
		$oPerformance->sAdmission = html_entity_decode($_POST['txtAdmission'], ENT_QUOTES);
		$oPerformance->sContactName = html_entity_decode($_POST['txtContactName'], ENT_QUOTES);
		$oPerformance->sContactEmail = html_entity_decode($_POST['txtContactEmail'], ENT_QUOTES);
		$oPerformance->sContactPhone = html_entity_decode($_POST['txtContactPhone'], ENT_QUOTES);
		$oPerformance->sNotes = html_entity_decode($_POST['txtNotes'], ENT_QUOTES);
		$oPerformance->sContract = $_POST['selContract'];
		$oPerformance->sAirfare = $_POST['selAirfare'];
		$oPerformance->sHotel = $_POST['selHotel'];
		$oPerformance->sRentalCar = $_POST['selRentalCar'];

		$oPerformance->nBookedAmount = html_entity_decode($_POST['txtBookedAmount'], ENT_QUOTES);

		//Pre-Recorded Flag
		if ($_POST['chkPreRecorded'] == "PreRecorded") {
			$oPerformance->bPreRecorded = TRUE;
		}

		//Adult Show Flag
		if ($_POST['chkAdultShow'] == "AdultShow") {
			$oPerformance->bAdultShow = TRUE;
		}

		// Colorado Sessions Flag
		if ($_POST['chkColoradoSessions'] == "ColoradoSessions") {
			$oPerformance->bColoradoSessions = TRUE;
		}

		//Performance Date	
		$dtPerformanceDate = isset($_REQUEST["PerformanceDate"]) ? $_REQUEST["PerformanceDate"] : "";
		if ($dtPerformanceDate > "0000-00-00") {
			//If no datepicker is displayed, use the hidden field
			$dtPerformanceDate = isset($_POST["PerformanceDate"]) ? $_POST["PerformanceDate"] : "";
		}

		if ($dtPerformanceDate > "0000-00-00") {
			$oPerformance->dtPerformanceDate  = $dtPerformanceDate;
		}

		//Booked Date	
		$dtBookedDate = isset($_REQUEST["BookedDate"]) ? $_REQUEST["BookedDate"] : "";
		if ($dtBookedDate > "0000-00-00") {
			//If no datepicker is displayed, use the hidden field
			$dtBookedDate = isset($_POST["BookedDate"]) ? $_POST["BookedDate"] : "";
		}

		if ($dtBookedDate > "0000-00-00") {
			$oPerformance->dtBookedDate  = $dtBookedDate;
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
	function loadPerformance(&$oPerformance, $form)
	{

		if (!is_null($oPerformance->nPerformanceID)) {
			//Load Hidden Fields
			$_POST['hdnPerformanceID'] = $oPerformance->nPerformanceID;
			$_POST['hdnTourID'] = $oPerformance->nTourID;
			$_POST['selArtist'] = $oPerformance->nArtistID;

			$_POST['txtPerformanceName'] = htmlentities($oPerformance->sPerformanceName, ENT_QUOTES);
			$_POST['txtPerformanceWebsite'] = htmlentities($oPerformance->sPerformanceWebsite ?? "", ENT_QUOTES);
			$_POST['PerformanceDate'] = htmlentities($oPerformance->dtPerformanceDate, ENT_QUOTES);
			$_POST['txtPerformanceTime'] = htmlentities($oPerformance->sPerformanceTime, ENT_QUOTES);
			$_POST['txtLocation'] = htmlentities($oPerformance->sLocation, ENT_QUOTES);
			$_POST['txtLocationWebsite'] = htmlentities($oPerformance->sLocationWebsite ?? "", ENT_QUOTES);
			$_POST['txtLocationAddr1'] = htmlentities($oPerformance->sLocationAddr1, ENT_QUOTES);
			$_POST['txtLocationAddr2'] = htmlentities($oPerformance->sLocationAddr2, ENT_QUOTES);
			$_POST['txtLocationCity'] = htmlentities($oPerformance->sLocationCity, ENT_QUOTES);
			$_POST['txtLocationState'] = htmlentities($oPerformance->sLocationState, ENT_QUOTES);
			$_POST['txtLocationZip'] = htmlentities($oPerformance->sLocationZip, ENT_QUOTES);
			$_POST['txtDescription'] = htmlentities($oPerformance->sDescription, ENT_QUOTES);
			$_POST['txtAdmission'] = htmlentities($oPerformance->sAdmission, ENT_QUOTES);
			$_POST['txtContactName'] = htmlentities($oPerformance->sContactName, ENT_QUOTES);
			$_POST['txtContactEmail'] = htmlentities($oPerformance->sContactEmail, ENT_QUOTES);
			$_POST['txtContactPhone'] = htmlentities($oPerformance->sContactPhone, ENT_QUOTES);
			$_POST['txtBookedAmount'] = htmlentities($oPerformance->nBookedAmount ?? '0', ENT_QUOTES);
			$_POST['BookedDate'] = htmlentities($oPerformance->dtBookedDate ?? '0000-00-00', ENT_QUOTES);
			$_POST['txtNotes'] = htmlentities($oPerformance->sNotes ?? "", ENT_QUOTES);
			$_POST['selContract'] = htmlentities($oPerformance->sContract ?? "", ENT_QUOTES);
			$_POST['selAirfare'] = htmlentities($oPerformance->sAirfare ?? "", ENT_QUOTES);
			$_POST['selHotel'] = htmlentities($oPerformance->sHotel ?? "", ENT_QUOTES);
			$_POST['selRentalCar'] = htmlentities($oPerformance->sRentalCar ?? "", ENT_QUOTES);
			$_POST['txtLastUpdate'] = htmlentities($oPerformance->dtLastUpdate, ENT_QUOTES);

			$_POST['chkPreRecorded'] = $oPerformance->bPreRecorded;
			$_POST['chkAdultShow'] = $oPerformance->bAdultShow;
			$_POST['chkColoradoSessions'] = $oPerformance->bColoradoSessions;
		} else {
			//Load Form field values into array 
			foreach ($_POST as $fieldName => $fieldValue) {

				//$_POST[$fieldName]= htmlentities(stripslashes($fieldValue));
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