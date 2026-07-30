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
include_once(INCLUDE_DIR . "/commonFunctions.php");

//Page Name 
$sPageName = "Steal This Song";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
//Inlcude a common <HEAD> section
include(INCLUDE_DIR . "/HTMLHead.php");
?>

<BODY>
	<TABLE BORDER=0 ALIGN="CENTER">
		<TR>
			<TD ALIGN="CENTER">
				<table border="0" cellpadding="1" align="center" width="550px">
					<tr>
						<td align="center">
							<p>
								<FONT SIZE="+2">Steal This Song </FONT>
							</p>
						</td>
					</tr>
					<tr>
						<td align="left">
							<p>&nbsp;</p>
							<p>On a long &quot;tour&quot; through Colorado, Nebraska, Iowa, Indiana and Michigan, I
								decided to take on a songwriting project and write a road song based on things I heard
								and saw. The link below is the end result, and below that is the story about the process
								if you're interested in seeing how the song formed. Enjoy! </p>
							<p align="center"><span class="style1"><SMALL><a
											href="<?php echo MP3_DIR ?>/StealThisSongRoughMix.mp3">STEAL THIS SONG (9.85
											MB)</a></SMALL></span> </p>
							<p align="center">&nbsp;</p>
						</td>
					</tr>
					<tr>
						<td align="center">
							<p align="center"><strong>The Idea</strong><br></p>
						</td>
					</tr>
					<tr>
						<td align="left">
							<p>A couple of my friends have always joked that I need to write a &ldquo;road
								song&rdquo;.&nbsp; They say that every travelling musician is required to have a good
								song about life on the highway, and I do travel a good bit, especially in the
								summer.<br>
								<br>
								Of course, I had never really taken the idea seriously.&nbsp; <br>
								But this past summer I had planned to drive from Colorado to Traverse City, MI,
								performing a series of shows along the way.&nbsp; That&rsquo;s almost 40 hours of
								driving round trip!&nbsp; I do quite a bit of driving around the country, but this was a
								long haul by my standards.<br>
								I usually occupy myself on long drives by working on little projects.&nbsp;
								They&rsquo;re usually silly little endeavors, but they&rsquo;re fun and break up the
								monotony.&nbsp; So I decided that on this trip I would write a road song &hellip;seemed
								like a fun idea, and I certainly had the time.<br>
								<br>
								The project would be fairly simple.&nbsp; I would jot down things that I overheard or
								saw along the way, and use them as inspiration for my requisite road song.<br>
								So on June 24, I packed up the car and headed into the prairie&hellip;
								&nbsp;&nbsp;&nbsp;<br>
								<br>
						</td>
					</tr>
					<tr>
						<td align="center">
							<strong>The Inspiration</strong>
						</td>
					</tr>
					<tr>
						<td align="left">
							<p>I overhear a lot of strange things travelling on the road.&nbsp; When you&rsquo;re
								sitting in some caf&eacute; or diner by yourself, eavesdropping becomes a bit of a
								hobby.&nbsp; I once heard a waitress tell a customer, &ldquo;I told my daughter she
								could go to the orphanage, but she better not bring back another orphan!&rdquo;&nbsp;
								<br>
								<br>
								So I brought along a notepad and just jotted down things I overheard people say, things
								I saw, or just general thoughts of inspiration.&nbsp; I had no idea how all of these
								things could possibly connect into a coherent theme.<br>
								<br>
								As I walked out the door the last thing my son said to me was &ldquo;I&rsquo;ll see you
								in a week&rdquo;.&nbsp; So I decided that had to be the first line of the song.&nbsp;
								Other things I jotted down were&hellip;<br>
								<br>
								&ldquo;I&rsquo;m in the middle of nowhere.&nbsp; Do you know how far that is?!?&rdquo;
								&ndash; Overheard from a guy on his cell phone at a truck stop in Eastern Colorado.<br>
								<br>
								&ldquo;I&rsquo;m not expecting too much of a change&rdquo; &ndash; Overheard from a lady
								at a sandwich shop in response to a guy asking how her mother was doing.&nbsp; I thought
								it was nice of him to be concerned, and her response seemed so sad.<br>
								<br>
								River - I followed the Platte River a good part of my trip through Nebraska, and in Iowa
								they had just had some pretty bad flooding.<br>
								<br>
								Hitching up trains in the rail yard &ndash; In North Platte, NE I visited the
								&ldquo;Golden Spike&rdquo;.&nbsp; It&rsquo;s a tower overlooking the giant rail yard
								there and a local tourist attraction.&nbsp; Worth a visit if you&rsquo;re ever
								there.<br>
								<br>
								Theft &ndash; When I left North Platte (before sunrise), I tuned in to one of the local
								radio stations, because the guy who ran sound for me at the theater there did the
								morning news.&nbsp; I just thought it&rsquo;d be interesting to hear him.&nbsp; One of
								the stories was about the theft of some bricks intended for the Veterans Memorial in
								town.&nbsp; I thought, &ldquo;What kind of a person would do that?&rdquo;&nbsp; So I
								decided theft would be a theme.&nbsp; I later decided the name of the song would be
								&ldquo;Steal This Song&rdquo;.<br>
								<br>
								Can&rsquo;t keep the birds &ndash; Apparently sometime in April there is a massive
								migration of cranes through Nebraska. &nbsp;Someone recommended to me that I stop at a
								particular rest area and see where the cranes normally come through.&nbsp; Seems a
								little tourist industry has cropped up around this event, but in the end you can&rsquo;t
								keep the cranes from moving on, so it&rsquo;s a short lived happening.&nbsp; <br>
								<br>
								Dancing in the Square - Walking around Iowa City after a show there, I stumbled upon a
								Latin dance party in a square on the pedestrian mall.&nbsp; I sat and watched for a
								while.&nbsp; Everyone seemed so happy.<br>
								<br>
								Toppling Goliath - In a pub in Iowa, the guy next to me starts chatting me up about
								brewing beer.&nbsp; He was very interested in the hand-crafted beers in Colorado.&nbsp;
								When I asked him if there were any good Iowan beers, he recommended &ldquo;Toppling
								Goliath&rdquo;.&nbsp; I ordered one, and he was right.&nbsp; It was really good!<br>
								<br>
								Leaving just when things get good &ndash; Sometimes it seems like all I do on the road
								is say goodbye.&nbsp; Before a show, I usually get to meet nice folks, and during and
								right after a show there&rsquo;s usually a lot of positive energy and kind words from
								the crowd.&nbsp; But after the show is done, I just pack up and move on.&nbsp; I felt
								this particularly after my last show in Iowa because the folks at the venue took me out
								to lunch.&nbsp; It was so nice getting to meet them and chat.&nbsp; But then, like
								always, they had to get on with the rest of their day, and I had to get back on the
								road.<br>
								<br>
								Driving into a storm &ndash; Just outside of South Bend Indiana, I drove underneath one
								of the scariest looking clouds I have seen.&nbsp; There wasn&rsquo;t any rotation, but
								it just didn&rsquo;t look right.&nbsp; Then the sky opened up and I could barely see
								through the rain and wind.&nbsp; It was a little frightening.<br>
								<br>
								If I&rsquo;m lucky I&rsquo;ll have a place to lay my head &ndash; After leaving Indiana,
								I headed to Traverse City, but I wasn&rsquo;t 100% sure I had a place to stay.&nbsp; I
								had been assured that there would be somewhere for me to crash, but nothing was nailed
								down.&nbsp; In fact, I didn&rsquo;t even know where I would be performing or where to
								meet anyone.&nbsp; I typically over-plan things, so this was a very unusual leap of
								faith for me.<br>
								<br>
								There were a few things that I jotted down that didn&rsquo;t make the lyrics, and one of
								them worth mentioning was &ldquo;Master Retarder&rdquo;.&nbsp; This was a train term I
								overheard the guide at the rail yard in Nebraska say.&nbsp; It&rsquo;ll take a better
								songwriter than me to work that into a song smoothly.
							</p>
						</td>
					</tr>
					<tr>
						<td align="center">
							<strong>The Lyrics </strong>
						</td>
					</tr>
					<tr>
						<td align="left">
							<p>I had to get back from Traverse City to Colorado in 2 days in order to catch a flight to
								San Diego, CA where my family would be waiting for me.&nbsp; The first day I got up at
								the crack of dawn and drove through to Des Moines, IA.&nbsp; <br>
								<br>
								During the ride, I pieced together the thoughts I had jotted down into semi-coherent
								lyrics.&nbsp; They changed a little bit afterwards, but not much.&nbsp; I didn&rsquo;t
								have any music, but the lyrics at least were done.&nbsp; The problem was I had nothing
								to work on for the next day.&nbsp; I should&rsquo;ve paced myself.&nbsp; <br>
								Oh well, at any rate the lyrics are&hellip;<br>
								<br>
								I&rsquo;ll see you in a week, 10 days at the most<br>
								I do recall the promise that I made<br>
								But a promise is a bird that can&rsquo;t be kept for long<br>
								And how long ago that was I cannot say<br>
								The river&rsquo;s always flowing slowly out of town<br>
								In the rail yard they&rsquo;re hitching up those trains<br>
								Seems nothing here was ever really meant to stick around<br>
								So I suppose that I&rsquo;ll be on my way<br>
								And you can steal this song<br>
								I won&rsquo;t mind<br>
								I&rsquo;ll move on and leave it behind<br>
								And when you say that it&rsquo;s yours<br>
								I won&rsquo;t say a word<br>
								I&rsquo;m headed into the mouth of another storm<br>
								Even so, it&rsquo;s always just the same<br>
								The mile ahead exactly like the one I left behind<br>
								I&rsquo;m not expecting too much of a change<br>
								Seems I&rsquo;m always leaving just when things get good<br>
								I&rsquo;ll never know how anything turns out<br>
								So while you&rsquo;re back there dancing in the square, I&rsquo;ll be here<br>
								Miles away, watching rain come down<br>
								And you can steal this song<br>
								I won&rsquo;t mind<br>
								I&rsquo;ll move on and leave it behind<br>
								And when you say that it&rsquo;s yours<br>
								I won&rsquo;t say a word<br>
								How can you ever know just how far it is<br>
								When you don&rsquo;t have anywhere to go<br>
								But if my luck holds I might find a place to lay my head<br>
								So I suppose I&rsquo;ll move on down the road<br>
								You never know what giants you might have to fight<br>
								Underdogs do not get to choose<br>
								So find me in the bar toppling Goliath<br>
								With a cup of beer and nothing left to lose<br>
								And you can steal this song<br>
								I won&rsquo;t mind<br>
								I&rsquo;ll move on and leave it behind<br>
								And when you say that it&rsquo;s yours<br>
								I won&rsquo;t say a word<br>
							</p>
						</td>
					</tr>
					<tr>
						<td align="center">
							<strong>The Music </strong>
						</td>
					</tr>
					<tr>
						<td align="left">
							<p>
								Now that I had lyrics for my &ldquo;road song&rdquo;, I needed some music.&nbsp; In the
								spirit of the project, I wanted to experiment a bit.&nbsp; First of all I decided to
								write the song in a DADGAD tuning.&nbsp; This is just an alternate tuning for a guitar
								and a very popular one for songwriters.&nbsp; I write and perform in several different
								tunings, but for some reason, I have never written or even learned a song in DADGAD, so
								I thought this would be a good chance to learn a little.<br>
								<br>
								I also decided that I would use my dobro on song.&nbsp; It&rsquo;s a lap guitar that you
								play with finger picks and a slide bar.&nbsp; I&rsquo;m not very good at the dobro, but
								I LOVE to play it.&nbsp; I figured there was no harm since this song was just an
								experiment anyway.<br>
								<br>
								Finally I decided to attempt to sing the song very quietly.&nbsp; This is not my
								forte.&nbsp; I feel much more comfortable belting out something.&nbsp; I just
								don&rsquo;t think my voice is strong enough to sing quietly.&nbsp; I am amazed at the
								singers who can sing so beautifully in a whisper.&nbsp; But again, there was no
								risk.<br>
								<br>
								I wrote the basis for the song one night in a hotel in Alamosa, CO and recorded it back
								in my home studio.&nbsp; As I expected, I&rsquo;m not thrilled with the vocals and in
								terms of dobro playing I&rsquo;m no Jerry Douglas, but it is what it is.&nbsp;&nbsp;
								Here the rough mix.&nbsp; The file is a little large and might take some time to
								download&hellip;&nbsp;&nbsp; <br>
							</p>
							<a href="http://steveweeksmusic.com/mp3/StealThisSongRoughMix.mp3"><br>
								http://steveweeksmusic.com/mp3/StealThisSongRoughMix.mp3</a>&nbsp;&nbsp; <br>
						</td>
					</tr>
					<tr>
						<td align="center">
							<strong>The Video </strong>
						</td>
					</tr>
					<tr>
						<td align="left">
							<p> While in Durango, CO for performances, I decided that I would try and make an amateur
								video for &ldquo;Steal This Song&rdquo;.&nbsp; I took a pen, paper and my iPhone to
								dinner that evening.&nbsp; I went to a nice Irish pub in downtown Durango, and took
								random video snippets of the bar.&nbsp; I also took video of me writing out the lyrics
								at the bar.&nbsp; I wasn&rsquo;t sure how this would make a video, but it was the idea I
								had a t the time.<br>
								<br>
								When I went back to my motel, I decided not to take the free trolley and walk
								instead.&nbsp; All along the route, I took more random video of Durango at night which
								was fairly deserted at the time.&nbsp; The entire trip the battery on my iPhone was
								threatening to run out, so I was frantically trying to get as much footage as I
								could.&nbsp; There wasn&rsquo;t much to see, just streetlights, signs and roads.&nbsp;
								It&rsquo;s what I had to work with.
							</p>
							</p>
							</p>
							<p><a href="http://youtu.be/s-LxQNlxQe8?list=UUaGCIgtX51I_TnfVcs6lauw" target="_blank">Steal
									This Song Video</a></p>
							So there it is.&nbsp; My little songwriting journey from inspiration to completed
							tune.&nbsp; I can now say that I have a road song even if it is a bit experimental
							<p class="style1">&nbsp;</p>
							<p>&nbsp;</p>
						</td>
					</tr>
				</table>
			</TD>
		</TR>
	</TABLE>

	<!-- Piwik -->
	<script type="text/javascript">
		var pkBaseURL = (("https:" == document.location.protocol) ? "https://www.offthekitchen.com/piwik/" : "http://www.offthekitchen.com/piwik/");
		document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
		try {
			var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 2);
			piwikTracker.trackPageView();
			piwikTracker.enableLinkTracking();
		} catch (err) { }
	</script><noscript>
		<p><img src="http://www.offthekitchen.com/piwik/piwik.php?idsite=2" style="border:0" alt="" /></p>
	</noscript>
	<!-- End Piwik Tracking Code -->

</BODY>

</HTML>