<?php
	//Don't Display Notice messages from PHP Server
	error_reporting(E_ALL ^ E_NOTICE);

	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//Common Functions
   	include (INCLUDE_DIR . "/commonFunctions.php");

	//include CD Class
	include_once (CLASS_DIR . "/class_UnavailableDate.php");

	//Page Name 
	$sPageName = "Availability";
	//Page Name 
	$sPageTitle = "Availability";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('More','more.php'),
		); 

	$sActiveMenuItem = HOME_ACTIVE;	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
?>
<BODY>
	<div class="container-fluid">
		<div class="row">
		<?php
			//include(INCLUDE_DIR . "/header.php");
		?>		
		<H1 style="text-align: center;">Steve Weeks Availability</H1>
		</div>
		<div class="row">
		<div class="col-xs-12">
		<center>
<TABLE align="center">
  <TR>
    <TD align="center">
	 	<?php 
		$date =time () ;  //This puts the day, month, and year in seperate variables 

	//Array of year links to be displayed 
	$nThisYear = date('Y', $date); 
	$aYears[0] = $nThisYear -1;
 	$aYears[1] = $nThisYear;
 	$aYears[2] = $nThisYear + 1;
	
	$month = isset($_GET['month']) ? $_GET['month'] : NULL; 
	$year = isset($_GET['year']) ? $_GET['year'] : NULL; 
 
   	if ($year == '') 
   	{
 		$year = date('Y', $date);
   	}
	$day = date('d', $date) ; 
	
	//Get Performances for given year
	$oPerformances = new Performance();
	$oPerformances->dtPerformanceDate = $year;

	//Get Unavailable Dates for the given year
	if(!$oPerformances->getPerformance())
	{
		echo "ERROR RETRIEVING PERFORMANCES FOR YEAR " . $oPerformances->dtPerformanceDate . ": " . $oPerformances->sErrorMessage;
	}

	//Get Unavailable Dates ft given year
	$oUnavailableDates = new UnavailableDate();
	$oUnavailableDates->nYear = $year;
	
	//Get Unavailable Dates for the given year
	if(!$oUnavailableDates->getUnavailableDate())
	{
		echo "ERROR RETRIEVING UNAVAILABLE DATES FOR YEAR " . $oUnavailableDates->nYear . ": " . $oUnavailableDates->sErrorMessage;
	}

	
?>
	</TD>
  </TR>
  <TR>
    <TD id="years-container">
		<?php
		foreach($aYears as $nYear)
		{
			if ($nYear == $year)
			{
				echo "<div class=\"year-selector year-selected\">{$nYear}</div>";
			}
			else
			{
				echo "<div class=\"year-selector year-unselected\"><A HREF=\"Availability.php?year={$nYear}\">{$nYear}</A></div>";
			}
		}
		?>
	</TD>
  </TR>
  <TR>
    <TD align="center">
