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
2026-10-12	Load local Form helper and calendar (no shared CLASS_DIR)
2026-10-14	renderVendorDropDown modes: entry, edit, report (ACTIVE filter)
2026-10-15	Category/TaxCategory/RevenueType ACTIVE dropdown modes
2026-10-15	Added renderYearDropDown (order + default); CD ALL vs SINGLES select fix
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
include_once(DATALAYER_DIR . "/Category.php");
include_once(DATALAYER_DIR . "/CategoryRepository.php");
include_once(DATALAYER_DIR . "/RevenueType.php");
include_once(DATALAYER_DIR . "/RevenueTypeRepository.php");
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

// Admin UI helpers (site-local — not shared offthekitchen/classes)
include_once(ADMIN_INCLUDE_DIR . "/Form.php");
require_once(ADMIN_DIR . "/calendar/classes/tc_calendar.php");

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
 * Renders a vendor drop-down. $mode:
 *   entry  — add/search: active vendors + NONE (0) + ALL (-1); default ALL
 *   edit   — edit record: active vendors + NONE; include current inactive
 *   report — Vendor Report: all vendors + ALL (-1); no NONE; no Edit link
 ********************************************************************************
*/
function renderVendorDropDown($nVendorID, $mode = 'entry')
{
	$selected = (int) $nVendorID;
	$vendorRepo = new \Datalayer\VendorRepository();

	if ($mode === 'report') {
		$vendors = $vendorRepo->find();
	} else {
		$criteria = ['active' => 1];
		if ($selected > 0) {
			$criteria['includeId'] = $selected;
		}
		$vendors = $vendorRepo->find($criteria);
	}

	echo "VENDOR:\n";
	echo "<SELECT NAME=\"selVendor\" ID=\"selVendor\">\n";

	if ($mode === 'entry' || $mode === 'report') {
		echo "<OPTION VALUE=\"-1\"";
		if ($selected === -1) {
			echo " SELECTED ";
		}
		echo ">ALL</OPTION>\n";
	}

	if ($mode === 'entry' || $mode === 'edit') {
		echo "<OPTION VALUE=\"0\"";
		if ($selected === 0) {
			echo " SELECTED ";
		}
		echo ">NONE</OPTION>\n";
	}

	foreach ($vendors as $vendor) {
		echo "<OPTION VALUE=\"{$vendor->id}\" ";
		if ((int) $vendor->id === $selected) {
			echo " SELECTED ";
		}
		echo ">" . htmlspecialchars($vendor->name ?? '', ENT_QUOTES) . "</OPTION>";
	}

	echo "</SELECT>";

	if ($mode !== 'report' && $selected > 0) {
		echo "<A HREF='" . ADMIN_DIR . "/VendorMaintenance.php?ID={$selected}' class='secondaryLinkButton'>Edit Vendor</A>";
	}
}

/*
 ********************************************************************************
 * renderTaxCategoryDropDown
 *
 * Modes: entry (active + NONE + ALL), edit (active + NONE), report (all + ALL)
 ********************************************************************************
*/
function renderTaxCategoryDropDown($nTaxCategoryID, $mode = 'entry')
{
	$selected = (int) $nTaxCategoryID;
	$taxCategoryRepo = new \Datalayer\TaxCategoryRepository();

	if ($mode === 'report') {
		$taxCategories = $taxCategoryRepo->find();
	} else {
		$criteria = ['active' => 1];
		if ($selected > 0) {
			$criteria['includeId'] = $selected;
		}
		$taxCategories = $taxCategoryRepo->find($criteria);
	}

	echo "TAX CATEGORY:\n";
	echo "<SELECT NAME=\"selTaxCategory\" ID=\"selTaxCategory\">\n";

	if ($mode === 'entry' || $mode === 'report') {
		echo "<OPTION VALUE=\"-1\"";
		if ($selected === -1) {
			echo " SELECTED ";
		}
		echo ">ALL</OPTION>\n";
	}

	if ($mode === 'entry' || $mode === 'edit') {
		echo "<OPTION VALUE=\"0\"";
		if ($selected === 0) {
			echo " SELECTED ";
		}
		echo ">NONE</OPTION>\n";
	}

	foreach ($taxCategories as $taxCategory) {
		echo "<OPTION VALUE=\"{$taxCategory->id}\" ";
		if ((int) $taxCategory->id === $selected) {
			echo " SELECTED ";
		}
		echo ">" . htmlspecialchars($taxCategory->name ?? '', ENT_QUOTES) . "</OPTION>";
	}

	echo "</SELECT>";

	if ($mode !== 'report' && $selected > 0) {
		echo "<A HREF='" . ADMIN_DIR . "/TaxCategoryMaintenance.php?ID={$selected}' class='secondaryLinkButton'>Edit Tax Category</A>";
	}
}

