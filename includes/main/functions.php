<?php 
$lang["no_permission"] = "<p style=\"text-align: center; font-weight: bold;\">Sorry, you do not have permission to access this page</p>";
$lang["invalid_page_load"] = "<p style=\"text-align: center; font-weight: bold;\">This is an invalid load of this page, please contact the administrator</p>";
$lang["invalid_page_id"] = "<p style=\"text-align: center; font-weight: bold;\">You have entered an invalid page ID</p>";
$lang["page_not_found"] = "<p style=\"text-align: center; font-weight: bold;\">The page you have requested cannot be found</p>";
$lang["must_be_logged_in_view"] = "<p style=\"text-align: center; font-weight: bold;\">You must log in to view this page</p>";
$lang["invalid_serial"] = "<p style=\"text-align: center; font-weight: bold;\">Invalid serial has been provided</p>";
$lang["no_such_item"] = "<p style=\"text-align: center; font-weight: bold;\">No such item found in the database.</p>";
$item_tbl_num = 36;
define("tbl_code_upper", "0");
define("tbl_code_lower", "1");
define("tbl_code_gauntlet", "2");
define("tbl_code_shoe", "3");
define("tbl_code_helmet", "4");
define("tbl_code_shield", "5");
define("tbl_code_weapon", "6");
define("tbl_code_cloak", "7");
define("tbl_code_ring", "8");
define("tbl_code_amulet", "9");
define("tbl_code_bullet", "10");
define("tbl_code_maketool", "11");
define("tbl_code_bag", "12");
define("tbl_code_potion", "13");
define("tbl_code_face", "14");
define("tbl_code_force", "15");
define("tbl_code_battery", "16");
define("tbl_code_ore", "17");
define("tbl_code_resource", "18");
define("tbl_code_unitkey", "19");
define("tbl_code_booty", "20");
define("tbl_code_map", "21");
define("tbl_code_town", "22");
define("tbl_code_battledungeon", "23");
define("tbl_code_animus", "24");
define("tbl_code_guardtower", "25");
define("tbl_code_trap", "26");
define("tbl_code_siegekit", "27");
define("tbl_code_ticket", "28");
define("tbl_code_event", "29");
define("tbl_code_recovery", "30");
define("tbl_code_box", "31");
define("tbl_code_firecracker", "32");
define("tbl_code_miningtool", "33");
define("tbl_code_radar", "34");
define("tbl_code_npclink", "35");
function getitemtablename($szPreFix)
{
    if( $szPreFix == "0" ) 
    {
        return "tbl_code_upper";
    }

    if( $szPreFix == "1" ) 
    {
        return "tbl_code_lower";
    }

    if( $szPreFix == "2" ) 
    {
        return "tbl_code_gauntlet";
    }

    if( $szPreFix == "3" ) 
    {
        return "tbl_code_shoe";
    }

    if( $szPreFix == "4" ) 
    {
        return "tbl_code_helmet";
    }

    if( $szPreFix == "5" ) 
    {
        return "tbl_code_shield";
    }

    if( $szPreFix == "6" ) 
    {
        return "tbl_code_weapon";
    }

    if( $szPreFix == "7" ) 
    {
        return "tbl_code_cloak";
    }

    if( $szPreFix == "8" ) 
    {
        return "tbl_code_ring";
    }

    if( $szPreFix == "9" ) 
    {
        return "tbl_code_amulet";
    }

    if( $szPreFix == "10" ) 
    {
        return "tbl_code_bullet";
    }

    if( $szPreFix == "11" ) 
    {
        return "tbl_code_maketool";
    }

    if( $szPreFix == "12" ) 
    {
        return "tbl_code_bag";
    }

    if( $szPreFix == "13" ) 
    {
        return "tbl_code_potion";
    }

    if( $szPreFix == "14" ) 
    {
        return "tbl_code_face";
    }

    if( $szPreFix == "15" ) 
    {
        return "tbl_code_force";
    }

    if( $szPreFix == "16" ) 
    {
        return "tbl_code_battery";
    }

    if( $szPreFix == "17" ) 
    {
        return "tbl_code_ore";
    }

    if( $szPreFix == "18" ) 
    {
        return "tbl_code_resource";
    }

    if( $szPreFix == "19" ) 
    {
        return "tbl_code_unitkey";
    }

    if( $szPreFix == "20" ) 
    {
        return "tbl_code_booty";
    }

    if( $szPreFix == "21" ) 
    {
        return "tbl_code_map";
    }

    if( $szPreFix == "22" ) 
    {
        return "tbl_code_town";
    }

    if( $szPreFix == "23" ) 
    {
        return "tbl_code_battledungeon";
    }

    if( $szPreFix == "24" ) 
    {
        return "tbl_code_animus";
    }

    if( $szPreFix == "25" ) 
    {
        return "tbl_code_guardtower";
    }

    if( $szPreFix == "26" ) 
    {
        return "tbl_code_trap";
    }

    if( $szPreFix == "27" ) 
    {
        return "tbl_code_siegekit";
    }

    if( $szPreFix == "28" ) 
    {
        return "tbl_code_ticket";
    }

    if( $szPreFix == "29" ) 
    {
        return "tbl_code_event";
    }

    if( $szPreFix == "30" ) 
    {
        return "tbl_code_recovery";
    }

    if( $szPreFix == "31" ) 
    {
        return "tbl_code_box";
    }

    if( $szPreFix == "32" ) 
    {
        return "tbl_code_firecracker";
    }

    if( $szPreFix == "33" ) 
    {
        return "tbl_code_unmannedminer";
    }

    if( $szPreFix == "34" ) 
    {
        return "tbl_code_radar";
    }

    if( $szPreFix == "35" ) 
    {
        return "tbl_code_npclink";
    }

    return "tbl_code_weapon";
}

