<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Server"]["Banned Users"] = $file;
}
else
{
    $lefttitle = "Latest Banned Users";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
        $page_gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
        $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
        $account_name = isset($_GET["account_name"]) ? $_GET["account_name"] : "";
        $account_name = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\\*|--|\\\\)/"), "", $account_name);
        $search_query = "";
        $query_p2 = "";
        $enable_exit = false;
        $top_limit = 50;
        $max_pages = 10;
        $enable_search = false;
        connectuserdb();
        $out .= "<form method=\"GET\">" . "\n";
        $out .= "<p style=\"text-align: right; font-weight: bold; margin: 0; padding: 0 2px 4px 0;\">Look up a banned account: <input type=\"text\" name=\"account_name\" value=\"" . $account_name . "\"/> <input type=\"submit\" name=\"search_fun\" value=\"Search\" /></p>" . "\n";
        $out .= "<input type=\"hidden\" name=\"do\" value=\"" . $_GET["do"] . "\"/>" . "\n";
        $out .= "</form>" . "\n";
        if( $search_fun != "" ) 
        {
            if( $account_name == "" ) 
            {
                $enable_exit = true;
                $out .= "<p align='center'><b>Enter a valid account name</b></p>";
            }

            if( preg_match("/[^a-zA-Z0-9_-]/i", $account_name) ) 
            {
                $enable_exit = true;
                $out .= "<p align='center'><b>Enter a valid account name (2)</b></p>";
            }

            if( $enable_exit != true ) 
            {
                $search_query = " WHERE ";
                if( $account_name != "" ) 
                {
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
                        }
                        else
                        {
                            $user_info = mssql_fetch_array($user_result);
                            $account_serial = $user_info["Serial"];
                            $search_query .= "" . " B.nAccountSerial = '" . $account_serial . "' ";
                            $enable_search = true;
                        }

                    }

                    @mssql_free_result($user_result);
                }

                if( $enable_exit == true ) 
                {
                    $search_query = "";
                    $query_p2 .= " WHERE ";
                }
                else
                {
                    $query_p2 .= " AND ";
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

        $out .= "<table class=\"tborder\" cellpadding=\"5\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
        $out .= "<tr>";
        $out .= "<td class=\"thead\" style=\"padding: 4px; text-align: center;\" nowrap><b>#</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Start Date</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>End Date</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Account Name</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Banned By</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Reason for ban</b></td>";
        $out .= "</tr>";
        $query_p1 = "" . "SELECT\r\n\tCONVERT(varchar, U.id) AS username, B.nAccountSerial, B.dtStartDate AS startdate, B.nPeriod, B.nKind, B.szReason, B.GMWriter\r\n\tFROM \r\n\t\ttbl_UserBan AS B\r\n\tINNER JOIN\r\n\t\ttbl_UserAccount AS U\r\n\tON U.serial = B.nAccountSerial\r\n\t" . $search_query;
        $query_p2 .= "" . "B.nAccountSerial NOT IN ( SELECT TOP [OFFSET] B.nAccountSerial \r\n\tFROM \r\n\t\ttbl_UserBan AS B\r\n\tINNER JOIN\r\n\t\ttbl_UserAccount AS U\r\n\tON U.serial = B.nAccountSerial\r\n\t" . $search_query . "\r\n\tORDER BY B.dtStartDate DESC) ORDER BY B.dtStartDate DESC";
        if( !$enable_search ) 
        {
            include("./includes/pagination/ps_pagination.php");
        }

        if( $page_gen == 1 ) 
        {
            $i = 1;
        }
        else
        {
            $i = $page_gen * $top_limit - ($top_limit - 1);
        }

        if( !$enable_search ) 
        {
            $url = str_replace("&page_gen=" . $page_gen, "", $_SERVER["REQUEST_URI"]);
            $pager = new PS_Pagination($user_dbconnect, $query_p1, $query_p2, $top_limit, $max_pages, $url);
            $rs = $pager->paginate();
        }
        else
        {
            $rs = mssql_query($query_p1) or exit( "Seems we got a problem with the query portion" );
        }

        connectdatadb();
        while( $row = mssql_fetch_array($rs) ) 
        {
            $username = ereg_replace("" . ";\$", "", $row["username"]);
            $username = ereg_replace("\\\\", "", $username);
            $startdate = preg_replace("/:[0-9][0-9][0-9]/", "", $row["startdate"]);
            $startdate = strtotime($startdate);
            $enddate = $row["nPeriod"] * 3600;
            $enddate = $startdate + $enddate;
            $enddate = date("d/m/Y h:i A", $enddate);
            $startdate = date("d/m/Y h:i A", $startdate);
            if( $row["GMWriter"] == "WS0" ) 
            {
                $row["szReason"] = "Auto-banned by FireGuard";
            }

            $gm_banned = $row["GMWriter"];
            if( $gm_banned == "WS0" ) 
            {
                $gm_banned = "-";
            }

            if( !preg_match("/TEMP/", $row["szReason"]) ) 
            {
                $out .= "<tr>";
                $out .= "<td class=\"alt2\" style=\"font-size: 10px; text-align: center; font-weight: bold;\" width=\"1%\">" . $i . "</td>";
                $out .= "<td class=\"alt2\" style=\"font-size: 10px; font-weight: bold;\" width=\"10%\" nowrap>" . $startdate . "</td>";
                $out .= "<td class=\"alt2\" style=\"font-size: 10px; font-weight: bold;\" width=\"10%\" nowrap>" . $enddate . "</td>";
                $out .= "<td class=\"alt2\" style=\"font-size: 10px; font-weight: bold;\" width=\"30%\" nowrap>" . $username . "</td>";
                $out .= "<td class=\"alt2\" style=\"font-size: 10px; font-weight: bold;\" width=\"30%\" nowrap>" . $gm_banned . "</td>";
                $out .= "<td class=\"alt2\" style=\"font-size: 10px; font-weight: bold;\" width=\"30%\" nowrap>" . $row["szReason"] . "</td>";
                $out .= "</td>";
                $char_query = "SELECT Serial, Name, DeleteName FROM tbl_base WHERE DCK = '0' AND AccountSerial = '" . $row["nAccountSerial"] . "' ORDER BY Serial DESC";
                $char_result = mssql_query($char_query) or exit( "A user might not exist? (" . $row["nAccountSerial"] . ") ERROR" );
                while( $char = mssql_fetch_array($char_result) ) 
                {
                    $out .= "<tr>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 10px; text-align: center; font-weight: bold;\">&raquo;</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" colspan=\"5\" nowrap>" . $char["Name"] . "</td>";
                    $out .= "</tr>";
                }
                @mssql_free_result($char_result);
            }

            $i++;
        }
        if( mssql_num_rows($rs) <= 0 ) 
        {
            $out .= "<tr>" . "\n";
            $out .= "<td colspan=\"6\" style=\"text-align: center;\">No banned user(s) found</td>" . "\n";
            $out .= "</tr>" . "\n";
        }
        else
        {
            if( !$enable_search ) 
            {
                $out .= "<tr>";
                $out .= "<td colspan=\"6\" style=\"text-align: center;\">" . $pager->renderFullNav() . "</td>";
                $out .= "</tr>";
            }

        }

        $out .= "</table>";
        @mssql_free_result($rs);
    }
    else
    {
        $out .= $lang["invalid_page_load"];
    }

}


