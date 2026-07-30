<?php
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

	//Page Name 
	$sPageName = "Contact";
	//Page Name 
	$sPageTitle = "Contact Me";
	//Custom FB Image
	$sFBImage = "FB_Performing.png";
	
	$aBreadcrumb = array(
		0=> array('Home','index.php'),
		1=> array('Contact','contact.php'),
		); 

	$sActiveMenuItem = CONTACT_ACTIVE;	
	$sName = isset($_POST['txtName']) ? $_POST['txtName'] : NULL;
	$sEmail = isset($_POST['txtEmail']) ? $_POST['txtEmail'] : NULL;
	$sMessage = isset($_POST['txtMessage']) ? $_POST['txtMessage'] : NULL;
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
?>
<script type="text/javascript">
$(document).ready(function($j){
	//Function for contact form submission
    $('form#Contact').submit(function(){
       var hasError = false;
		 		 
		//Check any field that should be an email
      	$('form#Contact .email-field').each(function() {	
			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,6})?$/;
			if(!emailReg.test(jQuery.trim($(this).val()))){
				jQuery("div#formMessage").html("<div class='contact-error'> That's not a real email address!</div>"); 				
				hasError = true;
			} 
		}); 

        if(!hasError){

			captcha_response = grecaptcha.getResponse();
//alert("Captcha Response: " + captcha_response);
			if (captcha_response)
			{
				name =  $("input#txtName").val();
				email =  $("input#txtEmail").val();
				message =  $("textarea#txtMessage").val();
				var form_post_data = "";
			
				//Make AJAX call to attempt email send
 				var html = $.ajax({
					type: "POST",
					url: "./includes/ajax_mail.php",
					data: "name=" + name + "&email=" + email + "&message=" + message + "&g-recaptcha-response=" + captcha_response,
					async: false
				}).responseText;
				//For some reason the ajax response had spaces in some cases
				html = $.trim(html);
			}
			else
			{
				
				html = "recaptcha-failure";
			}			
			

			
			//Based on the string returned from the AJAX call, display a message
			switch (html) {
		
				case "success":
			
					var formInput = $(this).serialize();
					$("div#formMessage").html("<div class='contact-success'>Got it...  Thanks.</div>");
					hasError = false;
					break;

				case "recaptcha-failure":

					$("div#formMessage").html("<div class='contact-error'> Verify that you're not a robot using the Captcha box above</a></div>");
					hasError = true;
					break;


				case "email-failure":

					$("div#formMessage").html("<div class='contact-error'> Hmmmm.  I had trouble sending that email... Try <a href='mailto:contact@steveweeksmusic.com'>contact@steveweeksmusic.com</a></div>");
					hasError = true;
					break;

				default:
					$("div#formMessage").html("<div class='contact-error'> Ooops.  Something went wrong!  I couldn't send the email.<br><a href='mailto:contact@steveweeksmusic.com'>contact@steveweeksmusic.com</a></div>");
					hasError = true;
					break;

			}
			
			if(hasError)
			{
				return false;
			}
			else
			{
				$('#Contact').trigger("reset");
				return false;
			}

        }
		else
		{
			//Validation failed, so don't re-post form
	        return false;
		}
	
    });

});
</script>  
<BODY>
	<div class="container-fluid">
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/header.php");
		?>		
		</div>
	<div class="row">
			<aside class="col-xs-12 col-sm-10 col-sm-push-1">
				<div class="orange-frame frame-box in-front-of-theme-image">
					<div class="thanks">
					Comments? Suggestions? Feedback? Drop me a line using the form below or email me at <em><a href="mailto:steve@steveweeksmusic.com">STEVE@STEVEWEEKSMUSIC.COM</a></em>. I'd love to hear from you! If you're interested in booking me you can find more information about my performance <em><a href="<?php echo $ROOT;?>/performance.php">HERE</a></em>.
					</div>
				</div>
			</aside>

			

		</div>	
		<?php
			include(INCLUDE_DIR . "/footer.php");
		?>		
		</div>
	</div>
</BODY>
<!--Recaptcha Javascript -->
<script src='https://www.google.com/recaptcha/api.js'></script>
</HTML>