<?php

 for ($nCalendarMonth=1;$nCalendarMonth<13;$nCalendarMonth++)
 {

	//echo "MONTH: " . $month . " " . $nCalendarMonth . "<BR>";

	if ($month =='' || $month == $nCalendarMonth )
	{
 		$first_day = mktime(0,0,0,$nCalendarMonth, 1, $year) ; 
	

		 //This gets us the month name 
 		$title = date('F', $first_day) ; 
 
		 //Here we find out what day of the week the first day of the month falls on 
 		$day_of_week = date('D', $first_day) ; 

		//echo "<FONT SIZE=5>AVAILABILITY FOR " . $title . " " . $year . "</FONT>";
		
 
		//Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
 		switch($day_of_week)
		{ 
 			case "Sun": $blank = 0; break; 
			case "Mon": $blank = 1; break; 
			case "Tue": $blank = 2; break; 
			case "Wed": $blank = 3; break; 
			case "Thu": $blank = 4; break; 
			case "Fri": $blank = 5; break; 
			case "Sat": $blank = 6; break; 
		} 
 

 		//We then determine how many days are in the current month
 		$days_in_month = cal_days_in_month(0, $nCalendarMonth, $year) ; 

		//Here we start building the table heads 
		echo "<div class=\"table-responsive\">";
		echo "<table class=\"calendar-month\"  border=1 width=600 >";
		echo "<tr class=\"title-month-year\"><th class=\"title-month-year\" colspan=7> $title $year </th></tr>";
		 echo "<tr class=\"title-days\">";
		 echo "<td class=\"header-day\" width=\"100px\">S</td>";
		 echo "<td class=\"header-day\" width=\"100px\">M</td>";
		 echo "<td class=\"header-day\" width=\"100px\">T</td>";
		 echo "<td class=\"header-day\" width=\"100px\">W</td>";
		 echo "<td class=\"header-day\" width=\"100px\">T</td>";
		 echo "<td class=\"header-day\" width=\"100px\">F</td>";
		 echo "<td class=\"header-day\" width=\"100px\">S</td></tr>";

 		//This counts the days in the week, up to 7
 		$day_count = 1;

 		echo "<tr class=\"calendar-week\">";
		
 		//first we take care of those blank days
 		while ( $blank > 0 ) 
 		{ 
 			echo "<td class=\"calendar-day\"></td>"; 
 			$blank = $blank-1; 
 			$day_count++;
 		} 
 
  		//sets the first day of the month to 1 
 		$day_num = 1;

 		//count up the days, untill we've done all of them in the month
 		while ( $day_num <= $days_in_month ) 
 		{ 
			 
			$sReason = null;
			
 			if ($day_num < 10)
			{
  				$sPerformanceDay = "0" . $day_num;
			}
			else
			{
  				$sPerformanceDay = $day_num;
			}
			
			//We need a 2-digit month number as a string
 			if ($nCalendarMonth < 10)
			{
  				$sCalendarMonth = "0" . $nCalendarMonth;
			}
			else
			{
  				$sCalendarMonth = $nCalendarMonth;
			}
				
			$bAvailable = TRUE;

			//See if I'm Performing
			foreach ($oPerformances->aPerformanceRecords as $oPerformance)
			{
				$sPerformanceLocation = null;

				if ($oPerformance->dtPerformanceDate == $year . "-" . $sCalendarMonth . "-" . $sPerformanceDay )
				{
					if($oPerformance->sLocationCity || $oPerformance->sLocationState){
						$sPerformanceLocation .= $oPerformance->sLocationCity . ", " . $oPerformance->sLocationState ;
					}
					if($oPerformance->bPreRecorded) {
						$sReason = "Pre-recorded show";
					}
					else {
						$bAvailable = FALSE; 
						
						if($oPerformance->bBooked)
						{
							$sReason = "BOOKED";
						}
						else 
						{
							$sReason = "TENATIVE";
						}
						
						break;
					}
				}
				
			}

			
			if($bAvailable)
			{
				foreach ($oUnavailableDates->aUnavailableDateRecords as $oUnavailableDate)
				{
					if($oUnavailableDate->dtUnavailableDate == $year . "-" . $sCalendarMonth . "-" . $sPerformanceDay )
					{
						$bAvailable = FALSE; 
						$sReason = $oUnavailableDate->sReason;
						break;
					}
				}
			}

 			echo "<td class=\"calendar-day "; 
			
			if (!$bAvailable)
			{
				echo " unavailable";
			}
			else
			{
				echo " available";
			}

			echo "\">";
			echo "<div class=\"day-content\">";
			echo "<div class=\"number-container\"><div class=\"day-number\">{$day_num}</div></div>";
			
			if ($bAvailable)
			{
				echo "<div class=\"availability\">AVAILABLE</div>";
				echo "<div class=\"reason\">{$sReason}</div>";
			}
			else
			{

				echo "<div class=\"availability\">UNAVAILABLE</div>";
				echo "<div class=\"reason\">{$sReason}</div>";
				if($sPerformanceLocation) {
					echo "<div class=\"location\">{$sPerformanceLocation}</div>";
				}
				echo "</div>";
			}
			echo "</td>";

			$day_num++; 
			$day_count++;

			 //Make sure we start a new row every week
			 if ($day_count > 7)
 			{
				 echo "</tr><tr>";
				 $day_count = 1;
 			}
 		} 
 
 		//Finaly we finish out the table with some blank details if needed
 		while ( $day_count >1 && $day_count <=7 ) 
		{ 
			 echo "<td> </td>"; 
 			$day_count++; 
 		} 
 
		 echo "</tr></table>";
		 echo "</div>"; 
		echo "<BR><HR><BR>";
 	}
 
 }
 
 ?>
 				</td>
			</tr>
		</table>
	</TD>
  </TR>
  <TR>
    <TD align="center">
      <?php 
	  include ("./includes/footer.php");
      ?>
    </TD>
  </TR>
</TABLE>
</center>
</div>
</div>
</BODY>
</HTML>
<style>
	#years-container{
		text-align: center;
		width: 100%;
		padding: 10px;
	}
	.year-selector {
		display: inline-block;
		width: 60px;
		padding: 5px;
		margin: 0 5px;
		border: 2px solid darkred;
		border-radius: 5px;
	}
	.year-selected {
		background-color: darkred;
		color: white;
	}
	.year-unselected {
		background-color: white;
		color: darkred;
	}

	.year-unselected:hover{
		background-color: darkred;
	}
	.year-unselected:hover a{
		color: white;
	}
	.year-unselected a {
		color: darkred;
	}
.title-month-year {
	text-align: center;
	background-color: darkgoldenrod;
    color: white;
}

.header-day {
	background-color: goldenrod;
}
.number-container {
	width: 100%;
	padding: 3px;
	text-align: left;
	float: left;
}
.day-number{
	width: 20px;
	height: 20px;
	background-color: antiquewhite;
	border: 2px solid darkgoldenrod;
	color: black;
}
.calendar-day {
	height: 75px;
	min-height: 75px;
	width: 75px;
	font-size: 12px;
	text-align: center;
}
.day-content{
	height: 100%;
}

.unavailable {
	background-color: #6699FF;
	color: white;
}
</style>

