<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Account"]["Account Info"] = $file;
}
else
{
    $lefttitle = "Viewing " . $userdata["username"] . "'s Account Information and Characters";
    if( $this_script == $script_name ) 
    {
        if( $isuser ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $equipment_names = array( "Upper", "Lower", "Gloves", "Shoes", "Head", "Shield", "Weapon", "Cloak" );
            if( !isset($config["specialgp_gender"]) ) 
            {
                $config["specialgp_gender"] = 1000000;
            }

            if( $page == "" ) 
            {
                $out .= "<p>You must be logged out in order to make any changes (gender, delete, and etc) to your characters</p>" . "\n";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Character Name</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\" nowrap>Race</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Level</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Class</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>\"Time Well Wasted\"</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" colspan=\"2\" nowrap>Options</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                connectdatadb();
                $char_query = "SELECT TOP 3 B.Serial,B.Name,B.Lv,B.Class,B.Race,B.Dalant,B.Gold,B.EK0,B.EK1,B.EK2,B.EK3,B.EK4,B.EK5,B.EK6,B.EK7,B.EU0,B.EU1,B.EU2,B.EU3,B.EU4,B.EU5,B.EU6,B.EU7,G.PvpPoint,G.TotalPlayMin\n\t\t\tFROM tbl_base AS B\n\t\t\tINNER JOIN tbl_general AS G\n\t\t\tON B.Serial = G.Serial\n\t\t\tWHERE B.DCK = 0 AND B.AccountSerial = '" . $userdata["serial"] . "'";
                if( !($char_result = mssql_query($char_query)) ) 
                {
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td colspan=\"7\" class=\"alt2\" style=\"text-align: center; font-width: bold;\">Unable to query the character database</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }

                connectitemsdb();
                while( $char = mssql_fetch_array($char_result) ) 
                {
                    $class_info_result = @mssql_query("SELECT class_name FROM tbl_Classes WHERE class_code = '" . $char["Class"] . "'", $items_dbconnect) or exit( "Error! Looks like you did not fill up your Items DB. Can't find the classes" );
                    $class_info = mssql_fetch_array($class_info_result);
                    $change_gender = " (<a href=\"" . $script_name . "?do=" . $_GET["do"] . "&page=change_gender&serial=" . $char["Serial"] . "\">";
                    if( $char["Race"] == 0 ) 
                    {
                        $gender = "Male";
                        $race = "<span style=\"color: #CC6699;\">Bell</span>";
                        $change_gender .= "Change to Female - " . $config["specialgp_gender"] . " GP</a>)";
                    }
                    else
                    {
                        if( $char["Race"] == 1 ) 
                        {
                            $gender = "Female";
                            $race = "<span style=\"color: #CC6699;\">Bell</span>";
                            $change_gender .= "Change to Male - " . $config["specialgp_gender"] . " GP</a>)";
                        }
                        else
                        {
                            if( $char["Race"] == 2 ) 
                            {
                                $gender = "Male";
                                $race = "<span style=\"color: #9933CC;\">Cora</span>";
                                $change_gender .= "Change to Female - " . $config["specialgp_gender"] . " GP</a>)";
                            }
                            else
                            {
                                if( $char["Race"] == 3 ) 
                                {
                                    $gender = "Female";
                                    $race = "<span style=\"color: #9933CC;\">Cora</span>";
                                    $change_gender .= "Change to Male - " . $config["specialgp_gender"] . " GP</a>)";
                                }
                                else
                                {
                                    if( $char["Race"] == 4 ) 
                                    {
                                        $gender = "Robot";
                                        $race = "<span style=\"color: grey;\">Acc</span>";
                                        $change_gender = "";
                                    }

                                }

                            }

                        }

                    }

                    if( $userdata["points"] < $config["specialgp_gender"] ) 
                    {
                        $change_gender = "";
                    }

                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"font-weight: bold;\" nowrap>" . $char["Name"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"text-align: center;\" nowrap>" . $race . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" nowrap>" . $char["Lv"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" nowrap>" . $class_info["class_name"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" nowrap>" . number_format(round($char["TotalPlayMin"] / 60)) . " Hours</td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"text-align: center;\" nowrap><a href=\"javascript:toggle_extra('" . $char["Serial"] . "')\">View More</a></td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"text-align: center;\" nowrap><a href=\"" . $script_name . "?do=" . $_GET["do"] . "&page=delete&serial=" . $char["Serial"] . "\">Delete</a></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr class=\"extra_" . $char["Serial"] . "\" style=\"display: none;\">" . "\n";
                    $out .= "\t\t<td class=\"alt1\">Gender</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" colspan=\"6\">" . $gender . $change_gender . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr class=\"extra_" . $char["Serial"] . "\" style=\"display: none;\">" . "\n";
                    $out .= "\t\t<td class=\"alt1\">PVP Points</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" colspan=\"6\">" . number_format(round($char["PvpPoint"])) . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr class=\"extra_" . $char["Serial"] . "\" style=\"display: none;\">" . "\n";
                    $out .= "\t\t<td class=\"alt1\">Dalant</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" colspan=\"6\">" . number_format($char["Dalant"]) . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr class=\"extra_" . $char["Serial"] . "\" style=\"display: none;\">" . "\n";
                    $out .= "\t\t<td class=\"alt1\">Gold</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" colspan=\"6\">" . number_format($char["Gold"]) . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    for( $i = 0; $i < 8; $i++ ) 
                    {
                        $k_value = antiject($char["" . "EK" . $i]);
                        $u_value = antiject($char["" . "EU" . $i]);
                        if( "-1" < $k_value ) 
                        {
                            if( $i == 0 ) 
                            {
                                $item_kind = tbl_code_upper;
                            }
                            else
                            {
                                if( $i == 1 ) 
                                {
                                    $item_kind = tbl_code_lower;
                                }
                                else
                                {
                                    if( $i == 2 ) 
                                    {
                                        $item_kind = tbl_code_gauntlet;
                                    }
                                    else
                                    {
                                        if( $i == 3 ) 
                                        {
                                            $item_kind = tbl_code_shoe;
                                        }
                                        else
                                        {
                                            if( $i == 4 ) 
                                            {
                                                $item_kind = tbl_code_helmet;
                                            }
                                            else
                                            {
                                                if( $i == 5 ) 
                                                {
                                                    $item_kind = tbl_code_shield;
                                                }
                                                else
                                                {
                                                    if( $i == 6 ) 
                                                    {
                                                        $item_kind = tbl_code_weapon;
                                                    }
                                                    else
                                                    {
                                                        if( $i == 7 ) 
                                                        {
                                                            $item_kind = tbl_code_cloak;
                                                        }
                                                        else
                                                        {
                                                            $item_kind = tbl_code_helmet;
                                                        }

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }

                            }

                            $table_name = getitemtablename($item_kind);
                            $items_query = @mssql_query("SELECT item_code, item_id, item_name FROM " . $table_name . "" . " WHERE item_id = '" . $k_value . "'", $items_dbconnect) or exit( "Error! Looks like you did not fill up your Items DB." );
                            $items = mssql_fetch_array($items_query);
                            if( $items["item_name"] != "" ) 
                            {
                                $item_name = str_replace("_", " ", $items["item_name"]);
                            }
                            else
                            {
                                $item_name = "Not found in DB - " . $k_value . ":" . $item_kind;
                            }

                            $item_code = $items["item_code"];
                            @mssql_free_result($items_query);
                        }
                        else
                        {
                            $item_name = "<i>No item equipped</i>";
                            $item_code = "-";
                        }

                        $base_code = 268435455;
                        $ux_value = $u_value;
                        $item_slots = $u_value;
                        $item_slots = $item_slots - $base_code;
                        $item_slots = $item_slots / ($base_code + 1);
                        $upgrades = "";
                        $ceil_slots = ceil($item_slots);
                        $slots_code = $base_code + ($base_code + 1) * $ceil_slots;
                        $slots = $ceil_slots;
                        $km_allorArray = array( 0, 1, 2, 3, 4, 5, 6, 7 );
                        if( 0 < $ceil_slots && "-1" < $k_value && in_array($item_kind, $km_allorArray) ) 
                        {
                            $u_value = dechex($u_value);
                            $item_ups = $u_value[0];
                            $slots = 0;
                            $u_value = strrev($u_value);
                            for( $m = 0; $m < $item_ups; $m++ ) 
                            {
                                $talic_id = hexdec($u_value[$m]);
                                $upgrades .= "<img src=\"./includes/images/talics/t-" . sprintf("%02d", $talic_id) . ".png\" width=\"12\"/>";
                            }
                            $bgc = " background-color: #10171f;";
                        }
                        else
                        {
                            $upgrades = "No Upgrades";
                            $bgc = "";
                        }

                        $out .= "<tr class=\"extra_" . $char["Serial"] . "\" style=\"display: none;\">" . "\n";
                        $out .= "\t<td class=\"alt1\" width=\"15%\" nowrap>" . $equipment_names[$i] . "</td>" . "\n";
                        $out .= "\t<td class=\"alt1\" colspan=\"4\" nowrap>" . $item_name . "</td>" . "\n";
                        $out .= "\t<td class=\"alt1\"" . $bgc . " colspan=\"2\" nowrap>" . $upgrades . "</td>" . "\n";
                        $out .= "</tr>" . "\n";
                    }
                    @mssql_free_result($class_info_result);
                }
                if( mssql_num_rows($char_result) <= 0 ) 
                {
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" colspan=\"7\" style=\"text-align: center;\" nowrap>No characters found for your account</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }

                $out .= "</table>";
                @mssql_free_result($char_result);
            }
            else
            {
                if( $page == "change_gender" ) 
                {
                    $char_serial = isset($_GET["serial"]) && is_int((int) $_GET["serial"]) ? antiject((int) $_GET["serial"]) : "";
                    if( $char_serial == "" ) 
                    {
                        $out .= $lang["invalid_serial"];
                    }
                    else
                    {
                        if( $config["specialgp_gender"] <= $userdata["points"] ) 
                        {
                            connectdatadb();
                            $char_query = mssql_query("" . "SELECT Name,Race,AccountSerial FROM tbl_base WHERE Serial = '" . $char_serial . "'");
                            $char_info = mssql_fetch_array($char_query);
                            if( mssql_num_rows($char_query) <= 0 ) 
                            {
                                $out .= $lang["invalid_serial"];
                            }
                            else
                            {
                                if( $char_info["AccountSerial"] != $userdata["serial"] ) 
                                {
                                    $out .= $lang["invalid_serial"];
                                }
                                else
                                {
                                    if( $char_info["Race"] == 0 ) 
                                    {
                                        $race = 1;
                                    }
                                    else
                                    {
                                        if( $char_info["Race"] == 1 ) 
                                        {
                                            $race = 0;
                                        }
                                        else
                                        {
                                            if( $char_info["Race"] == 2 ) 
                                            {
                                                $race = 3;
                                            }
                                            else
                                            {
                                                if( $char_info["Race"] == 3 ) 
                                                {
                                                    $race = 2;
                                                }
                                                else
                                                {
                                                    $race = $char_info["Race"];
                                                }

                                            }

                                        }

                                    }

                                    $update_query = "" . "UPDATE tbl_base SET Race = '" . $race . "' WHERE Serial = '" . $char_serial . "'";
                                    if( !($update_result = mssql_query($update_query)) ) 
                                    {
                                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Unable to change this characters gender</p>" . "\n";
                                    }
                                    else
                                    {
                                        connectgamecpdb();
                                        $minus_credits = $config["specialgp_gender"];
                                        $username = $userdata["username"];
                                        $update_credits = "" . "UPDATE gamecp_gamepoints SET user_points = user_points-" . $minus_credits . " WHERE user_account_id = '" . $userdata["serial"] . "'";
                                        if( $credits_result = mssql_query($update_credits) ) 
                                        {
                                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully updated " . $char_info["Name"] . "'s gender</p>";
                                            gamecp_log(1, $userdata["username"], "GAMECP - ACCOUNT INFO - Updated Gender - Char Serial: " . $char_serial);
                                            header("Refresh: 1; URL=" . $script_name . "?do=" . $_GET["do"]);
                                        }
                                        else
                                        {
                                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Failed to update your credits!</p>";
                                            gamecp_log(1, $userdata["username"], "GAMECP - ACCOUNT INFO - FAILED - Updated Gender - Char Serial: " . $char_serial);
                                        }

                                    }

                                }

                            }

                        }
                        else
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have enough game points to change your gender</p>";
                        }

                    }

                }
                else
                {
                    if( $page == "delete" ) 
                    {
                        $char_serial = isset($_GET["serial"]) && is_int((int) $_GET["serial"]) ? antiject((int) $_GET["serial"]) : "";
                        if( $char_serial == "" ) 
                        {
                            $out .= $lang["invalid_serial"];
                        }
                        else
                        {
                            connectdatadb();
                            $char_query = mssql_query("" . "SELECT Name,AccountSerial FROM tbl_base WHERE Serial = '" . $char_serial . "'");
                            $char_info = mssql_fetch_array($char_query);
                            if( mssql_num_rows($char_query) <= 0 ) 
                            {
                                $out .= $lang["invalid_serial"];
                            }
                            else
                            {
                                if( $char_info["AccountSerial"] != $userdata["serial"] ) 
                                {
                                    $out .= $lang["invalid_serial"];
                                }
                                else
                                {
                                    $out .= "<form method=\"post\">" . "\n";
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Are you sure you want the delete the character: <u>" . $char_info["Name"] . "</u>?</p>" . "\n";
                                    $out .= "<p style=\"text-align: center;\"><input type=\"hidden\" name=\"serial\" value=\"" . $char_serial . "\"/><input type=\"hidden\" name=\"page\" value=\"delete_char\"/><input type=\"submit\" name=\"yes\" value=\"Yes\"/> <input type=\"submit\" name=\"no\" value=\"No\"/></p>";
                                    $out .= "</form>";
                                }

                            }

                            @mssql_free_result($char_query);
                        }

                    }
                    else
                    {
                        if( $page == "delete_char" ) 
                        {
                            $yes = isset($_POST["yes"]) ? "1" : "0";
                            $no = isset($_POST["no"]) ? "1" : "0";
                            if( isset($_POST["serial"]) && is_int((int) $_POST["serial"]) ) 
                            {
                                $serial = antiject((int) $_POST["serial"]);
                            }
                            else
                            {
                                $serial = "";
                            }

                            if( $no != 1 && $serial != "" ) 
                            {
                                connectdatadb();
                                $char_query = mssql_query("" . "SELECT Name,AccountSerial FROM tbl_base WHERE Serial = '" . $serial . "'", $data_dbconnect);
                                $char_info = mssql_fetch_array($char_query);
                                if( mssql_num_rows($char_query) <= 0 ) 
                                {
                                    $out .= $lang["invalid_serial"];
                                }
                                else
                                {
                                    if( $char_info["AccountSerial"] != $userdata["serial"] ) 
                                    {
                                        $out .= $lang["invalid_serial"];
                                    }
                                    else
                                    {
                                        $cquery = mssql_query("UPDATE tbl_base SET deletename=name  WHERE serial = " . $serial);
                                        $cquery = mssql_query("UPDATE tbl_base SET name='*'+cast(serial as varchar)  WHERE serial = " . $serial);
                                        $cquery = mssql_query("UPDATE tbl_base SET DCK = 1,  Arrange = 1, DeleteTime = getdate()  WHERE Serial = " . $serial);
                                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Your character has been successfully deleted!</p>";
                                        gamecp_log(3, $userdata["username"], "GAMECP - DELETE CHARACTER - Character Serial:  " . $serial, 1);
                                    }

                                }

                                @mssql_free_result($char_query);
                            }
                            else
                            {
                                header("" . "Location: " . $script_name . "?do=" . $_GET["do"]);
                            }

                        }
                        else
                        {
                            $out .= $lang["page_not_found"];
                        }

                    }

                }

            }

        }
        else
        {
            $out .= $lang["no_permission"];
        }

    }
    else
    {
        $out .= $lang["invalid_page_load"];
    }

}


