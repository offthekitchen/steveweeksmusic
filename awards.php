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

	//include Award Class
	include_once (CLASS_DIR . "/class_Award.php");

	$oAwards = new Award();
	
	//If a CD ID is passed, display awards for that CD
	if (isset($_REQUEST['cd-id']) && $_REQUEST['cd-id'] != "")
	{
		$oAwards->nCDID = $_REQUEST['cd-id'];		
	}

	//If a Performance indicator is passed, display awards for performances
	if (isset($_REQUEST['performance-related']) && $_REQUEST['performance-related'] != "")
	{
		$oAwards->bPerformanceRelated = TRUE;		
	}

	$oAwards->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;
	
	if(!$oAwards->getAward())
	{
		//TODO: What about an error?
		echo "ERROR RETRIEVING Awards: {$oAwards->sErrorMessage}";	
	}

	//Page Name 
	$sPageName = "Awards";
	//Page Name 
	$sPageSubTitle = "Awards and Credentials";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('More','more.php'),
		2=> array('Awards & Credentials','awards.php'),
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
	<?php include(INCLUDE_DIR . "/theme-image.php"); ?>
	<div class="container-fluid">
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/header.php");
		?>		
		</div>
		<div class="row">
			<main id="main-content" class="col-xs-12">
				<section id="award-list" class="row">
					<?php
					$nListingNumber = 1;
					foreach ($oAwards->aAwardRecords as $oAward)
					{
						echo "<div class=\"col-xs-12 col-lg-10 col-lg-push-1\">";
						echo "<aside class=\"award-listing listing-{$nListingNumber}\">";
						echo renderAward($oAward, AWARD_SIZE_LARGE);
						echo "</aside>";
						echo "</div>";
						$nListingNumber++;
					}
					?>
				</section>
			</main>
		</div>
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/footer.php");
		?>		
		<script>
		//******************************************
		// alternate classes on Awards and Singles
		//******************************************
		$( document ).ready(function() 
		{
			//Set alternating classes on Awards and singles
			$("#award-list aside.award-listing:odd").addClass("green-frame frame-box in-front-of-theme-image");
			$("#award-list aside.award-listing:even").addClass("purple-frame frame-box behind-theme-image avoid-theme");
		})
		</script>
		</div>
	</div>
</BODY>
</HTML>
