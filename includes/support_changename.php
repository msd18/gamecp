<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Logs"]["Name Changes Logs"] = $file;
}
else
{
    $lefttitle = "Support Desk - Name Changes Logs";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_POST["search_fun"]) ? $_POST["search_fun"] : "";
            if( empty($page) ) 
            {
                connectgamecpdb();
                $query_text = "SELECT \r\n\t\t\tid, username_charid, username_oldname, username_newname, username_ip\r\n\t\t\tFROM gamecp_username_log\r\n\t\t\tORDER BY id DESC";
                $result = mssql_query($query_text);
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Char ID</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>OLD NAME</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>NEW NAME</b></td>";
                $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>IP Address</b></td>";
                $out .= "</tr>";
                while( $row = mssql_fetch_array($result) ) 
                {
                    $out .= "<tr>";
                    $out .= "<td class=\"alt2\" width=\"1%\" nowrap>" . $row["username_charid"] . "</td>";
                    $out .= "<td class=\"alt1\" width=\"10%\" nowrap>" . $row["username_oldname"] . "</td>";
                    $out .= "<td class=\"alt1\" width=\"10%\" nowrap>" . $row["username_newname"] . "</td>";
                    $out .= "<td class=\"alt1\" width=\"15%\" nowarp>" . $row["username_ip"] . "</td>";
                    $out .= "</td>";
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


