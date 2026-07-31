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
2026-07-30	Migrated dropdown helpers to Datalayer repositories
2026-07-30	Fixed renderDatePicker to show picker for empty add-mode dates
*******************************************************************
*/	
?>

<!-- Javascript required for Calendar picker -->
<script language="javascript" src="<?php echo ADMIN_JS_DIR ?>/calendar.js"></script>

<?php

// Datalayer (PDO) used by render*DropDown helpers
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Product.php");
include_once(DATALAYER_DIR . "/ProductRepository.php");
include_once(DATALAYER_DIR . "/Vendor.php");
include_once(DATALAYER_DIR . "/VendorRepository.php");
include_once(DATALAYER_DIR . "/TaxCategory.php");
include_once(DATALAYER_DIR . "/TaxCategoryRepository.php");
include_once(DATALAYER_DIR . "/Song.php");
include_once(DATALAYER_DIR . "/SongRepository.php");
include_once(DATALAYER_DIR . "/ThurdyDrop.php");
include_once(DATALAYER_DIR . "/ThurdyDropRepository.php");
include_once(DATALAYER_DIR . "/CD.php");
include_once(DATALAYER_DIR . "/CDRepository.php");
include_once(DATALAYER_DIR . "/Artist.php");
include_once(DATALAYER_DIR . "/ArtistRepository.php");
include_once(DATALAYER_DIR . "/Tour.php");
include_once(DATALAYER_DIR . "/TourRepository.php");

