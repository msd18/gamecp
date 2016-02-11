<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Server Admin"]["GM Accounts"] = $file;
}
else
{
    $lefttitle = "Support Desk - Admin GM Accounts/Characters";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            if( empty($page) ) 
            {
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Status</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Serial</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Account Name</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Password</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Real Name</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Create Date</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Last Connect IP</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Last Login Date</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Last Logoff Date</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Grade</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Sub Grade</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Expire Date</b></td>";
                $out .= "</tr>";
                connectuserdb();
                $result = mssql_query("SELECT \r\n\t\t\t\t\tSerial, convert(varchar,id) as username,  convert(varchar,pw) as password, Grade, RealName, LastConnIP, CreateDT, LastLoginDT, LastLogoffDT, SubGrade, ExpireDT\r\n\t\t\t\t\tFROM \r\n\t\t\t\t\ttbl_StaffAccount\r\n\t\t\t\t\tORDER BY LastLoginDT DESC");
                connectdatadb();
                while( $row = mssql_fetch_array($result) ) 
                {
                    $t_login = strtotime($row["LastLoginDT"]);
                    $t_logout = strtotime($row["LastLogoffDT"]);
                    $t_cur = time();
                    $t_maxlogin = $t_login + 2592000;
                    if( $t_login <= $t_logout ) 
                    {
                        $status = "offline";
                    }
                    else
                    {
                        if( $t_maxlogin < $t_cur ) 
                        {
                            $status = "offline";
                        }
                        else
                        {
                            $status = "online";
                        }

                    }

                    $username = ereg_replace("" . ";\$", "", $row["username"]);
                    $username = ereg_replace("\\\\", "", $username);
                    $password = ereg_replace("" . ";\$", "", $row["password"]);
                    $password = ereg_replace("\\\\", "", $password);
                    $out .= "<tr>";
                    $out .= "<td class=\"alt2\" width=\"5%\" style=\"text-align: center; font-size: 10px; font-weight: bold;\" nowrap><img src=\"./includes/images/" . $status . ".gif\" /></td>";
                    $out .= "<td class=\"alt2\" width=\"5%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $row["Serial"] . "</td>";
                    $out .= "<td class=\"alt2\" width=\"10%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $username . "</td>";
                    $out .= "<td class=\"alt2\" width=\"10%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $password . "</td>";
                    $out .= "<td class=\"alt2\" width=\"10%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $row["RealName"] . "</td>";
                    $out .= "<td class=\"alt2\" width=\"15%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $row["CreateDT"] . "</td>";
                    $out .= "<td class=\"alt2\" width=\"10%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $row["LastConnIP"] . "</td>";
                    $out .= "<td class=\"alt2\" width=\"10%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $row["LastLoginDT"] . "</td>";
                    $out .= "<td class=\"alt2\" width=\"10%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $row["LastLogoffDT"] . "</td>";
                    $out .= "<td class=\"alt2\" width=\"10%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $row["Grade"] . "</td>";
                    $out .= "<td class=\"alt2\" width=\"10%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $row["SubGrade"] . "</td>";
                    $out .= "<td class=\"alt2\" width=\"10%\" style=\"font-size: 10px; font-weight: bold;\" nowrap>" . $row["ExpireDT"] . "</td>";
                    $out .= "</tr>";
                    $char_query = "SELECT Serial, Name, DeleteName FROM tbl_base WHERE AccountSerial = '" . $row["Serial"] . "' ORDER BY Serial DESC";
                    $char_result = mssql_query($char_query);
                    while( $char = mssql_fetch_array($char_result) ) 
                    {
                        $out .= "<tr>";
                        $out .= "<td class=\"alt1\" width=\"5%\" style=\"font-size: 10px;\" nowrap>" . $char["Serial"] . "</td>";
                        if( $char["DeleteName"] == "*" ) 
                        {
                            $out .= "<td colspan=\"11\" class=\"alt1\" width=\"5%\" style=\"font-size: 10px;\" nowrap>" . $char["Name"] . "</td>";
                        }
                        else
                        {
                            $out .= "<td colspan=\"11\" class=\"alt1\" width=\"5%\" style=\"font-size: 10px;\" nowrap><i>Deleted</i> " . $char["DeleteName"] . "</td>";
                        }

                        $out .= "</tr>";
                    }
                    @mssql_free_result($char_result);
                }
                $out .= "</table>";
                @mssql_free_result($result);
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


