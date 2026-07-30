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
	$sPageName = "Tehúsið";
	$sPageTitle = "Tehúsið";
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
				<img src="./images/tehusid-banner.png"  class="responsive">				
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
				They aren't kidding when they say the weather in Iceland can be unpredictable.&nbsp;&nbsp;The current conditions on my phone's 
				weather app were wrong 90% of the time, so when some of the locals advised us strongly against driving to H&uacute;sav&iacute;k 
				the next day even though the forecast seemed OK, we booked another night at our hostel and spent a second day in Akureyri.&nbsp;&nbsp;
				Hannah told me that there would be live music again that night, so after taking in the local art museum and botanical gardens, we grabbed
				a wonderful dinner and showed up about 8:30 pm, which had been my approximate start time, only to find a 2 guys packing up their guitar 
				and trumpet.&nbsp;&nbsp;We missed them!
                </p>
			</div>
			<div class="col-xs-12 col-sm-7 col-md-9 col-lg-9">
				<p>
				The next day things were clear, so we parted ways with Bill and Maria, who were returning to the airport to continue on to Ireland,
				and hit the road for Egilssta&eth;ir where I had my third gig booked at a hostel pub called <a href="https://tehusidhostel.is/" target="_blank">Teh&uacute;si&eth;</a>. 
				After doing some hiking around the geothermically active area of Myvatn, we began the drive through through the highlands to the Eastern
				side of Iceland.&nbsp;&nbsp;The road conditions were really icy in some parts, and there were lots of cars off the road, some on their sides
				or roofs.&nbsp;&nbsp;We had made the right decision staying the night in Akureyri.	 
				</p>
				<p>
					En route, I got an email from Halldor (Dori), the owner of Teh&uacute;si&eth;, asking how long my set was.&nbsp;&nbsp;He was considering
					having two acts that night.&nbsp;&nbsp;If Iceland had taught me anything, it was to be flexible, and as everyone had been so gracious to 
					me on this trip, I told him I'd play for as little or as long as he needed.&nbsp;&nbsp;About an hour later, I got another email, this 
					time to me and an address for someone called Sleepwalker Station, asking both acts if we were going to make it with the bad road conditions
					and asking if, given the probably low turnout due to the weather, we should cancel altogether.&nbsp;&nbsp;I responded that we would be there and 
					that I really wanted to play no matter the crowd.&nbsp;&nbsp;So Dori said we were on.    
				</p>
			</div>
			<div class="col-xs-12 col-sm-5 col-md-2 col-lg-2">
				<img src="./images/th1.png"  class="responsive venue-img">
			</div>	
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-7 col-sm-push-5 col-md-9 col-md-push-2">
				<p>
				We arrived and the staff Nikola and Piotr got us set up with a nice clean room and a wonderful meal.&nbsp;&nbsp;They showed me where 
				I'd be performing, a beautiful corner of the pub with warm lights, a piano and instruments on the wall.&nbsp;&nbsp;I was setting up when 
				Dori showed up.&nbsp;&nbsp;He was super nice and welcoming and a musician and sound engineer himself.&nbsp;&nbsp;So when he grabbed his 
				tablet and started adjusting things, it suddenly sounded incredible.&nbsp;&nbsp;I started to suspect this might be another great show.&nbsp;&nbsp;  
				He said he never heard from the other band.&nbsp;&nbsp;They had been scheduled to play the night before at the big farewell party for all the 
				seasonal foreign workers' last night in Iceland, but the weather had prevented them from making it.&nbsp;&nbsp;
				As we were talking the band walked in, two musicians named Daniel and Nick&nbsp;&nbsp;I told them was happy to be able to 
				help them out and that I'd cut my set short so that they could share the stage.
				</p>
			</div>
			<div class="col-xs-12 col-sm-5 col-sm-pull-7 col-md-2 col-md-pull-9">
				<img src="./images/th2.png"  class="responsive venue-img">
			</div>	
		
		</div>
		<div class="row">

			<div class="col-xs-12 col-sm-7 col-md-9 col-lg-9">
				<p>
				Once again, the show went really well.&nbsp;&nbsp;The sound was amazing, and the crowd and staff were really supportive.&nbsp;&nbsp;
				Piotr lit a candelabra which added to the ambiance.&nbsp;&nbsp;I could tell people in the crowd were enjoying themselves which always gives me 
				confidence and makes me feel appreciated.&nbsp;&nbsp;The Icelanders were patient and encouraging with my Icelandic.&nbsp;&nbsp;
				I toned things down a bit to match the chill vibe of the room.&nbsp;&nbsp;
				The surprising thing was that the crowd continued to grow until it was fairly full.&nbsp;&nbsp;It turns out that the winter storm the day
				before prevented the seasonal workers from catching their flights, so they were back for another final farewell.&nbsp;&nbsp;Overall, I had a 
				blast playing in such a beautiful and imtimate space.				
				</p>
			</div>
			<div class="col-xs-12 col-sm-5 col-md-2 col-lg-2">
				<img src="./images/th3.png"  class="responsive venue-img">
			</div>	
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-7 col-sm-push-5 col-md-9 col-md-push-2">
				<p>
				After I finished, I joined Mary to listen to Sleepwalker's Station.&nbsp;&nbsp;As they set up, Nick pulled out a trumpet.&nbsp;&nbsp;
				This was the band that we had missed in Akureyri!&nbsp;&nbsp;It all made sense now.&nbsp;&nbsp;They also had gotten delayed an extra day
				in Akureyri and had booked a last minute show at Akureyri Backpackers.&nbsp;&nbsp;It was good to get to see them.&nbsp;&nbsp;They were a 
				really fun and talented act.&nbsp;&nbsp; 
				</p>
				<p>
					We also met a local guy Jonni who is a musician, drum teacher and guitar addict.&nbsp;&nbsp;It was really fun chatting with him 
					about guitars, love of music and touring.&nbsp;&nbsp;He was very passionate about all which I can totally relate to.&nbsp;&nbsp;
					I hope our paths cross again.&nbsp;&nbsp;Dori suggested that I return someday in the Summer when the crowds are more consistent.
					&nbsp;&nbsp;
				</p>
				<p>
					You don't have to twist my arm.
				</p>
			</div>
			<div class="col-xs-12 col-sm-5 col-sm-pull-7 col-md-2 col-md-pull-9">
				<img src="./images/th4.png"  class="responsive venue-img">
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