function game_cp_4($psItemCode)
{
    $szPreFix = substr($psItemCode, 0, 2);
    if( $szPreFix == "iu" ) 
    {
        return tbl_code_upper;
    }

    if( $szPreFix == "il" ) 
    {
        return tbl_code_lower;
    }

    if( $szPreFix == "ig" ) 
    {
        return tbl_code_gauntlet;
    }

    if( $szPreFix == "is" ) 
    {
        return tbl_code_shoe;
    }

    if( $szPreFix == "ih" ) 
    {
        return tbl_code_helmet;
    }

    if( $szPreFix == "id" ) 
    {
        return tbl_code_shield;
    }

    if( $szPreFix == "iw" ) 
    {
        return tbl_code_weapon;
    }

    if( $szPreFix == "im" ) 
    {
        return tbl_code_maketool;
    }

    if( $szPreFix == "ie" ) 
    {
        return tbl_code_bag;
    }

    if( $szPreFix == "ip" ) 
    {
        return tbl_code_potion;
    }

    if( $szPreFix == "ib" ) 
    {
        return tbl_code_bullet;
    }

    if( $szPreFix == "if" ) 
    {
        return tbl_code_face;
    }

    if( $szPreFix == "ic" ) 
    {
        return tbl_code_force;
    }

    if( $szPreFix == "it" ) 
    {
        return tbl_code_battery;
    }

    if( $szPreFix == "io" ) 
    {
        return tbl_code_ore;
    }

    if( $szPreFix == "ir" ) 
    {
        return tbl_code_resource;
    }

    if( $szPreFix == "in" ) 
    {
        return tbl_code_unitkey;
    }

    if( $szPreFix == "iy" ) 
    {
        return tbl_code_booty;
    }

    if( $szPreFix == "ik" ) 
    {
        return tbl_code_cloak;
    }

    if( $szPreFix == "ii" ) 
    {
        return tbl_code_ring;
    }

    if( $szPreFix == "ia" ) 
    {
        return tbl_code_amulet;
    }

    if( $szPreFix == "iz" ) 
    {
        return tbl_code_map;
    }

    if( $szPreFix == "iq" ) 
    {
        return tbl_code_town;
    }

    if( $szPreFix == "ix" ) 
    {
        return tbl_code_battledungeon;
    }

    if( $szPreFix == "ij" ) 
    {
        return tbl_code_animus;
    }

    if( $szPreFix == "gt" ) 
    {
        return tbl_code_guardtower;
    }

    if( $szPreFix == "tr" ) 
    {
        return tbl_code_trap;
    }

    if( $szPreFix == "sk" ) 
    {
        return tbl_code_siegekit;
    }

    if( $szPreFix == "ti" ) 
    {
        return tbl_code_ticket;
    }

    if( $szPreFix == "ev" ) 
    {
        return tbl_code_event;
    }

    if( $szPreFix == "re" ) 
    {
        return tbl_code_recovery;
    }

    if( $szPreFix == "bx" ) 
    {
        return tbl_code_box;
    }

    if( $szPreFix == "fi" ) 
    {
        return tbl_code_firecracker;
    }

    if( $szPreFix == "un" ) 
    {
        return tbl_code_miningtool;
    }

    if( $szPreFix == "rd" ) 
    {
        return tbl_code_radar;
    }

    if( $szPreFix == "lk" ) 
    {
        return tbl_code_npclink;
    }

    return 0 - 1;
}

function antiject($str)
{
    $str = stripslashes($str);
    $str = htmlspecialchars($str);
    $str = trim($str);
    $str = preg_replace("/'/", "''", $str);
    $str = preg_replace("/\"/", "\"\"", $str);
    $str = str_replace("`", "", $str);
    return $str;
}

function commas($str)
{
    return number_format(floor($str));
}

function isemail($e)
{
    if( eregi("" . "^[a-zA-Z0-9]+[_a-zA-Z0-9-]*(\\.[_a-z0-9-]+)*@[a-z?G0-9]+(-[a-z?G0-9]+)*(\\.[a-z?G0-9-]+)*(\\.[a-z]{2,4})\$", $e) ) 
    {
        return true;
    }

    return false;
}

function connectuserdb()
{
    global $mssql;
    global $user_dbconnect;
    global $userdb;
    $user_dbconnect = @mssql_connect($mssql["user"]["host"], $mssql["user"]["username"], $mssql["user"]["password"]) or exit( "Couldn't connect to user database server. " . mssql_get_last_message() );
    $userdb = mssql_select_db($mssql["user"]["db"], $user_dbconnect) or exit( mssql_get_last_message() );
}

function game_cp_32()
{
    global $mssql;
    global $user2_dbconnect;
    global $user2db;
    $user2_dbconnect = @mssql_connect($mssql["user2"]["host"], $mssql["user2"]["username"], $mssql["user2"]["password"]) or exit( "Couldn't connect to user2 database server. " . mssql_get_last_message() );
    $user2db = mssql_select_db($mssql["user2"]["db"], $user2_dbconnect) or exit( mssql_get_last_message() );
}

function connectdatadb()
{
    global $mssql;
    global $data_dbconnect;
    global $datadb;
    $data_dbconnect = @mssql_connect($mssql["data"]["host"], $mssql["data"]["username"], $mssql["data"]["password"]) or exit( "Couldn't connect to data database server. " . mssql_get_last_message() );
    $datadb = @mssql_select_db($mssql["data"]["db"], $data_dbconnect) or exit( mssql_get_last_message() );
}

function connectgamecpdb()
{
    global $mssql;
    global $gamecp_dbconnect;
    global $gamecpdb;
    $gamecp_dbconnect = @mssql_connect($mssql["gamecp"]["host"], $mssql["gamecp"]["username"], $mssql["gamecp"]["password"]) or exit( "Couldn't connect to the Game CP database server. " . mssql_get_last_message() );
    $gamecpdb = @mssql_select_db($mssql["gamecp"]["db"], $gamecp_dbconnect) or exit( mssql_get_last_message() );
}

function game_cp_40()
{
    global $mssql;
    global $donate_dbconnect;
    global $donatedb;
    $donate_dbconnect = @mssql_connect($mssql["gamecp"]["host"], $mssql["gamecp"]["username"], $mssql["gamecp"]["password"]) or exit( "Couldn't connect to donations database server. " . mssql_get_last_message() );
    $donatedb = @mssql_select_db($mssql["gamecp"]["db"], $donate_dbconnect) or exit( mssql_get_last_message() );
}

function connectitemsdb()
{
    global $mssql;
    global $items_dbconnect;
    global $itemsdb;
    $items_dbconnect = @mssql_connect($mssql["items"]["host"], $mssql["items"]["username"], $mssql["items"]["password"]) or exit( "Couldn't connect to items database server. " . mssql_get_last_message() );
    $itemsdb = @mssql_select_db($mssql["items"]["db"], $items_dbconnect) or exit( mssql_get_last_message() );
}

function getRace($strrace)
{
    $race = "Unknown";
    $racechar = substr($strrace, 0, 1);
    if( $racechar == "C" ) 
    {
        $race = "Cora";
    }
    else
    {
        if( $racechar == "B" ) 
        {
            $race = "Bellato";
        }
        else
        {
            if( $racechar == "A" ) 
            {
                $race = "Accretia";
            }

        }

    }

    return $race;
}

function game_cp_7($racechar)
{
    $race = "Unknown";
    if( $racechar == 0 || $racechar == 1 ) 
    {
        $race = "Bellato";
    }
    else
    {
        if( $racechar == 2 || $racechar == 3 ) 
        {
            $race = "Cora";
        }
        else
        {
            if( $racechar == 4 ) 
            {
                $race = "Accretia";
            }

        }

    }

    return $race;
}

