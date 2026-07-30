<?php
/*
*******************************************************************
commonFunctions.php
This PHP file contains functions used commonly across the site
NOTES
Date        Change
-------------------------------------------------------------
2017-09-06	Added Internal Review Prcoessing
2017-12-10	Added renderBookingVideo()
2020-12-01	Made City and State conditional on performance listing
2021-05-26	Added online concert icon
2021-08-30	Updated for PHP 8
*******************************************************************
*/

//include DB Class		
include_once(CLASS_DIR . "/class_DB.php");

//include Error Class
include_once(CLASS_DIR . "/class_Error.php");

//include Performance Class
include_once(CLASS_DIR . "/class_Performance.php");

/*
 ********************************************************************************
 * renderBreadcrumb()
 * 
 * This function renders breadcrumb based on items in the apassed array
 ********************************************************************************
 */
function renderBreadCrumb($aBreadcrumb)
{
	foreach ($aBreadcrumb as $aBreadcrumbItem) {
		echo "{$aBreadcrumbItem[0]} >";
	}
}


/*
 ********************************************************************************
 * renderCDListing()
 * 
 * This function renders an CD Listing
 ********************************************************************************
 */
function renderCDListing($oCD)
{
	echo "<div class=\"music-item\">";
	echo "<figure class=\"music-item-thumbnail\">";
	echo "<img src=\"" . IMG_DIR . "/{$oCD->sCDThumbnail}\" class=\"float-left\">";
	echo "</figure>";
	echo "<span class=\"music-item-title\">{$oCD->sCDName}</span>";
	echo "<span class=\"music-item-year\">" . substr($oCD->dtReleaseDate, 0, 4) . "</span>";
	echo "</div>";
}

/*
 ********************************************************************************
 * renderSongListing()
 * 
 * This function renders an CD Listing
 ********************************************************************************
 */
function renderSongListing($oSong)
{
	echo "<div class=\"music-item\">";
	echo "<figure class=\"music-item-thumbnail\">";
	echo "<img src=\"" . IMG_DIR . "/{$oSong->sSongThumbnail}\" class=\"float-left\">";
	echo "</figure>";
	echo "<span class=\"music-item-title\">{$oSong->sSongName}</span>";
	echo "<span class=\"music-item-year\">" . substr($oSong->dtReleaseDate, 0, 4) . "</span>";
	echo "</div>";
}

/*
 ********************************************************************************
 * renderPurchaseButton
 * 
 ********************************************************************************
 */
function renderPurchaseButton($sURL)
{
	if (!empty($sURL)) {
		echo "<div class=\"center-contents\">";
		echo "<a href=\"{$sURL}\" target=\"_blank\"><div class=\"button purchase-button\">Purchase</div></a>";
		echo "</div>";
	}
}

/*
 ********************************************************************************
 * renderScheduleButton
 * 
 ********************************************************************************
 */
function renderScheduleButton()
{
	global $ROOT;

	echo "<div class=\"center-contents\">";
	echo "<a href=\"{$ROOT}/schedule.php\" target=\"_blank\"><div class=\"button schedule-button\">See Full Schedule</div></a>";
	echo "</div>";
}


/*
 ********************************************************************************
 * renderLyricsButton
 * 
 ********************************************************************************
 */
function renderLyricsButton($nLyricsType, $nEntityID)
{
	if (!is_numeric($nEntityID)) {
		error_log('renderLyricsButton() in commonFunctions.php: INVALID Entity ID: ' . $nEntityID);
	}

	global $ROOT;

	switch ($nLyricsType) {
		case LYRICS_TYPE_SONG:
			echo "<div class=\"center-contents\">";
			echo "<a href=\"{$ROOT}/lyrics.php?song-id={$nEntityID}\"><div class=\"button lyrics-button\">Lyrics</div></a>";
			echo "</div>";
			break;

		case LYRICS_TYPE_CD:
			echo "<div class=\"center-contents\">";
			echo "<a href=\"{$ROOT}/lyrics.php?cd-id={$nEntityID}\"><div class=\"button lyrics-button\">Lyrics</div></a>";
			echo "</div>";
			break;
		default:


	}

}

/*
 ********************************************************************************
 * renderNextPerformance
 * 
 ********************************************************************************
 */
