<!--Carousel-->
<?php 
//This include defines the relative path to the root directory from this sub-directory
include_once("root.inc.php");
//inlcude web site settings
include_once($ROOT . "/includes/websiteSettings.php");
//inlcude web site settings
include_once(SETTINGS_DIR . "/SteveWeeksMusicSettings.php");
//include Performance Class
include_once (CLASS_DIR . "/class_Performance.php");

$oPerformance = new Performance();
if(!$oPerformance->getNextPerformance())
{
	error_log('main_carousel.php: ERROR RETRIEVING PERFORMANCES FOR ' . $nScheduleYear . ':' . $oPerformances->sErrorMessage);
}
else
{	
	$oNextPerformance = NULL;
	if(sizeof($oPerformance->aPerformanceRecords) > 0)
	{
		$oNextPerformance = $oPerformance->aPerformanceRecords[0];
	}
	else {
		error_log('NO PERFORMANCES FOUND!');
	}
	
}
?>
<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
	<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
	<li data-target="#myCarousel" data-slide-to="1"></li>
	<li data-target="#myCarousel" data-slide-to="2"></li>
	<li data-target="#myCarousel" data-slide-to="3"></li>
	<li data-target="#myCarousel" data-slide-to="4"></li>
	<li data-target="#myCarousel" data-slide-to="5"></li>
	<li data-target="#myCarousel" data-slide-to="6"></li>
	<li data-target="#myCarousel" data-slide-to="7"></li>
  </ol>

	<div class="carousel-inner" role="listbox">
		<div class="item active">
			<div class="slide-container">
				<a href="<?php echo "{$ROOT}/schedule.php";?>">
					<div class="slide-title">Live, in Concert</div>
					<img src="<?php echo IMG_DIR; ?>/slide_schedule.jpg" alt="Steve's Schedule" style="width:100%; height: auto;">
					<div class="slide-caption"><span class="slide-overlay">see schedule</span></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<!-- <a href="https://store.cdbaby.com/cd/steveweeks12" target="_blank"> -->
				<a href="<?php echo "{$ROOT}/music.php";?>">
					<div class="slide-title">New Single (featuring Joanie Leeds)!</div>
					<img src="<?php echo IMG_DIR; ?>/slide_agree.png" alt="Agree to Disagree"  style="width:100%; height: auto;">
					<div class="slide-caption"><span class="slide-overlay">Latest release</span></div>
				</a>
			</div>	
		</div>		
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo "{$ROOT}/music.php";?>">
					<div class="slide-title">Award-winning Music</div>
					<img src="<?php echo IMG_DIR; ?>/slide_music.jpg" alt="The Music"  style="width:100%; height: auto;">
					<div class="slide-caption"><span class="slide-overlay">CDs & Singles</span></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo "{$ROOT}/performance.php";?>">
					<div class="slide-title">Book Me!</div>
					<img src="<?php echo IMG_DIR; ?>/slide_performance.jpg" alt="Book Me"  style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="http://www.offthekitchen.com/music/performance/memories-of-50-states/" target="_blank">
					<div class="slide-title">Steve has performed in 50 States!</div>
					<img src="<?php echo IMG_DIR; ?>/slide_news.jpg" alt="alt text"  style="width:100%; height: auto;">
					<div class="slide-caption"><span class="slide-overlay">Memories of 50 States</span></div>
				</a>
			</div>	
		</div>		
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo "{$ROOT}/more.php";?>">
					<div class="slide-title">Lyrics, Credentials & More</div>
					<img src="<?php echo IMG_DIR; ?>/slide_more.jpg" alt="alt text"  style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>	
		</div>		
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo "{$ROOT}/photo-video.php";?>">
					<div class="slide-title">Music Videos</div>
					<img src="<?php echo IMG_DIR; ?>/slide_video.jpg" alt="alt text"  style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo "{$ROOT}/performance.php#next-performance";?>">
					<div class="slide-title">NEXT SHOW - <?php echo "{$oNextPerformance->sPerformanceName}";?></div>
					<img src="<?php echo IMG_DIR; ?>/slide_next_show.jpg" alt="Next Show"  style="width:100%; height: auto;">
					<div class="slide-caption">
						<span class="slide-overlay">
							<?php 
							if(!empty($oNextPerformance->sLocationCity) && !empty($oNextPerformance->sLocationState)) {
								echo "{$oNextPerformance->sLocationCity}, {$oNextPerformance->sLocationState} - " . date("m-d-Y",strtotime($oNextPerformance->dtPerformanceDate));
							}
							else {
								echo "{$oNextPerformance->sPerformanceName}, {$oNextPerformance->sLocation}";
							}
							?>
						</span>
					</div>
				</a>
			</div>		
		</div>
	</div>

	<!-- Left and right controls -->
	<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	</a>
	<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	</a>
</div>
<!--End Carousel-->