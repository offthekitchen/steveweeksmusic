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
2026-10-12	Migrated to shared PDO datalayer
*******************************************************************
*/

include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/Performance.php");
include_once(DATALAYER_DIR . "/PerformanceRepository.php");
include_once(DATALAYER_DIR . "/Award.php");
include_once(DATALAYER_DIR . "/AwardRepository.php");

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
	echo "<img src=\"" . IMG_DIR . "/{$oCD->thumbnail}\" class=\"float-left\">";
	echo "</figure>";
	echo "<span class=\"music-item-title\">{$oCD->name}</span>";
	echo "<span class=\"music-item-year\">" . substr($oCD->releaseDate, 0, 4) . "</span>";
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
	echo "<img src=\"" . IMG_DIR . "/{$oSong->thumbnail}\" class=\"float-left\">";
	echo "</figure>";
	echo "<span class=\"music-item-title\">{$oSong->name}</span>";
	echo "<span class=\"music-item-year\">" . substr($oSong->releaseDate, 0, 4) . "</span>";
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

	$repo = new \Datalayer\PerformanceRepository();
	$oNextPerformance = $repo->findNext(false);

	if ($oNextPerformance !== null) {
		echo "<header class=\"next-show-header\">";
		echo "<figure class=\"performance-listing-image\"><img src=\"" . IMG_DIR . "/next-performance-image.png\"></figure>";
		renderPerformanceListing($oNextPerformance, $bRenderScheduleLink, $bCollapseDetails);
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

	$repo = new \Datalayer\PerformanceRepository();
	$oNextPerformance = $repo->findNext(false);

	if ($oNextPerformance !== null) {
		echo "<span class=\"next-performance-description\">Next Performance: ";
		echo "<span class=\"performance-name\">";
		if (!empty($oNextPerformance->website)) {
			echo "<a href=\"{$oNextPerformance->website}\" name =\"performance-{$oNextPerformance->id}\" target=_blank>{$oNextPerformance->name}</a>";
		} else {
			echo "<a class=\"no-underline\" name =\"performance-{$oNextPerformance->id}\">{$oNextPerformance->name}</a>";
		}

		if (!empty($oNextPerformance->locationCity)) {
			echo " - {$oNextPerformance->locationCity}";
		}
		if (!empty($oNextPerformance->locationState)) {
			echo ", {$oNextPerformance->locationState}";
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
		$performanceGeocodeURL = buildGeocode($oPerformance->locationAddr1, $oPerformance->locationAddr2, $oPerformance->locationCity, $oPerformance->locationState, $oPerformance->locationZip);

		//If it's online render the icon
		if ($oPerformance->preRecorded) {
			echo "<figure class=\"performance-listing-image\">";
			echo "<img src=\"" . IMG_DIR . "/Online-Show-Icon.png\">";
			echo "</figure>";
		}

		//Display Basic Performance Info
		echo "<div class=\"performance-info\">";
		echo "<span class=\"performance-name\">";
		if (!empty($oPerformance->website)) {
			echo "<a href=\"{$oPerformance->website}\" name =\"performance-{$oPerformance->id}\" target=_blank>{$oPerformance->name}</a>";
		} else {
			echo "<a class=\"no-underline\" name =\"performance-{$oPerformance->id}\">{$oPerformance->name}</a>";
		}

		if (!empty($oPerformance->locationCity)) {
			echo " - {$oPerformance->locationCity}";
		}
		if (!empty($oPerformance->locationState)) {
			echo ", {$oPerformance->locationState}";
		}
		echo "</span>";

		echo "<div>" . date("m-d-Y", strtotime($oPerformance->performanceDate)) . " (" . date("l", strtotime($oPerformance->performanceDate)) . "), {$oPerformance->performanceTime}</div>";
		echo "</div><!--End performance-info-->";

		//If the Performance was in the past display message, other wise render a collapsable details section		
		if (date("Y-mm-dd") > date("Y-mm-dd", strtotime($oPerformance->performanceDate))) {
			echo "<div class=\"performance-message\">";
			echo "<p>This performance occurred in the past</p>";
			echo "</div>";
		} else {
			echo "<div>";
			if ($bCollapseDetails) {
				echo "<button class=\"button details-button\" data-toggle=\"collapse\" data-target=\"#more-details-{$oPerformance->id}\">Details</button>";
			}
			if ($bRenderScheduleLink) {
				echo "<span class=\"spacer\">";
				echo "<a href=\"{$ROOT}/schedule.php\" class=\"schedule-link\">full schedule</a>";
				echo "</span>";
			}
			echo "</div>";

			echo "<div class=\"performance-details-container\">";
			echo "<div class=\"performance-details\">";
			echo "<div id=\"more-details-{$oPerformance->id}\"";
			if ($bCollapseDetails) {
				echo " class=\"collapse\"";
			}
			echo ">";
			echo "<p>{$oPerformance->description}<br><p>";

			//Render Google Map
			if (!empty($oPerformance->locationAddr1)) {
				echo "<div class=\"inline-block map-icon\">";
				echo "<a href =\"http://maps.google.com/maps?f=q&hl=en&geocode=&q={$performanceGeocodeURL}\" target=\"_blank\">";
				echo "<img src=\"" . IMG_DIR . "/Map.gif\" border=0></a>";
				echo "</div>";
			}

			//Render Location
			echo "<div class=\"inline-block performance-location\">";
			if (!empty($oPerformance->locationWebsite)) {
				echo "<A HREF={$oPerformance->locationWebsite} TARGET=_blank>{$oPerformance->location}</A><br>";
			} else {
				echo "$oPerformance->location<br>";
			}

			if (!empty($oPerformance->locationAddr1) || !empty($oPerformance->locationAddr2) || !empty($oPerformance->locationCity)) {
				if (!empty($oPerformance->locationAddr1)) {
					echo "{$oPerformance->locationAddr1}<BR>";
				}

				if (!empty($oPerformance->locationAddr2)) {
					echo "{$oPerformance->locationAddr2}<BR>";
				}

				if (!empty($oPerformance->locationCity)) {
					echo $oPerformance->locationCity;

					if (!empty($oPerformance->locationState)) {
						echo ", {$oPerformance->locationState}";
					}
					if (!empty($oPerformance->locationZip)) {
						echo " {$oPerformance->locationZip}";
					}
				}
			}
			echo "</div><!--End Location Div-->";

			//Render Contact and Admission
			echo "<div class=\"inline-block\">";
			if (!empty($oPerformance->admission)) {
				echo "<b>Admission:</b> {$oPerformance->admission}<br>";
			}
			if (!empty($oPerformance->contactEmail)) {
				echo "<b>Email:</b> <A CLASS=\"performance-email\" HREF=mailto:{$oPerformance->contactEmail}>";
				if (!empty($oPerformance->contactName)) {
					echo $oPerformance->contactName;
				} else {
					echo $oPerformance->contactEmail;
				}
				echo "</A>";
				echo "<BR>";
			}

			if (!empty($oPerformance->contactPhone)) {
				echo "<b>Phone:</b> {$oPerformance->contactPhone}";
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
	echo "<span>{$oReview->excerpt}</span> ";
	if (!empty($oReview->url)) {
		if ($oReview->internalReviewUrl) {
			echo "<span class=\"read-more-quote\"><a href=\"full-review.php?REVIEW_ID=" . $oReview->id . "\">Read More...</a></span>";
		} else {
			echo "<span class=\"read-more-quote\"><a href=\"{$oReview->url}\" target=\"_blank\">Read More...</a></span>";
		}
	}
	echo "<cite>{$oReview->author}";
	if (!empty($oReview->source)) {
		echo " ({$oReview->source})";
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
	$awardImage = empty($oAward->image) ? DEFAULT_AWARD_IMAGE : $oAward->image;
	echo "<img src=\"" . IMG_DIR . "/{$awardImage}\">";
	echo "</span>";
	echo "<span class=\"award-name award-name-{$sSize}\">";
	if (!empty($oAward->url)) {
		echo "<a href=\"{$oAward->url}\" target=\"_blank\">{$oAward->name}</a>";
	} else {
		echo "{$oAward->name}";
	}
	echo "</span>";
	if ($sSize == AWARD_SIZE_LARGE) {
		echo "<div class=\"award-description\">";
		echo "{$oAward->description}";
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
	$repo = new \Datalayer\AwardRepository();

	switch ($nAwardType) {
		case AWARD_TYPE_CD:
			$awards = $repo->find(['cdId' => (int) $nEntityID]);
			if (sizeof($awards) > 0) {
				echo "<div class=\"awards-list\">";
				foreach ($awards as $oAward) {
					renderAward($oAward, $sSize);
				}
				echo "</div>";
			}
			break;

		case AWARD_TYPE_SONG:
			$awards = $repo->find(['songId' => (int) $nEntityID]);
			if (sizeof($awards) > 0) {
				echo "<div class=\"awards-list\">";
				foreach ($awards as $oAward) {
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
