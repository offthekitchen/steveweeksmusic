<?php
/*
*******************************************************************
admin/CommonFunctions.php
This PHP file generates contains common functions used by the admin
screens and reports 
NOTES
Date        Change
-------------------------------------------------------------
2015-10-19  Added setReportStyleClass()
2016-01-27  added renderCDDropDown and renderSongDropDown functions
2016-09-15  Sorted CD dropdown by name
2017-08-28	Styled Edit Artist button
2018-02-22	Added renderVendorDropdown() function and button to 
			renderProductDropdown()
2019-11-05	Added renderDatePicker() function
2020-09-30	Changed Datepicker to render hidden field if date is old
2022-05-16  Added NONE and ALL to Product dropdown
2024-02-01 Corrected error in tour dropdown
*******************************************************************
*/	
?>

<!-- Javascript required for Calendar picker -->
<script language="javascript" src="<?php echo ADMIN_JS_DIR ?>/calendar.js"></script>

<?php

//include Product Class		
include_once (CLASS_DIR . "/class_Product.php");

require_once (CLASS_DIR . "/tc_calendar.php");

/*
 ********************************************************************************
 * calculateSongTimeTotal
 * 
 * This function calculates the total performance time of an array of songs
 * NOTE:  The array must be PerformanceSongs objects
 ********************************************************************************
*/
function calculateSongTimeTotal($aSongs)
{
	$seconds = 0;

	foreach ($aSongs as $oPerformanceSongsRecord) {
		list($hour, $minute, $second) = explode(':', $oPerformanceSongsRecord->tEstimatedTime ?? '');
		$seconds += $hour * 3600;
		$seconds += $minute * 60;
		$seconds += $second;
	}

	$hours = floor($seconds / 3600);
	$seconds -= $hours * 3600;
	$minutes  = floor($seconds / 60);
	$seconds -= $minutes * 60;

	if ($hours < 10) {
		$hours = "0{$hours}";
	}

	if ($minutes < 10) {
		$minutes = "0{$minutes}";
	}

	if ($seconds < 10) {
		$seconds = "0{$seconds}";
	}

	$totalTimeFormated = "{$hours}:{$minutes}:{$seconds}";

	return $totalTimeFormated;
}

/*
 ********************************************************************************
 * renderProductDropDown
 * 
 * This function renders a drop-down list of Products
 ********************************************************************************
*/
function renderProductDropDown($nProductID)
{
 	echo "PRODUCT: \n"; 
	echo "<SELECT NAME=\"selProduct\" ID=\"selProduct\">\n";
	echo "<OPTION VALUE=\"\">ALL</OPTION>\n";
	echo "<OPTION VALUE=\"0\" ";
	if ($nProductID === 0){
		echo "SELECTED ";
	}
	echo "}>NONE</OPTION>\n";
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

	if ($nProductID > 0 )
	{
		echo "<A HREF='" . ADMIN_DIR . "/ProductMaintenance.php?ID={$nProductID}' class='secondaryLinkButton'>Edit Product</A>";
	}
}

/*
 ********************************************************************************
 * renderVendorDropDown
 * 
 * This function renders a drop-down list of Vendors
 ********************************************************************************
*/
function renderVendorDropDown($nVendorID)
{
	//include Song Class		
	include_once (CLASS_DIR . "/class_Vendor.php");
	
 	echo "VENDOR:\n"; 
	echo "<SELECT NAME=\"selVendor\" ID=\"selVendor\">\n";
	echo "<OPTION VALUE=\"0\">NONE</OPTION>\n";
	$oVendors = new Vendor();
	if($oVendors->getVendor())
	{
		foreach($oVendors->aVendorRecords as $oVendor)
		{
			echo "<OPTION VALUE=\"{$oVendor->nVendorID}\" ";
			if ($oVendor->nVendorID == $_POST['selVendor'])
			{
				echo " SELECTED ";
			}
			echo ">{$oVendor->sVendorName}</OPTION>";

		}
	}
	
	echo "</SELECT>";

	if ($nVendorID > 0 )
	{
		echo "<A HREF='" . ADMIN_DIR . "/VendorMaintenance.php?ID={$nVendorID}' class='secondaryLinkButton'>Edit Vendor</A>";
	}
}

/*
 ********************************************************************************
 * renderTaxCategoryDropDown
 * 
 * This function renders a drop-down list of Tax Categories
 ********************************************************************************
*/
function renderTaxCategoryDropDown($nTaxCategoryID)
{
	//include Song Class		
	include_once (CLASS_DIR . "/class_TaxCategory.php");
	
 	echo "TAX CATEGORY:\n"; 
	echo "<SELECT NAME=\"selTaxCategory\" ID=\"selTaxCategory\">\n";
	echo "<OPTION VALUE=\"0\">NONE</OPTION>\n";
	$oTaxCategories = new TaxCategory();
	if($oTaxCategories->getTaxCategory())
	{
		foreach($oTaxCategories->aTaxCategoryRecords as $oTaxCategory)
		{
			echo "<OPTION VALUE=\"{$oTaxCategory->nTaxCategoryID}\" ";
			if ($oTaxCategory->nTaxCategoryID == $_POST['selTaxCategory'])
			{
				echo " SELECTED ";
			}
			echo ">{$oTaxCategory->sTaxCategoryName}</OPTION>";

		}
	}
	
	echo "</SELECT>";

	if ($nTaxCategoryID > 0 )
	{
		echo "<A HREF='" . ADMIN_DIR . "/TaxCategoryMaintenance.php?ID={$nTaxCategoryID}' class='secondaryLinkButton'>Edit Tax Category</A>";
	}
}