function renderNextPerformance($bRenderScheduleLink, $bCollapseDetails)
{
	global $ROOT;

	echo "<a name=\"next-performance\"></a>";
	echo "<section class=\"performance-listing\">";

	$oPerformance = new Performance();

	if (!$oPerformance->getNextPerformance()) {
		//TODO: What about an error?
		echo "ERROR RETRIEVING PERFORMANCES:" . $oPerformance->sErrorMessage;
	} else if (sizeof($oPerformance->aPerformanceRecords) > 0) {
		echo "<header class=\"next-show-header\">";
		echo "<figure class=\"performance-listing-image\"><img src=\"" . IMG_DIR . "/next-performance-image.png\"></figure>";
		renderPerformanceListing($oPerformance->aPerformanceRecords[0], $bRenderScheduleLink, $bCollapseDetails);
		echo "</header>";
	} else {
		echo "<header>";
		echo "<figure class=\"performance-listing-image\"><img src=\"" . IMG_DIR . "/default-performance-image.png\"></figure>";
		echo "<div class=\"next-show-title\">Live, In Concert!</div>";
		echo "</header>";
		echo "<div class=\"performance-info\">";
		echo "<span class=\"default-performance-text\">";
		echo "I've travelled all over the US and Canada performing music for kids and their families.  I'd love to get the chance to come to your town and play!";
		echo "<a href=\"{$ROOT}/performance.php\" class=\"button link-button booking-information\">Booking</a>";
		echo "</span>";
		echo "</div>";
	}
	echo "</section>";
}

/*
 ********************************************************************************
 * renderNextPerformanceDescription
 * 
 ********************************************************************************
 */
function renderNextPerformanceDescription()
{
	global $ROOT;

	$oPerformance = new Performance();
	if (!$oPerformance->getNextPerformance()) {
		//TODO: What about an error?
		echo "ERROR RETRIEVING PERFORMANCES" . $oPerformance->sErrorMessage;
	} else if (sizeof($oPerformance->aPerformanceRecords) > 0) {
		echo "<span class=\"next-performance-description\">Next Performance: ";
		echo "<span class=\"performance-name\">";
		if (!empty($oPerformance->aPerformanceRecords[0]->sPerformanceWebsite)) {
			echo "<a href=\"{$oPerformance->aPerformanceRecords[0]->sPerformanceWebsite}\" name =\"performance-{$oPerformance->aPerformanceRecords[0]->nPerformanceID}\" target=_blank>{$oPerformance->aPerformanceRecords[0]->sPerformanceName}</a>";
		} else {
			echo "<a class=\"no-underline\" name =\"performance-{$oPerformance->aPerformanceRecords[0]->nPerformanceID}\">{$oPerformance->aPerformanceRecords[0]->sPerformanceName}</a>";
		}

		if (!empty($oPerformance->aPerformanceRecords[0]->sLocationCity)) {
			echo " - {$oPerformance->aPerformanceRecords[0]->sLocationCity}";
		}
		if (!empty($oPerformance->aPerformanceRecords[0]->sLocationState)) {
			echo ", {$oPerformance->aPerformanceRecords[0]->sLocationState}";
		}
		echo "</span>";
	}
}

/*
 ********************************************************************************
 * renderPerformanceListing
 * 
 ********************************************************************************
 */
