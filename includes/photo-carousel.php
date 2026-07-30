<!--Carousel-->
<?php 
//include Performance Class
include_once (CLASS_DIR . "/class_Performance.php");

$oPerformance = new Performance();
if(!$oPerformance->getNextPerformance())
{
	echo "ERROR RETRIEVING PERFORMANCES FOR " . $nScheduleYear . ":" . $oPerformances->sErrorMessage;	
}
else
{	
	$oNextPerformance = $oPerformance->aPerformanceRecords[0];
}
?>
<div id="photoCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
	<li data-target="#photoCarousel" data-slide-to="0" class="active"></li>
	<li data-target="#photoCarousel" data-slide-to="1"></li>
	<li data-target="#photoCarousel" data-slide-to="2"></li>
	<li data-target="#photoCarousel" data-slide-to="3"></li>
	<li data-target="#photoCarousel" data-slide-to="4"></li>
	<li data-target="#photoCarousel" data-slide-to="5"></li>
  </ol>

	<div class="carousel-inner" role="listbox">
		<div class="item active">
			<div class="slide-container">
				<a href="<?php echo IMG_DIR . "/hi-res/slide_photo1.jpg";?>">
					<div class="slide-title">Riverwalk Center, Breckenridge, CO</div>
					<img src="<?php echo IMG_DIR; ?>/slide_photo1.jpg" alt="Riverwalk Center" style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo IMG_DIR . "/hi-res/slide_photo2.jpg";?>">
					<div class="slide-title">Las Vegas, NV</div>
					<img src="<?php echo IMG_DIR; ?>/slide_photo2.jpg" alt="Las Vegas" style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo IMG_DIR . "/hi-res/slide_photo3.jpg";?>">
					<div class="slide-title">Recording "Castillo de Lodo"</div>
					<img src="<?php echo IMG_DIR; ?>/slide_photo3.jpg" alt="Castillo de Lodo" style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo IMG_DIR . "/hi-res/slide_photo4.jpg";?>">
					<div class="slide-title">Tejon Street Music</div>
					<img src="<?php echo IMG_DIR; ?>/slide_photo4.jpg" alt="Tejon Street Music" style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo IMG_DIR . "/hi-res/slide_photo5.jpg";?>">
					<div class="slide-title">Yuma, AZ</div>
					<img src="<?php echo IMG_DIR; ?>/slide_photo5.jpg" alt="Yuma, AZ" style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo IMG_DIR . "/hi-res/slide_photo6.jpg";?>">
					<div class="slide-title">Rumpus Room, Washington, DC</div>
					<img src="<?php echo IMG_DIR; ?>/slide_photo6.jpg" alt="Rumpus Room" style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo IMG_DIR . "/hi-res/slide_photo7.jpg";?>">
					<div class="slide-title">Solvang, CA</div>
					<img src="<?php echo IMG_DIR; ?>/slide_photo7.jpg" alt="Solvang, CA" style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>
		<div class="item">
			<div class="slide-container">
				<a href="<?php echo IMG_DIR . "/hi-res/slide_photo8.jpg";?>">
					<div class="slide-title">Bookmobile during a storm, Tulsa, OK</div>
					<img src="<?php echo IMG_DIR; ?>/slide_photo8.jpg" alt="Tulsa, OK" style="width:100%; height: auto;">
					<div class="slide-caption"></div>
				</a>
			</div>		
		</div>		
	</div>

	<!-- Left and right controls -->
	<a class="left carousel-control" href="#photoCarousel" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	</a>
	<a class="right carousel-control" href="#photoCarousel" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	</a>
</div>
<!--End Carousel-->