<?php
/*
*******************************************************************
DiscographyAdmin.php
This PHP file defines the Admin page for discography related tasks.
NOTES
Date        Change
-------------------------------------------------------------
*******************************************************************
*/	

	//This include defines the relative path to the root directory from this sub-directory
	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");

	 $sActiveMenuItem = DISCOGRAPHY_ACTIVE;
	$sPageName = "Discography Admin";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

	<?php
	
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader.php");
	?>
	<TABLE align="left">
		<TR>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/CDMaintenance.php'><IMG SRC='<?php echo IMG_DIR; ?>/music-thumbnail.png'>
				<BR />CD Maintenance</A>
			</TD>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/SongMaintenance.php'><IMG SRC='<?php echo IMG_DIR; ?>/SongMaint.jpg'>
				<BR />Song Maintenance</A>
			</TD>
		</TR>
		<TR>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/AwardMaintenance.php'><IMG SRC='<?php echo IMG_DIR; ?>/AwardMaint.jpg'>
				<BR />Award Maintenance</A>
			</TD>
			<TD align="center">
				<A HREF='<?php echo ADMIN_DIR; ?>/ReviewMaintenance.php'><IMG SRC='<?php echo IMG_DIR; ?>/ReviewMaint.jpg'>
				<BR />Review Maintenance</A>
			</TD>
		</TR>	</TABLE>	
</body>
</html>
