<!--site footer-->
<footer id="site-footer" class="col-xs-12" role="contentinfo">
	<div class="hidden-lg row">
	<?php
		include (INCLUDE_DIR . "/socialMedia.php");
	?>
   	</div>
	<div class="row">
		<div class="col-xs-12">	
			<p class="email in-front-of-theme-image"><A href="mailto:steve@steveweeksmusic.com">steve@steveweeksmusic.com</A><br />
			</p>
		</div>
		<div class="col-xs-12">	
			<p>&copy; copyright <?php echo date("Y");?> <b>Steve Weeks Music</b></p>
		</div>	
	</div>	
</footer>

<!--Bootstrap Javascript-->
<script src="<?php echo JS_DIR; ?>/bootstrap.min.js"></script>

<!--Theme Javascript-->
<script type="text/javascript" src="<?php echo JS_DIR; ?>/theme.js"></script>

<!--Ensure that the theme image is the same height as the screen -->
<SCRIPT>
$( document ).ready(function(){
  $('.theme-image').css({ height: $(window).innerHeight() });
  $(window).resize(function(){
    $('.theme-image').css({ height: $(window).innerHeight() });
  });
});
</SCRIPT>

<!--END site footer-->


