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
	$sPageName = "Agree to Disagree (feat. Joanie Leeds)";
	$sPageTitle = "Agree to Disagree (feat. Joanie Leeds)";
	//Custom FB Image
	$sFBImage = "FB_AgreeToDisagree.png";


	$sActiveMenuItem = HOME_ACTIVE;	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
?>
<style>

.icelandic-music-item {
	min-height: 110px;
}
</style>
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
					<p>
						Here is all the information, links and files for Steve's single "Agree to Disagree (feat. Joanie Leeds)".
					</p>   
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12">
				
				
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header purple-box">
						<a name="Info">
							Song Info
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box purple-frame" style="padding: 15px;">
							<div class="thanks">
								<div class="table-responsive">	
									<table class="table table-striped"> 
										<tr>
											<td>Artist</td>
											<td>Steve Weeks</td>
										</tr>
										<tr>
											<td>Song Title</td>
											<td>Agree to Disagree (feat. Joanie Leeds)</td>
										</tr>
										<tr>
											<td>Album/CD</td>
											<td></td>
										</tr>
										<tr>
											<td>Composer/Songwriter</td>
											<td>Steve Weeks</td>
										</tr>
										<tr>
											<td>Label and Label reference </td>
											<td>Steve Weeks Music WWX2201</td>
										</tr>
										<tr>
											<td>ISRC</td>
											<td>US-WWX-22-00001</td>
										</tr>
										<tr>
											<td>Copyright(s)</td>
											<td>2022 Steve Weeks</td>
										</tr>
										<tr>
											<td>Release Year</td>
											<td>2022</td>
										</tr>
										<tr>
											<td>Licensing</td>
											<td>BMI</td>
										</tr>
									</table>
								</div>
							</div>
						</div>								
					</div>
									
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header green-box">
						<a name="Links">
							LINKS TO AUDIO FILES
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box green-frame" style="padding: 15px;">
							<div class="thanks">
							<div class="table-responsive">	
									<table class="table table-striped"> 
										<tr>
											<td>WAV</td>
											<td><a href="http://steveweeksmusic.com/mp3/AgreeToDisagree/AgreeToDisagree.wav" target="_blank">http://steveweeksmusic.com/mp3/AgreeToDisagree/AgreeToDisagree.wav</a></td>
										</tr>
										<tr>
											<td>MP3 (320 kbps)</td>
											<td><a href="http://steveweeksmusic.com/mp3/AgreeToDisagree/AgreeToDisagree.mp3" target="_blank">http://steveweeksmusic.com/mp3/AgreeToDisagree/AgreeToDisagree.mp3</a></td>
										</tr>
									</table>
								</div>							
							</div>
						</div>								
					</div>									
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header red-box">
						<a name="Release">
							Press Release
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box red-frame" style="padding: 15px;">
							<div class="thanks">
							<div class="presskit-document">
								<A HREF="<?php echo PDF_DIR ?>/AgreeToDisagreePressRelease.pdf" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/MyFamilyPR-Icon.gif" BORDER=0><br>
								Press Release<br>
								Agree to Disagree (feat. Joanie Leeds) </A>
							</div>	
							<div class="press-release">
							<h1>STEVE WEEKS PRESENTS: AGREE TO DISAGREE</h1>
							<h2>A NEW SINGLE FOCUSED ON GETTING ALONG DESPITE DIFFERENCES</h2>
							<h2>FEATURING GRAMMY WINNING ARTIST, JOANIE LEEDS</h2>
							<h2>OUT FRIDAY SEPTEMBER 2, 2022</h2>
