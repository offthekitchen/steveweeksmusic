<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<?php
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");
	?>
<?php
 	include_once (CLASS_DIR . "/class_Form.php");

	//Get Image Name from query string
	$sImageFullPath = $_REQUEST['image'];

?>

<SCRIPT type="text/javascript">

//Function to close window 
function closeWindow(){
	window.close();
}

</script>


</head>

<BODY>
<center>

<TABLE align="center" border="0">
	<TR>
		<TD>
		<?php 
			$thisForm = new Form();
			$thisForm->renderImage($sImageFullPath);
			
			echo "<HR> IMAGE FULL PATH: <i>" . $sImageFullPath . "</i><HR>";

		?>
		</TD>
	<TR>
		<TD>
			<table height="50">
				<tr>
					<td align="center" class="Subtitle">
						<A HREF='javascript: window.close();'>CLOSE WINDOW</A>
					</td>
				</tr>
			</table>
		</TD>
	</TR>
</TABLE>
</center>
</html>
