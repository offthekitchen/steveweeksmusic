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
	$sPageName = "Hús Máls og Menningar";
	$sPageTitle = "Hús Máls og Menningar";
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
				<img src="./images/hus-mals-og-menningar-banner.png"  class="responsive">				
			</div>		
		</div>
		<div class="row">
			<div >
                <a href="./index.php" class="read-more-link">BACK</a>
            </div>
		</div>
		<div class="row">
			<div class="col-xs-12 ring-road-summary">
                <p>My first gig was in the capital city of Reykjav&iacute;k at <a href="https://husmalsogmenningar.is/" target="_blank">H&uacute;s M&aacute;ls og Menningar</a>
				    (Art and Culture House), a bookstore that doubles as a cocktail bar and live music venue.&nbsp;&nbsp; 
					As I had just landed in Iceland 12 hours before and hadn't gotten much sleep, I was running on
					fumes, but the excitement of performing in Iceland at such a great venue, kept me on my feet.
                </p>
			</div>
			<div class="col-xs-12 col-sm-7 col-md-9 col-lg-9">
				<p>
				I was especially excited about this show because my wife Mary was with me on this trip and would get to experience first hand 
				all the wonderful things about Iceland I had been telling her about.&nbsp;&nbsp;My friends Bill and Maria were also with us for the first 4 days of this trip which meant that they
				would be at the first 2 shows.&nbsp;&nbsp;I was also a little nervous as H&uacute;s M&aacute;ls og Menningar was a pretty well known 
				live music venue hosting some serious acts.&nbsp;&nbsp;A friend back in Colorado had told me that he had gone there to see a blues
				band when he was in Iceland earlier in the year.&nbsp;&nbsp;Was this stage too big for the likes of me?&nbsp;&nbsp;Would I fall asleep
				in the middle of my performance?
				</p>
				<p>
				When I walked through the door at H&uacute;s M&aacute;ls og Menningar, I knew it was a special place.&nbsp;&nbsp;There are books everyehwere!&nbsp;&nbsp;
				2 stories of them!&nbsp;&nbsp;There's a cocktail bar in the front and a balcony overlooking a very prominent and professional stage.
				&nbsp;&nbsp;Music is obviously a very important part of the vibe here.&nbsp;&nbsp;In fact, on the outside there is permanent signage that 
				says "Live Music Yesterday,m Live Music Today, Live Music Tomorrow".&nbsp;&nbsp;My kind of place.  
				</p>
			</div>
			<div class="col-xs-12 col-sm-5 col-md-2 col-lg-2">
				<img src="./images/hmom1.png"  class="responsive venue-img">
			</div>	
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-7 col-sm-push-5 col-md-9 col-md-push-2">
				<p>
				As I began to set up my equipment, I noticed that all of the cables on the sound system were labeled with specifiic names like
				"Kjartan" and "Sibbi", and there was a set list on the floor.&nbsp;&nbsp;I was a bit tentative about rearranging things too much.
				&nbsp;&nbsp;If I've learned one thing from J. R. R.Tolkien, it's to be careful about messing with objects that have names.&nbsp;&nbsp;
				So I decided to skip the vocal effects unit and simplify my setup to just one vocal mic and my guitar looper.&nbsp;&nbsp; 
				</p>
				<p>
					Ella, who had booked me for this gig came over and welcomed me and helped me get things set up.&nbsp;&nbsp;She also explained that
					there was another band performing after me and that I'd need to clear off by 7:30 pm.&nbsp;&nbsp;The start and end time of my performance
					had been in flux for a while.&nbsp;&nbsp;One thing I've learned from performing in Iceland is that Icelanders are really laid back and
					just go with the flow.&nbsp;&nbsp;They have a saying "&THORN;etta Reddast", which kind of means "so it goes".&nbsp;&nbsp;
					It's a nice casual attitude to have and necessary when unpredictable weather and long distances can change your plans at the 
					drop of a hat.&nbsp;&nbsp;I've learned to just go with it and be flexible, a good exercise for me.
				</p>
			</div>
			<div class="col-xs-12 col-sm-5 col-sm-pull-7 col-md-2 col-md-pull-9">
				<img src="./images/hmom2.png"  class="responsive venue-img">
			</div>	
		
		</div>
		<div class="row">
			<div class="col-xs-12 ring-road-summary">
                <p>				
				The crowd was a little light when I started.&nbsp;&nbsp;6:00 pm was a little early for this venue.&nbsp;&nbsp;But as I played,
				people began to gather.&nbsp;&nbsp;I think music attracts a crowd naturally.&nbsp;&nbsp;Eventually there were people on both levels,
				and almost all of the tables were filled.&nbsp;&nbsp;People seemed to be enjoying themselves.&nbsp;&nbsp;And the negroni that 
				the bartender made for me was amazingly good.&nbsp;&nbsp;Looking out and seeing familiar friendly faces made me feel that it was going to be alright.
				&nbsp;&nbsp;A couple of times I looked down and thought, "Wait, I don't know how to play AC/DC's 'Highway to Hell'.  Why is it on my set list?!?",
				before realizing that I was looking at the set list left on the stage.
                </p>
			</div>
			<div class="col-xs-12 col-sm-7 col-md-9 col-lg-9">
				<p>
				Eventually, a guy came in with a guitar.&nbsp;&nbsp;He looked a little confused when he saw me.&nbsp;&nbsp;It turns out that the regular
				house band "Sibbi and the Guns" had no idea that I'd be performing before them, and they were probably wondering if I had stolen their
				slot.&nbsp;&nbsp;I let them know that I'd be clearing off in time for their set.&nbsp;&nbsp;They were super nice guys and very engaging and supportive.&nbsp;&nbsp;
				They seemed very interested in my looper and were listening intently.&nbsp;&nbsp;Things were going well, so I decided to try out a little Icelandic.&nbsp;&nbsp;
			</p>
			<p>
				With shaking hands, I launched into a song "Einu Sinni &Aacute; &Aacute;g&uacute;st Kv&ouml;ldi".&nbsp;&nbsp;It's older tune I had seen
				performed by Icelandic songwriter <a htrf="https://www.facebook.com/watch/?v=231449504694908" target="_blank">KK</a> in a video.&nbsp;&nbsp;
				I had actually contacted him asking for help learning it, and he was kind enough 
				to write me back with lyrics and chords.&nbsp;&nbsp;I was unsure about my ability to pronounce the words correctly and about whether locals
				would know it.&nbsp;&nbsp;Again I had nothing to worry about.&nbsp;&nbsp;The members the band sang along and seemed very impressed and 
				happy that I had attempted this song.  
			</p>
			</div>
			<div class="col-xs-12 col-sm-5 col-md-2 col-lg-2">
				<img src="./images/hmom3.png"  class="responsive venue-img">
			</div>	
		</div>
		<div class="row">
			<div class="col-xs-12">
				<p>
				The show went swimmingly from my perspective, Mary, Bill, Maria, the other musicians and the folks in the crowd said it sounded good
				and offered such kind words.&nbsp;&nbsp;Kjartan, the lead guitarist for the house band talked to me quite a bit about music,
				my looper, touring and Iceland in general.&nbsp;&nbsp;He was a really nice guy, and the whole band was very gracious for sharing their stage 
				with me.&nbsp;&nbsp;Oh, and they were also an amazing group by the way.&nbsp;&nbsp;They played cover tunes and sounded excatly like the 
				original versions.&nbsp;&nbsp;Kjartan is one of the best guitarists I've seen, pulling off Dire Straits' "Sultans of Swing" flawlessly.&nbsp;&nbsp;
				The whole band was an amazing group of musicians, an dwe had a blast hanging out and watching them.
				</p>
				<p>
					Only one show down, and I was already on cloud nine.
				</p>
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
