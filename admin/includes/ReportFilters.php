<?php
/*
**********************************************************************
*  ReportFilters.php
* 
*   This file contains filters for narrowing reporting data.  The
*   filters can be turned enabled via variables
Date        Change
-------------------------------------------------------------
2016-01-05  Added Song and CD filters
2017-04-15	Made Responsive
2024-04-12	Added Artist Dropdown
2024-05-09	Added Colorado Sessions Filter
**********************************************************************
*/

//CALENDAR PICKER DATE FILTERS
if($bCalendarFilter == TRUE)
{
	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");

	echo "<div class='row'>";
	echo "<div class='col-xs-6'>";
	echo "START DATE:<BR />"; 
	$myCalendar = new tc_calendar("StartDate", true, false);	  
	$myCalendar->setIcon(ADMIN_IMG_DIR . "/iconCalendar.gif");
	$myCalendar->setPath("./calendar/");	  
	$myCalendar->setYearInterval(2025, 2030);	  
	$myCalendar->dateAllow('2025-01-01', '2030-03-01');	  
	$myCalendar->setDateFormat('j F Y');	  
	$myCalendar->setAlignment('left', 'bottom');	  
	//Parse Start Date
	if ($_POST['StartDate'] > "0000-00-00")
	{
		//Must parse out date parts to set the date on the calendar picker
		$aStartDateParts = explode("-",$_POST['StartDate']);
		$myCalendar->setDate($aStartDateParts[2], $aStartDateParts[1], $aStartDateParts[0]); 	  
	}

	$myCalendar->writeScript();	  
	echo "</div>";
	echo "<div class='col-xs-6'>";
	echo "END DATE:<BR />"; 
		$myCalendar = new tc_calendar("EndDate", true, false);	  
		$myCalendar->setIcon(ADMIN_IMG_DIR . "/iconCalendar.gif");
		$myCalendar->setPath("./calendar/");	  
		$myCalendar->setYearInterval(2025, 2030);	  
		$myCalendar->dateAllow('2025-01-01', '2030-03-01');	  
		$myCalendar->setDateFormat('j F Y');	  
		$myCalendar->setAlignment('left', 'bottom');	  
		//Parse End Date
		if ($_POST['EndDate'] > "0000-00-00")
		{
			//Must parse out date parts to set the date on the calendar picker
			$aEndDateParts = explode("-",$_POST['EndDate']);
			$myCalendar->setDate($aEndDateParts[2], $aEndDateParts[1], $aEndDateParts[0]); 	  
		}

		$myCalendar->writeScript();	  
	  echo "</div>";
	  echo "</div>";
} 

// YEAR  AND QUARTER FILTERS
if($bYearFilter == TRUE || $bQuarterFilter == TRUE)
{
	echo "<div class='row'>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "YEAR:";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<SELECT NAME=\"selYear\" ID=\"selYear\">";
	echo "<OPTION VALUE=\"0\">ALL</OPTION>";
	$nCurrentYear = date("Y");
	for ($nYear = 2003; $nYear <= $nCurrentYear+1; $nYear++) 
	{
		echo "<OPTION VALUE=\"{$nYear}\"";
		if ($nYear == $_POST['selYear'])
		{
			echo " SELECTED ";
		}
		echo ">{$nYear}</OPTION>";
	} 	
	echo "</SELECT>";
	echo "</div>";
	echo "</div>";

	if($bQuarterFilter == TRUE)
	{
		echo "<div class='row'>";
		echo "<div class='col-xs-12 col-sm-2'>";
		echo "QUARTER:";
		echo "</div>";
		echo "<div class='col-xs-12 col-sm-10'>";
		echo "<SELECT NAME=\"selQuarter\" ID=\"selQuarter\">";

		echo "<OPTION VALUE=\"0\"";
		echo ">ALL</OPTION>";
		echo "<OPTION VALUE=\"1\"";
		if ($_POST['selQuarter'] == 1)
		{
			echo " SELECTED ";
		}
		echo ">1st</OPTION>";
		echo "<OPTION VALUE=\"2\"";
		if ($_POST['selQuarter'] == 2)
		{
			echo " SELECTED ";
		}
		echo ">2nd</OPTION>";
		echo "<OPTION VALUE=\"3\"";
		if ($_POST['selQuarter'] == 3)
		{
			echo " SELECTED ";
		}
		echo ">3rd</OPTION>";
		echo "<OPTION VALUE=\"4\"";
		if ($_POST['selQuarter'] == 4)
		{
			echo " SELECTED ";
		}
		echo ">4th</OPTION>";

		echo "</SELECT>";
		echo "</div>";
		echo "</div>";
	} 
} 

// COLORADO SESSIONS FILTER
if($bColoradoSessionsFilter == TRUE) {
	echo "<div class='row'>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "</div>";	
	echo "<div class=\"col-xs-12 col-sm-10\">";
	echo "<div style=\"float: left; height: 30px; width: 30px;\">";
	echo "	<input type=\"checkbox\" name=\"chkColoradoSessions\" style=\"float: left;\" class=\"result-checkbox\" value=\"ColoradoSessions\"";
	 if ($_POST['chkColoradoSessions']) {
		echo " checked ";
	}; 
	echo"/>";
	echo "</div>";
	echo "<div style=\"float: left;\">";
	echo "	Colorado Sessions";
	echo "</div>";
	echo" </div>";
	echo" </div>";
}

// ARTIST FILTER
if($bArtistFilter == TRUE )
{
	renderArtistDropDown($_POST['selArtist']);
}
 //CATEGORY FILTER
