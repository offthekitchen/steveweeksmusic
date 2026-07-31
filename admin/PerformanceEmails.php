<?php
/*
*******************************************************************
PerformanceEmails.php
This PHP file generates email verbiage for a specific performance.
NOTES
Date        Change
-------------------------------------------------------------
2017-05-01	Made Responsive
2018-06-04	Removed signatures and unneccessary code
2022-01-20	Adjusted Veribiage
2026-07-30	Migrated to new Datalayer Performance repository
*******************************************************************
*/	
	
error_reporting(E_ALL ^ E_NOTICE);
	
//This include defines the relative path to the root directory from this sub-directory
 include_once ("root.inc.php");

//inlcude web site settings
include_once ($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//inlcude admin settings
include_once (ADMIN_DIR . "/includes/AdminSettings.php");

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");

$sActiveMenuItem = PERFORMANCES_ACTIVE;	
$sPageName = "Performance Emails";
$sPageDescription = "Performance Emails";
$sFBImage = "";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<?php

		//Instantiate needed objects
		$performanceRepo = new \Datalayer\PerformanceRepository();
		$thisPerformance = null;
		$sPerformanceURL = '';

		//Get the ID query string parameter
		$nThisPerformanceID = $_REQUEST['ID'] ?? null;
		
		//If an ID was passed to the page, retrieve that record for update		
		if (!is_null($nThisPerformanceID) && $nThisPerformanceID !== '')
		{
			$thisPerformance = $performanceRepo->findById((int) $nThisPerformanceID);

			if ($thisPerformance)
			{
				$sPerformanceURL = "http://www.steveweeksmusic.com/schedule.php?year=" . date("Y",strtotime($thisPerformance->performanceDate)) . "&eventID=" . $thisPerformance->id . "#performance-" . $thisPerformance->id;
			}
		}

	?>	

<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
?>	
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 Title">
				Show Details
			</div>
			<div class="col-xs-12">
				<div class="table-responsive" style="font-size: 11px;">
					<table class="table table-striped">
						<tbody>
							<th>Date</th>
							<th>Time</th>
							<th>Location</th>
							<th>Contact</th>
							<?php
							if ($thisPerformance) {
								echo "<tr>";
								echo "<td>";
								echo date("l", strtotime($thisPerformance->performanceDate)) . "<br>" . date("m-d-Y", strtotime($thisPerformance->performanceDate));
								echo "</td>";
								echo "<td>";
								echo $thisPerformance->performanceTime;
								echo "</td>";
								echo "<td>";
								echo "{$thisPerformance->location}<br>";
								echo "{$thisPerformance->locationAddr1}<br>";
								echo $thisPerformance->locationAddr2 ? "{$thisPerformance->locationAddr2}<br>" : "";
								echo "{$thisPerformance->locationCity}, {$thisPerformance->locationState} {$thisPerformance->locationZip}<br>";
								echo "</td>";
								echo "<td>";
								echo $thisPerformance->contactName ? "{$thisPerformance->contactName}<br>" : "";
								echo $thisPerformance->contactPhone ? "{$thisPerformance->contactPhone}<br>" : "";
								echo $thisPerformance->contactEmail ? "{$thisPerformance->contactEmail}<br>" : "";
						
								echo "</td>";
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 Title">
				Verification Email (right after LOA) 
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12"><p>Thanks so much for getting the Letter of Agreement back to  me for my performance there at <b> <?php echo $thisPerformance ? $thisPerformance->location . " (" . date("l",strtotime($thisPerformance->performanceDate)) . ", " . date("m-d-Y",strtotime($thisPerformance->performanceDate)) . " at " . $thisPerformance->performanceTime . ")" : "" ?></b>.&nbsp;&nbsp;&nbsp; Here's some info for you when you get a chance.<br />
			  <br />
                <strong>WEB SITE LISTING</strong><br />
				I&rsquo;ve posted the event on my web site at the link below.&nbsp; Let me know if you&rsquo;d like to see any  adjustments to the information or verbiage.<br />
			  	<?php
				if ($thisPerformance) {
					echo "<BR><a href='" . $sPerformanceURL . "'>";
					echo $thisPerformance->location . " (" . date("l",strtotime($thisPerformance->performanceDate)) . ", " . date("m-d-Y",strtotime($thisPerformance->performanceDate)) . " at " . $thisPerformance->performanceTime . ")";
					echo "</a>";
				}
				?>
				<br /><br />
		      <p><strong>PERFORMANCE SPACE AND SOUND</strong><br />
			    If you could tell me a little about the performance space so  that I can show up prepared with the appropriate sound system I&rsquo;d greatly  appreciate it.&nbsp; I don&rsquo;t need technical  specifications, just a basic idea of the space to give me an idea of what to  expect.&nbsp; <br />
			    My show easily scales, and I&rsquo;ve performed everywhere from  &ldquo;between the stacks&rdquo; (literally) to larger theaters.<br />
			    If the space has a sound system, I&rsquo;ll happily use that.&nbsp; Otherwise I&rsquo;ll provide all my own equipment  and only require access to an electrical outlet.<br />
  				<strong><br />
    			PROMOTION</strong><br />
			    You can find hi-res photos, verbiage, flyers etc. on  my press kit at:<br />
  <a href="http://www.steveweeksmusic.com/PressKit/">http://www.steveweeksmusic.com/PressKit/</a><br />
			    
				Please feel free to use anything you find there or anywhere  on my web site.<br />
  <br />
			    Also, I can create a custom video to help with promotion if you need.&nbsp;&nbsp;
				It would be like the one at the link below, but with the details of your show at the end instead.&nbsp;&nbsp;
				Just let me know if you'd like me to create the video, and I'll get it to you.<BR><BR>
				<a href="http://www.steveweeksmusic.com/PressKit/PromoVideoExample.php">http://www.steveweeksmusic.com/PressKit/PromoVideoExample.php</a><br /><br>
				
				
				We&rsquo;ll be in touch, but in the meantime, please don&rsquo;t  hesitate to contact me if you need anything.&nbsp; <br />
  <br />
			    Thanks so much and have a great day!</p>

			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 Title">
				3 Month Email
				</div>
		</div>
		<div class="row">
			<div class="col-xs-12"><p>Hope all is well with you.<br /><br />
			  I usually like to reach out about 3 months before a performance, and here we are!<br></br>
			  I think I'm all set on my end for the performance at <b> <?php echo $thisPerformance ? $thisPerformance->location . " (" . date("l",strtotime($thisPerformance->performanceDate)) . ", " . date("m-d-Y",strtotime($thisPerformance->performanceDate)) . " at " . $thisPerformance->performanceTime . ")" : "" ?></b>.
			  <br><br>Please don't hesitate to contact me if you have any questions or need anything.&nbsp;&nbsp;Otherwise, I'll get back in touch about a month out.<br><br>
			  I&rsquo;m really looking forward to it!<br /><br />
			  Take care,<br /><br />
			  Steve Weeks<br />
		    719-640-9986</p>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 Title">
				1 Month Email
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12"><p>Hope all is well with you.<br /><br />
			  Since it&rsquo;s about a month from my performance there at <b> <?php echo $thisPerformance ? $thisPerformance->location . " (" . date("l",strtotime($thisPerformance->performanceDate)) . ", " . date("m-d-Y",strtotime($thisPerformance->performanceDate)) . " at " . $thisPerformance->performanceTime . ")" : "" ?></b>, I thought I&rsquo;d  check in to see if you need anything.&nbsp; <br /><br />
			  I&rsquo;m attaching a copy of the invoice, so you have it.&nbsp; I do not expect payment until the performance  is complete, but I thought you might want the invoice ahead of time.<br /><br />
			  Please note, that any checks should be made payable to <b>&rdquo;Steve Weeks&rdquo; &rdquo;John S Weeks&rdquo;</b> or 
			  <b>&rdquo;Off the Kitchen LLC&rdquo;</b>, not Steve Weeks Music (my  bank has a little heartburn with that).<br /><br />
			  I usually arrive at least 1 hour before my performance to  give myself plenty of time to load in and set up.&nbsp; I provide all my own equipment and only  require access to an electrical outlet. <br /><br />
			  I think I&rsquo;m all set on my end, but please feel free to  contact me if there&rsquo;s anything you need or anything you think I need to know.<br /><br />
			  I&rsquo;ll check back in one last time before I leave.<br /><br />
			  I&rsquo;m really looking forward to it!<br /><br />
			  Take care,<br /><br />
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 Title">
				1 Week Email
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12"><p>Well, it&rsquo;s only about a week until my performance there at <b> <?php echo $thisPerformance ? $thisPerformance->location . " (" . date("l",strtotime($thisPerformance->performanceDate)) . ", " . date("m-d-Y",strtotime($thisPerformance->performanceDate)) . " at " . $thisPerformance->performanceTime . ")" : "" ?></b>, so I thought I&rsquo;d  check in one last time.&nbsp;&nbsp;I think I'm set on my end, but please contact me if you need anything. &nbsp; <br />
			    <br />
				You can reach me on my cell phone if you  need to contact me <strong>719-640-9986</strong>.<br />
				<BR />
				Otherwise, I&rsquo;ll see you soon!<br /><BR />
				Thanks!<br /><BR />
			</div>
		</div>
	</div>
	
</body>
</html>
