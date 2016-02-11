<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Logs"]["Manage Redeem Logs"] = $file;
}
else
{
    $lefttitle = "Item Shop Admin - Redeem Logs";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
            $search_fun = isset($_GET["search_fun"]) ? 1 : "";
            $page_gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
            $enable_exit = false;
            $search_query = "";
            $query_p2 = "";
            $max_pages = 10;
            $top_limit = 50;
            $out .= "<form method=\"GET\">" . "\n";
            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"thead\" colspan=\"2\">Search for a user</td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Account Name:</td>" . "\n";
            $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"account_name\"/></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Character Name:</td>" . "\n";
            $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"character_name\"/></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Item Name:</td>" . "\n";
            $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"item_name\"/></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt2\" colspan=\"2\" nowrap><input type=\"submit\" name=\"search_fun\" value=\"Search\"/></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "</table>" . "\n";
            $out .= "<input type=\"hidden\" name=\"do\" value=\"" . $_GET["do"] . "\"/>" . "\n";
            $out .= "</form>" . "\n";
            if( $search_fun != "" ) 
            {
                $account_name = isset($_GET["account_name"]) ? antiject(trim($_GET["account_name"])) : "";
                $character_name = isset($_GET["character_name"]) ? antiject(trim($_GET["character_name"])) : "";
                $item_name = isset($_GET["item_name"]) ? antiject(trim($_GET["item_name"])) : "";
                if( $account_name == "" && $character_name == "" && $item_name == "" ) 
                {
                    $enable_exit = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You must enter either an account name, character name or an item name to search</p>";
                }

                if( $enable_exit != true ) 
                {
                    $search_query = " WHERE ";
                    if( $account_name != "" ) 
                    {
                        connectuserdb();
                        $user_sql = "" . "SELECT Serial FROM tbl_UserAccount WHERE id = convert(binary,'" . $account_name . "')";
                        if( !($user_result = mssql_query($user_sql, $user_dbconnect)) ) 
                        {
                            $enable_exit = true;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to obtain account information</p>";
                        }
                        else
                        {
                            if( mssql_rows_affected($user_dbconnect) <= 0 ) 
                            {
                                $enable_exit = true;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">No such account name found in the database</p>";
                            }
                            else
                            {
                                $user_info = mssql_fetch_array($user_result);
                                $account_serial = $user_info["Serial"];
                                $search_query .= "" . " R.redeem_account_id = '" . $account_serial . "' ";
                            }

                        }

                    }

                    if( $character_name != "" && $enable_exit != true ) 
                    {
                        if( $account_name != "" ) 
                        {
                            $search_query .= " OR ";
                        }

                        connectdatadb();
                        $char_sql = "" . "SELECT Serial FROM tbl_base WHERE Name = '" . $character_name . "'";
                        if( !($char_result = mssql_query($char_sql, $data_dbconnect)) ) 
                        {
                            $enable_exit = true;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to obtain character information</p>";
                        }
                        else
                        {
                            if( mssql_rows_affected($data_dbconnect) <= 0 ) 
                            {
                                $enable_exit = true;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">No such character name found in the database</p>";
                            }
                            else
                            {
                                $char_info = mssql_fetch_array($char_result);
                                $character_serial = $char_info["Serial"];
                                $search_query .= "" . " R.redeem_char_id = '" . $character_serial . "' ";
                            }

                            @mssql_free_result($char_result);
                        }

                    }

                    if( $item_name != "" && $enable_exit != true ) 
                    {
                        if( $account_name != "" || $character_name != "" ) 
                        {
                            $search_query .= " OR ";
                        }

                        connectgamecpdb();
                        $item_sql = "" . "SELECT item_id FROM gamecp_shop_items WHERE item_name = '" . $item_name . "'";
                        if( !($item_result = mssql_query($item_sql, $gamecp_dbconnect)) ) 
                        {
                            $enable_exit = true;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to obtain item information</p>";
                        }
                        else
                        {
                            if( mssql_rows_affected($gamecp_dbconnect) <= 0 ) 
                            {
                                $enable_exit = true;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">No such item found in the database</p>";
                            }
                            else
                            {
                                $item_info = mssql_fetch_array($item_result);
                                $item_id = $item_info["item_id"];
                                $search_query .= "" . " R.redeem_item_id = '" . $item_id . "' ";
                            }

                            @mssql_free_result($item_result);
                        }

                    }

                    if( $enable_exit == true ) 
                    {
                        $search_query = "";
                        $query_p2 .= " WHERE ";
                    }
                    else
                    {
                        $query_p2 .= " AND ";
                        gamecp_log(0, $userdata["username"], "" . "ADMIN - MANAGE REDEEM - SEARCH - Account Name: " . $account_name . " | Character Name: " . $character_name . " | Item Name: " . $item_name, 1);
                    }

                }
                else
                {
                    $query_p2 .= " WHERE ";
                }

            }
            else
            {
                $query_p2 .= " WHERE ";
            }

            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\" nowrap>#</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Date</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Item Name</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Account Name</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Character Name</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Amount</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Item Price</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>GP After Purchase</td>" . "\n";
            $out .= "\t</tr>" . "\n";
            connectgamecpdb();
            $query_p1 = "" . "SELECT \r\n\t\tR.redeem_item_id, R.redeem_char_id, R.redeem_price, R.redeem_item_id, R.redeem_total_gp, R.redeem_time, R.redeem_item_amount, R.redeem_item_name, I.item_name, I.item_delete\r\n\t\tFROM \r\n\t\t\tgamecp_redeem_log AS R \r\n\t\t\tLEFT JOIN \r\n\t\t\tgamecp_shop_items AS I\r\n\t\t\tON R.redeem_item_id = I.item_id\r\n\t\t\t" . $search_query;
            $query_p2 .= "" . "R.redeem_id NOT IN ( SELECT TOP [OFFSET] R.redeem_id FROM \r\n\t\t\tgamecp_redeem_log AS R \r\n\t\t\tLEFT JOIN \r\n\t\t\tgamecp_shop_items AS I\r\n\t\t\t\tON R.redeem_item_id = I.item_id\r\n\t\t\t" . $search_query . "\r\n\t\t\tORDER BY R.redeem_id DESC) ORDER BY R.redeem_id DESC";
            include("./includes/pagination/ps_pagination.php");
            $url = str_replace("&page_gen=" . $page_gen, "", $_SERVER["REQUEST_URI"]);
            $filename = $_GET["do"] . "_" . md5($query_p1);
            if( !($query_count = readCache($filename . ".cache", 5)) ) 
            {
                $query_count = mssql_query("" . "SELECT COUNT(redeem_item_id) AS Count FROM \r\n\t\t\tgamecp_redeem_log AS R \r\n\t\t\tLEFT JOIN \r\n\t\t\tgamecp_shop_items AS I\r\n\t\t\tON R.redeem_item_id = I.item_id " . $search_query);
                $query_count = mssql_fetch_array($query_count);
                $query_count = $query_count["Count"];
                writeCache($query_count, $filename . ".cache");
                @mssql_free_result($query_count);
            }

            $pager = new PS_Pagination($gamecp_dbconnect, $query_p1, $query_p2, $top_limit, $max_pages, $url, $query_count);
            $rs = $pager->paginate();
            if( $gen == 1 ) 
            {
                $i = 1;
            }
            else
            {
                $i += ($gen - 1) * $top_limit + 1;
            }

            connectdatadb();
            while( $row = mssql_fetch_array($rs) ) 
            {
                $char = mssql_query("SELECT Account,Name,DCK FROM tbl_base WHERE Serial = '" . $row["redeem_char_id"] . "'");
                $char = mssql_fetch_array($char);
                $char_name = $char["Name"] != "" ? $char["Name"] : "<i>Unknown</i>";
                if( $char["DCK"] == 1 ) 
                {
                    $char_name = "<i>" . $char_name . "</i>";
                }

                if( $row["item_delete"] == 1 ) 
                {
                    $item_name = "<i>" . $row["item_name"] . "</i>";
                }
                else
                {
                    if( $row["redeem_item_name"] != "" ) 
                    {
                        $item_name = $row["redeem_item_name"];
                    }
                    else
                    {
                        $item_name = "<i>Unknown ID: " . $row["redeem_item_id"] . "</i>";
                    }

                }

                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt2\" style=\"text-align: center;\" nowrap>" . $i . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . date("d/m/y h:i:s A", $row["redeem_time"]) . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . $item_name . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . ($char["Account"] != "" ? $char["Account"] : "<i>Unknown</i>") . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . $char_name . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . $row["redeem_item_amount"] . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . number_format($row["redeem_price"], 2) . " GP</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . number_format($row["redeem_total_gp"], 2) . " GP</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $i++;
            }
            if( mssql_num_rows($rs) <= 0 ) 
            {
                $out .= "\t\t<tr>" . "\n";
                $out .= "\t\t\t<td class=\"alt1\" colspan=\"8\" style=\"text-align: center; font-weight: bold;\">No redeem logs found.</td>" . "\n";
                $out .= "\t\t</tr>" . "\n";
            }
            else
            {
                $out .= "\t\t<tr>" . "\n";
                $out .= "\t\t\t<td class=\"alt2\" colspan=\"8\" style=\"text-align: center; font-weight: bold;\">" . $pager->renderFullNav() . "</td>" . "\n";
                $out .= "\t\t</tr>" . "\n";
            }

            $out .= "</table>";
            @mssql_free_result($rs);
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


