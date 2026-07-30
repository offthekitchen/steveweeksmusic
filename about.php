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
	$sPageName = "About Steve";
	//Page Name 
	$sPageSubTitle = "About Steve";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";

	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('About','about.php')
		); 

	$sActiveMenuItem = ABOUT_ACTIVE;	

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
					<div class="col-xs-12 col-sm-6 col-sm-push-3 full-width-xs">
						<div class="about-header avoid-theme-md in-front-of-theme-image-xs">
							<figure class="about-header-image">
								<img src="<?php echo IMG_DIR; ?>/about-image.jpg">
							</figure>
							<blockquote>
							A truly  original voice on the Kindie music scene
							</blockquote>
						</div>
					</div>
					<div class="col-xs-12">
						<section class="avoid-theme">
							 <p align="left">Steve Weeks performs with both a  boisterous sense of humor and a deep sense of purpose.&nbsp; &ldquo;We&rsquo;re supposed to be entertaining and  educating kids,&rdquo; the Colorado-based singer-songwriter enthuses. &ldquo;I am sincere  about the music I write.&nbsp; If it&rsquo;s not  coming from the heart or isn&rsquo;t fun, I won&rsquo;t write it.&rdquo;</p>
										<p>That  focused attitude about his craft has led to a series of smile-inducing delights  across multiple albums, beginning with a trio of themed projects known as <a href="<?php echo "{$ROOT}/search.php?search-text=alphabet+songs"; ?>">The Alphabet Songs Series</a>, the critically acclaimed album &ldquo;<a href="<?php echo "{$ROOT}/cd.php?cd-id=" . DANDELION_CD_ID; ?>">Dandelion</a>, 
										and the collection of lullabies &ldquo;<a href="<?php echo "{$ROOT}/cd.php?cd-id=" . ONCE_CD_ID; ?>">Once I Lived Upon the Sea</a>&rdquo;&rdquo;.&nbsp;  More recently, Weeks has issued &ldquo;<a href="<?php echo "{$ROOT}/cd.php?cd-id=" . WYWH_CD_ID; ?>">Wish You Were Here</a>&rdquo;, a CD intended to thank everyone who has helped him over the years in achieving his
										goal of performing in <a href="http://www.offthekitchen.com/music/performance/memories-of-50-states/" target="_blank">all 50 United States</a>.&nbsp;&nbsp;He has also performed in Canada, England and Iceland. </p>
										<p>Weeks  has long been known for his richly layered acoustic-based songs, experimentation  with unusual percussion (including cans, pots and pans, even tinker-toys) and  diverse musical styles that range from reggae to bluegrass to folk-hop. But at the heart of his music is always an outlandish story or a touching tale.  His latest work is no exception.</p>
										<p>&ldquo;This is simple, elegant songwriting,&rdquo; says  Kenny Curtis, program director at Sirius XM satellite radio, &ldquo;with a heavy dose of whimsy.&rdquo;</p>
										<p>Weeks, who placed first (with his song &ldquo;Up!&rdquo;) in the children&rsquo;s music category of the  2007 U.S. Songwriting Competition, boasts four tracks that have reached No. 1  on Sirius XM&rsquo;s Kids Place Live. </p>
										<p>In part, that <a href="<?php echo "{$ROOT}/awards.php"; ?>">national success</a> is due to the fact that Steve's music doesn't leave adults out in the cold.  Woven throughout his songs are heartfelt messages and stories that young and old alike can appreciate.  &ldquo;Tiny House&rdquo; weaves a lovably absurd story of downsizing, and &ldquo;Monday I Woke Up Purple&rdquo; tells of a protagonist who wakes up 
										completely different every morning.&nbsp;&nbsp;&ldquo;Run-On&rdquo; is literally a single run-on sentence, and &ldquo;Backwards Song The&rdquo; is, well... backwards.</p>
										<p>As always with Steve's music, you'll also hear catchy melodies and warm instrumentation.&nbsp;  &ldquo;A Raindrop for Me&rdquo; is an endearing tale of thankfulness that starts with a single drop of rain, and the infectious &ldquo;Baby Started Dancing&rdquo; will have you up on your feet just like characters in the song. </p>
										  <p>Although music was a childhood passion, the South  Carolina native says his career in independent kids (or Kindie) music was  forged through happenstance.&nbsp; A  self-taught musician, he found himself composing a series of songs for his  own children&rsquo;s pre-school curriculum a few years ago. &nbsp;The songs were well-received, and his  reputation grew from a network of friends outward, eventually making Weeks'  music a staple on the national children's music scene: &ldquo;The whole thing grew  very organically.&rdquo;</p>
										<p>Listeners have responded.&nbsp;  After the national recognition that <em>Alphabet  Songs</em>, <em>Dandelion</em>, and <em>Once I Lived Upon the Sea</em> received, <em>Wish You Were Here</em> arrived to overwhelming praise, proof that Weeks is an artist who&rsquo;s here to  stay. </p>
										<p>&ldquo;I want to be able to leave a real legacy with  people,&rdquo; Weeks says. &ldquo;It really makes my day when someone tells me that they  listen to my music as a family and that my songs mean something to them.&nbsp; I&rsquo;d love to think that someday people will  remember my music as a fond part of their growing-up experience.&rdquo;<BR Clear=left>
										</P>	
						</section>									
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
