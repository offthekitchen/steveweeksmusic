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
	$sPageName = "Copyright";
	//Page Title
	$sPageTitle = "Copyright";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('Copyright','copyright.php')
		); 

	//Copyright is linked from the footer, not the main menu
	$sActiveMenuItem = "";	

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
				<article class="row">
					<div class="col-xs-12 col-sm-8 col-sm-push-2">
						<section class="avoid-theme">
							<p>&copy; <?php echo date("Y"); ?> Steve Weeks Music. All rights reserved.</p>
							<p>All original works published on this website are owned by Steve Weeks Music unless a page says otherwise. This includes musical compositions, sound recordings, vocal performances, lyrics, visual art, graphic designs, illustrations, and photography.</p>
							<p>You may view these pages and listen to the music for your own personal, non-commercial use. Copying, downloading for reuse, reproducing, distributing, publicly performing, adapting, or otherwise using this material requires prior written permission, except for uses the law already allows.</p>
							<p>Permission requests: <a href="mailto:steve@steveweeksmusic.com">steve@steveweeksmusic.com</a> or the <a href="<?php echo $ROOT; ?>/contact.php">contact page</a>.</p>
							<p>Prohibition on AI Training &amp; Voice/Style Cloning: All original works on this site&mdash;including musical compositions, sound recordings, vocal performances, lyrics, visual art, graphic designs, and photography&mdash;are exclusively owned by Steve Weeks Music. Automated scraping, text and data mining (TDM), or any use of these works to train, validate, or operate generative AI, voice-cloning, or machine learning systems is strictly prohibited. All rights to license these works for AI or machine learning purposes are expressly reserved. (Note: Standard search engine indexing of performance schedules and public event listings is permitted.)</p>
						</section>
					</div>
				</article>
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