function getcharacters($id)
{
    $charinfo = "";
    $i = 0;
    connectdatadb();
    for( $cquery = mssql_query("SELECT \n\tB.Serial,B.Name,B.AccountSerial,B.Account,B.Class,B.Lv,B.Race,G.TotalPlayMin\n\tFROM tbl_base AS B\n\tRIGHT JOIN\n\ttbl_general AS G\n\tON B.Serial = G.Serial\n\tWHERE B.AccountSerial=\"" . $id . "\" and B.DCK=0"); $character = mssql_fetch_array($cquery); $i++ ) 
    {
        $charinfo[$i]["Serial"] = $character["Serial"];
        $charinfo[$i]["Name"] = $character["Name"];
        $charinfo[$i]["AccountSerial"] = $character["AccountSerial"];
        $charinfo[$i]["Account"] = $character["Account"];
        $charinfo[$i]["Race"] = getrace($character["Class"]);
        $charinfo[$i]["Level"] = $character["Lv"];
        $charinfo[$i]["Sex"] = $character["Race"];
        $charinfo[$i]["TotalPlay"] = $character["TotalPlayMin"];
    }
    return $charinfo;
}

function game_cp_41($id, $server)
{
    connectdatadb($server);
    for( $usertable = 1; $usertable <= 4; $usertable++ ) 
    {
        $query = mssql_fetch_row(mssql_query("SELECT Char1,Char2,Char3 FROM UserInfo_" . $usertable . " WHERE UID=\"" . $id . "\""));
        if( $query != "" ) 
        {
            foreach( $query as $cid ) 
            {
                for( $chartable = 1; $chartable <= 4; $chartable++ ) 
                {
                    for( $cquery = mssql_query("SELECT name, Nationality, Class, Level, UID FROM CharInfo_" . $chartable . " WHERE UID=\"" . $cid . "\""); $character = mssql_fetch_row($cquery); $i++ ) 
                    {
                        $charinfo[$i][0] = $character[0];
                        $charinfo[$i][1] = getrace($character[1]);
                        $charinfo[$i][2] = $character[2];
                        $charinfo[$i][3] = $character[3];
                        $charinfo[$i][4] = $servers[$server];
                        $charinfo[$i][5] = $character[4];
                        $charinfo[$i][6] = $chartable;
                    }
                }
            }
        }

    }
    return $charinfo;
}

function game_cp_14($name)
{
    $game_cp_14 = false;
    connectdatadb();
    $cquery = mssql_result(mssql_query("SELECT count(*) FROM tbl_base WHERE Name=\"" . $name . "\""), 0, 0);
    if( 0 < $cquery ) 
    {
        return true;
    }

    return false;
}

function getaccount($id)
{
    connectuserdb();
    $acc = mssql_fetch_row(mssql_query("SELECT * FROM usertbl WHERE UID=\"" . $id . "\""));
    return $acc;
}

function isusers($id, $charid)
{
    $isusers = false;
    connectdatadb();
    $query = mssql_query("SELECT Name,Account FROM tbl_base WHERE Name=\"" . $charid . "\"");
    $query = mssql_fetch_array($query);
    if( $query != "" && $query["Account"] == $id ) 
    {
        $isusers = true;
    }

    return $isusers;
}

function game_cp_42($id, $charid)
{
    $isusers = false;
    connectdatadb();
    $query = mssql_query("SELECT Name,Account FROM tbl_base WHERE Serial=\"" . $charid . "\"");
    $query = mssql_fetch_array($query);
    if( $query != "" && $query["Account"] == $id ) 
    {
        $isusers = true;
    }

    return $isusers;
}

function game_cp_15($id)
{
    connectdatadb();
    $cquery = mssql_query("SELECT \n\tB.Serial, B.Name, B.AccountSerial, B.Account, B.Class, B.Lv, G.TotalPlayMin\n\tFROM tbl_base AS B\n\tRIGHT JOIN\n\ttbl_general AS G\n\tON B.Serial = G.Serial\n\tWHERE B.Name=\"" . $id . "\"");
    for( $i = 0; $character = mssql_fetch_array($cquery); $i++ ) 
    {
        $charinfo["Serial"] = $character["Serial"];
        $charinfo["Name"] = $character["Name"];
        $charinfo["AccountSerial"] = $character["AccountSerial"];
        $charinfo["Account"] = $character["Account"];
        $charinfo["Race"] = getrace($character["Class"]);
        $charinfo["Level"] = $character["Lv"];
        $charinfo["TotalPlay"] = $character["TotalPlayMin"];
    }
    return $charinfo;
}

function gamecp_log($log_level, $log_account, $log_message, $disable_return = 0)
{
    global $userdb;
    global $gamecpdb;
    global $datadb;
    global $user2db;
    global $itemsdb;
    global $gamecp_dbconnect;
    global $data_dbconnect;
    global $user_dbconnect;
    global $items_dbconnect;
    $return_user = false;
    $return_gamecp = false;
    $return_data = false;
    $return_event = false;
    $return_user2 = false;
    $return_items = false;
    if( $disable_return == 0 ) 
    {
        if( $userdb == 1 ) 
        {
            $return_user = true;
            mssql_close($user_dbconnect);
        }

        if( $gamecpdb == 1 ) 
        {
            $return_gamecp = true;
            mssql_close($gamecp_dbconnect);
        }

        if( $datadb == 1 ) 
        {
            $return_data = true;
            mssql_close($data_dbconnect);
        }

        if( $itemsdb == 1 ) 
        {
            $return_items = true;
            mssql_close($items_dbconnect);
        }

    }

    $log_time = time();
    $log_ip = $_SERVER["REMOTE_ADDR"];
    $log_browser = antiject($_SERVER["HTTP_USER_AGENT"]);
    $log_page = antiject($_SERVER["REQUEST_URI"]);
    $log_account = ereg_replace("" . ";\$", "", $log_account);
    $log_account = ereg_replace("\\\\", "", $log_account);
    $log_message = ereg_replace("" . ";\$", "", $log_message);
    $log_message = ereg_replace("\\\\", "", $log_message);
    $log_message = htmlentities($log_message);
    if( $log_account != "" && $log_message != "" ) 
    {
        connectgamecpdb();
        $gamecploq_query = "" . "INSERT INTO gamecp_log (log_level, log_time, log_account, log_message, log_ip, log_page, log_browser) VALUES ('" . $log_level . "','" . $log_time . "','" . $log_account . "','" . $log_message . "','" . $log_ip . "','" . $log_page . "','" . $log_browser . "')";
        $gamecploq_query = mssql_query($gamecploq_query);
        mssql_close($gamecp_dbconnect);
        if( $return_user == true ) 
        {
            connectuserdb();
        }

        if( $return_data == true ) 
        {
            connectdatadb();
        }

        if( $return_event == true ) 
        {
            connecteventdb();
        }

        if( $return_gamecp == true ) 
        {
            connectgamecpdb();
        }

        if( $return_items == true ) 
        {
            connectitemsdb();
        }

    }

}

function get_date($time, $hrs = 0)
{
    if( $hrs == 0 ) 
    {
        $hrs = 1;
    }
    else
    {
        $hrs = 86400 * $hrs;
    }

    return date("Ymd", $time - $hrs);
}

