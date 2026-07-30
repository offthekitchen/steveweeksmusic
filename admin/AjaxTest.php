<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Ajax Test</title>
	<?php
	
	//error_reporting(E_ALL ^ E_NOTICE );
	
	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");

	//inlcude web site settings
 	include_once ($ROOT . "/includes/websiteSettings.php");
	//inlcude admin settings
 	include_once (ADMIN_DIR . "/includes/AdminSettings.php");
	?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHead.php");
?>

<!-- Javascript required for Calendar picker -->
<script language="javascript" src="<?php echo ADMIN_JS_DIR ?>/calendar.js"></script>

<script>
function getTours() {
	sDate = document.getElementById("TourIncludeDate").value;

    if (sDate == "" || sDate == "0000-00-00") {
        document.getElementById("ddTours").innerHTML = "<OPTION Value='0'>NONE</OPTION>";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("ddTours").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","GetPotentialTours.php?date="+sDate,true);
        xmlhttp.send();
    }
}

function alertID()
{
	sID = document.getElementById("ddTours").value;
	alert("ID=" + sID + "!");
}
</script>

</head>

<body>

	<?php
	
 	//include Form Class		
 	include (CLASS_DIR . "/class_Form.php");

	//Require the Class for the calendar picker
 	require_once (CLASS_DIR . "/tc_calendar.php");

	?>


<?php
 	include (ADMIN_INCLUDE_DIR . "/AdminHeader.php");
?>	
	<table border="0" width="90%">
		<tr>
			<td colspan="2" class="Title">
				Ajax Test
			</td>
			<td align="right">
			</td>
		</tr>
	</table>

	<table border="0">
		<tr>
			<td>
				 <TABLE>
				 	<TR>
						<TD CLASS="FieldGroupTitle">
							DETAILS
						</TD>
					</TR>
					<TR>
						<TD class="FieldGroup">
							<table>
								<tr>
									<td width="206">
										INCLUDE DATE:<BR /> 
									  	<?php	  

									  	$myCalendar = new tc_calendar("TourIncludeDate", true, false);	  
										$myCalendar->setIcon(ADMIN_IMG_DIR . "/iconCalendar.gif");
										$myCalendar->setPath("./calendar/");	  
										$myCalendar->setYearInterval(2012, 2020);	  
										$myCalendar->dateAllow('2003-01-01', '2020-03-01');	  
										$myCalendar->setDateFormat('j F Y');	  
										$myCalendar->setAlignment('left', 'bottom');	  
										//Parse Tour Date
  
										$myCalendar->writeScript();	  
										?>								    
									</td>								
								</tr>
								<tr>
									<td colspan="3">
										TOURS: 
											<SELECT id="ddTours">
												<OPTION VALUE="2">TEST TOUR</OPTION>
											</SELECT>
									</td>
									<td>
										<INPUT type="button" ID="btnAssignTour" value="Assign Tour" onclick="getTours()" />
									</td>							
								</tr>
								<tr>
									<td>
										<INPUT type="button" ID="btnPost" value="Assign Tour" onclick="alertID()" />
									</td>							
								</tr>
							</table>
						</TD>
					</TR>
			  </TABLE>
			</td>
		</tr>
	</table>
	
</body>
</html>
