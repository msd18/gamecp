<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Logs"]["Game CP Logs"] = $file;
}
else
{
    $lefttitle = "Game CP - Logs";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        $logTypes_array = array( "--- N/A ---", "GAMECP - CHANGE PASSWORD", "GAMECP - DONATE", "GAMECP - DELETE CHARACTER", "SUPPORT - USER INFO", "SUPPORT - LOG OUT LOGS", "ADMIN - MAIL LOGS", "ADMIN - CHAR LOOK UP", "PAYPAL - <b>REVERSED</b>", "PAYPAL - <b>CANCELED REVERSAL</b>", "PAYPAL - SUCCESSFULL PAYMENT", "PAYPAL - ADDED CREDITS", "PAYPAL - DUPLICATE TXN ID", "PAYPAL - INVALID BUSINESS", "PAYPAL - <b>INCOMPLETE</b>", "PAYPAL - PAYMENT INVALID", "PAYPAL - PAYMENT FAILED", "PAYPAL - Unable to connect to www.paypal.com", "GAMECP - PASSWORD RECOVERY", "GAMECP - ACCOUNT INFO", "ADMIN - ITEM EDIT", "ADMIN - BANK EDIT", "ADMIN - ITEM SEARCH", "ADMIN - ITEM LIST", "ADMIN - MANAGE BANS - ADDED", "ADMIN - MANAGE BANS - UPDATED", "ADMIN - MANAGE BANS - UNBAN", "ADMIN - CHARACTER EDIT", "SUPER ADMIN - PERMISSIONS", "SUPER ADMIN - CONFIG", "ADMIN - MANAGE USERS", "ADMIN - MANAGE ITEMS - UPDATED", "ADMIN - MANAGE ITEMS - ADDED", "ADMIN - MANAGE ITEMS - DELETED", "ADMIN - MANAGE CATEGORIES", "GAMECP - CHANGE CHAR NAME", "ADMIN - MANAGE REDEEM", "ADMIN - VOTE SITES", "GOOGLE - Authentication Failure", "GOOGLE - ERROR", "GOOGLE - NEW ORDER", "GOOGLE - DUPLICATE ORDER", "GOOGLE - PAYMENT DECLINED", "GOOGLE - CANCELLED", "GOOGLE - CANCELLED BY GOOGLE", "GOOGLE - SUCCESSFUL ORDER", "GOOGLE - ADDED POINTS", "GOOGLE - NOT DELIVERED", "GOOGLE - CHARGEBACK", "GOOGLE - REFUND", "GOOGLE - ORDER REFUND ERROR", "GOOGLE - ORDER CHARGEBACK ERROR" );
        sort($logTypes_array);
        if( hasPermissions($do) ) 
        {
            $page = "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $page_gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
            $search_query = " ";
            $query_p2 = "";
            if( empty($page) ) 
            {
                $out .= "<table class=\"tborder\" cellpadding=\"5\" cellspacing=\"1\" border=\"0\" width=\"200\">" . "\n";
                $out .= "  <tr>";
                $out .= "\t<td class=\"level_0\" nowrap>Level 0</td>";
                $out .= "\t<td class=\"level_1\" nowrap>Level 1</td>";
                $out .= "\t<td class=\"level_2\" nowrap>Level 2</td>";
                $out .= "\t<td class=\"level_3\" nowrap>Level 3</td>";
                $out .= "\t<td class=\"level_4\" nowrap>Level 4</td>";
                $out .= "\t<td class=\"level_5\" nowrap>Level 5</td>";
                $out .= "  </tr>";
                $out .= "</table>";
                $out .= "<form method=\"get\" action=\"" . $script_name . "\">";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\">" . "\n";
                $out .= "\t\t<tr>";
                $out .= "\t\t\t<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Search the Logs</b></td>";
                $out .= "\t\t</tr>";
                $out .= "\t\t<tr>";
                $out .= "\t\t\t<td class=\"alt1\">Account Name:</td>";
                $out .= "\t\t\t<td class=\"alt2\"><input type=\"text\" name=\"account_name\"/></td>";
                $out .= "\t\t</tr>";
                $out .= "\t\t<tr>";
                $out .= "\t\t\t<td class=\"alt1\">Log Type:</td>";
                $out .= "\t\t\t<td class=\"alt2\">";
                $out .= "\t\t\t\t<select name=\"log_type\">";
                if( 0 < ($logType_count = count($logTypes_array)) ) 
                {
                    foreach( $logTypes_array as $key => $value ) 
                    {
                        $out .= "\t\t\t\t\t<option value=\"" . $key . "\">" . $value . "</option>";
                    }
                }

                $out .= "\t\t\t\t</select>";
                $out .= "\t\t\t</td>";
                $out .= "\t\t</tr>";
                $out .= "\t\t<tr>";
                $out .= "\t\t\t<td colspan=\"2\"><input type=\"hidden\" name=\"do\" value=\"admin_gamecp_logs\"/><input type=\"submit\" value=\"Look Up\" name=\"search_fun\" /></td>";
                $out .= "\t\t</tr>";
                $out .= "\t</table>";
                $out .= "</form>";
                $out .= "<br/>";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>ID</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Time</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Account</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Message</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>IP Address</b></td>";
                $out .= "</tr>";
                if( $search_fun != "" ) 
                {
                    $account_name = isset($_GET["account_name"]) ? antiject($_GET["account_name"]) : "";
                    $log_type = isset($_GET["log_type"]) && $_GET["log_type"] != 0 ? antiject($_GET["log_type"]) : "";
                    if( $account_name != "" || $log_type != "" ) 
                    {
                        $search_query = " WHERE ";
                        if( $account_name != "" ) 
                        {
                            $account_name = ereg_replace("" . ";\$", "", $account_name);
                            $account_name = ereg_replace("\\\\", "", $account_name);
                            $search_query .= "log_account = '" . $account_name . "'";
                        }

                        if( $log_type != "" ) 
                        {
                            if( $account_name != "" ) 
                            {
                                $search_query .= " AND ";
                            }

                            $search_query .= "log_message LIKE '" . $logTypes_array[$log_type] . "%'";
                        }

                        $query_p2 = " AND ";
                    }
                    else
                    {
                        $query_p2 = "WHERE ";
                    }

                }
                else
                {
                    $query_p2 = "WHERE ";
                }

                include("./includes/pagination/ps_pagination.php");
                connectgamecpdb();
                $query_text = "SELECT id, log_level, log_time, log_account, log_message, log_ip, log_page, log_browser FROM gamecp_log" . $search_query;
                $query_p2 .= "id NOT IN ( SELECT TOP [OFFSET] id FROM gamecp_log " . $search_query . " ORDER BY id DESC) ORDER BY id DESC";
                $filename = $_GET["do"] . "_" . md5($query_text);
                if( !($query_count = readCache($filename . ".cache", 5)) ) 
                {
                    $query_count_result = mssql_query("" . "SELECT COUNT(id) AS Count FROM gamecp_log " . $search_query);
                    $query_count = mssql_fetch_array($query_count_result);
                    $query_count = $query_count["Count"];
                    writeCache($query_count, $filename . ".cache");
                    @mssql_free_result($query_count_result);
                }

                $url = str_replace("&page_gen=" . $page_gen, "", $_SERVER["REQUEST_URI"]);
                $pager = new PS_Pagination($gamecp_dbconnect, $query_text, $query_p2, 50, 20, $url, $query_count);
                $rs = $pager->paginate();
                $out .= "Total number of logs found: " . number_format($pager->totalResults());
                while( $row = mssql_fetch_array($rs) ) 
                {
                    if( $row["log_level"] == 5 ) 
                    {
                        $class = "level_5";
                    }
                    else
                    {
                        if( $row["log_level"] == 4 ) 
                        {
                            $class = "level_4";
                        }
                        else
                        {
                            if( $row["log_level"] == 3 ) 
                            {
                                $class = "level_3";
                            }
                            else
                            {
                                if( $row["log_level"] == 2 ) 
                                {
                                    $class = "level_2";
                                }
                                else
                                {
                                    if( $row["log_level"] == 1 ) 
                                    {
                                        $class = "level_1";
                                    }
                                    else
                                    {
                                        $class = "level_0";
                                    }

                                }

                            }

                        }

                    }

                    if( $row["log_ip"] == "66.211.170.66" ) 
                    {
                        $ip = "PayPal Notify";
                    }
                    else
                    {
                        if( $row["log_ip"] == "74.125.64.136" ) 
                        {
                            $ip = "Google Notify";
                        }
                        else
                        {
                            $ip = $row["log_ip"];
                        }

                    }

                    $out .= "<tr>";
                    $out .= "<td class=\"" . $class . "\" style=\"text-align: center; font-size: 10px;\" nowrap>" . $row["id"] . "</td>";
                    $out .= "<td class=\"" . $class . "\" style=\"text-align: center; font-size: 10px;\" nowrap>" . date("d/m/y - h:i:s A", $row["log_time"]) . "</td>";
                    $out .= "<td class=\"" . $class . "\" style=\"font-size: 10px;\" nowrap>" . $row["log_account"] . "</td>";
                    $out .= "<td class=\"" . $class . "\" style=\"font-size: 10px;\" nowrap>" . $row["log_message"] . "</td>";
                    $out .= "<td class=\"" . $class . "\" style=\"text-align: center; font-size: 10px;\" nowrap>" . $ip . "</td>";
                    $out .= "</td>";
                }
                $out .= "</table>";
                $out .= "<br/>";
                $out .= $pager->renderFullNav();
                @mssql_free_result($rs);
            }
            else
            {
                $out .= $lang["invalid_page_id"];
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


