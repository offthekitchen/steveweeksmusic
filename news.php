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
	$sPageName = "Latest News";
	//Page Name 
	$sPageSubTitle = "Latest News";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('News','news.php')
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
			<main id="main-content" class="col-xs-12 col-md-7 col-lg-8">
				<div class="row">
					<?php
					echo "<div class=\"col-xs-12\">";			
					$sNewsClass = "news-container-main news-full";
					$sThemeClass = "behind-theme-image avoid-theme-sm avoid-theme-xs";
					echo "<div class=\"news-container {$sNewsClass} {$sThemeClass}\">";
					renderNewsItem(1);
					echo "</div>";
					echo "</div>";
					?>
					
				</div>
				<div class="row">
					<?php
					echo "<div class=\"col-xs-12\">";			
					$sNewsClass = "news-container-secondary news-full";
					$sThemeClass = "behind-theme-image avoid-theme-sm avoid-theme-xs";
					echo "<div class=\"news-container {$sNewsClass} {$sThemeClass}\">";
					renderNewsItem(2);
					echo "</div>";
					echo "</div>";
					?>
					
				</div>
			</main>
			<aside class="col-xs-12 col-md-5 col-lg-4">
				<div class="row">
					<?php
					echo "<div class=\"col-xs-12\">";			
					$sNewsClass = "news-container-odd";
					$sThemeClass = "in-front-of-theme-image";
					for ($i = 3; $i <= 5; $i++) 
					{
						echo "<div class=\"news-container {$sNewsClass} {$sThemeClass}\">";
						renderNewsItem($i);
						echo "</div>";
						
						if ($sNewsClass == "news-container-odd")
						{
							$sNewsClass="news-container-even";
							$sThemeClass = "behind-theme-image avoid-theme";
						}
						else
						{
							$sNewsClass="news-container-odd";
							$sThemeClass = "in-front-of-theme-image";
						}
					}
					echo "</div>";
					?>
					
				</div>
			</aside>
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
