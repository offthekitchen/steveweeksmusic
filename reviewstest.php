<?php
	//Don't Display Notice messages from PHP Server
	error_reporting(E_ALL ^ E_NOTICE);

	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");

	//inlcude web site settings
 	include_once ($ROOT . "/includes/websiteSettings.php");

	//Common Functions
   	include (INCLUDE_DIR . "/commonFunctions.php");

	//include Review Class
	include_once (CLASS_DIR . "/class_Review.php");

	$oReviews = new Review();
	
	if ($_GET)
	{
	
		//If a CD ID is passed, display reviews for that CD
		if (isset($_REQUEST['cd-id']) && $_REQUEST['cd-id'] != "")
		{
			$oReviews->nCDID = $_REQUEST['cd-id'];		
		}
	
		//If a Performance indicator is passed, display reviews for performances
		if (isset($_REQUEST['performance-related']) && $_REQUEST['performance-related'] != "")
		{
			$oReviews->bPerformanceRelated = TRUE;		
		}
	}
	else
	{
		$oReviews->bGeneral = TRUE;	
	}
	$oReviews->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;
	
	if(!$oReviews->getReview())
	{
		//TODO: What about an error?
		echo "ERROR RETRIEVING Reviews: {$oReviews->sErrorMessage}";	
	}

	//Page Name 
	$sPageName = "Reviews";
	//Page Name 
	$sPageSubTitle = "What are people saying?";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('More','more.php'),
		2=> array('Reviews','reviews.php')
		); 


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
?>

<BODY>
	<?php include(INCLUDE_DIR . "/theme-image.php"); ?>
	<div class="container-fluid">
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/header.php");
		?>		
		</div>
		<div class="row">
			<aside class="col-xs-12 col-sm-10 col-sm-push-1">

			</aside>
			<main id="main-content" class="col-xs-12">
				<section id="review-list" class="row">
					<div class="col-xs-12">
						<div class="orange-frame frame-box in-front-of-theme-image">
						
					<?php
					renderReviewSlideshow($oReviews->aReviewRecords);
					?>
					</div>
				</section>				
			</main>
		</div>
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/footer.php");
		?>		
		<script>
		//******************************************
		// alternate classes on Reviews and Singles
		//******************************************
		$( document ).ready(function() 
		{
			//Set alternating classes on Reviews and singles
			$("#review-list aside.review-listing:odd").addClass("green-frame frame-box in-front-of-theme-image");
			$("#review-list aside.review-listing:even").addClass("purple-frame frame-box behind-theme-image avoid-theme");
		})
		</script>
		</div>
	</div>
</BODY>
</HTML>
