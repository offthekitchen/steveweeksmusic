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
	$sPageName = "WeeksGPT 4.0";
	$sPageDescription = "WeeksGPT 4.0";
	$sPageTitle = "WeeksGPT 4.0";


	$sActiveMenuItem = HOME_ACTIVE;	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
?>

<BODY>
	<div class="container-fluid">
		<div class="row">
			<header>
				<div class="col-xs-12 weeksgpt-logo">
					<img src = "./WeeksGPTHeaderLogo.png" class="img-responsive">
				</div>
			</header>
			<main id="main-content" class="col-xs-12">
			<section class="current-prompt">
				<form id="form">
					<div class="row">
						<div class="col-xs-1">
							
						</div>
						<div id="search" class="col-xs-9">
						<div >
							<input id="txtPrompt" name="txtPrompt" type="text" placeholder="Prompt me baby!" class="form-control input-md" required="" value="" >
							<input id="sendPrompt" type="image" src="send.png" alt="Submit" style="float:right" width="48" height="48">
						</div>

					</div>
				</form>
			</section>	
			<section id="answers" class="row">

			</section>

			</main>
		</div>
		<div class="row">
	</div>

</BODY>
</HTML>
<SCRIPT src='./gpt.js'></SCRIPT>

<STYLE>
	.console {
		font-family: arial;
		font-size:	18px;
	}

	#txtPrompt {
		width: 100%;
		background-color: #fff;
	}
	header {
		margin-top: 50px;
	}
	#main-content {
		margin: 15px 0px;
	}
	.answer {
		background-color: lightgrey;
		margin: 20px;
		padding: 15px;
		border-top: darkgrey solid 1px;
		border-bottom: darkgrey solid 1px;
	}
	.answer .row {

	}
	.prompt {
		margin: 20px;
		padding: 15px;
	}
	#form {
		margin: 20px;
	}

	#Search {
	position: relative;
	display: inline-block;
	}

	input[type=text] {
	position: relative;
	width: 100%;
	height: 36px;
	box-sizing: border-box;
	border: 2px solid #4d7fc3;
	border-radius: 4px;
	font-size: 16px;
	background-color: white;
	padding: 2px 40px 2px 10px;
	}

	input[type=image] {
	position:absolute;
	width: 30px;
	height: 95%;
	top: 0px;
	right: 20px;
	border: none;
	color: white;
	display: block;
	cursor: pointer;
	}

	.thumbs {
		display: inline-block!important;
	}

</STYLE>