/*
 ********************************************************************************
 * renderCategoryDropDown
 *
 * Modes: entry (active + NONE + ALL), edit (active + NONE), report (all + ALL)
 ********************************************************************************
*/
function renderCategoryDropDown($nCategoryID, $mode = 'entry')
{
	$selected = (int) $nCategoryID;
	$categoryRepo = new \Datalayer\CategoryRepository();

	if ($mode === 'report') {
		$categories = $categoryRepo->find();
	} else {
		$criteria = ['active' => 1];
		if ($selected > 0) {
			$criteria['includeId'] = $selected;
		}
		$categories = $categoryRepo->find($criteria);
	}

	echo "CATEGORY:\n";
	echo "<SELECT NAME=\"selCategory\" ID=\"selCategory\">\n";

	if ($mode === 'entry' || $mode === 'report') {
		echo "<OPTION VALUE=\"-1\"";
		if ($selected === -1) {
			echo " SELECTED ";
		}
		echo ">ALL</OPTION>\n";
	}

	if ($mode === 'entry' || $mode === 'edit') {
		echo "<OPTION VALUE=\"0\"";
		if ($selected === 0) {
			echo " SELECTED ";
		}
		echo ">NONE</OPTION>\n";
	}

	foreach ($categories as $category) {
		echo "<OPTION VALUE=\"{$category->id}\" ";
		if ((int) $category->id === $selected) {
			echo " SELECTED ";
		}
		echo ">" . htmlspecialchars($category->name ?? '', ENT_QUOTES) . "</OPTION>";
	}

	echo "</SELECT>";

	if ($mode !== 'report' && $selected > 0) {
		echo "<A HREF='" . ADMIN_DIR . "/CategoryMaintenance.php?ID={$selected}' class='secondaryLinkButton'>Edit Category</A>";
	}
}

/*
 ********************************************************************************
 * renderRevenueTypeDropDown
 *
 * Modes: entry (active + NONE + ALL), edit (active + NONE), report (all + ALL)
 ********************************************************************************
*/
function renderRevenueTypeDropDown($nRevenueTypeID, $mode = 'entry')
{
	$selected = (int) $nRevenueTypeID;
	$revenueTypeRepo = new \Datalayer\RevenueTypeRepository();

	if ($mode === 'report') {
		$revenueTypes = $revenueTypeRepo->find();
	} else {
		$criteria = ['active' => 1];
		if ($selected > 0) {
			$criteria['includeId'] = $selected;
		}
		$revenueTypes = $revenueTypeRepo->find($criteria);
	}

	echo "REVENUE TYPE:\n";
	echo "<SELECT NAME=\"selRevenueType\" ID=\"selRevenueType\">\n";

	if ($mode === 'entry' || $mode === 'report') {
		echo "<OPTION VALUE=\"-1\"";
		if ($selected === -1) {
			echo " SELECTED ";
		}
		echo ">ALL</OPTION>\n";
	}

	if ($mode === 'entry' || $mode === 'edit') {
		echo "<OPTION VALUE=\"0\"";
		if ($selected === 0) {
			echo " SELECTED ";
		}
		echo ">NONE</OPTION>\n";
	}

	foreach ($revenueTypes as $revenueType) {
		echo "<OPTION VALUE=\"{$revenueType->id}\" ";
		if ((int) $revenueType->id === $selected) {
			echo " SELECTED ";
		}
		echo ">" . htmlspecialchars($revenueType->name ?? '', ENT_QUOTES) . "</OPTION>";
	}

	echo "</SELECT>";

	if ($mode !== 'report' && $selected > 0) {
		echo "<A HREF='" . ADMIN_DIR . "/RevenueTypeMaintenance.php?ID={$selected}' class='secondaryLinkButton'>Edit Revenue Type</A>";
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
		// Require a real filter value so unset/empty ALL does not match SINGLES (id 0)
		if ($nCDID !== null && $nCDID !== '' && is_numeric($nCDID) && (int) $cd->id === (int) $nCDID)
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
 * renderYearDropDown
 *
 * Renders the shared report year <select> (ALL + year list).
 * @param mixed  $nSelectedYear  Current selection (null/'' → use $nDefaultYear)
 * @param string $sOrder         'desc' (newest first, default) or 'asc'
 * @param mixed  $nDefaultYear   Selected when $nSelectedYear is empty (default: current year)
 ********************************************************************************
*/
function renderYearDropDown($nSelectedYear = null, $sOrder = 'desc', $nDefaultYear = null)
{
	$nCurrentYear = (int) date('Y');
	$nStartYear = 2003;
	$nEndYear = $nCurrentYear + 1;

	if ($nDefaultYear === null || $nDefaultYear === '') {
		$nDefaultYear = $nCurrentYear;
	}

	if ($nSelectedYear === null || $nSelectedYear === '') {
		$nSelectedYear = $nDefaultYear;
	}

	echo "<SELECT NAME=\"selYear\" ID=\"selYear\">";
	echo "<OPTION VALUE=\"0\"";
	if ((int) $nSelectedYear === 0) {
		echo " SELECTED ";
	}
	echo ">ALL</OPTION>";

	$bAscending = (strtolower((string) $sOrder) === 'asc');
	if ($bAscending) {
		for ($nYear = $nStartYear; $nYear <= $nEndYear; $nYear++) {
			echo "<OPTION VALUE=\"{$nYear}\"";
			if ((int) $nYear === (int) $nSelectedYear) {
				echo " SELECTED ";
			}
			echo ">{$nYear}</OPTION>";
		}
	} else {
		for ($nYear = $nEndYear; $nYear >= $nStartYear; $nYear--) {
			echo "<OPTION VALUE=\"{$nYear}\"";
			if ((int) $nYear === (int) $nSelectedYear) {
				echo " SELECTED ";
			}
			echo ">{$nYear}</OPTION>";
		}
	}

	echo "</SELECT>";
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
