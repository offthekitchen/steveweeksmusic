<HEAD>
	<?php 
	$sPageName = empty($sPageName) ? 'ADMINISTRATIVE PAGE' : $sPageName;
	$sFBImage = empty($sFBImage) ? 'FB_Performing.png' : $sFBImage;
	?>
<META http-equiv=Content-Type content="text/html; charset=windows-1252">

<meta name="keywords" content="Steve Weeks, <?php echo "$sPageName" ?>, Children's Music, Kids Music, Kindie Music">
<meta name="description" content="Admin- <?php echo "$sPageName" ?>">
<meta name="title" content="Steve Weeks Music - <?php echo "$sPageName" ?>">
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=0">
<TITLE>Admin - <?php echo "$sPageName" ?></TITLE>


<LINK HREF="<?php echo ADMIN_CSS_DIR; ?>/Admin-responsive.css" type="text/css" rel="StyleSheet">
<script language="JavaScript" src="<?php echo ADMIN_JS_DIR; ?>/ExpandCollapse.js"></script>
<link rel="stylesheet" href="<?php echo ADMIN_CSS_DIR; ?>/ImagePopup.css" type="text/css">
<script src="<?php echo ADMIN_JS_DIR; ?>/mouseover_popup.js" language="JavaScript"></script>
<script src="<?php echo ADMIN_JS_DIR; ?>/CommonJavascripts.js" language="JavaScript" type="text/javascript"></script>

<!-- customized Boostrap - http://getbootstrap.com/customize/?id=1aadfb3bf645d83ccfc7ae41f22ed034 -->
<link rel="stylesheet" href="<?php echo $ROOT; ?>/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo $ROOT; ?>/css/bootstrap-theme.min.css">

<!-- Google Fonts -->
<link href='https://fonts.googleapis.com/css?family=Amatic+SC:400,700' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Boogaloo' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<link href="https://fonts.googleapis.com/css?family=Sue+Ellen+Francisco" rel="stylesheet">

<LINK HREF="<?php echo $ROOT; ?>/css/swm.css" type="text/css" rel="StyleSheet">
<LINK REL="SHORTCUT ICON" HREF="http://www.steveweeksmusic.com/SWM.ICO">
<link rel="image_src" href="http://www.steveweeksmusic.com/images/<?php echo $sFBImage; ?>" />

<link href="touch-icon-iphone-admin.png" rel="apple-touch-icon">
<link href="touch-icon-ipad-admin.png" rel="apple-touch-icon" sizes="152x152" >
<link href="touch-icon-iphone-retina-admin.png" rel="apple-touch-icon" sizes="180x180">
<link href="touch-icon-ipad-retina-admin.png" rel="apple-touch-icon" sizes="167x167" >

<!-- Adds HTML5 element support for browsers older than IE9 -->
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![end if]-->
<!-- Include jQuery library -->
<script type="text/javascript" src="<?php echo ADMIN_JS_DIR; ?>/jquery-2.2.0.min.js"></script>
<!--Bootstrap Javascript-->
<script src="<?php echo ADMIN_JS_DIR; ?>/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_JS_DIR; ?>/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo ADMIN_CSS_DIR; ?>/bootstrap-datepicker.standalone.min.css"><br />
<SCRIPT>
	$( document ).ready(function() {
		$('.datepicker').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
    		clearBtn: true,

		});
	})
</SCRIPT>
</HEAD>