function renderPerformanceListing($oPerformance, $bRenderScheduleLink, $bCollapseDetails)
{
	global $ROOT;

	if (!empty($oPerformance)) {
		// Generate a Geocode for the performance based on the address
		$performanceGeocodeURL = buildGeocode($oPerformance->sLocationAddr1, $oPerformance->sLocationAddr2, $oPerformance->sLocationCity, $oPerformance->sLocationState, $oPerformance->sLocationZip);

		//If it's online render the icon
		if ($oPerformance->bPreRecorded) {
			echo "<figure class=\"performance-listing-image\">";
			echo "<img src=\"" . IMG_DIR . "/Online-Show-Icon.png\">";
			echo "</figure>";
		}

		//Display Basic Performance Info
		echo "<div class=\"performance-info\">";
		echo "<span class=\"performance-name\">";
		if (!empty($oPerformance->sPerformanceWebsite)) {
			echo "<a href=\"{$oPerformance->sPerformanceWebsite}\" name =\"performance-{$oPerformance->nPerformanceID}\" target=_blank>{$oPerformance->sPerformanceName}</a>";
		} else {
			echo "<a class=\"no-underline\" name =\"performance-{$oPerformance->nPerformanceID}\">{$oPerformance->sPerformanceName}</a>";
		}

		if (!empty($oPerformance->sLocationCity)) {
			echo " - {$oPerformance->sLocationCity}";
		}
		if (!empty($oPerformance->sLocationState)) {
			echo ", {$oPerformance->sLocationState}";
		}
		echo "</span>";

		echo "<div>" . date("m-d-Y", strtotime($oPerformance->dtPerformanceDate)) . " (" . date("l", strtotime($oPerformance->dtPerformanceDate)) . "), {$oPerformance->sPerformanceTime}</div>";
		echo "</div><!--End performance-info-->";

		//If the Performance was in the past display message, other wise render a collapsable details section		
		if (date("Y-mm-dd") > date("Y-mm-dd", strtotime($oPerformance->dtPerformanceDate))) {
			echo "<div class=\"performance-message\">";
			echo "<p>This performance occurred in the past</p>";
			echo "</div>";
		} else {
			echo "<div>";
			if ($bCollapseDetails) {
				echo "<button class=\"button details-button\" data-toggle=\"collapse\" data-target=\"#more-details-{$oPerformance->nPerformanceID}\">Details</button>";
			}
			if ($bRenderScheduleLink) {
				echo "<span class=\"spacer\">";
				echo "<a href=\"{$ROOT}/schedule.php\" class=\"schedule-link\">full schedule</a>";
				echo "</span>";
			}
			echo "</div>";

			echo "<div class=\"performance-details-container\">";
			echo "<div class=\"performance-details\">";
			echo "<div id=\"more-details-{$oPerformance->nPerformanceID}\"";
			if ($bCollapseDetails) {
				echo " class=\"collapse\"";
			}
			echo ">";
			echo "<p>{$oPerformance->sDescription}<br><p>";

			//Render Google Map
			if (!empty($oPerformance->sLocationAddr1)) {
				echo "<div class=\"inline-block map-icon\">";
				echo "<a href =\"http://maps.google.com/maps?f=q&hl=en&geocode=&q={$performanceGeocodeURL}\" target=\"_blank\">";
				echo "<img src=\"" . IMG_DIR . "/Map.gif\" border=0></a>";
				echo "</div>";
			}

			//Render Location
			echo "<div class=\"inline-block performance-location\">";
			if (!empty($oPerformance->sLocationWebsite)) {
				echo "<A HREF={$oPerformance->sLocationWebsite} TARGET=_blank>{$oPerformance->sLocation}</A><br>";
			} else {
				echo "$oPerformance->sLocation<br>";
			}

			if (!empty($oPerformance->sLocationAddr1) || !empty($oPerformance->sLocationAddr2) || !empty($oPerformance->sLocationCity)) {
				if (!empty($oPerformance->sLocationAddr1)) {
					echo "{$oPerformance->sLocationAddr1}<BR>";
				}

				if (!empty($oPerformance->sLocationAddr2)) {
					echo "{$oPerformance->sLocationAddr2}<BR>";
				}

				if (!empty($oPerformance->sLocationCity)) {
					echo $oPerformance->sLocationCity;

					if (!empty($oPerformance->sLocationState)) {
						echo ", {$oPerformance->sLocationState}";
					}
					if (!empty($oPerformance->sLocationZip)) {
						echo " {$oPerformance->sLocationZip}";
					}
				}
			}
			echo "</div><!--End Location Div-->";

			//Render Contact and Admission
			echo "<div class=\"inline-block\">";
			if (!empty($oPerformance->sAdmission)) {
				echo "<b>Admission:</b> {$oPerformance->sAdmission}<br>";
			}
			if (!empty($oPerformance->sContactEmail)) {
				echo "<b>Email:</b> <A CLASS=\"performance-email\" HREF=mailto:{$oPerformance->sContactEmail}>";
				if (!empty($oPerformance->sContactName)) {
					echo $oPerformance->sContactName;
				} else {
					echo $oPerformance->sContactEmail;
				}
				echo "</A>";
				echo "<BR>";
			}

			if (!empty($oPerformance->sContactPhone)) {
				echo "<b>Phone:</b> {$oPerformance->sContactPhone}";
			}
			echo "</div><!--End Contact Div-->";

			echo "</div><!--End more-details-->";
			echo "</div><!--End performance-details-->";
			echo "</div><!--End  performance-details-container-->";
		}

	}

}


