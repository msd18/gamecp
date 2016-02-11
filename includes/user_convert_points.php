<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Donations"]["Convert Points"] = $file;
}
else
{
    $lefttitle = "Convert Game Points";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( $isuser == true ) 
        {
            $page = isset($_POST["page"]) ? $_POST["page"] : "";
            $exit_1 = false;
            $exit_buy = false;
            $currency = "";
            if( !isset($config["gamecp_money_conversion_rate"]) && !isset($config["gamecp_gold_conversion_rate"]) ) 
            {
                $out .= "Configuration values cannot be found, unable to enable this script.";
                return 0;
            }

            $money_exchange_rate = ceil($config["gamecp_money_conversion_rate"]);
            $gold_exchange_rate = ceil($config["gamecp_gold_conversion_rate"]);
            if( $money_exchange_rate == 0 && $gold_exchange_rate == 0 ) 
            {
                $out .= "<p style=\"text-align: center; font-weight: bold;\">This feature has been disabled by the admins</p>";
                return 0;
            }

            if( empty($page) ) 
            {
                $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">" . number_format($userdata["points"], 2) . "</span> Game Points</p>" . "\n";
                connectdatadb();
                $user_sql = "SELECT Name, Serial, Race, Dalant, Gold FROM tbl_base WHERE DCK = 0 AND AccountSerial = '" . $userdata["serial"] . "'";
                if( !($user_result = @mssql_query($user_sql)) ) 
                {
                    $exit_1 = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to get your characters data</p>" . "\n";
                    if( $is_superadmin == true ) 
                    {
                        $out .= "<p>" . "\n";
                        $out .= "SQL: " . $user_sql . "<br/>" . "\n";
                        $out .= "MSSQL ERROR: " . mssql_get_last_message() . "\n";
                        $out .= "" . "\n";
                        $out .= "</p>" . "\n";
                    }

                }

                $out .= "<div class=\"panel\">" . "\n";
                $out .= "<p><b>Money exchange rate:</b>  1 Game Point for " . number_format($money_exchange_rate) . " in-game Money<br/><b>Gold exchange rate:</b> 1 Game Point for " . number_format($gold_exchange_rate) . " in-game Gold</p>" . "\n";
                for( $i = 1; $user = @mssql_fetch_array($user_result); $i++ ) 
                {
                    $max_money = floor(2000000000 - $user["Dalant"]);
                    $max_gold = floor(500000 - $user["Gold"]);
                    if( $user["Race"] == 0 || $user["Race"] == 1 ) 
                    {
                        $currency = "Dalant";
                    }
                    else
                    {
                        if( $user["Race"] == 2 || $user["Race"] == 3 ) 
                        {
                            $currency = "Disena";
                        }
                        else
                        {
                            if( $user["Race"] == 4 ) 
                            {
                                $currency = "CP";
                            }
                            else
                            {
                                $currency = "Unknown!";
                            }

                        }

                    }

                    if( $i != 1 ) 
                    {
                        $out .= "<br/>" . "\n";
                    }

                    $keyup_money = "" . "convert('" . $i . "'," . $money_exchange_rate . "," . $max_money . "," . $user["Dalant"] . "," . $userdata["points"] . "" . ",'" . $currency . "');";
                    $keyup_gold = "" . "convert('1" . $i . "'," . $gold_exchange_rate . "," . $max_gold . "," . $user["Gold"] . "," . $userdata["points"] . ",'Gold');";
                    $out .= "<form method=\"post\">" . "\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" valign=\"top\">Character Name:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" colspan=\"2\">" . antiject($user["Name"]) . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" valign=\"top\">Exchange Game Points for " . $currency . ":</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\"><input id=\"exchange_" . $i . "\" type=\"text\" name=\"exchange_money\" value=\"\" onKeyUp=\"" . $keyup_money . "\" size=\"4\"></td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" id=\"result_" . $i . "\">Current: " . number_format($user["Dalant"]) . " " . $currency . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" valign=\"top\">Exchange Game Points for Gold:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\"><input id=\"exchange_1" . $i . "\" type=\"text\" name=\"exchange_gold\" value=\"\" onKeyUp=\"" . $keyup_gold . "\" size=\"4\"></td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" id=\"result_1" . $i . "\">Current: " . number_format($user["Gold"]) . " Gold</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" valign=\"top\" colspan=\"3\"><input type=\"hidden\" name=\"page\" value=\"buy\"/><input type=\"hidden\" name=\"char_serial\" value=\"" . $user["Serial"] . "\"/><input type=\"submit\" name=\"submit\" value=\"Buy Now!\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "</table>" . "\n";
                    $out .= "</form>" . "\n";
                }
                $out .= "</div>" . "\n";
                if( mssql_num_rows($user_result) <= 0 ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have any characters</p>" . "\n";
                }

                mssql_free_result($user_result);
                return 1;
            }

            if( $page == "buy" ) 
            {
                $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">" . number_format($userdata["points"], 2) . "</span> Game Points</p>" . "\n";
                $char_serial = isset($_POST["char_serial"]) && is_int((int) $_POST["char_serial"]) ? antiject((int) $_POST["char_serial"]) : 0;
                $exchange_money = isset($_POST["exchange_money"]) && is_int((int) $_POST["exchange_money"]) ? antiject((int) $_POST["exchange_money"]) : 0;
                $exchange_gold = isset($_POST["exchange_gold"]) && is_int((int) $_POST["exchange_gold"]) ? antiject((int) $_POST["exchange_gold"]) : 0;
                $t_login = strtotime($userdata["lastlogintime"]);
                $t_logout = strtotime($userdata["lastlogofftime"]);
                $t_cur = time();
                $t_maxlogin = $t_login + 3600;
                if( $t_login <= $t_logout ) 
                {
                    $status = "offline";
                }
                else
                {
                    $status = "online";
                }

                if( $status == "online" ) 
                {
                    $exit_buy = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot buy items when logged into the game!<br/>If you have logged out and yet see this message, log back in and properly log out again (click the log out button!).</p>" . "\n";
                }

                if( $char_serial == 0 ) 
                {
                    $exit_buy = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">This should not happen, no character serial was provided</p>" . "\n";
                }

                if( $exchange_money == 0 && $exchange_gold == 0 ) 
                {
                    $exit_buy = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You must fill in an amount for the exchange.</p>" . "\n";
                }

                if( $exchange_money < 0 || $exchange_gold < 0 ) 
                {
                    $exit_buy = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You must exchange points greather than 1</p>";
                }

                if( 2000000000 < floor($exchange_money * $money_exchange_rate) ) 
                {
                    $exit_buy = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot buy money greater than 2,000,000,000</p>";
                }

                if( 500000 < floor($exchange_gold * $gold_exchange_rate) ) 
                {
                    $exit_buy = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot buy gold greater than 500,000</p>";
                }

                if( $userdata["points"] < $exchange_money || $userdata["points"] < $exchange_gold || $userdata["points"] < $exchange_money + $exchange_gold ) 
                {
                    $exit_buy = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have enough game points to make this exchange (" . number_format($exchange_money + $exchange_gold) . ")</p>";
                }

                $money = floor($exchange_money * $money_exchange_rate);
                $gold = floor($exchange_gold * $gold_exchange_rate);
                if( $exit_buy == false ) 
                {
                    connectdatadb();
                    $char_sql = "SELECT Name, Serial, Race, Dalant, Gold FROM tbl_base WHERE Serial = '" . $char_serial . "'";
                    if( !($char_result = @mssql_query($char_sql)) ) 
                    {
                        $exit_buy = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to get character data</p>";
                        $out .= "<p><b>Debug:</b><br/><i>SQL: </i>" . $char_sql . "<br/><i>SQL Return:</i> " . mssql_get_last_message() . "</p>";
                    }

                    $char = @mssql_fetch_array($char_result);
                    if( 2000000000 < $char["Dalant"] + $money ) 
                    {
                        $exit_buy = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot exchange for money over 2,000,000,000</p>";
                    }

                    if( 500000 < $char["Gold"] + $gold ) 
                    {
                        $exit_buy = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot exchange for gold over 500,000 " . $gold . "</p>";
                    }

                }

                mssql_free_result($char_result);
                if( $exit_buy == false ) 
                {
                    $update_char = "" . "UPDATE tbl_base SET Dalant = Dalant+" . $money . ", Gold = Gold+" . $gold . " WHERE Serial = '" . $char_serial . "'";
                    if( !($update_result = @mssql_query($update_char)) ) 
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to update your character</p>";
                        $out .= "<p><b>Debug:</b><br/><i>SQL: </i>" . $update_char . "<br/><i>SQL Return:</i> " . mssql_get_last_message() . "</p>";
                        return 1;
                    }

                    $delete_npc = "" . "DELETE FROM tbl_NpcData WHERE Serial = '" . $char_serial . "'";
                    $result2 = mssql_query($delete_npc) or connectgamecpdb();
                    $subtract = $exchange_money + $exchange_gold;
                    $update_points = "" . "UPDATE gamecp_gamepoints SET user_points = user_points-" . $subtract . " WHERE user_account_id = '" . $userdata["serial"] . "'";
                    if( !($update_p_result = @mssql_query($update_points)) ) 
                    {
                        gamecp_log(1, $userdata["username"], "" . "GAMECP - CONVERT POINTS - Failed to update Game Points: -" . $subtract, 1);
                    }

                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully exchanged: <u>" . number_format($exchange_money, 2, ".", "") . "</u> points into <u>" . number_format($money) . "</u> money and <u>" . number_format($exchange_gold, 2, ".", "") . "</u> into <u>" . number_format($gold) . "</u> gold</p>";
                    gamecp_log(1, $userdata["username"], "" . "GAMECP - CONVERT POINTS - Char Serial: " . $char_serial . " | Exchanged: " . number_format($exchange_money, 2, ".", "") . " (GP) -> +" . number_format($money) . " (M) & " . number_format($exchange_gold, 2, ".", "") . " (GP) -> +" . number_format($gold) . " (G)", 1);
                    return 1;
                }

            }
            else
            {
                $out .= $lang["invalid_page_id"];
                return 1;
            }

        }
        else
        {
            $out .= $lang["no_permission"];
            return 1;
        }

    }
    else
    {
        $out .= $lang["invalid_page_load"];
    }

}


