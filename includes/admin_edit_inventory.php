<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Server Admin"]["Edit Inventory"] = $file;
}
else
{
    $lefttitle = "Support Desk - Inventory Edit";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $search_query = "";
            $top_limit = 10;
            $max_pages = 10;
            $num_of_bags = 100;
            $enable_exit = false;
            if( empty($page) ) 
            {
                $out .= "<form method=\"GET\" action=\"" . $script_name . "?do=" . $_GET["do"] . "\">";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Search for a User</b></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Charcter Name:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"character_name\" /></td>";
                $out .= "<tr>";
                $out .= "<td colspan=\"2\"><input type=\"hidden\" name=\"do\" value=\"" . $_GET["do"] . "\" /><input type=\"submit\" value=\"Search\" name=\"search_fun\" /></td>";
                $out .= "</tr>";
                $out .= "</table>";
                $out .= "</form>";
                if( $search_fun != "" ) 
                {
                    $out .= "<br/><br/>";
                    $item_id = isset($_GET["item_id"]) ? $_GET["item_id"] : "";
                    $character_name = isset($_GET["character_name"]) ? $_GET["character_name"] : "";
                    if( $item_id == "" && $character_name == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>You must enter a character name to look up.</b></p>";
                    }

                    if( $enable_exit != true ) 
                    {
                        connectdatadb();
                        $bag_numbers = "";
                        for( $i = 0; $i < 100; $i++ ) 
                        {
                            $bag_numbers .= "" . ", I.k" . $i . ", I.u" . $i . ", I.d" . $i;
                        }
                        $search_query .= " WHERE ";
                        if( $character_name != "" ) 
                        {
                            $character_name = ereg_replace("" . ";\$", "", $character_name);
                            $character_name = ereg_replace("\\\\", "", $character_name);
                            $character_name = trim($character_name);
                            $char_query = mssql_query("" . "SELECT Serial FROM tbl_base WHERE Name = '" . $character_name . "'");
                            $char_query = mssql_fetch_array($char_query);
                            $char_serial = $char_query["Serial"];
                            $search_query .= "" . "I.Serial = '" . $char_serial . "'";
                            @mssql_free_result($char_query);
                            connectdatadb();
                        }

                        $item_id = antiject($item_id);
                        include("./includes/pagination/ps_pagination.php");
                        $query_p1 = mssql_query("" . "SELECT \r\n\t\t\t\t\tB.AccountSerial, B.Name, B.Race, I.Serial" . $bag_numbers . "\r\n\t\t\t\t\tFROM \r\n\t\t\t\t\ttbl_inven AS I\r\n\t\t\t\t\tINNER JOIN \r\n\t\t\t\t\ttbl_base AS B\r\n\t\t\t\t\tON B.Serial = I.Serial\r\n\t\t\t\t\t" . $search_query);
                        $out .= "<form method=\"POST\" action=\"" . $script_name . "?do=" . $_GET["do"] . "\">";
                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                        connectitemsdb();
                        while( $row = mssql_fetch_array($query_p1) ) 
                        {
                            $out .= "<tr>";
                            $out .= "<td class=\"alt2\" colspan=\"5\" style=\"font-size: 10px; font-weight: bold;\">Account Serial: " . $row["AccountSerial"] . "</td>";
                            $out .= "</tr>";
                            $out .= "<tr>";
                            $out .= "<td class=\"alt2\" colspan=\"5\" style=\"font-size: 10px; font-weight: bold;\">Character Serial: " . $row["Serial"] . "</td>";
                            $out .= "</tr>";
                            $out .= "<tr>";
                            $out .= "<td class=\"alt2\" colspan=\"5\" style=\"font-size: 10px; font-weight: bold;\">Character Name: " . $row["Name"] . "</td>";
                            $out .= "</tr>";
                            $out .= "<tr>";
                            $out .= "<td class=\"alt2\" colspan=\"5\" style=\"font-size: 10px; font-weight: bold;\">Race: " . game_cp_7($row["Race"]) . "</td>";
                            $out .= "</tr>";
                            $out .= "<tr>";
                            $out .= "<td class=\"thead\" nowrap>Slot #</td>";
                            $out .= "<td class=\"thead\" nowrap>Item Name</td>";
                            $out .= "<td class=\"thead\" nowrap>Item Code</td>";
                            $out .= "<td class=\"thead\" nowrap>Amount</td>";
                            $out .= "<td class=\"thead\" nowrap>Upgrades</td>";
                            $out .= "</tr>";
                            for( $i = 0; $i < $num_of_bags; $i++ ) 
                            {
                                $k_value = $row["" . "k" . $i];
                                $u_value = $row["" . "u" . $i];
                                if( "-1" < $k_value ) 
                                {
                                    $slot = $i;
                                    $kn = 0;
                                    for( $n = 9; $n < $item_tbl_num; $n++ ) 
                                    {
                                        $item_id = ($k_value - $n * (256 + $slot + 1)) / 65536;
                                        if( $item_id == $k_value ) 
                                        {
                                            $kn = $n;
                                        }

                                    }
                                    $item_id = ceil($item_id);
                                    $kn = floor(($k_value - $item_id * 65536) / 256);
                                    $item_bagslot = ceil($k_value - $item_id * 65536 - $kn * 256);
                                    if( $item_bagslot == 0 ) 
                                    {
                                        $item_bagslot = $i;
                                    }

                                    $items_query = mssql_query("SELECT item_code, item_id, item_name FROM " . getitemtablename($kn) . "" . " WHERE item_id = '" . $item_id . "'", $items_dbconnect);
                                    $items = mssql_fetch_array($items_query);
                                    if( $items["item_name"] != "" ) 
                                    {
                                        $item_name = str_replace("_", " ", $items["item_name"]);
                                    }
                                    else
                                    {
                                        $item_name = "Not found in DB - " . $item_id . ":" . $kn;
                                    }

                                    $item_code = $items["item_code"];
                                    @mssql_free_result($items_query);
                                }
                                else
                                {
                                    $item_name = $k_value;
                                    $item_code = "-";
                                    $item_bagslot = $i;
                                }

                                $base_code = 268435455;
                                $item_slots = $u_value;
                                $item_slots = $item_slots - $base_code;
                                $item_slots = $item_slots / ($base_code + 1);
                                $upgrades = "";
                                $ceil_slots = ceil($item_slots);
                                $slots_code = $base_code + ($base_code + 1) * $ceil_slots;
                                $slots = $ceil_slots;
                                $km_allorArray = array( 0, 1, 2, 3, 4, 5, 6, 7 );
                                if( 0 < $ceil_slots && "-1" < $k_value && in_array($kn, $km_allorArray) ) 
                                {
                                    $ups = 0;
                                    if( $slots_code != $u_value ) 
                                    {
                                        for( $m = 1; $m <= 7; $m++ ) 
                                        {
                                            $item_ups = $u_value;
                                            $item_ups = ($base_code + ($base_code + 1) * $ceil_slots) - $item_ups;
                                            $talic_id = (pow(16, $m) - 1) / 15;
                                            $talic_id = ceil($item_ups / $talic_id);
                                            $raw_item_u = ceil($base_code + ($base_code + 1) * $ceil_slots) - $talic_id * (pow(16, $m) - 1) / 15;
                                            $talic_id = "";
                                            if( $raw_item_u == $u_value ) 
                                            {
                                                $ups = $m;
                                            }

                                        }
                                    }

                                    $item_ups = $u_value;
                                    $item_ups = ($base_code + ($base_code + 1) * $ceil_slots) - $item_ups;
                                    $talic_id = (pow(16, $ups) - 1) / 15;
                                    $talic_id = @ceil($item_ups / $talic_id);
                                    $bgc = "background-color: #10171f;";
                                }
                                else
                                {
                                    $upgrades = "No Upgrades";
                                    $bgc = "";
                                    $ups = 0;
                                }

                                $bgcolor = "";
                                $name = "item_code" . $i;
                                $name2 = "results_div" . $i;
                                $out .= "<tr>";
                                $out .= "<td class=\"alt2\" style=\"font-size: 10px;" . $bgcolor . " text-align: center;\" nowrap>" . $item_bagslot . "</td>";
                                $out .= "<td class=\"alt2\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>";
                                $out .= "<div id=\"" . $name2 . "\">" . $item_name . "</div></td>";
                                $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>";
                                $out .= "<input type=\"text\" onKeyUp=\"check_itemname('" . $name . "', '" . $name2 . "');\" id=\"item_code" . $i . "\" name=\"item_code" . $i . "\" value=\"" . $item_code . "\" />" . "\n";
                                $out .= "</td>";
                                $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>";
                                $out .= "<input type=\"text\" name=\"item_amount" . $i . "\" value=\"" . $row["" . "d" . $i] . "\" /></td>";
                                $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>";
                                if( $upgrades == "No Upgrades" ) 
                                {
                                    $ups = 0;
                                    $ceil_slots = 0;
                                    $talic_id = 1;
                                }

                                $out .= "<select name=\"item_ups" . $i . "\">";
                                for( $x = 0; $x <= 7; $x++ ) 
                                {
                                    $select_ups = "";
                                    if( $ups == $x ) 
                                    {
                                        $select_ups = " selected";
                                    }

                                    $out .= "<option value=\"" . $x . "\"" . $select_ups . ">+" . $x . "</option>";
                                }
                                $out .= "</select>";
                                $out .= "/";
                                $out .= "<select name=\"item_slots" . $i . "\">";
                                for( $y = 0; $y <= 7; $y++ ) 
                                {
                                    $select_slots = "";
                                    if( $ceil_slots == $y ) 
                                    {
                                        $select_slots = " selected";
                                    }

                                    $out .= "<option value=\"" . $y . "\"" . $select_slots . ">" . $y . "</option>";
                                }
                                $out .= "</select>";
                                $out .= " ";
                                $out .= "<select name=\"item_talic" . $i . "\">";
                                for( $z = 1; $z < 16; $z++ ) 
                                {
                                    if( $z == 15 ) 
                                    {
                                        $talic_name = "Ignorant";
                                    }
                                    else
                                    {
                                        if( $z == 14 ) 
                                        {
                                            $talic_name = "Destruction";
                                        }
                                        else
                                        {
                                            if( $z == 13 ) 
                                            {
                                                $talic_name = "Darkness";
                                            }
                                            else
                                            {
                                                if( $z == 12 ) 
                                                {
                                                    $talic_name = "Chaos";
                                                }
                                                else
                                                {
                                                    if( $z == 11 ) 
                                                    {
                                                        $talic_name = "Hatred";
                                                    }
                                                    else
                                                    {
                                                        if( $z == 10 ) 
                                                        {
                                                            $talic_name = "Favor";
                                                        }
                                                        else
                                                        {
                                                            if( $z == 9 ) 
                                                            {
                                                                $talic_name = "Wisdom";
                                                            }
                                                            else
                                                            {
                                                                if( $z == 8 ) 
                                                                {
                                                                    $talic_name = "Sacred Flame";
                                                                }
                                                                else
                                                                {
                                                                    if( $z == 7 ) 
                                                                    {
                                                                        $talic_name = "Belief";
                                                                    }
                                                                    else
                                                                    {
                                                                        if( $z == 6 ) 
                                                                        {
                                                                            $talic_name = "Guard";
                                                                        }
                                                                        else
                                                                        {
                                                                            if( $z == 5 ) 
                                                                            {
                                                                                $talic_name = "Glory";
                                                                            }
                                                                            else
                                                                            {
                                                                                if( $z == 4 ) 
                                                                                {
                                                                                    $talic_name = "Grace";
                                                                                }
                                                                                else
                                                                                {
                                                                                    if( $z == 3 ) 
                                                                                    {
                                                                                        $talic_name = "Mercy";
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        if( $z == 2 ) 
                                                                                        {
                                                                                            $talic_name = "Rebirth";
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            if( $z == 1 ) 
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

                                    $select_talic = "";
                                    if( $talic_id == $z ) 
                                    {
                                        $select_talic = " selected";
                                    }

                                    $out .= "<option value=\"" . $z . "\"" . $select_talic . ">" . $talic_name . "</option>";
                                }
                                $out .= "</select>";
                                $out .= "</td>";
                                $out .= "</tr>";
                                $out .= "<input type=\"hidden\" name=\"item_bagslot" . $i . "\" value=\"" . $item_bagslot . "\" />";
                            }
                            $out .= "<input type=\"hidden\" name=\"char_serial\" value=\"" . $row["Serial"] . "\" />";
                            $out .= "<input type=\"hidden\" name=\"char_name\" value=\"" . $row["Name"] . "\" />";
                            if( $search_fun != "" && $enable_exit != true ) 
                            {
                                $out .= "<input type=\"hidden\" name=\"page\" value=\"update\" />";
                                $out .= "<input type=\"submit\" name=\"Update\" value=\"Update\" />";
                            }

                        }
                        if( mssql_num_rows($query_p1) <= 0 ) 
                        {
                            $out .= "<tr>";
                            $out .= "\t<td class=\"alt2\">No such character found</td>" . "\n";
                            $out .= "</tr>";
                        }

                        @mssql_free_result($query_p1);
                        $out .= "</table>";
                        if( $search_fun != "" && $enable_exit != true ) 
                        {
                            $out .= "</form>";
                        }

                        $new_item_id = isset($_GET["item_id"]) ? $_GET["item_id"] : "";
                        gamecp_log(0, $userdata["username"], "" . "ADMIN - ITEM EDIT - Searched for: " . $character_name . " or " . $new_item_id, 1);
                        return 1;
                    }

                }

            }
            else
            {
                if( $page == "update" ) 
                {
                    connectitemsdb();
                    $base_code = 268435455;
                    $item_k = "";
                    $item_d = "";
                    $item_u = "";
                    $char_serial = isset($_POST["char_serial"]) && is_int((int) $_POST["char_serial"]) ? (int) $_POST["char_serial"] : "";
                    $char_name = isset($_POST["char_name"]) ? $_POST["char_name"] : "";
                    if( $char_serial == "" || $char_serial == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p style=\"text-align: center;\">Invalid!</p>";
                    }

                    if( !$enable_exit ) 
                    {
                        $update_query = "UPDATE tbl_inven SET ";
                        for( $n = 0; $n < 100; $n++ ) 
                        {
                            $info["item_code"] = antiject($_POST["item_code" . $n]);
                            $info["item_slots"] = antiject($_POST["item_slots" . $n]);
                            $info["item_ups"] = antiject($_POST["item_ups" . $n]);
                            $info["item_talic"] = antiject($_POST["item_talic" . $n]);
                            $info["item_amount"] = antiject($_POST["item_amount" . $n]);
                            $info["item_bagslot"] = antiject($_POST["item_bagslot" . $n]);
                            if( $info["item_code"] == "-" ) 
                            {
                                $item_k = "-1";
                                $item_u = $base_code;
                                $item_d = "0";
                            }
                            else
                            {
                                if( $info["item_slots"] < $info["item_ups"] ) 
                                {
                                    $info["item_ups"] = $info["item_slots"];
                                }

                                if( $info["item_talic"] == 1 ) 
                                {
                                    $item_u = $base_code + ($base_code + 1) * $info["item_slots"];
                                }
                                else
                                {
                                    $item_u = ($base_code + ($base_code + 1) * $info["item_slots"]) - $info["item_talic"] * (pow(16, $info["item_ups"]) - 1) / 15;
                                }

                                $info["item_kind"] = game_cp_4($info["item_code"]);
                                $km_allorArray = array( 0, 1, 2, 3, 4, 5, 6, 7 );
                                if( !in_array($info["item_kind"], $km_allorArray) ) 
                                {
                                    $item_u = $base_code;
                                }

                                $itemdata_query = mssql_query("SELECT item_code, item_name, item_id FROM " . getitemtablename($info["item_kind"]) . " WHERE item_code = '" . $info["item_code"] . "'", $items_dbconnect);
                                $itemdata = mssql_fetch_array($itemdata_query);
                                $info["item_id"] = $itemdata["item_id"];
                                $item_k = 65536 * $info["item_id"] + $info["item_kind"] * 256 + $info["item_bagslot"];
                                $item_d = $info["item_amount"];
                                if( $info["item_kind"] == tbl_code_unitkey ) 
                                {
                                    if( $info["item_id"] == 0 ) 
                                    {
                                        $item_u = 0;
                                    }
                                    else
                                    {
                                        if( $info["item_id"] == 1 ) 
                                        {
                                            $item_u = 0;
                                        }
                                        else
                                        {
                                            $item_u = 0;
                                        }

                                    }

                                }

                                @mssql_free_result($itemdata_query);
                            }

                            if( $n != 0 ) 
                            {
                                $update_query .= ", ";
                            }

                            if( !in_array($info["item_kind"], $km_allorArray) ) 
                            {
                                $update_query .= "" . "K" . $n . " = '" . $item_k . "', D" . $n . " = '" . $item_d . "'";
                            }
                            else
                            {
                                $update_query .= "" . "K" . $n . " = '" . $item_k . "', D" . $n . " = '" . $item_d . "', U" . $n . " = '" . $item_u . "'";
                            }

                        }
                        $update_query .= "" . " WHERE Serial = '" . $char_serial . "'";
                        connectdatadb();
                        mssql_query($update_query);
                        gamecp_log(0, $userdata["username"], "" . "ADMIN - ITEM EDIT - Updated Inventory of " . $char_name, 1);
                        header("Refresh: 2; URL=" . $script_name . "?do=" . $_GET["do"] . "&search_fun=true&character_name=" . $char_name);
                        $out .= "<p style=\"text-align: center;\">The user " . $char_name . "'s inventory has been updated... Redirecting..</p>";
                        return 1;
                    }

                }
                else
                {
                    if( $page == "getcode" ) 
                    {
                        connectitemsdb();
                        $item_code = isset($_GET["itemcode"]) ? trim($_GET["itemcode"]) : "";
                        if( $item_code != "" ) 
                        {
                            if( $item_code != "-" ) 
                            {
                                $kn = game_cp_4($item_code);
                                $items_query = mssql_query("SELECT TOP 1 item_name FROM " . getitemtablename($kn) . "" . " WHERE item_code LIKE '" . $item_code . "'", $items_dbconnect);
                                $items = mssql_fetch_array($items_query);
                                if( $items["item_name"] != "" ) 
                                {
                                    echo "<b>" . str_replace("_", " ", $items["item_name"]) . "</b>";
                                }
                                else
                                {
                                    echo "<b>No such item for: " . $item_code . "</b><br/>";
                                }

                                @mssql_free_result($items_query);
                            }
                            else
                            {
                                echo "-1";
                            }

                        }
                        else
                        {
                            echo "<b>No code provided</b>";
                        }

                        exit();
                    }

                    $out .= $lang["invalid_page_id"];
                    return 1;
                }

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

function game_cp_8($file, $text)
{
    global $userdata;
    $timestamp = date("d/m/Y h:i:s A");
    $myip = $_SERVER["REMOTE_ADDR"];
    $filename = "./includes/item_edit_logs/" . $file . ".log";
    if( isset($userdata["serial"]) ) 
    {
        $addserial = " [" . $userdata["username"] . "]";
    }

    $somecontent = "" . "[" . $timestamp . "]" . $addserial . " [" . $myip . "] " . $text . "\n";
    if( !($handle = @fopen($filename, "a")) ) 
    {
    }

    if( @fwrite($handle, $somecontent) === FALSE ) 
    {
    }

    @fclose($handle);
}