/*
 ********************************************************************************
 * buildGeocode
 * 
 * This function builds a geocode string based on the address parms passed in
 ********************************************************************************
 */
function buildGeocode($address1, $address2, $city, $state, $zip)
{
	$geocodeURL = "";
	$geocodeWordFound = "false";
	$geocodeLineFound = "false";

	// Explode the Address Lines and City into seperate words
	$exp_line1 = explode(" ", $address1);
	$exp_line2 = explode(" ", $address2);
	$exp_city = explode(" ", $city);

	// Build a string of the words in the first address line for use in the URL link to Google Maps
	// Format = "word1a+word1b+word1c"
	for ($wordCount = 0; $wordCount < count($exp_line1); $wordCount++) {

		if ($exp_line1[$wordCount] != "") {

			if ($geocodeWordFound == "true") {
				$geocodeURL .= "+";
			}

			$geocodeWordFound = "true";
			$geocodeLineFound = "true";

		}

		$geocodeURL .= $exp_line1[$wordCount];
	}


	$geocodeWordFound = "false";

	// Append any words in the second address line to the geocode string for use in the URL link to Google Maps
	// Format = "word1a+word1b+word1c,word2a+word2b+word2c"
	for ($wordCount = 0; $wordCount < count($exp_line2); $wordCount++) {

		if ($exp_line2[$wordCount] != "") {

			if ($geocodeLineFound == "true" && $geocodeWordFound == "false") {
				$geocodeURL .= ",";
			}

			if ($geocodeWordFound == "true") {
				$geocodeURL .= "+";
			}

			$geocodeWordFound = "true";
			$geocodeLineFound = "true";
		}

		$geocodeURL .= $exp_line2[$wordCount];
	}

	$geocodeWordFound = "false";

	// Append any words in the city to the geocode string for use in the URL link to Google Maps
	// Format = "word1a+word1b+word1c,word2a+word2b+word2c+citya+cityb"
	for ($wordCount = 0; $wordCount < count($exp_city); $wordCount++) {

		if ($exp_city[$wordCount] != "") {

			if ($geocodeLineFound == "true" && $geocodeWordFound == "false") {
				$geocodeURL .= ",";
			}

			if ($geocodeWordFound == "true") {
				$geocodeURL .= "+";
			}

			$geocodeWordFound = "true";
			$geocodeLineFound = "true";
		}

		$geocodeURL .= $exp_city[$wordCount];
	}

	$geocodeWordFound = "false";

	// Append the state to the geocode string for use in the URL link to Google Maps
	// Format = "word1a+word1b+word1c,word2a+word2b+word2c+citya+cityb+state"
	if ($state != "") {

		if ($geocodeLineFound == "true") {
			$geocodeURL .= ",";
		}

		$geocodeLineFound = "true";
	}

	$geocodeURL .= $state;

	// Append the zip to the geocode string for use in the URL link to Google Maps
	// Format = "word1a+word1b+word1c,word2a+word2b+word2c+citya+cityb+state+zip"
	if ($zip != "") {

		if ($geocodeLineFound == "true") {
			$geocodeURL .= ",";
		}

		$geocodeLineFound = "true";
	}

	$geocodeURL .= $zip;
	$geocodeLineFound = "false";

	//DEBUG - echo "GEOCODE URL=" . $geocodeURL . "<BR>";

	return $geocodeURL;

}


/*
 ********************************************************************************
 * renderReview
 * 
 ********************************************************************************
 */
function renderReview(&$oReview)
{
	echo "<blockquote>";
	echo "<span>{$oReview->sReviewExcerpt}</span> ";
	if (!empty($oReview->sReviewURL)) {
		if ($oReview->bInternalReviewUrl) {
			echo "<span class=\"read-more-quote\"><a href=\"full-review.php?REVIEW_ID=" . $oReview->nReviewID . "\">Read More...</a></span>";
		} else {
			echo "<span class=\"read-more-quote\"><a href=\"{$oReview->sReviewURL}\" target=\"_blank\">Read More...</a></span>";
		}
	}
	echo "<cite>{$oReview->sReviewAuthor}";
	if (!empty($oReview->sReviewSource)) {
		echo " ({$oReview->sReviewSource})";
	}
	echo "</cite>";
	echo "</blockquote>";
}

