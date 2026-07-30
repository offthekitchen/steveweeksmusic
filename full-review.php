<?php
//Don't Display Notice messages from PHP Server
error_reporting(E_ALL ^ E_NOTICE);

//This include defines the relative path to the root directory from this sub-directory
include_once("root.inc.php");

//inlcude web site settings
include_once($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once(SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//Common Functions
include(INCLUDE_DIR . "/commonFunctions.php");

//include Review Class
include_once(CLASS_DIR . "/class_Review.php");

//include Error Log Class
include_once(CLASS_DIR . "/class_ErrorLog.php");

$oReviews = new Review();

if ($_GET) {

	//Get the reivew ID from the query string
	if (isset($_REQUEST['REVIEW_ID']) && $_REQUEST['REVIEW_ID'] != "") {
		$oReviews->nReviewID = preg_replace("/[^0-9]/", "", $_REQUEST['REVIEW_ID']);

		if (!empty($oReviews->nReviewID)) {
			if ($oReviews->getReview()) {

				//Custom FB Image
				$sFBImage = "FB_Performing.png";

				$aBreadcrumb = array(
					0 => array('Home', 'index.php'),
					1 => array('More', 'more.php'),
					2 => array('Reviews', 'reviews.php')
				);

				$sActiveMenuItem = HOME_ACTIVE;

				?>
				<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
				<HTML>
				<?php
				//Inlcude a common <HEAD> section
				include(INCLUDE_DIR . "/HTMLHead.php");
				?>

				<BODY>
					<?php include(INCLUDE_DIR . "/theme-image.php"); ?>
					<div class="container-fluid">
						<div class="row">
							<?php
							include(INCLUDE_DIR . "/header.php");
							?>
						</div>
						<?php
						if (sizeof($oReviews->aReviewRecords) == 1) {
							$oThisReview = $oReviews->aReviewRecords[0];
							//Page Name 
							$sPageName = "Review " . $oThisReview->sReviewSource;
							//Page Name 
							$sPageSubTitle = $oThisReview->sReviewSource . " Review";
							?>
							<div class="row">
								<main id="main-content" class="col-xs-12">
									<section id="review-list" class="row">
										<div class="col-xs-12 col-lg-10 col-lg-push-1">
											<aside class="review-listing listing-1 purple-frame frame-box behind-theme-image avoid-theme">
												<blockquote>
													<span>
														<?php include(REVIEWS_DIR . "/" . $oThisReview->sReviewURL) ?>
													</span>
													<cite>
														<?php echo $oThisReview->sReviewAuthor . "(" . $oThisReview->sReviewSource . ", " . $oThisReview->dtReviewDate . ")"; ?>
													</cite>
												</blockquote>
											</aside>
										</div>
									</section>
								</main>
							</div>
							<div class="row">
								<?php
								include(INCLUDE_DIR . "/footer.php");
								?>
							</div>
						</div>
					</BODY>

					</HTML>
					<?php
						} else {
							echo "<h2>I'm Sorry.  I couldn't find that Review.<h2>";
						}
			} else {
				$error = new ErrorLog('full-review.php', 'getReview()', 'Error getting Reviews', $oReviews->nReviewID, 'REVIEW');
				$error->writeErrorLog();
			}
		}
	}

} else {
	//Redirect to all reviews page

}

?>