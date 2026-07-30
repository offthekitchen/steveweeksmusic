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
	$sPageName = "Skool Beans";
	$sPageTitle = "Skool Beans";
	//Custom FB Image
	$sFBImage = "FB_RingRoad.png";


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
			<div class="col-xs-12">
				<img src="./images/skool-beans-banner.png"  class="responsive">				
			</div>		
		</div>
		<div class="row">
            <div >
                <a href="./index.php" class="read-more-link">BACK</a>
            </div>
		</div>
		<div class="row">
			<div class="col-xs-12 ring-road-summary">
                <p>
					I played in a school bus at the foot of a volcano under a glacier by the sea in Icleand.&nbsp;&nbsp;Yeah, I typed that correctly.&nbsp;&nbsp;
                </p>
				<p>
					When I was planning a tour of Iceland's famous Ring Road, I really became attached to the idea of having a show in 
					each of the four points of the compass.&nbsp;&nbsp;There seemed to be a balance in that plan, and it would give me 
					the opportunity to experience the different regions of the country.&nbsp;&nbsp;I had shows in the West (Reykjav&iacute;k), 
					North (Akureyri), and East (Egilssta&eth;ir), but I was struggling to land a gig in the South.&nbsp;&nbsp;There's a series of
					towns along the southern coast of Iceland, but they are all very small providing few options for venues.&nbsp;&nbsp;I had 
					been concentrating on the town of V&iacute;k, known for it's black sand beaches and nearby glacier.&nbsp;&nbsp;What's more 
					there's a brewery there called Smi&eth;jan.&nbsp;&nbsp;I play in brewpubs quite a bit, so I kept reaching out to 
					them.&nbsp;&nbsp;But I just wasn't getting any traction.
				</p>
			</div>
			<div class="col-xs-12 col-sm-9">

				<p>
				I decided that if I was going to be able to make a show happen in the South, I was going to have to get creative.&nbsp;&nbsp;
				So I opened up Google maps, typed in "V&iacute;k" and looked for anything on the map that had a food icon.&nbsp;&nbsp;I figured 
				anywhere people gathered was a potential venue, even if it was a cafe in the gas station.&nbsp;&nbsp;One of the places that caught 
				my eye was a coffee cup icon that marked something called Skool Beans, but the listing said it closed at 5:00 pm, not exactly 
				promising hours for a music show.&nbsp;&nbsp;Regardless, I decided to give it a try, so I emailed asking about the potential of
				booking a gig.&nbsp;&nbsp;  
				</p>
	
				<p>
				The email I got back from the owner Holly, was not only in the afirmative but also exuded positivity.&nbsp;&nbsp;
				Her email didn't just say "yes", it said "YEEEEEEEEESSSSSSS!!!".&nbsp;&nbsp;She sounded really excited about the idea and 
				told me her coffee shop was actually in an old converted school bus!&nbsp;&nbsp;The space would be tight; There would be no 
				sound system; They had never hosted live music;&nbsp;&nbsp;
				But if I was game, she was in.&nbsp;&nbsp;I had found my collaboratuer! 
				</p>
			</div>
			<div class="col-xs-12 col-sm-3">
				<img src="./images/sb1.png"  class="responsive venue-img">
			</div>	
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-push-3 col-md-9">

				<p>
					Over the next few weeks Holly and I corresponded back and forth about the gig.&nbsp;&nbsp;Every email I got from her was 
					filled with joy, positive vibes and lots of words in all caps.&nbsp;&nbsp;And with every email, I got more and more excited.&nbsp;&nbsp; 
					Her positivity was infectious!&nbsp;&nbsp;I think she was a bit nervous that I would look down my nose at such a small and informal venue.&nbsp;&nbsp;Little did she know that 
					I was more excited about this gig than any other one I had ever played.&nbsp;&nbsp;I mean, I was getting to play in a singularly
					unique space in a beautiful town in an amazing country!&nbsp;&nbsp;I was treating this gig like I was playing at Wembley Stadium.
					&nbsp;&nbsp;I even made a concert poster just for the occassion.
				</p>
				<p>
					In one of these email exchanges, I sent Holly a link to a <a href="https://youtu.be/fiShsfvbFUA" target="_blank">video</a> of the group Scary Pockets covering the song "MMMM Bop" because 
					I thought she'd find it uplifting.&nbsp;&nbsp;Her response was not only brimming with her usual joy, it also included a 
					request that I play "MMM Bop" at the show.&nbsp;&nbsp;And she said that the volcano had errupted, so I might have a good backdrop 
					for photos.&nbsp;&nbsp;I decided that I would be remiss if I didn't play Jimmy Buffet's "Volcano" during the gig.&nbsp;&nbsp;
					Then, when I was having a pint with my friend Andy, he suggested that since I was playing in a bus, I should learn the 
					Who's "Magic Bus".&nbsp;&nbsp;So it looked like I would be learning 3 new songs for this gig.  
				</p>
				<p>
					All of that is a pretty tall order, so I decided to mash up all three 
					songs into a medley instead.&nbsp;&nbsp;And what's more, I rewrote some of the words to make the song a little more 
					personal.&nbsp;&nbsp;I didn't know if I could pull it off or how it would go over, but one thing was for sure, we were going 
					to have some fun with this one.  
				</p>
				<p>				
					The day that Mary and I pulled into V&iacute;k, it was raining.&nbsp;&nbsp;In fact, it was the only entirely rainy day during 
					our whole trip..&nbsp;&nbsp;We had been hiking earlier to a waterfall called Svartifoss, so we were still in rain gear and 
					hiking boots..&nbsp;&nbsp;I thought I'd grab a quick shower before the show, but we couldn't check into our hotel until later
					in the day, so it appeared I'd be performing in hiking gear.&nbsp;&nbsp;Kind of appropriate acrtually.  
                </p>
			</div>
			<div class="col-xs-12 col-md-3 col-md-pull-9">
				<img src="./images/sb2.png"  class="responsive venue-img">
			</div>	
		
		</div>
		<div class="row">
			<div class="col-xs-12 ring-road-summary">

			</div>
			<div class="col-xs-12 col-md-9">
				<p>
				So we headed over to the bus.&nbsp;&nbsp;It's right beside a campground, so I wasn't the only person wearing hiking clothes for 
				sure.&nbsp;&nbsp;On arriving, I was told that Holly was on her way and had given explicit instructions that no one was to make a 
				move once I got there until she could come greet me.&nbsp;&nbsp;After a short time she came through the door like a whirlwind 
				of happiness and sunshine yelling "Steeeevviieee  Weeeeeeeks!".&nbsp;&nbsp;All of the wonderful emails suddenly made sense.  
				</p>
				<p>
				Mary and I both agree that Holly is one of the most joyful, uplifting people we've ever met.&nbsp;&nbsp;She works really hard 
				to run her coffee shop and make every person who climbs onboard feel safe and welcome.&nbsp;&nbsp;Mary said she thinks people 
				are a better version of themselves on the bus.&nbsp;&nbsp;I agree.  
				</p>			
				<p>
				Oh, and the coffee.&nbsp;&nbsp;We were served the best cup of coffee we had in all of Iceland by the barista Adonis and Holly's 
				step-mom Jackie.&nbsp;&nbsp;I had been told by three separate people on our travels that the coffee was amazing, and they 
				were right!
				</p>
				<p>
				Eventually I broke out the guitar and began to play.&nbsp;&nbsp;It was pure joy.&nbsp;&nbsp;It was raining outside, but Holly 
				has installed a wood fireplace in the bus and would occassionally put another log on the fire.&nbsp;&nbsp;Everyone there was 
				smailing and seemed to be aware how unique and magic this concert was.&nbsp;&nbsp;I felt really welcome, supported and 
				appreciated, which just filled my heart to the brim with gratitude.&nbsp;&nbsp;Holly's Dad, who was visting to help out, showed up and we 
				had fun talking to him about how his little girl had come to starting a coffee shop in a bus in the far reaches of Iceland.
				&nbsp;&nbsp;It was absolute magic.     
				</p>
			</div>
			<div class="col-xs-12 col-md-3">
				<img src="./images/sb3.png"  class="responsive venue-img">
			</div>	
		</div>
		<div class="row">


			<div class="col-xs-12">
				<p>				
				Then I decided to try out my MMMM Bop/Volcano/Magic Bus creation.&nbsp;&nbsp;Holly seemed thrilled that I had come through 
				on my prommise, and when I got to the part with the custom lyrics, she started laughing and crying.&nbsp;&nbsp;Everyone seemed 
				to really enjoy the whole thing.&nbsp;&nbsp;I know I did. 	
				</p>
			</div>	
			<div class="col-xs-12">
				<p>
				The next day, before we left, we went back to Skool Beans to get a poster.&nbsp;&nbsp;I wanted to hang one in my music studio to
				remind me of one of the most wonerful moments I've had in my music career.&nbsp;&nbsp;Once again Holly greeted us like we were 
				the royal family, and as we finally left, I tapped on the window to say goodbye.&nbsp;&nbsp;She yelled, "Wait, I'm coming out!".&nbsp;&nbsp;
				I thought she would head for the door, but instead she leaned out of the bus window and hugged us both warmly.&nbsp;&nbsp;It was one 
				of my favorite moments of the whole trip.&nbsp;&nbsp;  If you visit Iceland, go to V&iacute;k for a cup of coffee and some joy.&nbsp;&nbsp;
				You won't regret it. 	
				
				</p>	
			</div>	
			<div class="col-xs-12 col-md-3" style="text-align:center; margin-top: 10px; margin-bottom: 10px;">
				<iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fpermalink.php%3Fstory_fbid%3Dpfbid026PUe8rMENbZyCXMbgsguyrXNbGzcRdQcowbrhMAwMw2za2ET8amWCHG1k6wGtGb9l%26id%3D1260725962&show_text=true&width=500" width="250" height="550" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
			</div>
			<div class="col-xs-12 col-md-5" style="text-align:center; margin-top: 10px; margin-bottom: 10px;">
				<iframe width="560" height="315" src="https://www.youtube.com/embed/CJp-AWNnvxs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
			</div>
			<div class="col-xs-12 col-md-4" style="text-align:center; margin-top: 10px; margin-bottom: 10px;">
				<img src="./images/sb5.png"  class="responsive venue-img">	
			</div>
		</div>
		<div class="row">
            <div >
                <a href="./index.php" class="read-more-link">BACK</a>
            </div>
		</div>
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/footer.php");
		?>		
		</div>
	</div>
	<style>
	.align-center {
		text-align: center;
	}
	.responsive {
		max-width: 100%;
	}
	ul.venues-list {
  		list-style-type: none;
		text-align: left;
 	}
	.venue-item {
	  font-family: 'Amatic SC', bold;
	  padding: 5px;
	  margin-bottom: 5px;
	  border: 2px solid darkred;
	  border-radius: 5px;
	}
	.venue-link {
	  font-size: 30px;
	}
	.venue-city {
		font-size: 18px;
		font-family: Verdana, Geneva, Tahoma, sans-serif;
	}
	.venue-details{
		padding: 10px;
	}
	.venue-summary{
		font-family: Garamond;
		font-size: 16px;
	}
	.ring-road-summary {
		margin: 20px 0;
	}
	.performance-name {
		display: none;
	}
	.performance-info{
		margin: 5px;
	}
	.details-button {
		margin: 0px;
		background-color: darkred;
		font-size: 16px;
	}
	.details-button:hover {
		margin: 0px;
		background-color: red;
	}
	.venue-img  {
		display:block;
    	margin:auto;
	}
	.read-more-link{
		display: block;
		margin: 10px auto;
		text-align: center;
		width: 100px;
		padding: 5px;
    	background-color: #009933;
		color: #FFFFFF;
		border-radius: 5px;
		font-size: 18px;
	}
	.read-more-link:hover{
		background-color:#006633;
		color:#FFFFFF;
	}
	.read-more-link a:hover{
		color:#FFFFFF;
	}
	.read-more-link a:visited{
		color:#FFFFFF;
	}
	</style>
</BODY>
</HTML>