/*
 ********************************************************************************
 * renderReviewSlideshow
 * 
 ********************************************************************************
 */
function renderReviewSlideshow($aReviews)
{
	echo "<div class=\"slideshow-outer-container\">";
	echo "<div class=\"slideshow-horizontal-center\">";
	echo "<div class=\"slideshow-inner-container\">";
	echo "<div class=\"slide-vertical-center\">";

	echo "<div class=\"fadein\">";

	foreach ($aReviews as $oReview) {
		echo "<div class=\"review-slide\">";
		renderReview($oReview);
		echo "</div>";
	}

	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";

	echo "<script type=\"text/javascript\" src=\"" . JS_DIR . "/review-slideshow.js\"></script>";


}


/*
 ********************************************************************************
 * renderAward
 * 
 ********************************************************************************
 */
function renderAward(&$oAward, $sSize)
{
	echo "<div class=\"award-item\">";
	echo "<span class=\"award-icon award-icon-{$sSize}\">";
	if (empty($oAward->sAwardImage)) {
		$oAward->sAwardImage = DEFAULT_AWARD_IMAGE;
	}
	echo "<img src=\"" . IMG_DIR . "/{$oAward->sAwardImage}\">";
	echo "</span>";
	echo "<span class=\"award-name award-name-{$sSize}\">";
	if (!empty($oAward->sAwardURL)) {
		echo "<a href=\"$oAward->sAwardURL\" target=\"_blank\">{$oAward->sAwardName}</a>";
	} else {
		echo "{$oAward->sAwardName}";
	}
	echo "</span>";
	if ($sSize == AWARD_SIZE_LARGE) {
		echo "<div class=\"award-description\">";
		echo "{$oAward->sAwardDescription}";
		echo "</div>";
	}
	echo "</div>";
}


/*
 ********************************************************************************
 * renderAwards
 * 
 ********************************************************************************
 */
function renderAwards($nAwardType, $nEntityID, $sSize)
{
	switch ($nAwardType) {
		case AWARD_TYPE_CD:
			$oAwards = new Award();
			$oAwards->nCDID = $nEntityID;
			if (!$oAwards->getAward()) {
				//TODO: ERROR or Default
			} else if (sizeof($oAwards->aAwardRecords) > 0) {
				echo "<div class=\"awards-list\">";
				foreach ($oAwards->aAwardRecords as $oAward) {
					renderAward($oAward, $sSize);
				}
				echo "</div>";
			} else {
				//TODO: Get Song Awards
			}
			break;

		case AWARD_TYPE_SONG:
			$oAwards = new Award();
			$oAwards->nSongID = $nEntityID;
			if (!$oAwards->getAward()) {
				//TODO: ERROR or Default
			} else if (sizeof($oAwards->aAwardRecords) > 0) {
				echo "<div class=\"awards-list\">";
				foreach ($oAwards->aAwardRecords as $oAward) {
					renderAward($oAward, $sSize);
				}
				echo "</div>";
			}

			break;

		default:
	}

}

/*
 ********************************************************************************
 * renderAboutPerformance
 * 
 ********************************************************************************
 */
