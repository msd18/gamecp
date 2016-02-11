<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Support"]["New Accounts"] = $file;
}
else
{
    $lefttitle = "Support Desk - Lates Registered Accounts";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $search_query = " ";
            $query_p2 = "";
            if( empty($page) ) 
            {
                $out .= "<form method=\"get\" action=\"" . $script_name . "?do=support_newaccounts\">";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Look up an account/ip</b></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Account Name:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_name\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Last Logged Ip:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_ip\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td colspan=\"2\"><input type=\"hidden\" name=\"do\" value=\"support_newaccounts\" /><input type=\"submit\" value=\"Search\" name=\"search_fun\" /></td>";
                $out .= "</tr>";
                $out .= "</table>";
                $out .= "</form>";
                $out .= "<br/>" . "\n";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Account</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Time</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>IP Address</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Browser</b></td>";
                $out .= "</tr>";
                connectgamecpdb();
                if( $search_fun != "" ) 
                {
                    $account_name = isset($_GET["account_name"]) ? $_GET["account_name"] : "";
                    $account_ip = isset($_GET["account_ip"]) ? $_GET["account_ip"] : "";
                    if( $account_name != "" || $account_ip != "" ) 
                    {
                        $search_query = " WHERE ";
                        if( $account_name != "" ) 
                        {
                            $account_name = str_replace(" ", "", $account_name);
                            $account_name = ereg_replace("" . ";\$", "", $account_name);
                            $account_name = ereg_replace("\\\\", "", $account_name);
                            $search_query .= "reg_account = '" . $account_name . "' ";
                        }

                        if( $account_ip != "" ) 
                        {
                            $account_ip = str_replace(" ", "", $account_ip);
                            $account_ip = ereg_replace("" . ";\$", "", $account_ip);
                            $account_ip = ereg_replace("\\\\", "", $account_ip);
                            if( !preg_match("/%/", $account_ip) ) 
                            {
                                if( $account_name != "" ) 
                                {
                                    $search_query .= " AND ";
                                }

                                $search_query .= "reg_ip LIKE '%" . $account_ip . "%' ";
                            }
                            else
                            {
                                $search_query .= "reg_ip = '" . $account_ip . "' ";
                            }

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
                $query_text = "SELECT \r\n\t\t\tid, reg_account, reg_ip, reg_time, reg_browser\r\n\t\t\tFROM gamecp_registration_log" . $search_query;
                $query_p2 .= "id NOT IN ( SELECT TOP [OFFSET] id FROM gamecp_registration_log ORDER BY id DESC) ORDER BY id DESC";
                $pager = new PS_Pagination($gamecp_dbconnect, $query_text, $query_p2, 25, 5, "" . $script_name . "?do=support_newaccounts");
                $rs = $pager->paginate();
                while( $row = mssql_fetch_array($rs) ) 
                {
                    $out .= "<tr>";
                    $out .= "<td class=\"alt2\" style=\"font-size: 10px;\" width=\"2%\" nowrap>" . $row["reg_account"] . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" width=\"10%\" nowrap>" . date("d/m/y h:i:sA", $row["reg_time"]) . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" width=\"8%\" nowrap>" . $row["reg_ip"] . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" width=\"20%\" nowrap>" . $row["reg_browser"] . "</td>";
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


