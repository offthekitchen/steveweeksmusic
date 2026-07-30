<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>Flagline Choose Product Type</title>
	<?php
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");

	//inlcude common application settings
 	include_once ($ROOT . "/includes/AppSettings.php");
	?>

<LINK HREF="<?php echo CSS_DIR; ?>/SWM.css" type="text/css" rel="StyleSheet">
<?php
	//include Product Type Class		
 	include_once (CLASS_DIR . "/class_ProductType.php");
 	include_once (CLASS_DIR . "/class_Form.php");


	//Get the Product Type ID to exclude from the query string parameter
//	$nExcludeProdTypeID = $_REQUEST['ExcludeID'];
//	$nTopProdTypeID = $_REQUEST['TopID'];
	$sCallingForm = $_REQUEST['CallingForm'];
	$sCallingFormIDField = $_REQUEST['IDField'];
	$sCallingFormNameField = $_REQUEST['NameField'];

?>


<SCRIPT type="text/javascript">
//Function needed for Popup chooser 
function sendValue(nID,sName){
//alert("SENDING: " + sName + " " + nID);
//alert("Form field = <?php echo $sCallingForm . "." . $sCallingFormNameField ?>");
	window.opener.document.<?php echo $sCallingForm . "." . $sCallingFormNameField ?>.value = sName;
	//Set the ID Field of the calling form to the ID of the chosen Product
	window.opener.document.<?php echo $sCallingForm . "." . $sCallingFormIDField ?>.value = nID;
	window.close();
}

//Function to close window 
function closeWindow(){
	window.close();
}

</script>

</head>

<BODY>

<center>

<?php 

//DEBUG
//echo "Calling Form " . $sCallingForm . "<BR>"; 

	//Instantiate needed objects
	$thisProductType = new ProductType();
	$aSearchFields = array();

?>

<TABLE align="left">
	<TR>
		<TD>
			<table>
				<tr>
					<td class="Title">
						Choose Product Type
					</td>
				</tr>
				<tr>
					<td align="right" class="Subtitle">
						<A HREF='javascript: closeWindow();'>CLOSE WINDOW</A>
					</td>
				</tr>
			</table>
		</TD>
	</TR>
<?php

	//Get the entire Product Type Hierarchy from this Product Type down
	if($thisProductType->getProductType())
	{
		if (sizeof($thisProductType->aProductTypeRecords) > 0 )
		{
			foreach ($thisProductType->aProductTypeRecords as $oProductType)
			{
				echo "<TR>";
				echo "<TD align=left>";
				//slashes have to be added to quotes and double quotes and then HTML special chars encoded sicne the name will be passed to a javascript function and displayed as HTML
				echo "<A HREF=\"javascript: sendValue('" . $oProductType->nProductTypeID . "','" . htmlentities(addslashes($oProductType->sProductTypeName)) . "')\">" . htmlentities($oProductType->sProductTypeName) . "</A><BR>";	
				echo "</TD>";
				echo "</TR>";
			}
		}
		else
		{
			echo "<TR><TD>NO PRODUCT TYPES FOUND</TD></TR>";
		}
	}
	else
	{
		echo "<TR><TD> ERROR RETRIEVING PRODUCT TYPE RECORDS </TD></TR>";
		echo "<TR><TD>" . $thisProductType->ErrorMessage . "</TD></TR>";
	}

?>
	<TR>
		<TD>
			<table height="200">
				<tr>
					<td align="right" class="Subtitle">
						<A HREF='javascript: closeWindow();'>CLOSE WINDOW</A>
					</td>
				</tr>
			</table>
		</TD>
	</TR>
</TABLE>
</center>
</html>
