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

$sPageName = "You Belong Here With Me";
$sPageDescription = "You Belong Here With Me - by Steve Weeks";
$sPageTitle = "You Belong Here With Me";
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
						<h1 class="listen">"You Belong Here With Me" by Steve Weeks</h1>
						<!--<div class="mp3-player-container">-->
						<audio controls data-info-att="Music: Steve Weeks" data-info-att-link="https://www.steveweeksmusic.com">
							<source src="<?php echo MP3_DIR . "/you_belong_here.mp3"; ?>" type="audio/mpeg" />
							<a href="<?php echo MP3_DIR . "/you_belong_here.mp3"; ?>">You Belong Here With Me</a>
							An html5-capable browser is required to play this audio.
						</audio>
						<!--</div>-->
					</div>
					<div class="row">
						<div class="col-xs-12 col-md-6">
							<div class="presskit-document">
								<A HREF="<?php echo MP3_DIR ?>/you_belong_here.mp3" target="_blank"><IMG SRC="<?php echo IMG_DIR ?>/mp3-icon.png" BORDER=0><br><br>
									<b>"You Belong Here With Me" (MP3)</b></A>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<h2>Lyrics</h2>
						<p class=MsoTitle>You Belong Here With Me</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal>Stars belong up in the sky and birds in the trees<br>
Your best friend belongs right by your side and rainbows just out of reach<br>
Treasures belong down in the deep blue sea<br>
And you here with <span class=GramE>me</span></p>

<p class=MsoNormal>Climbers belong on the monkey bars and dreamers on the
swings<br>
Nessie belongs in a Scottish loch if that's what you believe<br>
Nashville belongs in old Tennessee<br>
And you here with <span class=GramE>me</span></p>

<p class=MsoNormal>Everything has got a place where it should be<br>
And everything's in place when you're right here with me <br>
The bride at the altar just in time<br>
A straight shot right down the third base line <br>
And you here with <span class=GramE>me</span></p>

<p class=MsoNormal>The Colts belong in Baltimore, <span class=GramE>yeah</span>
I never got over that<br>
Amelia's airplane belongs wherever it's at <br>
Good times rolling down Bourbon Street<br>
Zacchaeus up in his sycamore tree <br>
And you here with me </p>

<p class=MsoNormal>A plot twist belongs on the very last page<br>
That poor old King belongs in his gilded cage<br>
The privileged few up in first class seats<br>
The rest of us back here in economy<br>
And you here with <span class=GramE>me</span></p>

<p class=MsoNormal>Everything has got a place where it should be<br>
And everything's in place when you're right here with me <br>
Your car keys in the last place you look<br>
The knight sitting right beside the rook <br>
And you here with <span class=GramE>me</span></p>

<p class=MsoNormal><span style='font-family:"Avenir Next LT Pro Light",sans-serif'><i>Solo
<o:p></o:p></i></span></p>

<p class=MsoNormal>An ace up the sleeve when the chips are down <br>
Drinks on the house all around<br>
And you here with <span class=GramE>me</span><span style='font-size:18.0pt;
line-height:107%;font-family:"Cambria",serif;mso-ascii-theme-font:major-latin;
mso-fareast-font-family:"Times New Roman";mso-fareast-theme-font:major-fareast;
mso-hansi-theme-font:major-latin;mso-bidi-font-family:"Times New Roman";
mso-bidi-theme-font:major-bidi;text-transform:uppercase'><o:p></o:p></span></p>

<p class=MsoNormal>A rose belongs with thorns all around it<br>
My good pen belongs right back where you found it<br>
Secrets kept between friends<br>
A seventh chord right before the end <br>
And you here with <span class=GramE>me</span></p>

<p class=MsoNormal>Breakfast in bed, clothes on the line<br>
A lock on the door, and the world outside<br>
And you here with me</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>
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