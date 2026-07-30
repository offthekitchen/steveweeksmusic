//Function needed fro Popup chooser 
/*function sendValue(s){
var selvalue = s.options[s.selectedIndex].value;
window.opener.document.ProdTypeMaint.choice.value = selvalue;
window.close();
}
*/

//Numeric Check
function isNumeric(n) 
{
  return !isNaN(parseFloat(n)) && isFinite(n);
}

//Confirmation Popup Window
function show_confirm(sMessage)
{
var r=confirm(sMessage);
if (r==true)
  {
	  return true;
  }
else
  {
	 return false;
  }
}

function preview_image_old(sFieldName)
{
	//sImage = "document." + sCallingForm + "." + sFieldName + ".value";
	sImageTextBox = document.getElementById(sFieldName);
	sImageName = sImageTextBox.value;
	sURL = 'PreviewImage.php?image=' + sImageName;
	window.open(sURL, "PREVIEW", "height=500, width=500, resizable=1");
	//alert(sImageTextBox.value);
	//window.open('PreviewImage.php?image=' + sImageName);
}

function preview_image(sFieldName, sImagePath)
{

	sImageTextBox = document.getElementById(sFieldName);
	if (sImageTextBox.value > "")
	{
		sImageName = sImagePath + "/" + sImageTextBox.value;
	}
	else
	{
		sImageName = "";
	}
//DEBUG
//alert("IMAGE PATH: " + sImagePath);
//alert("FIELD NAME: " + sFieldName);
//alert("IMAGE NAME: " + sImageName);
	sURL = 'PreviewImage.php?image=' + sImageName;
	window.open(sURL, "PREVIEW", "height=500, width=500, resizable=1");
}


