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

//include Review Class
include_once(CLASS_DIR . "/class_Review.php");

//include Award Class
include_once(CLASS_DIR . "/class_Award.php");

//Page Name 
$sPageName = "Home";
$sPageDescription = "Steve Weeks Music";
$sActiveMenuItem = HOME_ACTIVE;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
//Inlcude a common <HEAD> section
include(INCLUDE_DIR . "/HTMLHead.php");
?>

<BODY>

	<?php include(INCLUDE_DIR . "/theme-image.php"); ?>
	<div class="container-fluid">
		<div class="row">
			<?php
			include(INCLUDE_DIR . "/header.php");
			?>
		</div>
		<div class="row">
			<main id="main-content" class="col-xs-12">
				<section class="row showcase-section">
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/music.php"; ?>">
							<div class="showcase-box behind-theme-image blue-box">
								Get the Music
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/performance.php"; ?>">
							<div class="showcase-box in-front-of-theme-image red-box">
								Book Me
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/contact.php"; ?>">
							<div class="showcase-box behind-theme-image purple-box">
								Contact
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-3">
						<a href="<?php echo "{$ROOT}/more.php"; ?>">
							<div class="showcase-box in-front-of-theme-image green-box">
								Find Out More
							</div>
						</a>
					</div>
				</section>
				<div class="row">
					<div class="col-xs-12 col-sm-8 col-sm-push-2 col-md-6 col-md-push-0">
						<div class="frame-box in-front-of-theme-image orange-frame ">
							<?php
							include(INCLUDE_DIR . "/main-carousel.php");
							?>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="row">
							<div class="col-xs-12">
								<aside class="frame-box red-frame in-front-of-theme-image" style="padding:12px;">
									<header class="page-title" style="color: red;">CLEARANCE SALE!</header>
									<div class="row">
										<div class="col-xs-12 col-md-6">
											<p style="color: black; text-decoration: none;">I've decided to unclutter a
												bit,
												so I'm having a <em>SALE</em> on my CDs. While supplies last I'm selling
												CDs
												and vinyl at a big discount.</p>
											<ul>
												<li>Music CDs: <s>$15</s> <span style="color: red;">$5</span> each</li>
												<li>Songbird Vinyl: <s>$50</s> <span style="color: red;">$25</span> each
													<a href="./SongbirdBirdsong.php">more info</a>
												</li>
											</ul>
											<p><i>If you're interested in wholesale, contact me for further
													discounts.</i></p>
											<div style="text-align: center;"><a href="mailto:steve@steveweeksmusic.com"
													class="button link-button">GET YOURS!</a>
											</div>
										</div>
										<figure class="col-xs-12 col-md-6 dynamic-width" style="padding: 8px;">
											<img src="<?php echo IMG_DIR . "/slide_music3.jpg"; ?>" class="responsive">
										</figure>
									</div>
								</aside>
							</div>
							<div class="col-xs-12 col-sm-10 col-sm-push-1 col-md-12 col-md-push-0 full-width-xs">
								<aside
									class="subtle-blue behind-theme-image avoid-theme-lg avoid-theme-md avoid-theme-xs">
									<?php renderNextPerformance(TRUE, TRUE); ?>
								</aside>
							</div>
							<div class="col-xs-12">
								<aside class=" frame-box green-frame in-front-of-theme-image review-slideshow">
									<?php
									$oReview = new Review();
									$oReview->bGeneral = TRUE;
									if (!$oReview->getReview()) {
										//TODO: Error
									} else {
										renderReviewSlideshow($oReview->aReviewRecords);
									}
									?>
								</aside>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-sm-push-6">
						<div class=" white-box basic-box behind-theme-image avoid-theme news-excerpt">
							<?php renderNewsItem(1); ?>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-sm-pull-6">
						<aside class=" frame-box in-front-of-theme-image yellow-box">
							<?php
							$oReview = new Review();
							$oReview->bPerformanceRelated = TRUE;
							$oReview->nArtistID = STEVE_WEEKS_FAMILY_ARTIST_ID;
							if (!$oReview->getReview()) {
								//TODO: Error
							} else {
								renderReview($oReview->aReviewRecords[0]);
							}
							?>
						</aside>
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

	<div class="hover_bkgr_fricc">
		<span class="helper"></span>
		<div class="popupHeader">
			<div class="popupCloseButton">X</div>
			<a href="./otk">
				<div style="padding: 15px; text-align: center;">
					<h2 class="popupHeader">Own Your Very Own Songbird</h2>
				</div>

				<div style="padding: 15px; font-size: 1em;">
					<a href="./SongbirdBirdsong.php"><img src="<?php echo IMG_DIR . "/Songbird-45-cover.png"; ?>"
							style="width: 200px;">
						<p style="color: black; text-decoration: none;">Well, Steve finally did it. His latest song
							“Songbird” put on vinyl; a 45 RPM vinyl single to be exact.&nbsp;&nbsp;If you’re interested
							in owning a copy, read on.</p>
						<div style="text-align: center;"><a href="./SongbirdBirdsong.php"
								class="button link-button">Read More</a></div>
				</div>
			</a>
		</div>
	</div>


	<style>
		.flex {
			display: flex;
			flex-direction: row;
			padding: 16px;
		}

		/* Popup box BEGIN */
		.hover_bkgr_fricc {
			background: rgba(0, 0, 0, .4);
			cursor: pointer;
			display: none;
			height: 100%;
			position: fixed;
			text-align: center;
			top: 0;
			width: 100%;
			z-index: 10000;
		}

		.hover_bkgr_fricc .helper {
			display: inline-block;
			height: 100%;
			vertical-align: middle;
		}

		.hover_bkgr_fricc>div {
			background-color: #fff;
			box-shadow: 10px 10px 60px #555;
			display: inline-block;
			height: auto;
			max-width: 551px;
			min-height: 100px;
			vertical-align: middle;
			width: 60%;
			position: relative;
			border-radius: 8px;
			padding: 15px 5%;
		}

		.popupCloseButton {
			background-color: #fff;
			border: 3px solid #999;
			border-radius: 50px;
			cursor: pointer;
			display: inline-block;
			font-family: arial;
			font-weight: bold;
			position: absolute;
			top: -20px;
			right: -20px;
			font-size: 25px;
			line-height: 30px;
			width: 35px;
			height: 35px;
			text-align: center;
		}

		.popupCloseButton:hover {
			background-color: #ccc;
		}

		.trigger_popup_fricc {
			cursor: pointer;
			font-size: 20px;
			margin: 20px;
			display: inline-block;
			font-weight: bold;
		}

		/* Popup box BEGIN */
	</style>

	<script>
		$(window).load(function () {

			// $('.hover_bkgr_fricc').show();

			//Only uncomment this if you want a button to show the popup
			//$(".trigger_popup_fricc").click(function(){
			//   $('.hover_bkgr_fricc').show();
			//});

			/* $('.hover_bkgr_fricc').click(function(){
				$('.hover_bkgr_fricc').hide();
			});
			$('.popupCloseButton').click(function(){
				$('.hover_bkgr_fricc').hide();
			});
			*/
		});
	</script>

</BODY>

</HTML>