require_once(CLASS_DIR . "/tc_calendar.php");

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
		$sEstimatedTime = $oPerformanceSongsRecord->estimatedTime ?? $oPerformanceSongsRecord->tEstimatedTime ?? '';
		list($hour, $minute, $second) = explode(':', $sEstimatedTime);
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
	echo ">NONE</OPTION>\n";
	$productRepo = new \Datalayer\ProductRepository();
	foreach ($productRepo->find() as $product)
	{
		echo "<OPTION VALUE=\"{$product->id}\" ";
		if ($product->id == ($_POST['selProduct'] ?? null))
		{
			echo " SELECTED ";
		}
		echo ">{$product->name}</OPTION>";
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
 	echo "VENDOR:\n"; 
	echo "<SELECT NAME=\"selVendor\" ID=\"selVendor\">\n";
	echo "<OPTION VALUE=\"0\">NONE</OPTION>\n";
	$vendorRepo = new \Datalayer\VendorRepository();
	foreach ($vendorRepo->find() as $vendor)
	{
		echo "<OPTION VALUE=\"{$vendor->id}\" ";
		if ($vendor->id == ($_POST['selVendor'] ?? null))
		{
			echo " SELECTED ";
		}
		echo ">{$vendor->name}</OPTION>";
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
 	echo "TAX CATEGORY:\n"; 
	echo "<SELECT NAME=\"selTaxCategory\" ID=\"selTaxCategory\">\n";
	echo "<OPTION VALUE=\"0\">NONE</OPTION>\n";
	$taxCategoryRepo = new \Datalayer\TaxCategoryRepository();
	foreach ($taxCategoryRepo->find() as $taxCategory)
	{
		echo "<OPTION VALUE=\"{$taxCategory->id}\" ";
		if ($taxCategory->id == ($_POST['selTaxCategory'] ?? null))
		{
			echo " SELECTED ";
		}
		echo ">{$taxCategory->name}</OPTION>";
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
 	echo "Song:\n"; 
	echo "<SELECT NAME=\"selSong\" ID=\"selSong\">\n";
	echo "<OPTION VALUE=\"\">ALL</OPTION>\n";
	$songRepo = new \Datalayer\SongRepository();
	foreach ($songRepo->find(['orderBy' => 'name']) as $song)
	{
		echo "<OPTION VALUE=\"{$song->id}\" ";
		if ($song->id == $nSongID)
		{
			echo " SELECTED ";
		}
		echo ">{$song->name}</OPTION>";
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
 	echo "Songs:\n"; 
	echo "<SELECT NAME=\"selSong\" ID=\"selSong\">\n";
	echo "<OPTION VALUE=\"\">ALL</OPTION>\n";
	$songRepo = new \Datalayer\SongRepository();
	foreach ($songRepo->find(['artistId' => (int) $nArtistID, 'orderBy' => 'name']) as $song)
	{
		echo "<OPTION VALUE=\"{$song->id}\" ";
		if ($song->id == $nSongID)
		{
			echo " SELECTED ";
		}
		echo ">{$song->name}</OPTION>";
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
	echo "Drop:\n"; 

	echo "<SELECT NAME=\"selDrop\" ID=\"selDrop\">\n";

	echo "<OPTION VALUE=\"0\">NONE</OPTION>\n";
	$dropRepo = new \Datalayer\ThurdyDropRepository();
	foreach ($dropRepo->find() as $drop)
	{
		echo "<OPTION VALUE=\"{$drop->id}\" ";
		if ($drop->id == $nDropID)
		{
			echo " SELECTED ";
		}
		echo ">{$drop->dropLocation}</OPTION>";
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
 	echo "CD:\n"; 
	echo "<SELECT NAME=\"selCD\" ID=\"selCD\">\n";
	echo "<OPTION VALUE=\"\">ALL</OPTION>\n";
	$cdRepo = new \Datalayer\CDRepository();
	foreach ($cdRepo->find(['includeSingles' => true, 'orderBy' => 'name']) as $cd)
	{
		echo "<OPTION VALUE=\"{$cd->id}\" ";
		if ($cd->id == $nCDID)
		{
			echo " SELECTED ";
		}
		echo ">{$cd->name}</OPTION>";
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
 	echo "ARTIST:\n"; 
	echo "<SELECT NAME=\"selArtist\" ID=\"selArtist\">\n";
	echo "<OPTION VALUE=\"0\">ALL</OPTION>\n";
	$artistRepo = new \Datalayer\ArtistRepository();
	foreach ($artistRepo->find() as $artist)
	{
		echo "<OPTION VALUE=\"{$artist->id}\" ";
		if ($artist->id == $nArtistID)
		{
			echo " SELECTED ";
		}
		echo ">{$artist->name}</OPTION>";
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
	$postedTourId = $_POST['hdnTourID'] ?? 0;
	echo "<input type=\"hidden\" id=\"hdnTourID\" name=\"hdnTourID\" value={$postedTourId} />";
	echo "TOUR:"; 
	echo "<SELECT NAME=\"selTour\" ID=\"selTour\" onchange=\"buildTourLink()\">";
	echo "<OPTION VALUE=\"0\">NONE</OPTION>";

	$nThisTourID = 0;
		
	if (!is_null($postedTourId) && $postedTourId > 0)
	{
		$nThisTourID = (int) $postedTourId;
	}
	
	if ($nThisTourID > 0)
	{
		$tourRepo = new \Datalayer\TourRepository();
		$thisTour = $tourRepo->findById($nThisTourID);
		if ($thisTour !== null)
		{
			echo "<OPTION VALUE={$thisTour->id} SELECTED>{$thisTour->name}</OPTION>";
		}
	}

	echo "</SELECT>";
	echo "<INPUT type=\"button\" ID=\"btnAssignTour\" value=\"Assign Tour\" onclick=\"getTours()\" />"; 

	if ($postedTourId > 0)
	{
		echo "<A HREF='" . ADMIN_DIR . "/TourMaintenance.php?ID={$postedTourId}' class='secondaryLinkButton'>Edit Tour</A>";	
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
	// Empty / placeholder dates (add mode) must show the picker. Only pre-2015
	// dates are read-only (legacy behavior for historical records).
	$dtExistingDate = $dtExistingDate ?? '';
	$showPicker = ($dtExistingDate === ''
		|| $dtExistingDate === '0000-00-00'
		|| $dtExistingDate > '2015-01-01');

	if ($showPicker)
	{
		$dateParts = explode("-", $dtExistingDate);
		$sExistingDate = '';
		if (sizeof($dateParts) == 3) {
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
