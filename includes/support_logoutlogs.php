<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Logs"]["Logout Logs"] = $file;
}
else
{
    $lefttitle = "Support Desk - Log Out Logs";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_POST["search_fun"]) ? $_POST["search_fun"] : "";
            $account_name = isset($_POST["account_name"]) ? antiject($_POST["account_name"]) : "";
            $account_serial = isset($_POST["account_serial"]) ? antiject($_POST["account_serial"]) : "";
            $account_ip = isset($_POST["account_ip"]) ? antiject($_POST["account_ip"]) : "";
            $todays_date = get_date(time());
            $account_ser = "";
            $enable_exit = false;
            if( empty($page) ) 
            {
                $out .= "<form method=\"post\" action=\"" . $script_name . "?do=support_logoutlogs\">";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Look up a user</b></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Account Name:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_name\" value=\"" . $account_name . "\"/></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">OR Account Serial:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_serial\" value=\"" . $account_serial . "\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">OR IP Address:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_ip\" value=\"" . $account_ip . "\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\"># of days to look back:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_dayback\" value=\"" . (isset($_POST["account_dayback"]) ? $_POST["account_dayback"] : "0") . "\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td colspan=\"2\"><input type=\"submit\" value=\"Search\" name=\"search_fun\" /></td>";
                $out .= "</tr>";
                $out .= "</table>";
                $out .= "</form>";
                if( $search_fun != "" ) 
                {
                    $out .= "<br/><br/>";
                    $account_serial = is_numeric($_POST["account_serial"]) ? $_POST["account_serial"] : "";
                    $account_name = isset($_POST["account_name"]) ? $_POST["account_name"] : "";
                    $account_ip = isset($_POST["account_ip"]) ? $_POST["account_ip"] : "";
                    $account_dayback = is_numeric($_POST["account_dayback"]) ? $_POST["account_dayback"] : "0";
                    if( $account_serial == "" && $account_name == "" && $account_ip == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>Sorry, make sure you filled in either the name or email or ip and days back for the account</b></p>";
                    }

                    if( $account_name != "" ) 
                    {
                        $account_add = "U.id LIKE CONVERT(binary,\"" . $account_name . "\")";
                    }

                    if( $account_serial != "" ) 
                    {
                        if( $account_name != "" ) 
                        {
                            $or_s = " OR ";
                        }

                        $account_ser = "U.serial = \"" . $account_serial . "\"";
                    }

                    if( $account_ip != "" ) 
                    {
                        if( $account_name != "" ) 
                        {
                            $or_i = " OR ";
                        }

                        if( $account_ser != "" ) 
                        {
                            $or_i = " OR ";
                        }

                        if( !preg_match("/%/", $account_ip) ) 
                        {
                            $account_ip = "B.ip = '" . $account_ip . "'";
                        }
                        else
                        {
                            $account_ip = "B.ip LIKE '" . $account_ip . "'";
                        }

                    }

                    if( $enable_exit != true ) 
                    {
                        $account_serial = antiject($account_serial);
                        $out .= "<b>These results are " . $account_dayback . " day(s) back from today</b>";
                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                        $out .= "<tr>";
                        $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Account Serial</b></td>";
                        $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Account</b></td>";
                        $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Login Date</b></td>";
                        $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Logout Date</b></td>";
                        $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>IP Address</b></td>";
                        $out .= "</tr>";
                        connectuserdb();
                        if( !($result = @mssql_query("SELECT TOP 50\r\n\t\t\t\t\tCONVERT(varchar, U.id) AS username, B.nAccountSerial, B.dtLoginDate, B.dtLogoutDate, B.ip\r\n\t\t\t\t\tFROM \r\n\t\t\t\t\ttbl_UserLogout_Log" . @get_date(@time(), $account_dayback) . " AS B\r\n\t\t\t\t\tINNER JOIN\r\n\t\t\t\t\ttbl_UserAccount AS U\r\n\t\t\t\t\tON U.serial = B.nAccountSerial\r\n\t\t\t\t\tWHERE " . $account_add . $or_s . $account_ser . $or_i . $account_ip . " ORDER BY B.dtLogoutDate DESC", $user_dbconnect)) ) 
                        {
                            $out .= "<tr>";
                            $out .= "<td class=\"alt1\" colspan=\"5\" style=\"text-align: center;\">";
                            $out .= "<b>Sorry cannot find any logs for this day!</b>";
                            $out .= "</td>";
                            $out .= "</tr>";
                        }
                        else
                        {
                            while( $row = mssql_fetch_array($result) ) 
                            {
                                $username = ereg_replace("" . ";\$", "", $row["username"]);
                                $username = ereg_replace("\\\\", "", $username);
                                $out .= "<tr>";
                                $out .= "<td class=\"alt2\" width=\"5%\" style=\"font-size: 10px;\" nowrap>" . $row["nAccountSerial"] . "</td>";
                                $out .= "<td class=\"alt1\" width=\"10%\" style=\"font-size: 10px;\" nowrap>" . $username . "</td>";
                                $out .= "<td class=\"alt1\" width=\"10%\" style=\"font-size: 10px;\" nowrap>" . date("D M jS, Y h:i A", strtotime(preg_replace("/:[0-9][0-9][0-9]/", "", $row["dtLoginDate"]))) . "</td>";
                                $out .= "<td class=\"alt1\" width=\"10%\" style=\"font-size: 10px;\" nowrap>" . date("D M jS, Y h:i A", strtotime(preg_replace("/:[0-9][0-9][0-9]/", "", $row["dtLogoutDate"]))) . "</td>";
                                $out .= "<td class=\"alt1\" width=\"15%\" style=\"font-size: 10px;\" nowrap>" . $row["ip"] . "</td>";
                                $out .= "</td>";
                            }
                            if( mssql_num_rows($result) <= 0 ) 
                            {
                                $out .= "<tr>";
                                $out .= "<td class=\"alt1\" colspan=\"5\" style=\"text-align: center;\">";
                                $out .= "<b>No results found</b>";
                                $out .= "</td>";
                                $out .= "</tr>";
                            }

                            $out .= "</table>";
                        }

                        gamecp_log(0, $userdata["username"], "" . "SUPPORT - LOG OUT LOGS - Searched for: " . $account_name . " or " . $account_serial . " or " . $account_ip, 1);
                        @mssql_free_result($result);
                        return 1;
                    }

                }
                else
                {
                    $out .= "<br/><br/>";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                    $out .= "<tr>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Account Serial</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Account</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Login Date</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>Logout Date</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\"><b>IP Address</b></td>";
                    $out .= "</tr>";
                    connectuserdb();
                    $result = @mssql_query("SELECT TOP 50\r\n\t\t\t\t\tCONVERT(varchar, U.id) AS username, B.nAccountSerial, B.dtLoginDate, B.dtLogoutDate, B.ip\r\n\t\t\t\t\tFROM \r\n\t\t\t\t\ttbl_UserLogout_Log" . $todays_date . " AS B\r\n\t\t\t\t\tINNER JOIN\r\n\t\t\t\t\ttbl_UserAccount AS U\r\n\t\t\t\t\tON U.serial = B.nAccountSerial\r\n\t\t\t\t\tORDER BY B.dtLogoutDate DESC", $user_dbconnect);
                    while( $row = @mssql_fetch_array($result) ) 
                    {
                        $username = ereg_replace("" . ";\$", "", $row["username"]);
                        $username = ereg_replace("\\\\", "", $username);
                        $out .= "<tr>";
                        $out .= "<td class=\"alt2\" width=\"5%\" style=\"font-size: 10px;\" nowrap>" . $row["nAccountSerial"] . "</td>";
                        $out .= "<td class=\"alt1\" width=\"10%\" style=\"font-size: 10px;\" nowrap>" . $username . "</td>";
                        $out .= "<td class=\"alt1\" width=\"10%\" style=\"font-size: 10px;\" nowrap>" . $row["dtLoginDate"] . "</td>";
                        $out .= "<td class=\"alt1\" width=\"10%\" style=\"font-size: 10px;\" nowrap>" . $row["dtLogoutDate"] . "</td>";
                        $out .= "<td class=\"alt1\" width=\"15%\" style=\"font-size: 10px;\" nowrap>" . $row["ip"] . "</td>";
                        $out .= "</td>";
                    }
                    if( @mssql_num_rows($result) <= 0 ) 
                    {
                        $out .= "<tr>";
                        $out .= "<td class=\"alt1\" colspan=\"5\" style=\"text-align: center;\">";
                        $out .= "<b>No logs found today</b>";
                        $out .= "</td>";
                    }

                    $out .= "</table>";
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


