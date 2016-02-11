<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Support"]["Bank Search"] = $file;
}
else
{
    $lefttitle = "Support Desk - Bank Search";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $page_gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
            $search_query = "";
            $top_limit = 10;
            $max_pages = 10;
            $num_of_bags = 100;
            $enable_itemsearch = false;
            $enable_exit = false;
            $query_p2 = "";
            if( empty($page) ) 
            {
                $out .= "<form method=\"GET\" action=\"" . $script_name . "?do=" . $_GET["do"] . "\">";
                $out .= "<table class=\"tborder\" cellpadding=\"2\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Search for an Item</b></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Account Serial:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_serial\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Account Name:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_name\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Item ID:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"item_id\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td colspan=\"2\"><input type=\"hidden\" name=\"do\" value=\"" . $_GET["do"] . "\" /><input type=\"submit\" value=\"Search\" name=\"search_fun\" /></td>";
                $out .= "</tr>";
                $out .= "</table>";
                $out .= "</form>";
                if( $search_fun != "" ) 
                {
                    $out .= "<br/><br/>";
                    $item_id = isset($_GET["item_id"]) ? antiject(trim($_GET["item_id"])) : "";
                    $account_serial = isset($_GET["account_serial"]) && is_int((int) $_GET["account_serial"]) ? antiject((int) $_GET["account_serial"]) : 0;
                    $account_name = isset($_GET["account_name"]) ? antiject($_GET["account_name"]) : "";
                    if( $item_id == "" && $account_serial == 0 && $account_name == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>Sorry, make sure you filled in either the item id, account name or account serial</b></p>";
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
                        if( $account_serial != 0 || $account_name != "" ) 
                        {
                            if( $account_name != "" ) 
                            {
                                connectuserdb();
                                $userresult = mssql_query("SELECT Serial FROM tbl_UserAccount WHERE id = convert(binary,'" . $account_name . "')");
                                $user = mssql_fetch_array($userresult);
                                $account_serial = $user["Serial"];
                                @mssql_free_result($userresult);
                                connectdatadb();
                            }

                            $account_serial = ereg_replace("" . ";\$", "", $account_serial);
                            $account_serial = ereg_replace("\\\\", "", $account_serial);
                            $account_serial = trim($account_serial);
                            $search_query .= "" . "I.AccountSerial = '" . $account_serial . "'";
                        }

                        if( $item_id != "" ) 
                        {
                            $enable_itemSearch = true;
                            connectitemsdb();
                            $item_id = str_replace(" ", "", $item_id);
                            $item_id = ereg_replace("" . ";\$", "", $item_id);
                            $item_id = ereg_replace("\\\\", "", $item_id);
                            $item_id = trim($item_id);
                            if( $account_serial != 0 ) 
                            {
                                $search_query .= " AND ";
                            }

                            $item_idq = $item_id;
                            $item_kind = game_cp_4($item_id);
                            $table_name = getitemtablename($item_kind);
                            $item_id = str_replace("%", "", $item_id);
                            $table_query = mssql_query("" . "SELECT item_code, item_id, item_name FROM " . $table_name . " WHERE item_code = '" . $item_id . "'", $items_dbconnect);
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

                                    $search_query .= "" . "I.k" . $i . " >= " . $item_id_start . " AND I.k" . $i . " <= " . $item_id_end;
                                }
                                $search_query .= ")";
                            }

                            @mssql_free_result($table_query);
                            connectdatadb();
                            $enable_itemsearch = true;
                        }
                        else
                        {
                            $item_idq = "";
                        }

                        $item_id = antiject($item_id);
                        if( $enable_itemsearch == true ) 
                        {
                            include("./includes/pagination/ps_pagination.php");
                        }

                        $query_p1 = "" . "SELECT \n\t\t\t\t\tI.AccountSerial AS Serial" . $bag_numbers . "\n\t\t\t\t\tFROM \n\t\t\t\t\ttbl_AccountTrunk AS I\n\t\t\t\t\t" . $search_query;
                        $query_p2 .= " AND I.AccountSerial NOT IN ( SELECT TOP [OFFSET] I.AccountSerial FROM tbl_AccountTrunk AS I " . $search_query . " ORDER BY I.AccountSerial DESC) ORDER BY I.AccountSerial DESC";
                        if( $enable_itemsearch == true ) 
                        {
                            $filename = $_GET["do"] . "_" . md5($query_p1);
                            if( !($query_count = readCache($filename . ".cache", 60)) ) 
                            {
                                $query_count_result = mssql_query("" . "SELECT COUNT(I.AccountSerial) AS Count FROM tbl_AccountTrunk as I " . $search_query);
                                $query_count = mssql_fetch_array($query_count_result);
                                $query_count = $query_count["Count"];
                                writeCache($query_count, $filename . ".cache");
                                @mssql_free_result($query_count_result);
                            }

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
                        connectitemsdb();
                        while( $row = mssql_fetch_array($rs) ) 
                        {
                            $out .= "<tr>" . "\n";
                            $out .= "\t<td class=\"thead\" colspan=\"5\">&nbsp;</td>" . "\n";
                            $out .= "</tr>" . "\n";
                            $out .= "<tr>";
                            $out .= "<td class=\"alt2\" colspan=\"5\" style=\"font-size: 10px;\"><span style=\"font-weight: bold;\">Account Serial:</span> " . $row["Serial"] . "</td>";
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
                                    @mssql_free_result($items_query);
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

                                if( $search_fun != "" && $enable_itemsearch ) 
                                {
                                    if( $item_code == trim($_GET["item_id"]) ) 
                                    {
                                        $bgcolor = " background-color: #dcdca5;";
                                        $out .= "<tr>";
                                        $out .= "<td class=\"alt2\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $i . "</td>";
                                        $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $item_code . "</td>";
                                        $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $item_id . "</td>";
                                        $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $row["" . "d" . $i] . "</td>";
                                        $out .= "<td class=\"alt1\" style=\"font-size: 10px; " . $bgc . "\" nowrap>" . $upgrades . "</td>";
                                        $out .= "</tr>";
                                    }

                                }
                                else
                                {
                                    $bgcolor = "";
                                    $out .= "<tr>";
                                    $out .= "<td class=\"alt2\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $i . "</td>";
                                    $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $item_code . "</td>";
                                    $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $item_id . "</td>";
                                    $out .= "<td class=\"alt1\" style=\"font-size: 10px;" . $bgcolor . "\" nowrap>" . $row["" . "d" . $i] . "</td>";
                                    $out .= "<td class=\"alt1\" style=\"font-size: 10px; " . $bgc . "\" nowrap>" . $upgrades . "</td>";
                                    $out .= "</tr>";
                                }

                            }
                        }
                        if( $enable_itemsearch == true ) 
                        {
                            $out .= "<tr>";
                            $out .= "<td colspan=\"5\" style=\"text-align: center;\">" . $pager->renderFullNav() . "</td>";
                            $out .= "</tr>";
                        }

                        $out .= "</table>";
                        gamecp_log(0, $userdata["username"], "" . "ADMIN - ITEM SEARCH - Searched for: " . $account_name . " or " . $item_idq, 1);
                        @mssql_free_result($rs);
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


