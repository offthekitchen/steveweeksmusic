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

	//Page Name 
	$sPageName = "Iceandic Language Resources";
	//Page Name 
	$sPageSubTitle = "Steve's Favorite";
	//Custom FB Image
	$sFBImage = "FB_Icelandic.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('Icelnadic','icelandic-resources.php')
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
			include(INCLUDE_DIR . "/header.php");
		?>		
		</div>
		<div class="row">
			<aside class="col-xs-12 col-sm-10 col-sm-push-1">
				<div class="orange-frame frame-box in-front-of-theme-image">
					<div class="thanks">
					<p>Here are a few of my favortie Icelandic language resources.  These are cetainly not all of them.  Nor are they they necessarily the best for everyone.  They are just the ones that seem to work best for me. 
					</p>
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12 col-sm-8 col-sm-push-4">
				<div class="row">
					<div class="col-xs-12 col-lg-10 col-lg-push-1">
						<section class="icelandic-section">
							<h3>Movies</h3>
							<i>This is a description for movies</i>
							<ul>
								<li> Djupi&eth; (<i>(The Deep)</i></li>
								<li> Jar City</li>
								<li> Metalhead</li>
								<li> Either Way</li>
							</ul>
						</section>
					</div>
				</div>				
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
