,<?php 
if( !defined("IN_GAMECP_SALT58585") ) 
{
    exit( "Hacking Attempt" );
}

if( !file_exists("./includes/main/config.php") ) 
{
    game_cp_22("Please setup your Game Control Panel config.php (re-name config.php.edit to config.php)");
}

if( !file_exists("./includes/main/definitions.php") ) 
{
    game_cp_22("Please go into your includes/main/ and rename definitions.php.edit to definitions.php. Edit its contents only if needed!", "warning");
}

if( !function_exists("mssql_connect") ) 
{
    game_cp_22("Your server does not have the MSSQL module loaded with PHP");
}

if( !is_dir("./includes/cache/") ) 
{
    game_cp_22("Woops! Please create the cache folder");
}

if( !is_writable("./includes/cache") ) 
{
    game_cp_22("Woops! It looks like I cannot read/write to the /includes/cache/ folder. Make sure I have the right permissions");
}

if( !isset($_SERVER["REQUEST_URI"]) ) 
{
    $_SERVER["REQUEST_URI"] = "/" . substr($_SERVER["PHP_SELF"], 1);
    if( isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != "" ) 
    {
        $_SERVER["REQUEST_URI"] .= "?" . $_SERVER["QUERY_STRING"];
    }

}

if( !isset($_SERVER["DOCUMENT_ROOT"]) && isset($_SERVER["SCRIPT_FILENAME"]) ) 
{
    $_SERVER["DOCUMENT_ROOT"] = str_replace("\\", "/", substr($_SERVER["SCRIPT_FILENAME"], 0, 0 - strlen($_SERVER["PHP_SELF"])));
}

if( !isset($_SERVER["DOCUMENT_ROOT"]) && isset($_SERVER["PATH_TRANSLATED"]) ) 
{
    $_SERVER["DOCUMENT_ROOT"] = str_replace("\\", "/", substr(str_replace("\\\\", "\\", $_SERVER["PATH_TRANSLATED"]), 0, 0 - strlen($_SERVER["PHP_SELF"])));
}

@session_start();
include("./includes/main/config.php");
include("./includes/main/functions.php");
include("./includes/main/definitions.php");
if( get_magic_quotes_runtime() ) 
{
    game_cp_22("Sorry, cannot have magic quotes enabled. Please disable magic quotes in your php.ini");
}

if( "5.0.0" <= @phpversion() && (!@ini_get("register_long_arrays") || @ini_get("register_long_arrays") == "0" || strtolower(@ini_get("register_long_arrays")) == "off") ) 
{
    $HTTP_POST_VARS = $_POST;
    $HTTP_GET_VARS = $_GET;
    $HTTP_SERVER_VARS = $_SERVER;
    $HTTP_COOKIE_VARS = $_COOKIE;
    $HTTP_ENV_VARS = $_ENV;
    $HTTP_POST_FILES = $_FILES;
    if( isset($_SESSION) ) 
    {
        $HTTP_SESSION_VARS = $_SESSION;
    }

}

if( isset($HTTP_POST_VARS["GLOBALS"]) || isset($HTTP_POST_FILES["GLOBALS"]) || isset($HTTP_GET_VARS["GLOBALS"]) || isset($HTTP_COOKIE_VARS["GLOBALS"]) ) 
{
    exit( "Hacking attempt" );
}

if( isset($HTTP_SESSION_VARS) && !is_array($HTTP_SESSION_VARS) ) 
{
    exit( "Hacking attempt" );
}

if( @ini_get("register_globals") == "1" || strtolower(@ini_get("register_globals")) == "on" ) 
{
    $not_unset = array( "HTTP_GET_VARS", "HTTP_POST_VARS", "HTTP_COOKIE_VARS", "HTTP_SERVER_VARS", "HTTP_SESSION_VARS", "HTTP_ENV_VARS", "HTTP_POST_FILES", "phpEx", "phpbb_root_path" );
    if( !isset($HTTP_SESSION_VARS) || !is_array($HTTP_SESSION_VARS) ) 
    {
        $HTTP_SESSION_VARS = array(  );
    }

    $input = array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_SESSION_VARS, $HTTP_ENV_VARS, $HTTP_POST_FILES);
    unset($input["input"]);
    unset($input["not_unset"]);
    while( list($var) = @each($input) ) 
    {
        if( in_array($var, $not_unset) ) 
        {
            exit( "Hacking attempt!" );
        }

        unset(${$var});
    }
    unset($input);
}

