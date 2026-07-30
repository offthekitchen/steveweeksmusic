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
	$sPageName = "Get Up";
	//Page Name 
	$sPageTitle = "Get Up";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('Music','music.php'),
		2=> array('Music','music.php')
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

				<div class="row">			
					<div class="col-xs-12 col-sm-8">
						<div class="frame-box orange-frame in-front-of-theme-image">							<div class="thanks">
								<div class="thanks-title">Right-click on the links below<BR>and choose "Save target as"<BR>to download a copy</div>
								<p>
									<A HREF="http://www.steveweeksmusic.com/mp3/Get_Up.mp3" target="_blank"><FONT size="+3">Download MP3</FONT></A> (9.09 MB)<BR><br>
									<A HREF="http://www.steveweeksmusic.com/mp3/Get_Up.wav" target="_blank"><FONT size="+3">Download WAV</FONT></A> (40.1 MB)
								</p>	
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="cd-cover">
							<figure class="dynamic-width in-front-of-theme-image">
								<img src="<?php echo IMG_DIR ;?>/GetUpCover.png">
							</figure>
						</div>
					</div>
				</div>	
				
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