function game_cp_43($i, $name, $desc, $time, $price)
{
    global $out;
    global $bgcolor;
    global $userdata;
    global $mescript;
    if( $userdata["points"] < $price ) 
    {
        $enable_disable = "disabled";
    }
    else
    {
        $enable_disable = "";
    }

    $out .= "<tr>" . "\n";
    $out .= "<form method=\"post\" action=\"" . $mescript . "?do=user_rented_items&amp;page=select\">" . "\n";
    $out .= "<input type=\"hidden\" name=\"item_id\" value=\"" . $i . "\">" . "\n";
    $out .= "<td class=\"" . $bgcolor . "\"><b>" . $name . "</b><br/>" . $desc . "</td>" . "\n";
    $out .= "<td class=\"" . $bgcolor . "\"><b>" . $time . "</b> Hours</td>" . "\n";
    $out .= "<td class=\"" . $bgcolor . "\" align=\"center\">" . $price . " GP</td>" . "\n";
    $out .= "<td class=\"" . $bgcolor . "\" align=\"center\"><input type=\"submit\" value=\"Select Item\" " . $enable_disable . "></td>" . "\n";
    $out .= "</form>" . "\n";
    $out .= "</tr>" . "\n";
}

function countdown($timestamp)
{
    global $return;
    $diff = $timestamp;
    if( $diff < 0 ) 
    {
        $diff = 0;
    }

    $dl = floor($diff / 60 / 60 / 24);
    $hl = floor(($diff - $dl * 60 * 60 * 24) / 60 / 60);
    $ml = floor(($diff - $dl * 60 * 60 * 24 - $hl * 60 * 60) / 60);
    $sl = floor($diff - $dl * 60 * 60 * 24 - $hl * 60 * 60 - $ml * 60);
    $return = array( $dl, $hl, $ml, $sl );
    return $return;
}

function write_log($username_charid, $username_oldchar, $username_newchar, $username_ip)
{
    global $mssql;
    global $gamecp_dbconnect;
    if( $username_charid != "" || $username_oldchar != "" || $username_newchar != "" || $username_ip != "" ) 
    {
        connectgamecpdb();
        $game_cp_44 = "" . "INSERT INTO gamecp_username_log (username_charid,username_oldname,username_newname,username_ip) VALUES ('" . $username_charid . "','" . $username_oldchar . "','" . $username_newchar . "','" . $username_ip . "')";
        $game_cp_44 = mssql_query($game_cp_44, $gamecp_dbconnect);
        return true;
    }

    return false;
}

function matheval($equation)
{
    $equation = preg_replace("/[^0-9+\\-.*\\/()%]/", "", $equation);
    $equation = preg_replace("/([+-])([0-9]+)(%)/", "*(1\$1.\$2)", $equation);
    $equation = preg_replace("/([0-9]+)(%)/", ".\$1", $equation);
    if( $equation == "" ) 
    {
        $return = 0;
    }
    else
    {
        eval("\$return=" . $equation . ";");
    }

    return $return;
}

function calculate_credits($mty = 1, $num_of_payments = 20, $price_value = 5, $credits_value = 25, $check_price = false)
{
    global $c_price;
    global $c_credits;
    global $c_bonus;
    global $c_total;
    global $config;
    $muntiplier = $mty;
    $c_price = array(  );
    $c_credits = array(  );
    $c_bonus = array(  );
    $c_total = array(  );
    for( $i = 0; $i < $num_of_payments; $i++ ) 
    {
        $raw_price = $price_value * $i;
        $raw_credits = $credits_value * $i * $muntiplier;
        $raw_bonus = round(($raw_credits * 1 + 10 * ($raw_credits - 25) / 25) - $raw_credits);
        if( isset($config["gamecp_bonus_formula"]) ) 
        {
            $formula = trim($config["gamecp_bonus_formula"]);
            if( !empty($formula) ) 
            {
                $formula = antiject($formula);
                $formula = str_replace("" . "\$", "", $formula);
                $formula = str_replace("x", $raw_credits, $formula);
                $formula = @matheval($formula);
                if( is_numeric($formula) && 0 <= $formula ) 
                {
                    $raw_bonus = $formula;
                }
                else
                {
                    $raw_bonus = 0;
                }

            }

        }

        $c_price[] = $raw_price;
        $c_credits[] = $raw_credits;
        $c_bonus[] = $raw_bonus;
        $c_total[] = $raw_credits + $raw_bonus;
    }
    if( $check_price === false ) 
    {
        return false;
    }

    $key = array_search($check_price, $c_price);
    if( $key === FALSE ) 
    {
        return 0;
    }

    return $c_total[$key];
}

function game_cp_9($price, $credits, $custom)
{
    global $config;
    $return = "\t\t\t<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"business\" value=\"" . $config["paypal_email"] . "\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"custom\" value=\"" . $custom . "\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"item_name\" value=\"" . $credits . " Web Credits\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"item_number\" value=\"" . $credits . " Web Credits\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"amount\" value=\"" . $price . "\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"no_shipping\" value=\"1\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"no_note\" value=\"1\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"currency_code\" value=\"" . (isset($config["paypal_currency"]) ? $config["paypal_currency"] : "USD") . "\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"notify_url\" value=\"" . $config["paypal_ipn_url"] . "\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"return\" value=\"" . $config["paypal_return_url"] . "\">" . "\n";
    $return .= "\t\t\t<input type=\"hidden\" name=\"cancel_return\" value=\"" . $config["paypal_cancel_url"] . "\">" . "\n";
    $return .= "\t\t\t<input type=\"submit\" border=\"0\" class=\"button\" name=\"submit\" value=\"Buy Now\">" . "\n";
    return $return;
}

function game_cp_1($reg_account, $reg_time, $reg_ip, $reg_browser)
{
    global $mssql;
    global $user_dbconnect;
    global $gamecp_dbconnect;
    global $data_dbconnect;
    global $event_dbconnect;
    global $user2_dbconnect;
    if( isset($user_dbconnect) ) 
    {
        $return_user = true;
    }

    if( $reg_account != "" || $reg_ip != "" || $reg_time != "" || $reg_browser != "" ) 
    {
        connectgamecpdb();
        $game_cp_44 = "" . "INSERT INTO gamecp_registration_log (reg_account,reg_ip,reg_time,reg_browser) VALUES ('" . $reg_account . "','" . $reg_ip . "','" . $reg_time . "','" . $reg_browser . "')";
        $game_cp_44 = mssql_query($game_cp_44);
        if( isset($return_user) ) 
        {
            connectuserdb();
        }

        return true;
    }

    return false;
}

