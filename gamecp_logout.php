<?php 
define("IN_GAMECP_SALT58585", true);
$notuser = true;
$isuser = false;
$logout_msg = "";
$_SESSION = array(  );
setcookie("gamecp_userdata", "", time() - 3600);
if( isset($_COOKIE["gamecp_userdata"]) ) 
{
    unset($_COOKIE["gamecp_userdata"]);
}

@session_destroy();
include("./gamecp_common.php");
$lefttitle = "Logout";
$title = $program_name . " - " . $lefttitle;
$out .= $logout_msg . "<p style=\"text-align: center; font-weight: bold;\">You have successfully logged out</p>";
gamecp_nav();
eval("print_outputs(\"" . gamecp_template("gamecp") . "\");");

