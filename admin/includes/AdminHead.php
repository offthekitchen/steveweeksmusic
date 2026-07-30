<!-- Style Sheet and CSS needed to make Navigation Menu Work -->
<link rel="stylesheet" type="text/css" href="<?php echo CSS_DIR; ?>/NavigationMenu.css" />
<script type="text/javascript" src="<?php echo ADMIN_JS_DIR; ?>/NavigationMenu.js"></script>
<LINK HREF="<?php echo ADMIN_CSS_DIR; ?>/Admin.css" type="text/css" rel="StyleSheet">
<script language="JavaScript" src="<?php echo ADMIN_JS_DIR; ?>/ExpandCollapse.js"></script>
<link rel="stylesheet" href="<?php echo CSS_DIR; ?>/ImagePopup.css" type="text/css">
<script src="<?php echo ADMIN_JS_DIR; ?>/mouseover_popup.js" language="JavaScript"></script>
<script src="<?php echo ADMIN_JS_DIR; ?>/CommonJavascripts.js" language="JavaScript" type="text/javascript"></script>
<!-- Include jQuery library -->
<?php 
//Include compresssed version of jQuery for Production or uncompressed version for other environments
if (ENVIRONMENT == "PRODUCTION")
{
	echo "<script type=\"text/javascript\" src=\"" . JS_DIR . "/jquery-2.2.0.min.js\"></script>";
} 
else
{
	echo "<script type=\"text/javascript\" src=\"" . JS_DIR . "/jquery-2.2.0.min.js\"></script>";
}
?>
 