function talic_name($talic_id)
{
    if( $talic_id == 15 ) 
    {
        $talic_name = "Ignorant";
    }
    else
    {
        if( $talic_id == 14 ) 
        {
            $talic_name = "Destruction";
        }
        else
        {
            if( $talic_id == 13 ) 
            {
                $talic_name = "Darkness";
            }
            else
            {
                if( $talic_id == 12 ) 
                {
                    $talic_name = "Chaos";
                }
                else
                {
                    if( $talic_id == 11 ) 
                    {
                        $talic_name = "Hatred";
                    }
                    else
                    {
                        if( $talic_id == 10 ) 
                        {
                            $talic_name = "Favor";
                        }
                        else
                        {
                            if( $talic_id == 9 ) 
                            {
                                $talic_name = "Wisdom";
                            }
                            else
                            {
                                if( $talic_id == 8 ) 
                                {
                                    $talic_name = "Sacred Flame";
                                }
                                else
                                {
                                    if( $talic_id == 7 ) 
                                    {
                                        $talic_name = "Belief";
                                    }
                                    else
                                    {
                                        if( $talic_id == 6 ) 
                                        {
                                            $talic_name = "Guard";
                                        }
                                        else
                                        {
                                            if( $talic_id == 5 ) 
                                            {
                                                $talic_name = "Glory";
                                            }
                                            else
                                            {
                                                if( $talic_id == 4 ) 
                                                {
                                                    $talic_name = "Grace";
                                                }
                                                else
                                                {
                                                    if( $talic_id == 3 ) 
                                                    {
                                                        $talic_name = "Mercy";
                                                    }
                                                    else
                                                    {
                                                        if( $talic_id == 2 ) 
                                                        {
                                                            $talic_name = "Rebirth";
                                                        }
                                                        else
                                                        {
                                                            if( $talic_id == 1 ) 
                                                            {
                                                                $talic_name = "No Talic";
                                                            }
                                                            else
                                                            {
                                                                $talic_name = 0;
                                                            }

                                                        }

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }

        }

    }

    return $talic_name;
}

function error_header()
{
    echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">" . "\n";
    echo "<HTML>" . "\n";
    echo "" . "\n";
    echo "<HEAD>" . "\n";
    echo "\t<style type=\"text/css\">" . "\n";
    echo "\t\tbody {" . "\n";
    echo "\t\t\tfont-size: 12px;" . "\n";
    echo "\t\t\tfont-family: Verdana, Arial, Helvetica, sans-serif;" . "\n";
    echo "\t\t\tbackground-color: #FFFFF;" . "\n";
    echo "\t\t}" . "\n";
    echo "\t</style>" . "\n";
    echo "</HEAD>" . "\n";
    echo "" . "\n";
    echo "<BODY>" . "\n";
}

function error_footer()
{
    echo "</BODY>" . "\n";
    echo "</HTML>" . "\n";
}

function errorHandler($errno, $errstr, $errfile, $errline)
{
    global $_SERVER;
    if( error_reporting() == 0 ) 
    {
        return NULL;
    }

    $errfile = str_replace("\\", "/", $errfile);
    $errfile = str_replace($_SERVER["DOCUMENT_ROOT"], "", $errfile);
    switch( $errno ) 
    {
        case E_ERROR:
        case E_USER_ERROR:
            error_header();
            if( $errstr == "(SQL)" ) 
            {
                echo "<b>[GameCP Debug] PHP SQL Error:</b> " . SQLMESSAGE . "<br />\n";
                echo "Query : " . SQLQUERY . "<br />\n";
                echo "On line " . SQLERRORLINE . " in file " . SQLERRORFILE . " ";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br /><br />" . "\n\n";
                echo "Aborting...<br />\n";
            }
            else
            {
                echo "" . "<b>[Game CP]</b> PHP Error: " . $errstr . "<br />\n";
                echo "" . "  Fatal error on line " . $errline . " in file " . $errfile;
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Aborting...<br /><br />" . "\n\n";
            }

            error_footer();
            exit( 1 );
        case E_WARNING:
        case E_USER_WARNING:
            error_header();
            echo "<b>[GameCP Debug] PHP Warning:</b> in file <b>" . $errfile . "</b> on line <b>" . $errline . "</b>: <b>" . $errstr . "</b><br /><br />" . "\n\n";
            error_footer();
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            error_header();
            echo "<b>[GameCP Debug] PHP Notice:</b> in file <b>" . $errfile . "</b> on line <b>" . $errline . "</b>: <b>" . $errstr . "</b><br /><br />" . "\n\n";
            error_footer();
            break;
        default:
            break;
    }
    return true;
}

function writeCache($content, $filename)
{
    global $out;
    global $_SERVER;
    $absolute_path = dirname(__FILE__) . "/";
    if( DIRECTORY_SEPARATOR == "\\" ) 
    {
        $absolute_path = str_replace("\\", "/", $absolute_path);
    }

    $absolute_path = str_replace("main/", "", $absolute_path);
    $fp = fopen($absolute_path . "cache/" . $filename, "w");
    fwrite($fp, $content);
    fclose($fp);
}

function readCache($filename, $expiry)
{
    global $out;
    global $out;
    global $_SERVER;
    $absolute_path = dirname(__FILE__) . "/";
    if( DIRECTORY_SEPARATOR == "\\" ) 
    {
        $absolute_path = str_replace("\\", "/", $absolute_path);
    }

    $absolute_path = str_replace("main/", "", $absolute_path);
    if( file_exists($absolute_path . "cache/" . $filename) ) 
    {
        if( filemtime($absolute_path . "cache/" . $filename) < time() - $expiry ) 
        {
            return false;
        }

        $cache = file($absolute_path . "cache/" . $filename);
        return implode("", $cache);
    }

    return false;
}

function sendheartbeat()
{
    global $out;
    global $_license_properties;
    global $_SERVER;
    $data = "server_path=" . $_SERVER["SCRIPT_NAME"] . "&server_ip=" . $_SERVER["SERVER_ADDR"] . "&server_domain=" . $_SERVER["SERVER_NAME"] . "&server_version=" . $_license_properties["version"];
    $host = "aarondm.com";
    $method = "GET";
    $path = "/gamecp_heartbeat.php";
    $useragent = true;
    $method = strtoupper($method);
    if( $method == "GET" ) 
    {
        $path .= "?" . $data;
    }

    if( function_exists("fsockopen") ) 
    {
        if( $filePointer = @fsockopen($host, 80, $errorNumber, $errorString, 10) ) 
        {
            $requestHeader = $method . " " . $path . "  HTTP/1.1\r\n";
            $requestHeader .= "Host: " . $host . "\r\n";
            $requestHeader .= "User-Agent:      Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0\r\n";
            $requestHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
            if( $method == "POST" ) 
            {
                $requestHeader .= "Content-Length: " . strlen($data) . "\r\n";
            }

            $requestHeader .= "Connection: close\r\n\r\n";
            if( $method == "POST" ) 
            {
                $requestHeader .= $data;
            }

            @fwrite($filePointer, $requestHeader);
            $responseHeader = "";
            $responseContent = "";
            do
            {
                $responseHeader .= @fread($filePointer, 1);
            }
            while( !preg_match("/\\r\\n\\r\\n\$/", $responseHeader) );
            if( !strstr($responseHeader, "Transfer-Encoding: chunked") ) 
            {
                while( !feof($filePointer) ) 
                {
                    $responseContent .= @fgets($filePointer, 128);
                }
            }
            else
            {
                while( $chunk_length = hexdec(@fgets($filePointer)) ) 
                {
                    $responseContentChunk = "";
                    $read_length = 0;
                    while( $read_length < $chunk_length ) 
                    {
                        $responseContentChunk .= @fread($filePointer, $chunk_length - $read_length);
                        $read_length = strlen($responseContentChunk);
                    }
                    $responseContent .= $responseContentChunk;
                    @fgets($filePointer);
                }
            }

            $responseContent = chop($responseContent);
            if( $responseContent != "failed" ) 
            {
                return $responseContent;
            }

            return false;
        }

        return false;
    }

    return false;
}

function hasPermissions($page = "")
{
    global $out;
    global $config;
    global $admin;
    global $userdata;
    global $isuser;
    global $user_access;
    $return = 0;
    if( $isuser && ($userdata != "" || $page == "") ) 
    {
        if( isset($userdata["username"]) && isset($userdata["serial"]) ) 
        {
            $super_admin = explode(",", $admin["super_admin"]);
            $page = strtolower($page);
            if( strpos($page, ".php") ) 
            {
                $page = substr($page, 0, strrpos($page, "."));
            }

            if( !in_array($userdata["username"], $super_admin) ) 
            {
                if( strpos($user_access["admin_permission"], $page) === FALSE ) 
                {
                    $return = 0;
                }
                else
                {
                    $return = 1;
                }

            }
            else
            {
                $return = 1;
            }

        }
        else
        {
            $return = 0;
        }

    }
    else
    {
        $return = 0;
    }

    if( $return == 1 ) 
    {
        return true;
    }

    return false;
}

function game_cp_2($map)
{
    if( $map == 0 ) 
    {
        $map = "Bell HQ";
    }
    else
    {
        if( $map == 1 ) 
        {
            $map = "Cora HQ";
        }
        else
        {
            if( $map == 2 ) 
            {
                $map = "Crag Mine";
            }
            else
            {
                if( $map == 3 ) 
                {
                    $map = "Acc HQ";
                }
                else
                {
                    if( $map == 4 ) 
                    {
                        $map = "Neutral BS1";
                    }
                    else
                    {
                        if( $map == 5 ) 
                        {
                            $map = "Neutral BS2";
                        }
                        else
                        {
                            if( $map == 6 ) 
                            {
                                $map = "Neutral CS1";
                            }
                            else
                            {
                                if( $map == 7 ) 
                                {
                                    $map = "Neutral CS2";
                                }
                                else
                                {
                                    if( $map == 8 ) 
                                    {
                                        $map = "Neutral AS1";
                                    }
                                    else
                                    {
                                        if( $map == 9 ) 
                                        {
                                            $map = "Neutral AS2";
                                        }
                                        else
                                        {
                                            if( $map == 10 ) 
                                            {
                                                $map = "Platform 01";
                                            }
                                            else
                                            {
                                                if( $map == 11 ) 
                                                {
                                                    $map = "Sette";
                                                }
                                                else
                                                {
                                                    if( $map == 12 ) 
                                                    {
                                                        $map = "Cauldron 01";
                                                    }
                                                    else
                                                    {
                                                        if( $map == 13 ) 
                                                        {
                                                            $map = "Elan";
                                                        }
                                                        else
                                                        {
                                                            if( $map == 14 ) 
                                                            {
                                                                $map = "Dungeon 00";
                                                            }
                                                            else
                                                            {
                                                                if( $map == 15 ) 
                                                                {
                                                                    $map = "Transport 01";
                                                                }
                                                                else
                                                                {
                                                                    if( $map == 16 ) 
                                                                    {
                                                                        $map = "Dungeon 01";
                                                                    }
                                                                    else
                                                                    {
                                                                        if( $map == 17 ) 
                                                                        {
                                                                            $map = "Acc GSD";
                                                                        }
                                                                        else
                                                                        {
                                                                            if( $map == 18 ) 
                                                                            {
                                                                                $map = "Bella GSD";
                                                                            }
                                                                            else
                                                                            {
                                                                                if( $map == 19 ) 
                                                                                {
                                                                                    $map = "Cora GSD";
                                                                                }
                                                                                else
                                                                                {
                                                                                    if( $map == 20 ) 
                                                                                    {
                                                                                        $map = "Acc GSP";
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        if( $map == 21 ) 
                                                                                        {
                                                                                            $map = "Bella GSP";
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            if( $map == 22 ) 
                                                                                            {
                                                                                                $map = "Cora GSP";
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                if( $map == 23 ) 
                                                                                                {
                                                                                                    $map = "Dungeon 02";
                                                                                                }
                                                                                                else
                                                                                                {
                                                                                                    if( $map == 24 ) 
                                                                                                    {
                                                                                                        $map = "Exile Land";
                                                                                                    }
                                                                                                    else
                                                                                                    {
                                                                                                        if( $map == 25 ) 
                                                                                                        {
                                                                                                            $map = "Beasts Mountain";
                                                                                                        }
                                                                                                        else
                                                                                                        {
                                                                                                            if( $map == 26 ) 
                                                                                                            {
                                                                                                                $map = "Medical Lab";
                                                                                                            }
                                                                                                            else
                                                                                                            {
                                                                                                                if( $map == 27 ) 
                                                                                                                {
                                                                                                                    $map = "Elven";
                                                                                                                }
                                                                                                                else
                                                                                                                {
                                                                                                                    if( $map == 28 ) 
                                                                                                                    {
                                                                                                                        $map = "Dungeon 03";
                                                                                                                    }
                                                                                                                    else
                                                                                                                    {
                                                                                                                        $map = "Unknown";
                                                                                                                    }

                                                                                                                }

                                                                                                            }

                                                                                                        }

                                                                                                    }

                                                                                                }

                                                                                            }

                                                                                        }

                                                                                    }

                                                                                }

                                                                            }

                                                                        }

                                                                    }

                                                                }

                                                            }

                                                        }

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }

        }

    }

    return $map;
}

function multiarray_keys($ar)
{
    foreach( $ar as $k => $v ) 
    {
        $keys[] = $k;
        if( is_array($ar[$k]) ) 
        {
            $keys = array_merge($keys, multiarray_keys($ar[$k]));
        }

    }
    return $keys;
}

function game_cp_33($haystack, $needle, $before_needle = FALSE)
{
    if( ($pos = strpos($haystack, $needle)) === FALSE ) 
    {
        return FALSE;
    }

    if( $before_needle ) 
    {
        return substr($haystack, 0, $pos + strlen($needle));
    }

    return substr($haystack, $pos);
}

function gamecp_nav($isuser = false)
{
    global $is_superadmin;
    global $user_access;
    global $admin;
    global $userdata;
    global $_license_properties;
    global $out;
    global $gamecp_nav;
    global $script_name;
    global $program_name;
    global $dont_allow;
    global $admin;
    global $config;
    $gamecp_nav = "";
    $gamecp_nav = "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"125\">" . "\n";
    $gamecp_nav .= "<tr>" . "\n";
    $gamecp_nav .= "\t<td class=\"tcat\" style=\"padding: 6px;\"><a href=\"" . $script_name . "\">" . $program_name . "</a></td>" . "\n";
    $gamecp_nav .= "</tr>" . "\n";
    if( $isuser == true ) 
    {
        $gamecp_nav .= "<tr>" . "\n";
        $gamecp_nav .= "\t<td nowrap=\"nowrap\" class=\"alt2\"><a class=\"smallfont\" href=\"./gamecp_logout.php\">Logout</a></td>" . "\n";
        $gamecp_nav .= "</tr>" . "\n";
    }
    else
    {
        $gamecp_nav .= "<tr>" . "\n";
        $gamecp_nav .= "\t<td nowrap=\"nowrap\" class=\"alt2\"><a class=\"smallfont\" href=\"./" . $script_name . "\">Login</a></td>" . "\n";
        $gamecp_nav .= "</tr>" . "\n";
        $gamecp_nav .= "<tr>" . "\n";
        $gamecp_nav .= "\t<td nowrap=\"nowrap\" class=\"alt2\"><a class=\"smallfont\" href=\"./gamecp_register.php\">Register</a></td>" . "\n";
        $gamecp_nav .= "</tr>" . "\n";
    }

    $phpEx = "php";
    if( !($fileinfo = readcache("fileinfo.cache", 86400)) ) 
    {
        $fileinfo = "";
        $dir = @opendir("./includes/");
        $setmodules = 1;
        while( $file = @readdir($dir) ) 
        {
            if( is_file("./includes/" . $file) && ($file != "main" || $file != "." || $file != "..") && !in_array($file, $dont_allow) ) 
            {
                $this_script = "";
                include("./includes/" . $file);
                if( isset($module) ) 
                {
                    $keys = multiarray_keys($module);
                    $fileinfo .= $file . "," . $keys[0] . "," . $keys[1] . "\n";
                }

                unset($module);
                unset($keys);
            }

        }
        @closedir($dir);
        unset($setmodules);
        writecache($fileinfo, "fileinfo.cache");
    }

    $fileinfo = explode("\n", $fileinfo);
    $filelist = "";
    $module = array(  );
    foreach( $fileinfo as $moduleinfo ) 
    {
        $info = explode(",", $moduleinfo);
        $filelist = $info[0] . ",";
        if( !empty($info[1]) && !empty($info[2]) ) 
        {
            if( preg_match("/^superadmin_.*?\\." . $phpEx . "" . "\$/", $info[0]) ) 
            {
                if( $isuser == true && $is_superadmin == true ) 
                {
                    $module[$info[1]][$info[2]] = $info[0];
                }

            }
            else
            {
                if( preg_match("/^admin_.*?\\." . $phpEx . "" . "\$/", $info[0]) || preg_match("/^support_.*?\\." . $phpEx . "" . "\$/", $info[0]) ) 
                {
                    if( $is_superadmin == true ) 
                    {
                        $module[$info[1]][$info[2]] = $info[0];
                    }
                    else
                    {
                        if( $isuser == true && $user_access != false ) 
                        {
                            $match = str_replace(".php", "", $info[0]);
                            if( strpos($user_access["admin_permission"], $match) !== FALSE ) 
                            {
                                $module[$info[1]][$info[2]] = $info[0];
                            }

                        }

                    }

                }
                else
                {
                    if( preg_match("/^user_.*?\\." . $phpEx . "" . "\$/", $info[0]) ) 
                    {
                        if( $isuser == true ) 
                        {
                            $module[$info[1]][$info[2]] = $info[0];
                        }

                    }
                    else
                    {
                        if( preg_match("/^all_.*?\\." . $phpEx . "" . "\$/", $info[0]) ) 
                        {
                            $module[$info[1]][$info[2]] = $info[0];
                        }

                    }

                }

            }

        }

    }
    ksort($module);
    for( $k = 1; list($cat, $action_array) = each($module); $k++ ) 
    {
        $cat = preg_replace("/_/", " ", $cat);
        ksort($action_array);
        $gamecp_nav .= "<tr>" . "\n";
        $gamecp_nav .= "\t<td class=\"thead\" width=\"100%\" valign=\"top\">" . $cat . "</td>" . "\n";
        $gamecp_nav .= "</tr>" . "\n";
        while( list($action, $file) = each($action_array) ) 
        {
            $action = preg_replace("/_/", " ", $action);
            $gamecp_nav .= "<tr>" . "\n";
            $gamecp_nav .= "\t<td nowrap=\"nowrap\" class=\"alt2\"><a class=\"smallfont\" href=\"./" . $script_name . "?do=" . substr($file, 0, 0 - 4) . "\">" . $action . "</a></td>" . "\n";
            $gamecp_nav .= "</tr>" . "\n";
        }
    }
    $gamecp_nav .= "</table>";
    $out .= "\t\t\t\t</div>" . "\n";
    $out .= "\t\t\t\t</td>" . "\n";
    $out .= "\t\t\t</tr>" . "\n";
    $out .= "\t\t</table>" . "\n";
    $out .= "<table align=\"center\">" . "\n";
    $out .= "<tr>" . "\n";
    $out .= "<td>" . "\n";
    $out .= "<div>" . "\n";
    $year_next = date("Y") + 2;
    $_license_properties["version"] = substr(str_replace(".", "", $_license_properties["version"]), 1);
    $_license_properties["version"] = str_split($_license_properties["version"], 2);
    $_license_properties["version"] = implode(".", $_license_properties["version"]);
    $a = array( "/^0(\\d+)/", "/\\.0(\\d+)/" );
    $b = array( "\\1", ".\\1" );
    $_license_properties["version"] = preg_replace($a, $b, $_license_properties["version"]);
    $license_info = ioncube_file_info();
    $license_expire_time = $license_info["FILE_EXPIRY"];
    if( isset($config["security_show_version"]) && $config["security_show_version"] == "1" ) 
    {
        $version_text = " v" . $_license_properties["version"];
    }
    else
    {
        $version_text = "";
    }

    $out .= "<p class=\"smallfont\" style=\"text-align: center; font-weight: bold;\">RF Online Game CP" . $version_text . "<br/>\n\tCopyright &copy;2008 - " . $year_next . ", <a href=\"http://www.aarondm.com/\" target=\"_blank\" title=\"Aaron DM\" alt=\"Aaron DM\">www.AaronDM.com</a></p>" . "\n";
    if( $license_expire_time <= time() + 604800 && $is_superadmin ) 
    {
        $extime = game_cp_21($license_expire_time - time());
        $out .= "<p class=\"smallfont\" style=\"text-align: center; font-weight: bold; color: red; font-size: 15px;\">[WARNING] GAME CP LICENSE EXPIRES IN: " . $extime["hours"] . " hours " . $extime["minutes"] . " minutes " . $extime["seconds"] . " seconds</p>";
    }

    return true;
}

function game_cp_21($seconds)
{
    $time["hours"] = floor($seconds / 3600);
    $seconds -= $time["hours"] * 3600;
    $time["minutes"] = floor($seconds / 60);
    $time["seconds"] = $seconds - $time["minutes"] * 60;
    $time["hours"] = sprintf("%02d", $time["hours"]);
    $time["minutes"] = sprintf("%02d", $time["minutes"]);
    $time["seconds"] = sprintf("%02d", $time["seconds"]);
    return $time;
}

function generate_menu($menu_array, $parent, $options = "", $prepend = array(  ), $item_cat_id = 0)
{
    global $options;
    $prepend[0] = "";
    foreach( $menu_array as $key => $value ) 
    {
        if( $value["parent"] == $parent ) 
        {
            $prepend[$value["id"]] = $prepend[$parent] . "&nbsp;&nbsp;";
            if( $parent == 0 ) 
            {
                $add_bg = "style=\"background-color: #E7E7E7; font-weight: bold;\"";
            }
            else
            {
                $add_bg = "style=\"background-color: #EBEBEB;\"";
            }

            if( $value["id"] == $item_cat_id ) 
            {
                $selected = " selected=\"selected\"";
            }
            else
            {
                $selected = "";
            }

            $options .= "<option value=\"" . $value["id"] . "\"" . $add_bg . $selected . ">" . $prepend[$parent] . $value["name"] . "</option>";
            generate_menu($menu_array, $key, $options, $prepend, $item_cat_id);
        }

    }
}

function print_outputs($data)
{
    unset($mssql);
    echo stripslashes($data);
}

function gamecp_template($template_name)
{
    global $config;
    global $is_superadmin;
    if( $template_name == "" ) 
    {
        return false;
    }

    $contents = "";
    $header = "./includes/templates/header.html";
    $footer = "./includes/templates/footer.html";
    $filename = "./includes/templates/" . $template_name . ".html";
    if( file_exists($filename) ) 
    {
        if( file_exists($header) ) 
        {
            $handle = fopen($header, "rb");
            $contents .= fread($handle, filesize($header)) . "\n";
            fclose($handle);
            $handle = fopen($filename, "rb");
            $contents .= fread($handle, filesize($filename)) . "\n";
            fclose($handle);
            if( file_exists($footer) ) 
            {
                $handle = fopen($footer, "rb");
                $contents .= fread($handle, filesize($footer));
                fclose($handle);
                return addslashes($contents);
            }

            return "Unable to load the footer.html file";
        }

        return "Unable to load the header.html file";
    }

    return "Invalid load of template";
}

function game_cp_34($table_name, $database)
{
    connectuserdb();
    $sql = "SELECT last_user_update\n\t\t\tFROM sys.dm_db_index_usage_stats\n\t\t\tWHERE database_id = DB_ID('" . antiject($database) . "') AND OBJECT_ID=OBJECT_ID('" . antiject($table_name) . "')";
    if( !($game_cp_35 = mssql_query($sql)) ) 
    {
        $return = false;
        exit( "Unable to select last update time!" );
    }

    if( $game_cp_36 = mssql_fetch_row($game_cp_35) ) 
    {
        $return = strtotime(preg_replace("/:[0-9][0-9][0-9]/", "", $game_cp_36[0]));
    }
    else
    {
        $return = false;
    }

    mssql_free_result($game_cp_35);
    return $return;
}

function game_cp_37($last_modified, $identifier)
{
    global $_SERVER;
    $_SERVER["HTTP_IF_MODIFIED_SINCE"] = getenv("HTTP_IF_MODIFIED_SINCE") != "" ? getenv("HTTP_IF_MODIFIED_SINCE") : false;
    $_SERVER["HTTP_IF_NONE_MATCH"] = getenv("HTTP_IF_NONE_MATCH") != "" ? getenv("HTTP_IF_NONE_MATCH") : false;
    $etag = "\"" . md5($last_modified . $identifier) . "\"";
    $client_etag = $_SERVER["HTTP_IF_NONE_MATCH"] ? trim($_SERVER["HTTP_IF_NONE_MATCH"]) : false;
    $game_cp_38 = $_SERVER["HTTP_IF_MODIFIED_SINCE"] ? trim($_SERVER["HTTP_IF_MODIFIED_SINCE"]) : false;
    $client_last_modified = date("D, d M Y H:i:s \\G\\M\\T", strtotime($game_cp_38));
    $game_cp_39 = true;
    if( !$client_last_modified || !$client_etag ) 
    {
        $game_cp_39 = false;
    }

    if( $game_cp_39 && $last_modified < $client_last_modified ) 
    {
        $game_cp_39 = false;
    }

    if( $game_cp_39 && $client_etag != $etag ) 
    {
        $game_cp_39 = false;
    }

    header("Cache-Control:public, must-revalidate", true);
    header("Pragma:cache", true);
    header("ETag: " . $etag);
    if( $game_cp_39 ) 
    {
        header("HTTP/1.0 304 Not Modified");
        exit();
    }

    header("Last-Modified:" . date("D, d M Y H:i:s \\G\\M\\T", $last_modified));
}

function fetch_remote_file($url, $post_data = array(  ))
{
    $post_body = "";
    if( !empty($post_data) ) 
    {
        foreach( $post_data as $key => $val ) 
        {
            $post_body .= "&" . urlencode($key) . "=" . urlencode($val);
        }
        $post_body = ltrim($post_body, "&");
    }

    if( function_exists("curl_init") ) 
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if( !empty($post_body) ) 
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
        }

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    if( function_exists("fsockopen") ) 
    {
        $url = @parse_url($url);
        if( !$url["host"] ) 
        {
            return false;
        }

        if( !$url["port"] ) 
        {
            $url["port"] = 80;
        }

        if( !$url["path"] ) 
        {
            $url["path"] = "/";
        }

        if( $url["query"] ) 
        {
            $url["path"] .= "" . "?" . $url["query"];
        }

        $fp = @fsockopen($url["host"], $url["port"], $error_no, $error, 10);
        @stream_set_timeout($fp, 10);
        if( !$fp ) 
        {
            return false;
        }

        $headers = array(  );
        if( !empty($post_body) ) 
        {
            $headers[] = "" . "POST " . $url["path"] . " HTTP/1.0";
            $headers[] = "Content-Length: " . strlen($post_body);
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
        }
        else
        {
            $headers[] = "" . "GET " . $url["path"] . " HTTP/1.0";
        }

        $headers[] = "" . "Host: " . $url["host"];
        $headers[] = "Connection: Close";
        $headers[] = "\r\n";
        if( !empty($post_body) ) 
        {
            $headers[] = $post_body;
        }

        $headers = implode("\r\n", $headers);
        if( !@fwrite($fp, $headers) ) 
        {
            return false;
        }

        while( !feof($fp) ) 
        {
            $data .= fgets($fp, 12800);
        }
        fclose($fp);
        $data = explode("\r\n\r\n", $data, 2);
        return $data[1];
    }

    if( empty($post_data) ) 
    {
        return @implode("", @file($url));
    }

    return false;
}