function renderAboutPerformance()
{
	echo "<article>";
	echo "<header>";
	renderBookingVideo();
	echo "<h2>About Steve's Performance</h2>";
	echo "</header>";
	echo "<section>";
	echo "Steve provides an interactive romp filled with music, humor and games for young kids and their families.  ";
	echo "He has performed in <a href=\"http://www.offthekitchen.com/music/performance/memories-of-50-states/\" target=\"_blank\">all 50 United States</a>, Europe and Canada, and his music is in frequent rotation on Sirius XM satellite radio where he has had multiple #1 hits.";
	echo "  He's won first place in the children's music category of the US Songwriting Competition, third place in the International Songwriting Competition, and numerous other parenting and music awards.  Some of the venues in which he has performed include...";
	echo "<div class=\"row\">";
	echo "<div class=\"col-xs-6\">";
	echo "<ul clas=\"venues-left-column\">";
	echo "<li><a href=\"http://jamminjava.com/\" target=\"_blank\">Jammin' Java</a><br><span class=\"venue-city\">(Vienna, VA)</span></li>";
	echo "<li><a href=\"http://queen.worldcafelive.com/\" target=\"_blank\">World Cafe Live at the Queen</a><br><span class=\"venue-city\">(Wilmington, DE)</span></li>";
	echo "<li><a href=\"http://www.northplattecommunityplayhouse.com/\" target=\"_blank\">Fox Theater</a><br><span class=\"venue-city\">(North Platte, NE)</span></li>";
	echo "<li><a href=\"http://www.licm.org/theater.php\" target=\"_blank\">Long Island Children's Museum</a><br><span class=\"venue-city\">(Garden City, NY)</span></li>";
	echo "<li>Strings Music Festival<br><span class=\"venue-city\">(Steamboat Springs, CO)</span></li>";
	echo "<li>Rivers and Spires Festival<br><span class=\"venue-city\">(Clarksville, TN)</span></li>";
	echo "</ul>";
	echo "</div>";
	echo "<div class=\"col-xs-6\">";
	echo "<ul clas=\"venues-left-column\">";
	echo "<li><a href=\"http://www.symphonyspace.org/\" target=\"_blank\">Symphony Space</a><br><span class=\"venue-city\">(NYC, NY)</span></li>";
	echo "<li><a href=\"http://www.suffolkcenter.org/\" target=\"_blank\">Suffolk Center for Cultural Arts</a><br><span class=\"venue-city\">(Suffolk, VA)</span></li>";
	echo "<li>Iowa City Oktoberfest<br><span class=\"venue-city\">(Iowa City, IA)</span></li>";
	echo "<li><a href=\"http://www.botanicgardens.org/\" target=\"_blank\">Denver Botanic Gardens</a><br><span class=\"venue-city\">(Denver, CO)</span></li>";
	echo "<li><a href=\"http://telluridepalm.com/\" target=\"_blank\">Michael D. Palm Theater</a><br><span class=\"venue-city\">(Telluride, CO)</span></li>";
	echo "</ul>";
	echo "</div>";
	echo "</div>";
	echo "</section>";
	echo "</article>";
}

/*
 ********************************************************************************
 * renderBookingVideo
 * 
 ********************************************************************************
 */
function renderBookingVideo()
{
	//Detect special conditions devices
	/*$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
	$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
	$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
	$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
	$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

	if( $iPod || $iPhone  || $iPad){
		echo "<a href=\"" . VIDEO_DIR . "/PerformanceBookingiOS.mp4\">";
		echo  "<img src=\"" . IMG_DIR . "/about-performance-with-play-icon.jpg\" width=\"100%\"/></a>";
	}
	else{
		echo "<video width=\"100%\" controls=\"true\"  poster=\"" . IMG_DIR . "/about-performance.jpg\">";
		echo "<source src=\"" . VIDEO_DIR . "/PerformanceBooking.mp4\" type=\"video/mp4\">";
		echo "<a href=\"" . VIDEO_DIR . "/PerformanceBookingiOS.mp4\">";
		echo  "<img src=\"" . IMG_DIR . "/about-performance-with-play-icon.jpg\" width=\"100%\"/></a>";
		echo "</video>";
	}*/
	echo "<div style=\"width: 100%; height: 50%;\">";
	echo "<iframe height=\"100%\" width=\"100%\" src=\"https://www.youtube.com/embed/n-apVsbAS18\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen></iframe>";
	echo "</div>";
}
/*

/*
 ********************************************************************************
 * renderPromoVideo
 * 
 ********************************************************************************
*/
function renderPromoVideo()
{
	echo "<div style=\"width: 100%; height: 50%;\">";
	echo "<iframe height=\"100%\" width=\"100%\" src=\"https://www.youtube.com/embed/VbAbDUvXhCA\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen></iframe>";
	echo "</div>";
}
/*
 ********************************************************************************
 * renderNewsItem
 * 
 ********************************************************************************
 */
function renderNewsItem($nNewsItemID)
{
	$sNewsFileName = "news{$nNewsItemID}.php";
	include(NEWS_DIR . "/{$sNewsFileName}");
}
?>