if( is_array($_GET) ) 
{
    while( list($k, $v) = each($_GET) ) 
    {
        if( is_array($_GET[$k]) ) 
        {
            while( list($k2, $v2) = each($_GET[$k]) ) 
            {
                $_GET[$k][$k2] = antiject($v2);
            }
            @reset($_GET[$k]);
        }
        else
        {
            $_GET[$k] = antiject($v);
        }

    }
    @reset($_GET);
}

if( is_array($_POST) ) 
{
    while( list($k, $v) = each($_POST) ) 
    {
        if( is_array($_POST[$k]) ) 
        {
            while( list($k2, $v2) = each($_POST[$k]) ) 
            {
                $_POST[$k][$k2] = antiject($v2);
            }
            @reset($_POST[$k]);
        }
        else
        {
            $_POST[$k] = antiject($v);
        }

    }
    @reset($_POST);
}

if( is_array($_COOKIE) ) 
{
    while( list($k, $v) = each($_COOKIE) ) 
    {
        if( is_array($_COOKIE[$k]) ) 
        {
            while( list($k2, $v2) = each($_COOKIE[$k]) ) 
            {
                $_COOKIE[$k][$k2] = antiject($v2);
            }
            @reset($_COOKIE[$k]);
        }
        else
        {
            $_COOKIE[$k] = antiject($v);
        }

    }
    @reset($_COOKIE);
}

if( version_compare(PHP_VERSION, "6.0.0-dev", ">=") ) 
{
    define("STRIP", false);
}
else
{
    @set_magic_quotes_runtime(0);
    if( @ini_get("register_globals") == "1" || strtolower(@ini_get("register_globals")) == "on" || !function_exists("ini_get") ) 
    {
        deregister_globals();
    }

    define("STRIP", get_magic_quotes_gpc() ? true : false);
}

$_SERVER["REMOTE_ADDR"] = getip();
connectgamecpdb();
$config_query = "SELECT config_name, config_value FROM gamecp_config";
if( !($config_result = mssql_query($config_query)) ) 
{
    echo "Unable to obtain data from the configuration database";
    exit();
}

while( $row = mssql_fetch_array($config_result) ) 
{
    $config[$row["config_name"]] = $row["config_value"];
}
mssql_free_result($config_result);
mssql_close($gamecp_dbconnect);
if( $config["security_enable_debug"] == 1 ) 
{
    error_reporting(E_ALL ^ E_NOTICE);
}
else
{
    error_reporting(0);
}

set_error_handler("errorHandler");
$script_name = isset($config["gamecp_filename"]) ? $config["gamecp_filename"] : "index.php";
$program_name = isset($config["gamecp_programname"]) ? $config["gamecp_programname"] : "Game CP";
$super_admin = explode(",", $admin["super_admin"]);
setlocale(LC_TIME, "en_US");
$onload = "";
$vbpath = "";
$index = "";
$out = "";
$mainincludes = "";
$isuser = false;
$notuser = true;
$exit_login = false;
$scripts = $_SERVER["PHP_SELF"];
$scripts = explode(chr(47), $scripts);
$this_script = $scripts[count($scripts) - 1];
$cookiedata = isset($_COOKIE["gamecp_userdata"]) ? $_COOKIE["gamecp_userdata"] : "";
$ip = gethostbyname($_SERVER["REMOTE_ADDR"]);
$out = "";
$title = "";
$exit_message = "";
$userdata = array(  );
$userdata["email"] = "";
$userdata["status"] = false;
$userdata["username"] = "Guest";
$userdata["serial"] = "-1";
$userdata["credits"] = "";
$userdata["createtime"] = "";
$userdata["lastconnectip"] = "";
$userdata["points"] = 0;
$userdata["vote_points"] = 0;
if( !isset($config["security_salt"]) || empty($config["security_salt"]) ) 
{
    game_cp_22("Cannot run the script without the security_salt set to a value!");
}

