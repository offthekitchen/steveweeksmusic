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

$sPageName = "Spin Her Around";
$sPageDescription = "Spin Her Around - by Steve Weeks";
$sPageTitle = "Spin Her Around";
$sPageSubTitle = "by Steve Weeks";
$sFBImage = "FB_Performing.png";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
//Inlcude a common <HEAD> section
include(INCLUDE_DIR . "/HTMLHead.php");
?>
<link href="<?php echo CSS_DIR; ?>/bootstrap3_player.css" rel="stylesheet">

<BODY>

	<div class="container-fluid">

		<div class="row">
			<main id="main-content" class="col-xs-12">


				<div class="row">
					<div class="col-xs-12">
						<h1 class="listen">"Spin Her Around" by Steve Weeks</h1>
						<!--<div class="mp3-player-container">-->
						<audio controls data-info-att="Music: Steve Weeks" data-info-att-link="https://www.steveweeksmusic.com">
							<source src="<?php echo MP3_DIR . "/spin_her_around.mp3"; ?>" type="audio/mpeg" />
							<a href="<?php echo MP3_DIR . "/spin_her_around.mp3"; ?>">Spin Her Around</a>
							An html5-capable browser is required to play this audio.
						</audio>
						<!--</div>-->
					</div>
					<div class="row">
						<div class="col-xs-12 col-md-6">
							<div class="presskit-document">
								<A HREF="<?php echo MP3_DIR ?>/spin_her_around.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
									<b>"Spin Her Around" (MP3)</b></A>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<h2>Lyrics</h2>
						<p class=MsoTitle><span style='font-variant:normal !important;text-transform:
uppercase'>Spin Her Round<o:p></o:p></span></p>

						<p class=MsoNormal>In the small-town bar light <br>
							The well-wishers gather<br>
							<span class=GramE>She's</span> come to have her<br>
							Last dance in this town<br>
							<br>
							The music is <span class=SpellE>slowin</span>'<br>
							Partners are chosen<br>
							He takes her hand<br>
							And he spins her around
						</p>

						<p class=MsoNormal>They turn with the music<br>
							And talk of the future<br>
							He's got no plans<br>
							But she smiles just the <span class=GramE>same</span></p>

						<p class=MsoNormal>He knows he's only<br>
							Holding a moment<br>
							And when the song's over<br>
							She won't remember his <span class=GramE>name</span></p>

						<p class=MsoNormal>Cause you can't stop the river from seeking the sea<br>
							And you can't keep a starling down on the ground<br>
							And you can't change the bright path of a young girl's ambition<br>
							But just for one night<br>
							He spins her <span class=GramE>around</span></p>

						<p class=MsoNormal>He's lost in this feeling<br>
							But it's only fleeting<br>
							Tomorrow she's leaving<br>
							To burn a trail across the <span class=GramE>sky</span></p>

						<p class=MsoNormal>And everyone knows that she's too big for this place<br>
							And everyone's knows that he's just the right <span class=GramE>size</span></p>

						<p class=MsoNormal>Cause you can't stop the river from seeking the sea<br>
							And you can't keep a starling down on the ground<br>
							And you can't change the bright path of a young girl's ambition<br>
							But just for one night<br>
							He spins her <span class=GramE>around</span></p>

						<p class=MsoNormal>Many days from now<br>
							On a hot, dusty job site<br>
							With his hands full<br>
							Of a hard day's work</p>

						<p class=MsoNormal>He'll pause for a moment<br>
							And sit on the tailgate<br>
							And squint in the sunlight<br>
							And he'll think about <span class=GramE>her</span></p>

						<p class=MsoNormal>Cause you can't stop the river from seeking the sea<br>
							And you can't keep a starling down on the ground<br>
							And you can't change the bright path of a young girl's ambition<br>
							But just for one night<br>
							He spun her <span class=GramE>around</span></p>

						<p class=MsoNormal>Just for one night<br>
							He spun her <span class=GramE>around</span></p>

						<p class=MsoNormal>
							<o:p>&nbsp;</o:p>
						</p>
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
<script src="<?php echo JS_DIR; ?>/bootstrap3_player.js"></script>