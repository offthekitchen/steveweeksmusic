<?php
/*
*******************************************************************
wish/index.php
NOTES
Date        Change
-------------------------------------------------------------
2026-10-12	Migrated to shared PDO datalayer
*******************************************************************
*/
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

	include_once(DATALAYER_DIR . "/Connection.php");
	include_once(DATALAYER_DIR . "/CD.php");
	include_once(DATALAYER_DIR . "/CDRepository.php");
	include_once(DATALAYER_DIR . "/Song.php");
	include_once(DATALAYER_DIR . "/SongRepository.php");

	$cdRepo = new \Datalayer\CDRepository();
	$oThisCD = $cdRepo->findById(7);

	$aSongs = [];
	if ($oThisCD !== null) {
		$songRepo = new \Datalayer\SongRepository();
		$aSongs = $songRepo->find(['cdId' => $oThisCD->id]);
	} else {
		error_log('wish/index.php: ERROR RETRIEVING CD 7');
		echo "ERROR RETRIEVING CD";
	}

	//Page Name 
	$sPageName = "For your consideration for...";
	//Page Name 
	$sPageSubTitle = "<small>For your consideration for...</small><BR>BEST CHILDREN'S ALBUM";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";
	
	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('Music','music.php'),
		2=> array($oThisCD ? $oThisCD->name : 'CD','cd.php?cd-id=' . ($oThisCD ? $oThisCD->id : 7))
		); 
		
	$sActiveMenuItem = MUSIC_ACTIVE;	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
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
				<article id="cd-list" class="row">
					<div class="col-xs-12 col-sm-6 col-sm-push-3 col-md-5 col-md-push-0">
						<div class="cd-cover">
							<figure class="dynamic-width in-front-of-theme-image">
								<img src="<?php echo IMG_DIR . "/" . ($oThisCD ? $oThisCD->image : '');?>">
							</figure>
                            <section class="behind-theme-image avoid-theme">
							<h4>LISTEN</h4>
							<ol>
								<?php
								foreach($aSongs as $oSong)
								{
								
                                ?> 
                                    <div class="row">
                                    <div class="col-xs-12">
                                    <h2 class="listen"><?php echo $oSong->name ?></h2>
                                    <!--<div class="mp3-player-container">-->
                                    <audio controls
                                          data-info-att="Music: Steve Weeks"
                                          data-info-att-link="https://www.steveweeksmusic.com">
                                          <source src="<?php echo MP3_DIR . "/WishYouWereHere/{$oSong->trackNumber}.mp3";?>" type="audio/mpeg" />
                                          <a href="<?php echo MP3_DIR . "/WishYouWereHere/{$oSong->trackNumber}.mp3";?>"><?php echo $oSong->name;?></a>
                                          An html5-capable browser is required to play this audio. 
                                        </audio>		
                                    <!--</div>-->
                                    </div>
                                </div>

                                <?php
								}
								?>
							</ol>							
						</section>

						</div>
					</div>
					<div class="col-xs-12 col-md-7">

                    <section class="behind-theme-image avoid-theme basic-box">
							<?php
                            echo "<div>";
                            ?>

                            <?php
                            echo $oThisCD ? $oThisCD->description : '';?>
                        
                            <?php
							echo "</div>";
							?>
						</section>

                    
					</div>	
				</article>
				<div id="cd-list" class="row">
					<div class="col-xs-12">
                   

						
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