if( $cookiedata != "" ) 
{
    $cookieex = explode(chr(255), $cookiedata);
    $cookie_username = antiject(trim($cookieex[0]));
    $cookie_password = antiject(trim($cookieex[1]));
    if( eregi("[^a-zA-Z0-9_-]", $cookie_username) ) 
    {
        $exit_login = true;
        $exit_message = "<p style=\"text-align: center; font-weight: bold;\">Invalid login usage supplied!</p>";
    }

    if( $exit_login != true ) 
    {
        connectuserdb();
        $login_sql = "SELECT\r\n\t\tAccountName = CAST(L.id AS varchar(255)), Password = CAST(L.Password AS varchar(255)), L.EMail, U.Serial, U.CreateTime, U.LastConnectIP, U.lastlogintime, U.lastlogofftime\r\n\t\tFROM\r\n\t\t\t tbl_rfaccount AS L\r\n\t\t\tINNER JOIN\r\n\t\t\ttbl_UserAccount AS U\r\n\t\t\tON L.id = U.id\r\n\t\tWHERE\r\n\t\t\tL.id = convert(binary,'" . $cookie_username . "')";
        if( !($query_result = mssql_query($login_sql)) ) 
        {
            $exit_login = true;
            $exit_message = "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to obtain user information</p>";
        }

        if( !($row = mssql_fetch_array($query_result)) ) 
        {
            $exit_login = true;
            $exit_message = "<p style=\"text-align: center; font-weight: bold;\">Unable to find your user information!</p>";
            $_SESSION = array(  );
            setcookie("gamecp_userdata", "", time() - 3600);
            if( isset($_COOKIE["gamecp_userdata"]) ) 
            {
                unset($_COOKIE["gamecp_userdata"]);
            }

            $notuser = true;
            $isuser = false;
            $is_superadmin = false;
            session_destroy();
        }

    }

    if( $exit_login != true ) 
    {
        $userdata["username"] = antiject(trim($row["AccountName"]));
        $userdata["password"] = antiject(trim($row["Password"]));
        $password_data = md5($userdata["username"]) . $ip . sha1(md5($userdata["password"] . $config["security_salt"]));
        if( $cookie_username == $userdata["username"] && $cookie_password == $password_data ) 
        {
            $isuser = true;
            $notuser = false;
            $userdata["serial"] = $row["Serial"];
            $userdata["email"] = $row["EMail"];
            $userdata["createtime"] = strtotime($row["CreateTime"]);
            $userdata["lastconnectip"] = $row["LastConnectIP"];
            $userdata["lastlogintime"] = $row["lastlogintime"];
            $userdata["lastlogofftime"] = $row["lastlogofftime"];
            $t_login = strtotime($userdata["lastlogintime"]);
            $t_logout = strtotime($userdata["lastlogofftime"]);
            if( $t_login <= $t_logout ) 
            {
                $userdata["status"] = false;
            }
            else
            {
                $userdata["status"] = true;
            }

            if( $userdata["serial"] == "" ) 
            {
                $_SESSION = array(  );
                setcookie("gamecp_userdata", "", time() - 3600);
                if( isset($_COOKIE["gamecp_userdata"]) ) 
                {
                    unset($_COOKIE["gamecp_userdata"]);
                }

                $notuser = true;
                $isuser = false;
                session_destroy();
            }

            $query_ban = mssql_query("SELECT nAccountSerial FROM tbl_UserBan WHERE nAccountSerial = '" . $userdata["serial"] . "'");
            if( $userdata["serial"] != "" && 0 < mssql_num_rows($query_ban) && !in_array($userdata["username"], $super_admin) ) 
            {
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

            mssql_free_result($query_ban);
            connectgamecpdb();
            $user_points_sql = "SELECT user_points,user_vote_points FROM gamecp_gamepoints WHERE user_account_id = '" . $userdata["serial"] . "'";
            if( !($user_points_result = mssql_query($user_points_sql)) ) 
            {
                echo "Unable to select query the Game Points table";
                exit();
            }

            $permission_query = "SELECT admin_permission FROM gamecp_permissions WHERE admin_serial = '" . $userdata["serial"] . "'";
            $permission_query = mssql_query($permission_query);
            if( !($user_access = @mssql_fetch_array($permission_query)) ) 
            {
                $user_access = false;
            }

            mssql_free_result($permission_query);
            $userpoints = mssql_fetch_array($user_points_result);
            if( mssql_num_rows($user_points_result) <= 0 ) 
            {
                $user_points_sql = "INSERT INTO gamecp_gamepoints (user_account_id) VALUES ('" . $userdata["serial"] . "')";
                if( !($user_points_result = mssql_query($user_points_sql)) ) 
                {
                    echo "Unable to insert query the Game Points table";
                    exit();
                }

                $userdata["points"] = 0;
                $userdata["vote_points"] = 0;
            }
            else
            {
                $userdata["points"] = $userpoints["user_points"];
                $userdata["vote_points"] = $userpoints["user_vote_points"];
            }

            @mssql_free_result($user_points_result);
            if( $userdata["points"] == "" ) 
            {
                $userdata["points"] = 0;
            }

            connectgamecpdb();
            unset($userdata["password"]);
        }
        else
        {
            $notuser = true;
            $isuser = false;
            if( isset($userdata["password"]) ) 
            {
                unset($userdata["password"]);
            }

        }

    }
    else
    {
        $out .= $exit_message;
    }

}
else
{
    $isuser = false;
}

if( $isuser == true && in_array($userdata["username"], $super_admin) ) 
{
    $is_superadmin = true;
}
else
{
    $is_superadmin = false;
    error_reporting(0);
}

$securitytoken_raw = sha1($userdata["serial"] . sha1($config["security_salt"]) . sha1($config["security_salt"]));
$securitytoken = time() . "-" . sha1(time() . $securitytoken_raw);
$_license_properties = ioncube_file_properties();
if( is_array($_license_properties) && isset($_license_properties["gamecp"]) ) 
{
    $_license_properties = $_license_properties["gamecp"];
}
else
{

}

if( isset($config["security_recaptcha_enable"]) && $config["security_recaptcha_enable"] == 1 ) 
{
    require_once("./includes/main/recaptchalib.php");
    $publickey = isset($config["security_recaptcha_public_key"]) ? $config["security_recaptcha_public_key"] : "";
    $privatekey = isset($config["security_recaptcha_private_key"]) ? $config["security_recaptcha_private_key"] : "";
    $resp = null;
    $error = null;
}

connectgamecpdb();
$vote = array(  );
$vote_sql = "SELECT vote_id, vote_site_name, vote_site_url, vote_site_image, vote_reset_time FROM gamecp_vote_sites";
if( !($vote_result = mssql_query($vote_sql)) ) 
{
    $exit_stage_0 = true;
    $show_form = false;
    $page_info .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to obtain vote sites data</p>";
}

while( $row = @mssql_fetch_array($vote_result) ) 
{
    $vote[] = $row;
}
mssql_free_result($vote_result);
$vote_page = "";
for( $i = 0; $i < count($vote); $i++ ) 
{
    $vote_page .= "\t\t\t<a href=\"javascript:voteScript('" . $vote[$i]["vote_id"] . "','" . $vote[$i]["vote_site_name"] . "')\" title=\"" . $vote[$i]["vote_site_name"] . "\"><img src=\"" . $vote[$i]["vote_site_image"] . "\" alt=\"" . $vote[$i]["vote_site_name"] . "\" border=\"0\"></a>" . "\n";
}
function game_cp_22($message, $type = "error")
{
    echo "\t<head>\r\n\t<title>RF Online Game Control Panel v2 - by rf overflow</title>\r\n\t<style type=\"text/css\">\r\n\tbody{\r\n\t\tfont-family:Arial, Helvetica, sans-serif; \r\n\t\tfont-size:13px;\r\n\t\tbackground-color: #F7F7F7;\r\n\t\tmargin: 50px;\r\n\t\tpadding: 20px;\r\n\t}\r\n\ta\r\n\t{\r\n\t\tcolor: #580000; \r\n\t\ttext-decoration: none;\r\n\t}\r\n\r\n\ta:hover\r\n\t{\r\n\t\tcolor: #C76E0F;\r\n\t}\r\n\r\n\t.info, .success, .warning, .error, .validation {\r\n\t\tborder: 1px solid;\r\n\t\tmargin: 10px 0px;\r\n\t\tpadding:15px 10px 15px 50px;\r\n\t\tbackground-repeat: no-repeat;\r\n\t\tbackground-position: 10px center;\r\n\t}\r\n\t.info {\r\n\t\tcolor: #00529B;\r\n\t\tbackground-color: #BDE5F8;\r\n\t\tbackground-image: url('./includes/images/knobs/info.png');\r\n\t}\r\n\t.success {\r\n\t\tcolor: #4F8A10;\r\n\t\tbackground-color: #DFF2BF;\r\n\t\tbackground-image:url('./includes/images/knobs/success.png');\r\n\t}\r\n\t.warning {\r\n\t\tcolor: #9F6000;\r\n\t\tbackground-color: #FEEFB3;\r\n\t\tbackground-image: url('./includes/images/knobs/warning.png');\r\n\t}\r\n\t.error {\r\n\t\tcolor: #D8000C;\r\n\t\tbackground-color: #FFBABA;\r\n\t\tbackground-image: url('./includes/images/knobs/error.png');\r\n\t}\r\n\t</style>\r\n\t</head>\r\n\r\n\t<body>\r\n\t<h2>RF Online Game Control Panel</h2>\r\n\t";
    echo "<div class=\"" . $type . "\">" . $message . "</div>";
    echo "\t<div style=\"text-align: center;\"><small>Copyright &copy; <a href=\"http://www.rfoverflow.us/\">rfoverflow</a>. Images by <a href=\"http://itweek.deviantart.com/art/Knob-Buttons-Toolbar-icons-73463960\" style=\"font-style: italic;\">iTweek (deviantArt)</a> & CSS by <a href=\"http://css.dzone.com/news/css-message-boxes-different-me\" style=\"font-style: italic;\">Janko Jovanovic</a></small></div>\r\n\t</body>\r\n\t</html>\r\n\t";
    exit( 1 );
}

function deregister_globals()
{
    $not_unset = array( "GLOBALS" => true, "_GET" => true, "_POST" => true, "_COOKIE" => true, "_REQUEST" => true, "_SERVER" => true, "_SESSION" => true, "_ENV" => true, "_FILES" => true, "phpEx" => true, "phpbb_root_path" => true );
    if( !isset($_SESSION) || !is_array($_SESSION) ) 
    {
        $_SESSION = array(  );
    }

    $input = array_merge(array_keys($_GET), array_keys($_POST), array_keys($_COOKIE), array_keys($_SERVER), array_keys($_SESSION), array_keys($_ENV), array_keys($_FILES));
    foreach( $input as $varname ) 
    {
        if( isset($not_unset[$varname]) ) 
        {
            if( $varname !== "GLOBALS" || isset($_GET["GLOBALS"]) || isset($_POST["GLOBALS"]) || isset($_SERVER["GLOBALS"]) || isset($_SESSION["GLOBALS"]) || isset($_ENV["GLOBALS"]) || isset($_FILES["GLOBALS"]) ) 
            {
                exit();
            }

            global $_COOKIE;
            while( isset($cookie["GLOBALS"]) ) 
            {
                foreach( $cookie["GLOBALS"] as $registered_var => $value ) 
                {
                    if( !isset($not_unset[$registered_var]) ) 
                    {
                        unset($GLOBALS[$registered_var]);
                    }

                }
                $cookie =& $cookie["GLOBALS"];
            }
        }

        unset($GLOBALS[$varname]);
    }
    unset($input);
}

function validip($ip)
{
    if( !empty($ip) && ip2long($ip) != 0 - 1 ) 
    {
        $reserved_ips = array( array( "0.0.0.0", "2.255.255.255" ), array( "10.0.0.0", "10.255.255.255" ), array( "127.0.0.0", "127.255.255.255" ), array( "169.254.0.0", "169.254.255.255" ), array( "172.16.0.0", "172.31.255.255" ), array( "192.0.2.0", "192.0.2.255" ), array( "192.168.0.0", "192.168.255.255" ), array( "255.255.255.0", "255.255.255.255" ) );
        foreach( $reserved_ips as $r ) 
        {
            $min = ip2long($r[0]);
            $max = ip2long($r[1]);
            if( $min <= ip2long($ip) && ip2long($ip) <= $max ) 
            {
                return false;
            }

        }
        return true;
    }

    return false;
}

function getip()
{
    if( isset($_SERVER["HTTP_CLIENT_IP"]) && validip($_SERVER["HTTP_CLIENT_IP"]) ) 
    {
        return $_SERVER["HTTP_CLIENT_IP"];
    }

    if( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) 
    {
        foreach( explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip ) 
        {
            if( validip(trim($ip)) ) 
            {
                return $ip;
            }

        }
    }

    if( isset($_SERVER["HTTP_X_FORWARDED"]) && validip($_SERVER["HTTP_X_FORWARDED"]) ) 
    {
        return $_SERVER["HTTP_X_FORWARDED"];
    }

    if( isset($_SERVER["HTTP_FORWARDED_FOR"]) && validip($_SERVER["HTTP_FORWARDED_FOR"]) ) 
    {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    }

    if( isset($_SERVER["HTTP_FORWARDED"]) && validip($_SERVER["HTTP_FORWARDED"]) ) 
    {
        return $_SERVER["HTTP_FORWARDED"];
    }

    if( isset($_SERVER["HTTP_X_FORWARDED"]) && validip($_SERVER["HTTP_X_FORWARDED"]) ) 
    {
        return $_SERVER["HTTP_X_FORWARDED"];
    }

    return $_SERVER["REMOTE_ADDR"];
}


