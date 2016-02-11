<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Support"]["Inventory Search"] = $file;
}
else
{
    $lefttitle = "Support Desk - Inventory Search";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $page_gen = isset($_GET["page_gen"]) && is_int((int) $_GET["page_gen"]) ? (int) $_GET["page_gen"] : "1";
            $item_ups = isset($_GET["item_ups"]) && is_int((int) $_GET["item_ups"]) ? (int) $_GET["item_ups"] : 0;
            $item_slots = isset($_GET["item_slots"]) && is_int((int) $_GET["item_slots"]) ? (int) $_GET["item_slots"] : 0;
            $item_talic = isset($_GET["item_talic"]) && is_int((int) $_GET["item_talic"]) ? (int) $_GET["item_talic"] : 0;
            $item_id = isset($_GET["item_id"]) ? trim($_GET["item_id"]) : "";
            $item_amount_max = isset($_GET["item_amount_max"]) && is_int((int) $_GET["item_amount_max"]) ? trim((int) $_GET["item_amount_max"]) : 0;
            $item_amount_min = isset($_GET["item_amount_min"]) && is_int((int) $_GET["item_amount_min"]) ? trim((int) $_GET["item_amount_min"]) : 0;
            $character_name = isset($_GET["character_name"]) ? antiject($_GET["character_name"]) : "";
            $character_serial = isset($_GET["character_serial"]) && is_int((int) $_GET["character_serial"]) ? antiject((int) $_GET["character_serial"]) : 0;
            $enable_exit = false;
            $search_query = "";
            $top_limit = 5;
            $max_pages = 10;
            $num_of_bags = 100;
            $query_p2 = "";
            $enable_itemsearch = false;
            if( empty($page) ) 
            {
                $out .= "<form method=\"GET\" action=\"" . $script_name . "?do=" . $_GET["do"] . "\">";
                $out .= "<table class=\"tborder\" cellpadding=\"2\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>";
                $out .= "\t\t<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Search for an Item</b></td>";
                $out .= "\t</tr>";
                $out .= "\t<tr>";
                $out .= "\t\t<td class=\"alt1\">Charcter Serial:</td>";
                $out .= "\t\t<td class=\"alt2\"><input type=\"text\" name=\"character_serial\" /></td>";
                $out .= "\t</tr>";
                $out .= "\t<tr>";
                $out .= "\t\t<td class=\"alt1\">Charcter Name:</td>";
                $out .= "\t\t<td class=\"alt2\"><input type=\"text\" name=\"character_name\" /></td>";
                $out .= "\t</tr>";
                $out .= "\t<tr>";
                $out .= "\t\t<td class=\"alt1\">Item Code:</td>";
                $out .= "\t\t<td class=\"alt2\"><input type=\"text\" name=\"item_id\" /></td>";
                $out .= "\t</tr>";
                $out .= "\t<tr>";
                $out .= "\t\t<td class=\"alt1\">Amount:</td>";
                $out .= "\t\t<td class=\"alt2\"><input type=\"text\" name=\"item_amount_min\" size=\"1\" /> - <input type=\"text\" name=\"item_amount_max\" size=\"1\" /></td>";
                $out .= "\t</tr>";
                $out .= "\t<tr>";
                $out .= "\t\t<td class=\"alt1\">Upgrades:</td>";
                $out .= "\t\t<td class=\"alt2\">" . "\n";
                $out .= "<select name=\"item_ups\">";
                for( $x = 0; $x <= 7; $x++ ) 
                {
                    $select_ups = "";
                    if( $item_ups == $x ) 
                    {
                        $select_ups = " selected";
                    }

                    $out .= "<option value=\"" . $x . "\"" . $select_ups . ">+" . $x . "</option>";
                }
                $out .= "</select>";
                $out .= "/";
                $out .= "<select name=\"item_slots\">";
                for( $y = 0; $y <= 7; $y++ ) 
                {
                    $select_slots = "";
                    if( $item_slots == $y ) 
                    {
                        $select_slots = " selected";
                    }

                    $out .= "<option value=\"" . $y . "\"" . $select_slots . ">" . $y . "</option>";
                }
                $out .= "</select>";
                $out .= " ";
                $out .= "<select name=\"item_talic\">";
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
                    if( $item_talic == $z ) 
                    {
                        $select_talic = " selected";
                    }

                    $out .= "<option value=\"" . $z . "\"" . $select_talic . ">" . $talic_name . "</option>";
                }
                $out .= "</select>";
                $out .= "\t\t</td>" . "\n";
                $out .= "\t</tr>";
                $out .= "\t<tr>";
                $out .= "\t\t<td colspan=\"2\"><input type=\"hidden\" name=\"do\" value=\"" . $_GET["do"] . "\" /><input type=\"submit\" value=\"Search\" name=\"search_fun\" /></td>";
                $out .= "\t</tr>";
                $out .= "</table>";
                $out .= "</form>";
                if( $search_fun != "" ) 
                {
                    $out .= "<br/><br/>";
                    if( $item_id == "" && $character_name == "" && $character_serial == 0 && ($item_talic == 0 || $item_talic == 1) ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>Sorry, make sure you filled in either the item id or character name</b></p>";
                    }

                    if( $enable_exit != true ) 
                    {
                        connectdatadb();
                        $bag_numbers = "";
                        for( $i = 0; $i < 100; $i++ ) 
                        {
                            $bag_numbers .= "" . ", k" . $i . ", u" . $i . ", d" . $i;
                        }
                        if( $character_name != "" || $character_serial != 0 || $item_id != "" || $item_talic != 0 || $item_talic != 1 ) 
                        {
                            $search_query .= " WHERE ";
                        }

                        if( $character_name != "" || $character_serial != 0 ) 
                        {
                            if( $character_serial != 0 ) 
                            {
                                $search_query .= "" . "Serial = '" . $character_serial . "'";
                            }
                            else
                            {
                                $character_name = ereg_replace("" . ";\$", "", $character_name);
                                $character_name = ereg_replace("\\\\", "", $character_name);
                                $character_name = trim($character_name);
                                $char_query = mssql_query("" . "SELECT Serial FROM tbl_base WHERE Name = '" . $character_name . "'");
                                $char_query = mssql_fetch_array($char_query);
                                $char_serial = $char_query["Serial"];
                                $search_query .= "" . "Serial = '" . $char_serial . "'";
                            }

                        }

                        if( $item_id != "" ) 
                        {
                            connectitemsdb();
                            $item_id = str_replace(" ", "", $item_id);
                            $item_id = trim($item_id);
                            if( $character_name != "" || $character_serial != 0 ) 
                            {
                                $search_query .= " AND ";
                            }

                            $item_idq = $item_id;
                            $item_kind = game_cp_4($item_id);
                            $table_name = getitemtablename($item_kind);
                            $item_id = str_replace("%", "", $item_id);
                            $table_query = @mssql_query("" . "SELECT item_code, item_id, item_name FROM " . $table_name . " WHERE item_code = '" . $item_id . "'", $items_dbconnect) or exit( "" . "Make sure you filled up your items DB...because I cannot seem to find the table " . $table_name );
                            $table = mssql_fetch_array($table_query);
                            $item_id = $table["item_id"];
                            if( $table["item_name"] != "" ) 
                            {
                                $search_query .= "(";
                                $item_id_start = ceil(65536 * $table["item_id"] + $item_kind * 256 + 0);
                                $item_id_end = ceil(65536 * $table["item_id"] + $item_kind * 256 + 99);
                                for( $i = 0; $i < 100; $i++ ) 
                                {
                                    if( $i != 0 ) 
                                    {
                                        $search_query .= " OR ";
                                    }

                                    if( $item_amount_min != 0 ) 
                                    {
                                        if( $item_amount_max == 0 ) 
                                        {
                                            $item_amount_max = $item_amount_min;
                                        }

                                        $append_amount = "" . " AND d" . $i . " >= " . $item_amount_min . " AND d" . $i . " <= " . $item_amount_max;
                                    }
                                    else
                                    {
                                        $append_amount = "";
                                    }

                                    $search_query .= "" . "k" . $i . " >= " . $item_id_start . " AND k" . $i . " <= " . $item_id_end . $append_amount;
                                }
                                $search_query .= ")";
                            }
                            else
                            {
                                $search_query = "";
                            }

                            @mssql_free_result($table_query);
                            connectdatadb();
                            $enable_itemsearch = true;
                        }

                        if( $item_talic != 0 && $item_talic != 1 ) 
                        {
                            if( $character_name != "" || $character_serial != 0 || $enable_itemsearch ) 
                            {
                                $search_query .= " AND ";
                            }

                            $search_query .= "(";
                            $base_code = 268435455;
                            $item_u = ($base_code + ($base_code + 1) * $item_slots) - $item_talic * (pow(16, $item_ups) - 1) / 15;
                            for( $i = 0; $i < 100; $i++ ) 
                            {
                                if( $i != 0 ) 
                                {
                                    $search_query .= " OR ";
                                }

                                if( !$enable_itemsearch ) 
                                {
                                    $append_k = "" . " AND k" . $i . " != '-1'";
                                }
                                else
                                {
                                    $append_k = "";
                                }

                                $search_query .= "" . "u" . $i . " = " . $item_u . $append_k;
                            }
                            $search_query .= ")";
                            $enable_itemsearch = true;
                        }

                        if( $enable_itemsearch ) 
                        {
                        }

                        $item_id = antiject($item_id);
                        if( $enable_itemsearch == true ) 
                        {
                            include("./includes/pagination/ps_pagination.php");
                        }

                        $query_p1 = "" . "SELECT \n\t\t\t\t\tSerial" . $bag_numbers . "\n\t\t\t\t\tFROM \n\t\t\t\t\ttbl_inven\n\t\t\t\t\t" . $search_query;
                        $query_p2 .= " AND Serial NOT IN ( SELECT TOP [OFFSET] Serial \n\t\t\t\t\tFROM \n\t\t\t\t\ttbl_inven\n\t\t\t\t\t" . $search_query . " ORDER BY Serial DESC) ORDER BY Serial DESC";
                        if( $enable_itemsearch == true ) 
                        {
                            $filename = $_GET["do"] . "_" . md5($query_p1);
                            $query_count = "1000";
                            $url = str_replace("&page_gen=" . $page_gen, "", $_SERVER["REQUEST_URI"]);
                            $pager = new PS_Pagination($data_dbconnect, $query_p1, $query_p2, $top_limit, $max_pages, $url, $query_count);
                        }

                        if( $enable_itemsearch == true ) 
                        {
                            $rs = $pager->paginate();
                        }
                        else
                        {
                            $rs = mssql_query($query_p1);
                        }

                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                        $base_code = 268435455;
                        $item_umx = ($base_code + ($base_code + 1) * $item_slots) - $item_talic * (pow(16, $item_ups) - 1) / 15;
                        while( $row = mssql_fetch_array($rs) ) 
                        {
                            connectdatadb();
                            $select_info = "SELECT Name, AccountSerial FROM tbl_base WHERE Serial = '" . $row["Serial"] . "'";
                            $select_result = mssql_query($select_info);
                            $charinfo = mssql_fetch_array($select_result);
                            $accountserial = $charinfo["AccountSerial"];
                            $name = $charinfo["Name"];
                            mssql_free_result($select_result);
                            if( $name[0] != "*" ) 
                            {
                                $out .= "<tr>" . "\n";
                                $out .= "\t<td class=\"thead\" colspan=\"5\">&nbsp;</td>" . "\n";
                                $out .= "</tr>" . "\n";
                                $out .= "<tr>";
                                $out .= "<td class=\"alt2\" colspan=\"5\" style=\"font-size: 10px;\"><span style=\"font-weight: bold;\">Character Serial:</span> " . $row["Serial"] . "</td>";
                                $out .= "</tr>";
                                $out .= "<tr>";
                                $out .= "<td class=\"alt2\" colspan=\"5\" style=\"font-size: 10px;\"><span style=\"font-weight: bold;\">Character Name:</span> " . $name . "</td>";
                                $out .= "</tr>";
                                $out .= "<tr>";
                                $out .= "<td class=\"alt2\" colspan=\"5\" style=\"font-size: 10px;\"><span style=\"font-weight: bold;\">Account Serial:</span> " . $accountserial . "</td>";
                                $out .= "</tr>";
                                $out .= "<tr>";
                                $out .= "<td class=\"thead\" nowrap>Slot #</td>";
                                $out .= "<td class=\"thead\" nowrap>Item Code</td>";
                                $out .= "<td class=\"thead\" nowrap>Item Name</td>";
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
                                            $item_id = ($k_value - $n * (256 + $slot)) / 65536;
                                            if( $item_id == $k_value ) 
                                            {
                                                $kn = $n;
                                            }

                                        }
                                        $item_id = ceil($item_id);
                                        $kn = floor(($k_value - $item_id * 65536) / 256);
                                        connectitemsdb();
                                        $items_query = mssql_query("SELECT item_code, item_id, item_name FROM " . getitemtablename($kn) . "" . " WHERE item_id = '" . $item_id . "'", $items_dbconnect);
                                        $items = mssql_fetch_array($items_query);
                                        if( $items["item_name"] != "" ) 
                                        {
                                            $item_id = str_replace("_", " ", $items["item_name"]);
                                        }
                                        else
                                        {
                                            $item_id = "Not found in DB - " . $item_id . ":" . $kn;
                                        }

                                        $item_code = $items["item_code"];
                                        mssql_free_result($items_query);
                                    }
                                    else
                                    {
                                        $item_id = $k_value;
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
                                    if( 0 < $ceil_slots && "-1" < $k_value && in_array($kn, $km_allorArray) ) 
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

                                    $text_color = "";
                                    if( $search_fun != "" && $enable_itemsearch ) 
                                    {
                                        if( $item_code == trim($_GET["item_id"]) ) 
                                        {
                                            $out .= "<tr>";
                                            $out .= "<td class=\"item_highlight\" nowrap>" . $i . "</td>";
                                            $out .= "<td class=\"item_highlight\" nowrap>" . $item_code . "</td>";
                                            $out .= "<td class=\"item_highlight\" nowrap>" . $item_id . "</td>";
                                            $out .= "<td class=\"item_highlight\" nowrap>" . $row["" . "d" . $i] . "</td>";
                                            $out .= "<td class=\"item_highlight\"" . $bgc . " nowrap>" . $upgrades . "</td>";
                                            $out .= "</tr>";
                                        }
                                        else
                                        {
                                            if( $ux_value == $item_umx && $item_talic != 0 && $item_talic != 1 ) 
                                            {
                                                $out .= "<tr>";
                                                $out .= "<td class=\"alt1\" nowrap>" . $i . "</td>";
                                                $out .= "<td class=\"alt1\" nowrap>" . $item_code . "</td>";
                                                $out .= "<td class=\"alt1\" style=\"" . $text_color . "\" nowrap>" . $item_id . "</td>";
                                                $out .= "<td class=\"alt1\" nowrap>" . $row["" . "d" . $i] . "</td>";
                                                $out .= "<td class=\"alt1\" style=\"" . $bgc . "\" nowrap>" . $upgrades . "</td>";
                                                $out .= "</tr>";
                                            }

                                        }

                                    }
                                    else
                                    {
                                        $bgcolor = "";
                                        $out .= "<tr>";
                                        $out .= "<td class=\"alt2\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $i . "</td>";
                                        $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $item_code . "</td>";
                                        $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . $text_color . "\" nowrap>" . $item_id . "</td>";
                                        $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $row["" . "d" . $i] . "</td>";
                                        $out .= "<td class=\"alt1\" style=\"font-size: 10px; " . $bgc . "\" nowrap>" . $upgrades . "</td>";
                                        $out .= "</tr>";
                                    }

                                }
                            }

                        }
                        if( mssql_num_rows($rs) <= 0 ) 
                        {
                            $out .= "<tr>" . "\n";
                            $out .= "<td colspan=\"5\" style=\"text-align: center;\">No such user/item found in the database</td>" . "\n";
                            $out .= "</tr>" . "\n";
                        }
                        else
                        {
                            if( $enable_itemsearch == true ) 
                            {
                                $out .= "<tr>";
                                $out .= "<td colspan=\"5\" style=\"text-align: center;\">" . $pager->renderFullNav() . "</td>";
                                $out .= "</tr>";
                            }

                        }

                        @mssql_free_result($rs);
                        $out .= "</table>";
                        if( !isset($item_idq) ) 
                        {
                            $item_idq = "";
                        }

                        gamecp_log(0, $userdata["username"], "" . "ADMIN - ITEM SEARCH - Searched for: " . $character_name . " or " . $item_idq, 1);
                        return 1;
                    }

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