/*
 ********************************************************************************
 * renderSongDropDown
 * 
 * This function renders a drop-down list of Songs
 ********************************************************************************
*/
function renderSongDropDown($nSongID)
{

//include Song Class		
include_once (CLASS_DIR . "/class_Song.php");

 	echo "Song:\n"; 
	echo "<SELECT NAME=\"selSong\" ID=\"selSong\">\n";
	echo "<OPTION VALUE=\"\">ALL</OPTION>\n";
	$oSongs = new Song();
	$oSongs->sOrderBy = NAME_ORDER;
	if($oSongs->getSong())
	{
		foreach($oSongs->aSongRecords as $oSong)
		{
			echo "<OPTION VALUE=\"{$oSong->nSongID}\" ";
			if ($oSong->nSongID == $nSongID)
			{
				echo " SELECTED ";
			}
			echo ">{$oSong->sSongName}</OPTION>";

		}
	}
	
	echo "</SELECT>";

	if ($nSongID > 0 )
	{
		echo "<A HREF='" . ADMIN_DIR . "/SongMaintenance.php?SONG_ID={$nSongID}' class='secondaryLinkButton'>Edit Song</A>";
	}
}

/*
 ********************************************************************************
 * renderArtistSongDropDown
 * 
 * This function renders a drop-down list of Songs for a given Artist
 ********************************************************************************
*/
function renderArtistSongDropDown($nSongID, $nArtistID)
{

//include Song Class		
include_once (CLASS_DIR . "/class_Song.php");

 	echo "Songs:\n"; 
	echo "<SELECT NAME=\"selSong\" ID=\"selSong\">\n";
	echo "<OPTION VALUE=\"\">ALL</OPTION>\n";
	$oSongs = new Song();
	$oSongs->sOrderBy = NAME_ORDER;
	$oSongs->nArtistID = $nArtistID;
	if($oSongs->getSong())
	{
		foreach($oSongs->aSongRecords as $oSong)
		{
			echo "<OPTION VALUE=\"{$oSong->nSongID}\" ";
			if ($oSong->nSongID == $nSongID)
			{
				echo " SELECTED ";
			}
			echo ">{$oSong->sSongName}</OPTION>";

		}
	}
	
	echo "</SELECT>";

	if ($nSongID > 0 )
	{
		echo "<A HREF='" . ADMIN_DIR . "/SongMaintenance.php?SONG_ID={$nSongID}' class='secondaryLinkButton'>Edit Song</A>";
	}
}

/*
 ********************************************************************************
 * renderDropDropDown
 * 
 * This function renders a drop-down list of ThurdyDrops
 ********************************************************************************
*/
function renderDropDropDown($nDropID)
{

	//include Song Class		
	include_once (CLASS_DIR . "/class_ThurdyDrop.php");

	echo "Drop:\n"; 

	echo "<SELECT NAME=\"selDrop\" ID=\"selDrop\">\n";

	echo "<OPTION VALUE=\"0\">NONE</OPTION>\n";
	$oDrops = new ThurdyDrop();
	if($oDrops->getThurdyDrop())
	{
		foreach($oDrops->aDropRecords as $oDrop)
		{
			echo "<OPTION VALUE=\"{$oDrop->nDropID}\" ";
			if ($oDrop->nDropID == $nDropID)
			{
				echo " SELECTED ";
			}
			echo ">{$oDrop->sDropLocation}</OPTION>";

		}
	}
	
	echo "</SELECT>";

	if ($nDropID > 0 )
	{
		echo "<A HREF='" . ADMIN_DIR . "/ThurdyDropMaintenance.php?DROP_ID={$nDropID}' class='secondaryLinkButton'>Edit Drop</A>";
	}

}

/*
 ********************************************************************************
 * renderCDDropDown
 * 
 * This function renders a drop-down list of CDs
 ********************************************************************************
*/
function renderCDDropDown($nCDID)
{

	//include CD Class		
	include_once (CLASS_DIR . "/class_CD.php");

 	echo "CD:\n"; 
	echo "<SELECT NAME=\"selCD\" ID=\"selCD\">\n";
	echo "<OPTION VALUE=\"\">ALL</OPTION>\n";
	$oCDs = new CD();
	$oCDs->bIncludeSingles = TRUE;
	$oCDs->sOrderBy = NAME_ORDER;
	if($oCDs->getCD())
	{
		foreach($oCDs->aCDRecords as $oCD)
		{
			echo "<OPTION VALUE=\"{$oCD->nCDID}\" ";
			if ($oCD->nCDID == $nCDID)
			{
				echo " SELECTED ";
			}
			echo ">{$oCD->sCDName}</OPTION>";

		}
	}
	
	echo "</SELECT>";

	if ($nCDID > 0 )
	{
		echo "<A HREF='" . ADMIN_DIR . "/CDMaintenance.php?CD_ID={$nCDID}' class='secondaryLinkButton'>Edit CD</a>";
	}
}


