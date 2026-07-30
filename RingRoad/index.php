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

	//include Performance Class
	include_once(CLASS_DIR . "/class_Tour.php");

	//Page Name 
	$sPageName = "The Steve Weeks Ring Road Tour";
	$sPageTitle = "Ring Road Tour";
	//Custom FB Image
	$sFBImage = "FB_RingRoad.png";


	$sActiveMenuItem = HOME_ACTIVE;	

	$oTour = new Tour();
	$oTour->nTourID = 114;
	$oTour->getTour();
	$oTour->getTourPerformances();
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
				<img src="./images/ring-road-banner.png"  class="responsive">				
			</div>		
		</div>
		<div class="row">
			<div class="col-xs-12 ring-road-summary">
				<p>It was a crazy idea I know.&nbsp;&nbsp;But crazy ideas are something that I seem to have no shortage of.</p>
				<p>In 2019, I had a gig in Manchester, UK, and I travelled on IcelandAir.&nbsp;&nbsp;Bill, an college friend, who was 
					travelling with me, and I opted for the 3-day layover in Reykjav&iacute;k on the return trip, and since I had my guitar
					in tow, I decided to see if I could get a gig while there.&nbsp;&nbsp;I was lucky enough to book a performance in Akureyri,
					so we drove the 5 hours (actually 7 including a side-trip to Kirkjufell) around the ring road to Akureyri Backpackers.  
				</p>
				<p>
					It was an amazing evening.&nbsp;&nbsp;I don't think I've ever felt as welcome at a venue as I did in that little pub in the
					North of Iceland.&nbsp;&nbsp;Everyone there was so helpful and kind.&nbsp;&nbsp;When I got there, local folks came out of the 
					woodwork with equipment and set up a sound system, and everyone was so patient with my poor Icelandic and 
					encouraged me.&nbsp;&nbsp;It was magic.  
				</p>
				<p>
					When the show was over, we stayed up way later than we intended talking with all the new friends we had made, and Bjarni,
					an Icelander who had helped me book the gig, said to me, "I think you should do this in towns around the ring road.&nbsp;&nbsp;I think it would work."&nbsp;&nbsp;
					That's all it took. 
				</p>
				<p>
					So 3 years later, despite a pandemic, travel restrictions and other obstacles, I found myself back in Iceland with my guitar.
					&nbsp;&nbsp;My wife Mary joined me as did Bill and his wife Maria.&nbsp;&nbsp;
					With 4 shows around the ring road, I was able to see more of Iceland and	meet more of it's wonderful people.&nbsp;&nbsp;
					And once again it was magic!  
				</p>
			</div>
			<div class="col-xs-12">
				<ul class="venues-list">

					<li class="venue-item">
						<div class="row">
							<div class="col-xs-5 col-md-4">
								<img src="./images/hus-mals-og-menningar.jpg"  class="responsive venue-img">
							</div>
							<div class="col-xs-7 col-md=8">
								<div class=" venue-details">
									<a href="https://husmalsogmenningar.is/" target="_blank" class="venue-link">H&uacute;s M&aacute;ls og Menningar</a>
									<div class="venue-city">Reykjav&iacute;k</div>
									<div class="venue-summary">
										My first gig was in the capital city of Reykjav&iacute;k at H&uacute;s M&aacute;ls og Menningar
										(Art and Culture House), a bookstore that doubles as a cocktail bar and live music venue.&nbsp;&nbsp; 
										As I had just landed in Iceland 12 hours before my set and hadn't gotten much sleep, I was running on
										fumes, but the excitement of performing in Iceland at such a great venue, kept me going.
									</div>
									<div >
										<a href="./hus-mals-og-menningar.php" class="read-more-link">Read More</a>
									</div>
								</div>
							</div>
						</div>

					</li>
					<li class="venue-item">
						<div class="row">

							<div class="col-xs-7 col-sm=9 col-md-10">
								<div class=" venue-details">
									<a href="https://www.akureyribackpackers.com/en" target="_blank" class="venue-link">Akureyri Backpackers</a>
									<div class="venue-city">Akureyri</div>
									<div class="venue-summary">
									My second show was at <a href="https://www.akureyribackpackers.com/" target="_blank">Akureyri Backpackers</a>, a hostel on the main street downtown with a pub on the first floor.&nbsp;&nbsp;
									This was the same venue at which I had performed during my last trip to Iceland, so it was exciting and a little surreal to 
									be back after 3 years.
									</div>
									<div >
										<a href="./akureyri-backpackers.php" class="read-more-link">Read More</a>
									</div>
								</div>
							</div>
							<div class="col-xs-5 col-sm-3 col-md-2">
								<img src="./images/akureyri-backpackers.png"  class="responsive venue-img">
							</div>
						</div>

					</li>
					<li class="venue-item">

					<div class="row">
							<div class="col-xs-5 col-sm-3">
								<img src="./images/tehusid.png"  class="responsive venue-img">
							</div>
							<div class="col-xs-7 col-sm=9">
								<div class=" venue-details">
									<a href="https://tehusidhostel.is/" target="_blank" class="venue-link">Teh&uacute;si&eth;</a>
									<div class="venue-city">Egilssta&eth;ir</div>
									<div class="venue-summary">
										We almost didn't make it to Eastern Iceland because of an unexpected winter storm, but persistence 
										and patience paid off.&nbsp;&nbsp;We did make it, and I got to play at a wonderful little pub and 
										share the stage with another fun music act. 
									</div>
									<div >
										<a href="./tehusid.php" class="read-more-link">Read More</a>
									</div>
								</div>
							</div>
						</div>
												
					</li>
					<li class="venue-item">
						<div class="row">

							<div class="col-xs-7 col-md=8">
								<div class=" venue-details">
									<a href="https://skoolbeans.com/" target="_blank" class="venue-link">Skool Beans</a>
									<div class="venue-city">V&iacute;k</div>
									<div class="venue-summary">
									I played in a school bus at the foot of a volcano under a glacier by the sea in Icleand.&nbsp;&nbsp;Yeah, I typed that correctly.&nbsp;&nbsp;
									</div>
									<div >
										<a href="./skool-beans.php" class="read-more-link">Read More</a>
									</div>
									
								</div>
							</div>
							<div class="col-xs-5 col-md-4">
								<img src="./images/skool-beans.png"  class="responsive venue-img">
							</div>
						</div>
						
						
					</li>

				</ul>
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
    	font-family: "Garamond";
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