if($bCategoryFilter == TRUE)
{
	//include Catergory Class		
 	include_once (CLASS_DIR . "/class_Category.php");

	echo "<div class='row'>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "CATEGORY:";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<SELECT NAME=\"selCategory\" ID=\"selCategory\">";
	echo "<OPTION VALUE=\"0\">ALL</OPTION>";

	$oCategories = new Category();
	if($oCategories->getCategory())
	{
		foreach($oCategories->aCategoryRecords as $oCategory)
		{
			echo "<OPTION VALUE=\"{$oCategory->nCategoryID}\" ";
			if ($oCategory->nCategoryID == $_POST['selCategory'])
			{
				echo " SELECTED ";
			}
			echo ">{$oCategory->sCategoryName}</OPTION>";

		}
	}

	echo "</SELECT>";
	echo "</div>";
	echo "</div>";
} 

//REVENUE TYPE FILTER
if($bRevenueTypeFilter == TRUE)
{
	//include Revenue Type Class		
 	include_once (CLASS_DIR . "/class_RevenueType.php");

	echo "<div class='row'>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "REVENUE TYPE:";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<SELECT NAME=\"selRevenueType\" ID=\"selRevenueType\">";
	echo "<OPTION VALUE=\"0\">ALL</OPTION>";

	$oRevenueTypes = new RevenueType();
	if($oRevenueTypes->getRevenueType())
	{
		foreach($oRevenueTypes->aRevenueTypeRecords as $oRevenueType)
		{
			echo "<OPTION VALUE=\"{$oRevenueType->nRevenueTypeID}\" ";
			if ($oRevenueType->nRevenueTypeID == $_POST['selRevenueType'])
			{
				echo " SELECTED ";
			}
			echo ">{$oRevenueType->sRevenueTypeName}</OPTION>";

		}
	}

	echo "</SELECT>";
	echo "</div>";
	echo "</div>";
} 
 
 //PRODUCT FILTER
if($bProductFilter == TRUE)
{
	//include Product Class		
 	include_once (CLASS_DIR . "/class_Product.php");

	echo "<div class='row'>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "PRODUCT:";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<SELECT NAME=\"selProduct\" ID=\"selProduct\">";
	echo "<OPTION VALUE=\"0\">ALL</OPTION>";

	$oProducts = new Product();
	if($oProducts->getProduct())
	{
		foreach($oProducts->aProductRecords as $oProduct)
		{
			echo "<OPTION VALUE=\"{$oProduct->nProductID}\" ";
			if ($oProduct->nProductID == $_POST['selProduct'])
			{
				echo " SELECTED ";
			}
			echo ">{$oProduct->sProductName}</OPTION>";

		}
	}

	echo "</SELECT>";
	echo "</div>";
	echo "</div>";
} 


// SONG FILTER 
if($bSongFilter == TRUE)
{
	//include Song Class		
 	include_once (CLASS_DIR . "/class_Song.php");

	echo "<div class='row'>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "SONG:";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<SELECT NAME=\"selProduct\" ID=\"selProduct\">";
	echo "<SELECT NAME=\"selSong\" ID=\"selSong\">";
	echo "<OPTION VALUE=\"0\">ALL</OPTION>";

	$oSongs = new Song();
	if($oSongs->getSong())
	{
		foreach($oSongs->aSongRecords as $oSong)
		{
			echo "<OPTION VALUE=\"{$oSong->nSongID}\" ";
			if ($oSong->nSongID == $_POST['selSong'])
			{
				echo " SELECTED ";
			}
			echo ">{$oSong->sSongName}</OPTION>";

		}
	}

	echo "</SELECT>";
	echo "</div>";
	echo "</div>";
} 


// CD FILTER 
if($bCDFilter == TRUE)
{
	//include CD Class		
 	include_once (CLASS_DIR . "/class_CD.php");

	echo "<div class='row'>";
	echo "<div class='col-xs-12 col-sm-2'>";
	echo "CD:";
	echo "</div>";
	echo "<div class='col-xs-12 col-sm-10'>";
	echo "<SELECT NAME=\"selCD\" ID=\"selCD\">";
	echo "<OPTION VALUE=\"0\">ALL</OPTION>";

	$oCDs = new CD();
	if($oCDs->getCD())
	{
		foreach($oCDs->aCDRecords as $oCD)
		{
			echo "<OPTION VALUE=\"{$oCD->nCDID}\" ";
			if ($oCD->nCDID == $_POST['selCD'])
			{
				echo " SELECTED ";
			}
			echo ">{$oCD->sCDName}</OPTION>";

		}
	}

	echo "</SELECT>";
	echo "</div>";
	echo "</div>";
} 


$sBaseQueryString = "?X=0";

//Add Selected Song to base Query String
if(isset($_POST['selSong']) && $_POST['selSong'] > 0)
{
	$sBaseQueryString .= "&SONG_ID={$_POST['selSong']}";
}

//Add Selected CD to base Query String
if(isset($_POST['selCD']) && $_POST['selCD'] > 0)
{
	$sBaseQueryString .= "&CD_ID={$_POST['selCD']}";
}

//Add Selected Product to base Query String
if(isset($_POST['selProduct']) && $_POST['selProduct'] > 0)
{
	$sBaseQueryString .= "&PRODUCT_ID={$_POST['selProduct']}";
}

if(isset($_POST['selYear']) && $_POST['selYear'] > 0)
{
	$sBaseQueryString .= "&YEAR={$_POST['selYear']}";
}

if(isset($_POST['selCategory']) && $_POST['selCategory'] > 0)
{
	$sBaseQueryString .= "&CATEGORY_ID={$_POST['selCategory']}";
}
 
 
?>
