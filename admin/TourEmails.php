<?php
/*
*******************************************************************
ToureEmails.php
This PHP file generates email verbiage for a specific tour.
NOTES
Date        Change
-------------------------------------------------------------
2017-05-01	Made Responsive
2022-01-20	Adjusted Veribiage
2024-03-09	Added Show List
*******************************************************************
*/

error_reporting(E_ALL ^ E_NOTICE);

//This include defines the relative path to the root directory from this sub-directory
include_once("root.inc.php");

//inlcude web site settings
include_once($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once(SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//inlcude admin settings
include_once(ADMIN_DIR . "/includes/AdminSettings.php");

//include Tour Class		
include_once(CLASS_DIR . "/class_Tour.php");

//include Performance Class		
include_once(CLASS_DIR . "/class_Performance.php");

//Array of Performance records from the DB
global $aTourRecords;

$sActiveMenuItem = PERFORMANCES_ACTIVE;
$sPageName = "Tour Emails";
$sPageDescription = "Tour Emails";
$sFBImage = "";
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<?php

	//Instantiate needed objects
	$thisTour = new Tour();

	//Get the ID query string parameter
	$nThisTourID = $_REQUEST['TOUR_ID'];

	//If an ID was passed to the page, retrieve that record for update		
	if (!is_null($nThisTourID)) {

		$thisTour->nTourID = $nThisTourID;

		//Search the Database for records matching the search criteria			
		if ($thisTour->getTour()) {
			//Records found
			if (sizeof($thisTour->aTourRecords) > 0) {

				//Get all the performances for the tour
				$oTourPerformances = new Performance();
				$oTourPerformances->nTourID = $nThisTourID;
				if (!$oTourPerformances->getPerformance()) {
					echo "ERROR RETRIEVING PERFORMANCES FOR TOUR " . $thisTour->aTourRecords[0]->sTourName;
				}

			} else {
				echo "NO TOUR DATA FOUND FOR ID: " . $thisTour->aTourRecords[0]->nTourID;
			}
		} else {
			echo "ERROR RETRIEVING TOUR DATA FOR " . $thisTour->aTourRecords[0]->sTourName;
		}
	} else {
		//NO ID Passed
	}

	?>

	<?php
	include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 Title">
				Show List
			</div>
			<div class="col-xs-12">
				<div class="table-responsive" style="width: 700px; font-size: 11px;">
					<table class="table table-striped">
						<tbody>
							<th>Date</th>
							<th>Time</th>
							<th>Location</th>
							<th>Contact</th>
							<?php
							foreach ($oTourPerformances->aPerformanceRecords as $oTourPerformance) {
								echo "<tr>";
								echo "<td>";
								echo date("l", strtotime($oTourPerformance->dtPerformanceDate)) . "<br>" . date("m-d-Y", strtotime($oTourPerformance->dtPerformanceDate));
								echo "</td>";
								echo "<td>";
								echo $oTourPerformance->sPerformanceTime;
								echo "</td>";
								echo "<td>";
								echo "{$oTourPerformance->sLocation}<br>";
								echo "{$oTourPerformance->sLocationAddr1}<br>";
								echo $oTourPerformance->sLocationAddr2 ? "{$oTourPerformance->sLocationAddr2}<br>" : "";
								echo "{$oTourPerformance->sLocationCity}, {$oTourPerformance->sLocationState} {$oTourPerformance->sLocationZip}<br>";
								echo "</td>";
								echo "<td>";
								echo $oTourPerformance->sContactName ? "{$oTourPerformance->sContactName}<br>" : "";
								echo $oTourPerformance->sContactPhone ? "{$oTourPerformance->sContactPhone}<br>" : "";
								echo $oTourPerformance->sContactEmail ? "{$oTourPerformance->sContactEmail}<br>" : "";
						
								echo "</td>";
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 Title">
				Verification Email (right after LOA)
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<p>Thanks so much for getting the Letter of Agreement back to me for my performances
					there.&nbsp;&nbsp;&nbsp; Here's some info for you when you get a chance.<br />
					<br />
					<strong>WEB SITE LISTING</strong><br />
					I&rsquo;ve posted the events on my web site at the links below.&nbsp; Let me know if you&rsquo;d
					like to see any adjustments to the information or verbiage.<br />
					<?php
					foreach ($oTourPerformances->aPerformanceRecords as $oTourPerformance) {
						$sPerformanceURL = "http://www.steveweeksmusic.com/schedule.php?year=" . date("Y", strtotime($oTourPerformance->dtPerformanceDate)) . "&eventID=" . $oTourPerformance->nPerformanceID . "#performance-" . $oTourPerformance->nPerformanceID;

						echo "<BR><a href='{$sPerformanceURL}'>";
						echo "{$oTourPerformance->sLocation} (" . date("l", strtotime($oTourPerformance->dtPerformanceDate)) . ", " . date("m-d-Y", strtotime($oTourPerformance->dtPerformanceDate)) . " at {$oTourPerformance->sPerformanceTime})";
						echo "</a>";
					}
					?>
					<br /><br />
				<p><strong>PERFORMANCE SPACE AND SOUND</strong><br />
					If you could tell me a little about the performance space so that I can show up prepared with the
					appropriate sound system I&rsquo;d greatly appreciate it.&nbsp; I don&rsquo;t need technical
					specifications, just a basic idea of the space to give me an idea of what to expect.&nbsp; <br />
					My show easily scales, and I&rsquo;ve performed everywhere from &ldquo;between the stacks&rdquo;
					(literally) to larger theaters.<br />
					If the space has a sound system, I&rsquo;ll happily use that.&nbsp; Otherwise I&rsquo;ll provide all
					my own equipment and only require access to an electrical outlet.<br />
					<strong><br />
						PROMOTION</strong><br />
					You can find hi-res photos, verbiage, flyers etc. on my press kit at:<br />
					<a
						href="http://www.steveweeksmusic.com/PressKit/">http://www.steveweeksmusic.com/PressKit/</a><br />

					Please feel free to use anything you find there or anywhere on my web site.<br /> <br />
					Also, I can create a custom video to help with promotion if you need.&nbsp;&nbsp;
					It would be like the one at the link below, but with the details of your show at the end
					instead.&nbsp;&nbsp;
					Just let me know if you'd like me to create the video, and I'll get it to you.<BR><BR>
					<a
						href="http://www.steveweeksmusic.com/PressKit/PromoVideoExample.php">http://www.steveweeksmusic.com/PressKit/PromoVideoExample.php</a><br /><br>

					<br>
					We&rsquo;ll be in touch, but in the meantime, please don&rsquo;t hesitate to contact me if you need
					anything.&nbsp; <br />
					<br />
					Thanks so much and have a great day!
				</p>
				Steve Weeks<br />
				719-640-9986<br />
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 Title">
				3 Month Email
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<p>Hope all is well with you.<br /><br />
					I usually like to reach out about 3 months before a performance, and here we are!<br></br>
					I think I'm all set on my end for the performances below.
					<br><br>Please don't hesitate to contact me if you have any questions or need
					anything.&nbsp;&nbsp;Otherwise I'll get back in touch about a month out.<br>
					<?php
					foreach ($oTourPerformances->aPerformanceRecords as $oTourPerformance) {
						$sPerformanceURL = "http://www.steveweeksmusic.com/schedule.php?year=" . date("Y", strtotime($oTourPerformance->dtPerformanceDate)) . "&eventID=" . $oTourPerformance->nPerformanceID . "#performance-" . $oTourPerformance->nPerformanceID;

						echo "<BR><a href='{$sPerformanceURL}'>";
						echo "{$oTourPerformance->sLocation} (" . date("l", strtotime($oTourPerformance->dtPerformanceDate)) . ", " . date("m-d-Y", strtotime($oTourPerformance->dtPerformanceDate)) . " at {$oTourPerformance->sPerformanceTime})";
						echo "</a>";
					}
					?>
					<br><br>I&rsquo;m really looking forward to it!<br /><br />
					Take care,<br /><br />
					Steve Weeks<br />
					719-640-9986
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 Title">
				1 Month Email
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<p>Hope all is well with you.<br /><br />
					Since it&rsquo;s about a month from my performances there, I thought I&rsquo;d check in to see if
					you need anything.&nbsp; <br /><br />
					I&rsquo;m attaching a copy of the invoice so you have it.&nbsp; I do not expect payment until the
					performance is complete, but I thought you might want the invoice ahead of time.<br /><br />
					Please note, that any checks should be made payable to <b>&rdquo;Steve Weeks&rdquo;</b>, not Steve
					Weeks Music (my bank has a little heartburn with that).<br /><br />
					I usually arrive at least 1 hour before my performance to give myself plenty of time to load in and
					set up.&nbsp; I provide all my own equipment and only require access to an electrical outlet.
					<br /><br />
					I think I&rsquo;m all set on my end, but please feel free to contact me if there&rsquo;s anything
					you need or anything you think I need to know.<br /><br />
					I&rsquo;ll check back in one last time before I leave.<br /><br />
					I&rsquo;m really looking forward to it!<br /><br />
					Take care,<br /><br />
					Steve Weeks<br />
					719-640-9986</p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 Title">
				1 Week Email
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<p>Well, it&rsquo;s only about a week until my performances there, so I thought I&rsquo;d check in one
					last time.&nbsp;&nbsp;I think I'm set on my end, but please contact me if you need anything. &nbsp;
					<br />
					<br />
					You can reach me on my cell phone if you need to contact me <strong>719-640-9986</strong>.<br />
					<BR />
					Otherwise, I&rsquo;ll see you soon!<br /><BR />
					Thanks!<br /><BR />
			</div>
		</div>
	</div>
</body>

</html>