<?php 
define("IN_GAMECP_SALT58585", true);
include("./gamecp_common.php");
$notuser = true;
$isuser = false;
$exit_stage1 = false;
$exit_stage2 = false;
$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$re_password = isset($_POST["re_password"]) ? $_POST["re_password"] : "";
$email = isset($_POST["email"]) ? $_POST["email"] : "";
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";
$ip = isset($_SERVER["REMOTE_ADDR"]) ? gethostbyname($_SERVER["REMOTE_ADDR"]) : "";
$exit_form = false;
if( !$config["security_max_accounts"] ) 
{
    $config["security_max_accounts"] = 3;
}

$lefttitle = "Register";
$title = $program_name . " - " . $lefttitle;
$navbits = array( $script_name => $program_name, "" => $lefttitle );
$out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
$out .= "\t<tr>" . "\n";
$out .= "\t\t<td class=\"alt1\">" . "\n";
$out .= "\t\t\t<p style=\"font-weight: bold; text-align: center; padding: 2px;\">Username Must be 16 Characters or Less in Length.<br />";
$out .= "\t\t\tPassword must be 24 characters or less and include atleast some letters and 1 number!<br />";
$out .= "\t\t\tUsername and password are CaSe SenSiTiVe!!</p>" . "\n";
$out .= "\t\t</td>" . "\n";
$out .= "\t</tr>" . "\n";
$out .= "</table>" . "\n";
if( $submit != "" ) 
{
    if( $username == "" ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You forgot to fill in a username</p>";
    }
    else
    {
        if( eregi("[^a-zA-Z0-9]", $username) ) 
        {
            $exit_stage1 = true;
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Invalid username entered. Letters and numbers only!</p>";
        }
        else
        {
            if( strlen($username) < 4 || 12 < strlen($username) ) 
            {
                $exit_stage1 = true;
                $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Username must be greater than 4 and less than 12 characters long</p>";
            }

        }

    }

    if( $password == "" ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You forgot to fill in your password</p>";
    }
    else
    {
        if( eregi("[^a-zA-Z0-9]", $password) ) 
        {
            $exit_stage1 = true;
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Invalid password entered. Letters and numbers only!</p>";
        }
        else
        {
            if( strlen($password) < 4 || 16 < strlen($password) ) 
            {
                $exit_stage1 = true;
                $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Password must be greater than 4 and less than 16 characters long</p>";
            }

        }

    }

    if( $password != "" && $re_password == "" ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You forgot to re-type your password</p>";
    }

    if( $email == "" ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You forgot to fill in an E-Mail address</p>";
    }
    else
    {
        if( !isemail($email) ) 
        {
            $exit_stage1 = true;
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You have entered an invalid E-Mail address</p>";
        }

    }

    if( $password != "" && $re_password != "" && $password != $re_password ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Your two passwords do not match. Please re-type them.</p>";
    }
    else
    {
        if( eregi("[^a-zA-Z0-9_-]", $password) ) 
        {
            $exit_stage1 = true;
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Invalid password. Letters and numbers only, both required!</p>";
        }

    }

    if( $exit_stage1 != true ) 
    {
        $username = antiject(trim($username));
        $password = antiject(trim($password));
        $email = antiject(trim($email));
        connectuserdb();
        $username_check = mssql_query("SELECT id FROM tbl_rfaccount WHERE id=CONVERT(binary,'" . $username . "')");
        if( 0 < mssql_num_rows($username_check) ) 
        {
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Sorry, the username you choose has already been taken.</p>";
            $exit_stage2 = true;
        }

        $email_check = mssql_query("SELECT Email FROM tbl_rfaccount WHERE Email='" . $email . "'");
        if( 0 < mssql_num_rows($email_check) ) 
        {
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Sorry, your e-mail address has already been used.</p>";
            $exit_stage2 = true;
        }

        if( 0 < $config["security_max_accounts"] ) 
        {
            $maxip_check = mssql_query("SELECT createip FROM tbl_UserAccount WHERE createip='" . $ip . "' OR lastconnectip='" . $ip . "'") or exit( "wtf did not work" );
            if( $config["security_max_accounts"] <= mssql_num_rows($maxip_check) ) 
            {
                $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You cannot register more than " . $config["security_max_accounts"] . " accounts per ip.</p>";
                $exit_stage2 = true;
            }

        }

        if( $exit_stage2 == false ) 
        {
            $register_query = "INSERT INTO tbl_rfaccount(id,password,accounttype,Email) VALUES ((CONVERT (binary,'" . $username . "')),(CONVERT (binary,'" . $password . "')),0,'" . $email . "')";
            if( !($register_query = mssql_query($register_query)) ) 
            {
                $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error inserting data into the database</p>";
                if( $config["security_enable_debug"] == 1 ) 
                {
                    $out .= "<p>DEBUG(?):<br/>" . "\n";
                    $out .= mssql_get_last_message();
                    $out .= "</p>";
                }

            }
            else
            {
                $insert_sql = "" . "INSERT INTO tbl_UserAccount (id, createip) VALUES(convert(binary,'" . $username . "'), '" . $ip . "')";
                if( !($insert_result = mssql_query($insert_sql)) ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error inserting data into the User database</p>";
                    if( $config["security_enable_debug"] == 1 ) 
                    {
                        $out .= "<p>DEBUG(?):<br/>" . "\n";
                        $out .= mssql_get_last_message();
                        $out .= "</p>";
                    }

                }
                else
                {
                    $timenow = time();
                    game_cp_1($username, $timenow, $_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"]);
                    @mssql_free_result($register_query);
                    $out .= "<p style=\"font-weight: bold; text-align: center; padding: 2px;\" class=\"alt2\">Successfully registered a new account!</p>";
                    $exit_form = true;
                }

            }

        }

        @mssql_free_result($username_check);
    }

}

if( $exit_form != true ) 
{
    $out .= "<form method=\"post\" action=\"gamecp_register.php\">";
    $out .= "<input type=\"hidden\" name=\"regstatus\" value=\"done\">";
    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
    $out .= "<tr>";
    $out .= "\t<td class=\"alt1\">Login Name:</td>";
    $out .= "\t<td class=\"alt2\"><input type=\"text\" name=\"username\" size=\"12\" maxlength=\"16\" value=\"" . $username . "\"> (From 4 to 16 characters)</td>";
    $out .= "</tr>";
    $out .= "<tr>";
    $out .= "\t<td class=\"alt1\">Password:</td>";
    $out .= "\t<td class=\"alt2\"><input type=\"password\" size=\"12\" name=\"password\" value=\"" . $password . "\"> (From 4 to 24 characters)</td>";
    $out .= "</tr>";
    $out .= "<tr>";
    $out .= "\t<td class=\"alt1\">Retype Password:</td>";
    $out .= "\t<td class=\"alt2\"><input type=\"password\" size=\"12\" name=\"re_password\"></td>";
    $out .= "</tr>";
    $out .= "<tr>";
    $out .= "\t<td class=\"alt1\">Email Address:</td>";
    $out .= "\t<td class=\"alt2\"><input type=\"text\" size=\"24\" name=\"email\" value=\"" . $email . "\"></td>";
    $out .= "</tr>";
    $out .= "<tr>";
    $out .= "\t<td class=\"alt1\" align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Register\">&nbsp;<input type=\"reset\" value=\"Reset\"></td>";
    $out .= "</tr>";
    $out .= "</table>";
}

gamecp_nav();
eval("print_outputs(\"" . gamecp_template("gamecp") . "\");");

