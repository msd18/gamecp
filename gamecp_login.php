<?php 
define("IN_GAMECP_SALT58585", true);
include("./gamecp_common.php");
$lefttitle = "Login";
$title = $program_name . " - " . $lefttitle;
$exit_stage1 = false;
$exit_stage2 = false;
$notuser = true;
$isuser = false;
$gamecp_nav = "";
$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
if( isset($_COOKIE["gamecp_userdata"]) ) 
{
    unset($_COOKIE["gamecp_userdata"]);
}

if( $username != "" && $password != "" ) 
{
    $username = antiject(trim($username));
    $password = antiject(trim($password));
    $username = ereg_replace("" . ";\$", "", $username);
    $username = ereg_replace("\\\\", "", $username);
    $password = ereg_replace("" . ";\$", "", $password);
    $password = ereg_replace("\\\\", "", $password);
    $ip = gethostbyname($_SERVER["REMOTE_ADDR"]);
    if( ereg("[^a-zA-Z0-9]", $username) && ereg("[^a-zA-Z0-9]", $password) ) 
    {
        $out .= "<center>Invalid Username or Password. Please try again.</center>";
        $exit_stage1 = true;
    }

    if( isset($config["security_recaptcha_enable"]) && $config["security_recaptcha_enable"] == 1 && $privatekey != "" ) 
    {
        $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
        if( !$resp->is_valid && !preg_match("/Input error: privatekey:/", $resp->error) ) 
        {
            $out .= "<center>" . $resp->error . "</center>";
            $exit_stage1 = true;
        }

        if( preg_match("/Input error: privatekey:/", $resp->error) ) 
        {
            gamecp_log(5, $userdata["username"], "RECAPTCHA - Invalid/Bad Private Key Supplied", 1);
        }

    }

}
else
{
    $out .= "<center>No Username or Password was provided</center>";
    $exit_stage1 = true;
}

if( $exit_stage1 != true ) 
{
    connectuserdb();
    $query_user = mssql_query("SELECT username = CAST(id as varchar(255)), password = CAST(password as varchar(255)) FROM tbl_rfaccount WHERE id=CONVERT(binary,\"" . $username . "\")");
    if( mssql_num_rows($query_user) <= 0 ) 
    {
        $out .= "<center>Invalid Username or Password. Please try again.</center>";
    }
    else
    {
        $query_user = mssql_fetch_array($query_user);
        $query_username = trim($query_user["username"]);
        $query_password = trim($query_user["password"]);
        $query_username = ereg_replace("" . ";\$", "", $query_username);
        $query_username = ereg_replace("\\\\", "", $query_username);
        $query_password = ereg_replace("" . ";\$", "", $query_password);
        $query_password = ereg_replace("\\\\", "", $query_password);
        if( $username == $query_username && $password == $query_password ) 
        {
            $isuser = true;
            $notuser = false;
            $userdata["username"] = $query_username;
            $query_account = mssql_query("SELECT serial FROM tbl_UserAccount WHERE id = CONVERT(binary,\"" . $userdata["username"] . "\")");
            $query_account = mssql_fetch_array($query_account);
            $userdata["serial"] = $query_account["serial"];
            $query_ban = mssql_query("SELECT nAccountSerial FROM tbl_UserBan WHERE nAccountSerial = '" . $userdata["serial"] . "'");
            if( $userdata["serial"] != "" && 0 < mssql_num_rows($query_ban) && !in_array($userdata["username"], $super_admin) ) 
            {
                $lefttitle .= " - Failed";
                $exit_stage2 = true;
                $isuser = false;
                $notuser = true;
                $out .= "<p style=\"text-align: center; font-weight: bold;\">Your account has been blocked! Please contact an Administrator.</p>";
                $_SESSION = array(  );
                setcookie("gamecp_userdata", "", time() - 3600);
                if( isset($_COOKIE["gamecp_userdata"]) ) 
                {
                    unset($_COOKIE["gamecp_userdata"]);
                }

                session_destroy();
                gamecp_log(4, $userdata["username"], "GAMECP - LOGIN - Blocked account user tried to login", 1);
            }

            if( $userdata["serial"] == "" ) 
            {
                $lefttitle .= " - Failed";
                $exit_stage2 = true;
                $notuser = true;
                $isuser = false;
                $_SESSION = array(  );
                setcookie("gamecp_userdata", "", time() - 3600);
                if( isset($_COOKIE["gamecp_userdata"]) ) 
                {
                    unset($_COOKIE["gamecp_userdata"]);
                }

                session_destroy();
                $out .= "<p style=\"text-align: center; font-weight: bold;\">You must log in the game (via the launcher) at least once in order to use the Game CP.</p>";
            }

            @mssql_free_result($query_account);
            @mssql_free_result($query_ban);
        }
        else
        {
            $out .= "<center>Invalid Username or Password. Please try again.</center>";
            $exit_stage2 = true;
        }

        if( $exit_stage2 != true ) 
        {
            $password_data = md5($username) . $ip . sha1(md5($query_password . $config["security_salt"]));
            $cookie_data = $username . chr(255) . $password_data;
            setcookie("gamecp_userdata", $cookie_data);
            if( in_array($userdata["username"], $super_admin) ) 
            {
                gamecp_log(3, $userdata["username"], "SUPER ADMIN - LOGGED IN", 1);
            }

            header("Location: ./" . $script_name);
        }

    }

    @mssql_free_result($query_user);
    @mssql_close($user_dbconnect);
}

if( !isset($disable_nav) ) 
{
    gamecp_nav();
}

$navbits = array( $script_name => $program_name, "" => $lefttitle );
eval("print_outputs(\"" . gamecp_template("gamecp") . "\");");