/*
********************************************************************************
renderArtistDropDown

This function renders a drop-down list of Artists
----------------------------------------------------------------------------
2016-02-11	Added renderArtistDropDown()
********************************************************************************
*/
function renderArtistDropDown($nArtistID = 0)
{

	//include Artist Class		
	include_once (CLASS_DIR . "/class_Artist.php");

 	echo "ARTIST:\n"; 
	echo "<SELECT NAME=\"selArtist\" ID=\"selArtist\">\n";
	echo "<OPTION VALUE=\"0\">ALL</OPTION>\n";
	$oArtists = new Artist();
	if($oArtists->getArtist())
	{
		foreach($oArtists->aArtistRecords as $oArtist)
		{
			echo "<OPTION VALUE=\"{$oArtist->nArtistID}\" ";
			if ($oArtist->nArtistID == $nArtistID)
			{
				echo " SELECTED ";
			}
			echo ">{$oArtist->sArtistName}</OPTION>";

		}
	}
	
	echo "</SELECT>";

	if ($nArtistID > 0 )
	{
		echo "<A HREF='" . ADMIN_DIR . "/ArtistMaintenance.php?ARTIST_ID={$nArtistID}' class='secondaryLinkButton'>Edit Artist</A>";
	}
}

/*
 ********************************************************************************
 * renderTourDropDown
 * 
 * This function renders a drop-down list of Tours
 ********************************************************************************
*/
function renderTourDropDown()
{
	echo "<input type=\"hidden\" id=\"hdnTourID\" name=\"hdnTourID\" value={$_POST['hdnTourID']} />";
	echo "TOUR:"; 
	echo "<SELECT NAME=\"selTour\" ID=\"selTour\" onchange=\"buildTourLink()\">";
	echo "<OPTION VALUE=\"0\">NONE</OPTION>";

	$nThisTourID = 0;
		
	if(!is_null($_POST['hdnTourID']) && $_POST['hdnTourID'] > 0)
	{
		$nThisTourID = $_POST['hdnTourID'];
	}
	elseif((!is_null($_POST['hdnTourID']) && $_POST['hdnTourID'] > 0))
	{
		$nThisTourID = $_POST['hdnTourID'];
	}
	
	if (!is_null($nThisTourID) && $nThisTourID > 0)
	{
		$oThisTour = new Tour();
		$oThisTour->nTourID = $nThisTourID;
		if($oThisTour->getTour())
		{
			if(sizeof($oThisTour->aTourRecords) > 0)
			{
				echo "<OPTION VALUE={$oThisTour->aTourRecords[0]->nTourID} SELECTED>{$oThisTour->aTourRecords[0]->sTourName}</OPTION>";
			}
		}
		
	}

	echo "</SELECT>";
	echo "<INPUT type=\"button\" ID=\"btnAssignTour\" value=\"Assign Tour\" onclick=\"getTours()\" />"; 

	if($_POST['hdnTourID'] > 0)
	{
		echo "<A HREF='" . ADMIN_DIR . "/TourMaintenance.php?ID={$_POST['hdnTourID']}' class='secondaryLinkButton'>Edit Tour</A>";	
	} 
}

/*
 ********************************************************************************
 * renderDatePicker
 * 
 * This function renders a date picker
 ********************************************************************************
*/
function renderDatePicker($sDateFieldName, $dtExistingDate)
{
	if($dtExistingDate > "2015-01-01" || $dtExistingDate == "0000-00-00" || is_null($dtExistingDate))
	{
		//DEBUG
		//echo "DATE IS: {$dtExistingDate}";
		$dateParts = explode("-",$dtExistingDate ?? '');
		$sExistingDate = '';
		if(sizeof($dateParts) == 3){
			$sExistingDate = "{$dateParts[1]}/{$dateParts[2]}/{$dateParts[0]}";
		}

		echo "
		<input type=\"text\" class=\"form-control datepicker\" id=\"{$sDateFieldName}\" name=\"{$sDateFieldName}\" value=\"{$dtExistingDate}\">";
	}
	else
	{
		echo "<input type=\"hidden\" id=\"{$sDateFieldName}\" name=\"{$sDateFieldName}\" value=\"{$dtExistingDate}\">";
		echo "<i>{$dtExistingDate}</i>";
	}
}


/*
 ********************************************************************************
 * setReportStyleClass
 * 
 * This function simply alternates the report style class used in TD elements
 * to make reporting easier to read.
 ********************************************************************************
*/
function setReportStyleClass(&$sReportStyleClass)
{
	//Alternate the report style
	if ($sReportStyleClass == REPORT_STYLE_CLASS)
	{
		$sReportStyleClass = REPORT_STYLE_CLASS_ALT;
	}
	else
	{
		$sReportStyleClass = REPORT_STYLE_CLASS;
	}
}
?>