<p><em>Steve Weeks</em> is back, and he's not alone!  
His new family music single "<em>Agree to Disagree</em>", is a bouncy, folk duet featuring
<em>GRAMMY</em> winning singer-songwriter <em>Joanie Leeds</em>.</p>
<p>As the title would suggest, this song is about getting along with each other
despite our many differences, something both artists believe we could use a little
more of these days. Honestly, a girl from bustling New York City and a guy in the
mountains of Colorado might seem an unlikely pair for a collaboration, but the
two fell in like birds of a feather, and the tongue in cheek tune benefited from
having different approaches.</p>
<p>
"When I initially demoed the song and it was just me, it sounded a little flat and
one-dimensional.", Weeks recalls, "Joanie immediately added color and took the
tune in a new direction. It was like the whole thing just brightened up!"</p>
<p>
Joanie was also thrilled to work with Steve again after they were randomly paired
up by mutual friend and fellow songwriter <em>Cory Cullinan</em> during the pandemic to
sing on a verse together for a climate change awareness song. <em>Positive Energy</em>
featured 25 Children's musicians, including 4 GRAMMY Winners. "While working
on that project, Steve and I developed a friendship that mostly left me crying with
laughter due to his hilarious banter. His witty writing isn't just for emails though- it
carries over to all of his lyrics and "<em>Agree to Disagree</em>" is no exception. I'm
thrilled for the world to finally hear his tune and honored he chose me to be part
of it.</p>
<p>
<p>
"Agree to Disagree" is clear evidence that despite distance and differences, we
can all find harmony if we try and will be available Sept 2nd for streaming on
Spotify, iTunes, Amazon Music and anywhere parents are searching for great
music.</p>
<p>
<h2>ABOUT STEVE WEEKS:</h2>
Steve has already made his mark on the national music scene with 6 critically acclaimed CDs,
multiple #1 hits on Sirius XM's Kids Place Live and a first-place award in the USA Songwriting
Competition and other awards.
</p> 
<p> 
<h2>ABOUT JOANIE LEEDS:</h2>
For her original kids music GRAMMY Winning NYC- based singer-songwriter Joanie Leeds
won first place in the USA Songwriting Competition, an Independent Music Award, a Gold
Parents' Choice Award, NAPPA Gold Award, Family Choice Award and she is a John Lennon
Songwriting Award Finalist and an International Songwriting Competition Finalist. For the past
decade, she has performed songs from her 9 high-energy and interactive albums in dozens of
cities across the country at venues and festivals such as Lollapalooza, Clearwater Festival, The
Kennedy Center, Lincoln Center, The Smithsonian, CMAs, Hang Out Festival and the Skirball
Center in LA. Her music has climbed up the charts to #1 on Sirius-XM's Kids Place Live and has
been raved about in People Magazine, Parents Magazine, New York Times, Washington Post
and Billboard. For more information: <a href="https://joanieleeds.com/" target="_blank">www.joanieleeds.com</a></p>
<p>For more information:
								<div class="table-responsive">	
									<table class="table table-striped"> 
										<tr>
											<td>Steve Weeks:</td>
											<td><a href="mailto:steve@steveweeksmusic.com" target="_blank">steve@steveweeksmusic.com</a></td>
											<td><a href="www.steveweeksmusic.com" target="_blank">steveweeksmusic.com</a></td>
										</tr>
										<tr>
											<td>Joanie Leeds:</td>
											<td><a href="mailto:joanieleeds@gmail.com" target="_blank">joanieleeds@gmail.com</a></td>
											<td><a href="https://joanieleeds.com/" target="_blank">joanieleeds.com</a></td>
										</tr>
									</table>
								</div>	
</p>
<p style="text-align:center;">
# # #
</p>
							</div>						
						</div>							
					</div>
					
				</div>
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header blue-box">
						<a name="images">
							Images
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box blue-frame" style="padding: 15px;">
							<div class="thanks">
								<div class="image">
									<figure>
										<img src="<?php echo IMG_DIR ?>/AgreeToDisagreeCover.png" class="responsive-image"><br>
										<caption>Agree to Disagree Cover Art</caption>
									</figure>
								</div>
								<div class="image">
									<figure>
										<img src="<?php echo IMG_DIR ?>/slide_photo2.jpg" class="responsive-image"><br>
										<caption>Steve Weeks</caption>
									</figure>
								</div>
							</div>
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
	<style>
		h1, h2, h3 {
			text-align: center;
			color: black;
			font-family: Arial, Helvetica, sans-serif;
			font-style: normal;
			font-weight: 700;
		}
		h1{
			font-size: 18px;
		}
		h2 {
			font-size: 15px;
		}
		.press-release {
			color: black;
			font-family: Verdana, Geneva, Tahoma, sans-serif;
			font-style: normal;
		}
		.thanks em {
			color: black;
			font-family: Verdana, Geneva, Tahoma, sans-serif;
			font-style: normal;
			font-weight: 700;
		}
		p {
			padding: 15px;
		}
		.image {
			text-align: center;
		}

		.responsive-image{
			max-width: 100%;
		}
	</style>
</BODY>
</HTML>
