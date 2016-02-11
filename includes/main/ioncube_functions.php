<?php

/* Left unencoded for your pleasure....not erally :D */

function aaron()
{
	return '<a href="http://www.aarondm.com/">Aaron DM</a>';
}

function ioncube_event_handler($err_code, $params) {

	?>
	<head>
	<title>RF Online Game Control Panel v2 - by Aaron DM</title>
	<style type="text/css">
	body{
		font-family:Arial, Helvetica, sans-serif; 
		font-size:13px;
		background-color: #F7F7F7;
		margin: 50px;
		padding: 20px;
	}
	a
	{
		color: #580000; 
		text-decoration: none;
	}

	a:hover
	{
		color: #C76E0F;
	}

	.info, .success, .warning, .error, .validation {
		border: 1px solid;
		margin: 10px 0px;
		padding:15px 10px 15px 50px;
		background-repeat: no-repeat;
		background-position: 10px center;
	}
	.info {
		color: #00529B;
		background-color: #BDE5F8;
		background-image: url('./includes/images/knobs/info.png');
	}
	.success {
		color: #4F8A10;
		background-color: #DFF2BF;
		background-image:url('./includes/images/knobs/success.png');
	}
	.warning {
		color: #9F6000;
		background-color: #FEEFB3;
		background-image: url('./includes/images/knobs/warning.png');
	}
	.error {
		color: #D8000C;
		background-color: #FFBABA;
		background-image: url('./includes/images/knobs/error.png');
	}
	</style>
	</head>

	<body>
	<h2>RF Online Game Control Panel</h2>
	<?php

	switch($err_code)
	{
		case 1:
			echo '<div class="error">This encoded file has been corrupted. Please ensure that during upload you use the BINARY method of transfer (via ftp) to upload your files</div>';
		break;

		case 2:
			echo '<div class="error">This file has expired. Please contact '.aaron().' to get it re-newed</div>';
		break;

		case 3:
			echo '<div class="error">The Game CP does not have permissions to access this file</div>';
		break;

		case 4:
			echo '<div class="error">Unforunately, there was an error with your system clock. It has been BACK-IN-TIME and will not allow the Game CP to run. Please set your system time to today\'s date and time</div>';
		break;

		case 5:
			echo '<div class="error">An untrusted extension has been loaded with your PHP configuration. Please un-load all third-party extensions in order to use the Game CP</div>';
		break;

		case 6:
			echo '<div class="error">Game CP is <b>unable to find the license file: '.$params['license_file'].'</b><br/>Please ensure that the file ('.$params['license_file'].') is only placed in your top-level folder of the Game CP</div>';
		break;

		case 7:
			echo '<div class="error">The license file '.$params['license_file'].' appeares to be <b>corrupt</b>. Please first try to re-upload the license file, if this fails, please contact '.aaron().'</div>';
		break;

		case 8:
			echo '<div class="error"><b>Your Game CP license has expired</b>. Please contact '.aaron().' to get it re-newed</div>';
		break;

		case 9:
			echo '<div class="error"><b>There is a problem with the license property</b>. Please contact '.aaron().' regarding this problem</div>';
		break;

		case 10:
			echo '<div class="error"><b>Licese header modified and is invalid</b>. Please contact '.aaron().'</div>';
		break;

		case 11:
			echo '<div class="error"><b>This license is not valid for this server</b>. Please contact '.aaron().' if this is a mistake.</div>';
		break;

		case 12:
			echo '<div class="error"><b>Unauthorized including file</b>. Please contact '.aaron().' if this is a mistake.</div>';
		break;

		case 13:
			echo '<div class="error"><b>Unauthorized include file</b>. Please contact '.aaron().' if this is a mistake.</div>';
		break;

		case 14:
			echo '<div class="error"><b>This server has APPEND & PREPEND enabled</b>. Please disable it in your php.ini configuration.</div>';
		break;

		default:
			echo '<div class="info">['.$err_code.'] An unknown error has occured. Please contact '.aaron().'</div>';
	}

	?>
	<div style="text-align: center;"><small>Copyright &copy; <a href="http://www.aarondm.com/">Aaron DM</a>. Images by <a href="http://itweek.deviantart.com/art/Knob-Buttons-Toolbar-icons-73463960" style="font-style: italic;">iTweek (deviantArt)</a> & CSS by <a href="http://css.dzone.com/news/css-message-boxes-different-me" style="font-style: italic;">Janko Jovanovic</a></small></div>
	</body>
	</html>
	<?php

}

?>