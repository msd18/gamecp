<?php 
define("THIS_SCRIPT", "gamecp");
define("IN_GAMECP_SALT58585", true);
include("./gamecp_common.php");
$navbits = array( $config["gamecp_filename"] => $config["gamecp_programname"] );
$do = isset($_REQUEST["do"]) ? antiject($_REQUEST["do"]) : "";
$forum_username = "N/A";
if( $do == "" && $notuser ) 
{
    $lefttitle = $program_name;
    $title = $program_name;
    $navbits = array( $script_name => $program_name, "" => $lefttitle );
    $out .= "<center><strong>Welcome to the Game CP please login with your game info.</strong>" . "\n";
    $out .= "<form method=\"post\" action=\"gamecp_login.php\">" . "\n";
    $out .= "<table border=\"0\">" . "\n";
    $out .= "\t<tr>" . "\n";
    $out .= "\t\t<td><strong>Username: </strong></td>" . "\n";
    $out .= "\t\t<td><input type=\"text\" name=\"username\"></td>" . "\n";
    $out .= "\t</tr>" . "\n";
    $out .= "\t<tr>" . "\n";
    $out .= "\t\t<td><strong>Password: </strong></td>" . "\n";
    $out .= "\t\t<td><input type=\"password\" name=\"password\"></td>" . "\n";
    $out .= "\t</tr>" . "\n";
    if( isset($config["security_recaptcha_enable"]) && $config["security_recaptcha_enable"] == 1 ) 
    {
        $out .= "\t<tr>" . "\n";
        $out .= "\t\t<td colspan=\"2\">" . recaptcha_get_html($publickey, $error) . "</td>";
        $out .= "\t</tr>" . "\n";
    }

    $out .= "\t<tr>" . "\n";
    $out .= "\t\t<td><input type=\"submit\" value=\"Login\"></td>" . "\n";
    $out .= "\t</tr>" . "\n";
    $out .= "</table>" . "\n";
    $out .= "</form>" . "\n";
    $out .= "<br>" . "\n";
    $out .= "Don't have a game account? Get one <a href=\"gamecp_register.php\">Here</a>.<br/>" . "\n";
    $out .= "Lost or forgot your password? Recover it <a href=\"" . $script_name . "?do=user_passwordrecover\">Here</a>." . "\n";
}
else
{
    if( $do == "" && $isuser ) 
    {
        $lefttitle = "Game CP";
        $title = $program_name;
        $navbits = array( $script_name => $program_name, "" => $lefttitle );
        $out .= "<h2 style=\"text-align: center;\">Welcome <strong><i>" . $userdata["username"] . "</i></strong> to the Game Control Panel</h2>" . "\n";
        $out .= "<p><b>Game Points:</b> <i>" . number_format($userdata["points"], 2) . "</i></p>" . "\n";
        $out .= "<p><b>Account E-Mail:</b> <i>" . $userdata["email"] . "</i></p>" . "\n";
        $out .= "<p><b>Last Log In Time:</b> <i>" . $userdata["lastlogintime"] . "</i></p>" . "\n";
        $out .= "<p><b>Last Log Off Time:</b> <i>" . $userdata["lastlogofftime"] . "</i></p>" . "\n";
        $out .= "<p><b>Last Connect IP Address:</b> <i>" . ($userdata["lastconnectip"] != 0 ? $userdata["lastconnectip"] : "None") . "</i></p>" . "\n";
        $out .= "<p><b>Current State:</b> <i>" . ($userdata["status"] ? "Online" : "Offline") . "</i></p>" . "\n";
    }
    else
    {
        $do = str_replace(".", "", $do);
        $do = str_replace("\\", "", $do);
        $do = str_replace("/", "", $do);
        if( !file_exists("./includes/" . $do . ".php") ) 
        {
            $out .= $lang["page_not_found"];
            $lefttitle = "Page Not Found";
        }
        else
        {
            include("./includes/" . $do . ".php");
        }

        $title = $program_name . " - " . $lefttitle;
        $navbits = array( $script_name => $program_name, "" => $lefttitle );
        if( isset($gamecp_dbconnect) ) 
        {
            @mssql_close($gamecp_dbconnect);
        }

        if( isset($items_dbconnect) ) 
        {
            @mssql_close($items_dbconnect);
        }

        if( isset($donate_dbconnect) ) 
        {
            @mssql_close($donate_dbconnect);
        }

        if( isset($user_dbconnect) ) 
        {
            @mssql_close($user_dbconnect);
        }

        if( isset($data_dbconnect) ) 
        {
            @mssql_close($data_dbconnect);
        }

    }

}

gamecp_nav($isuser);
eval("print_outputs(\"" . gamecp_template("gamecp") . "\");");

?>