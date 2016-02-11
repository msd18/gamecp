<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Item Shop Admin"]["Manage Users"] = $file;
}
else
{
    $lefttitle = "Item Shop Admin - Manage Users";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $update = isset($_POST["update"]) ? $_POST["update"] : "";
            $page_gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
            $search_fun = isset($_POST["search_fun"]) ? $_POST["search_fun"] : "";
            $query_p2 = "";
            $search_query = "";
            $exit_process = 0;
            $account_name = isset($_POST["account_name"]) ? antiject($_POST["account_name"]) : "";
            $out .= "<form method=\"post\">" . "\n";
            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"thead\" colspan=\"2\">Look up a user</td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt2\">Account Name</td>" . "\n";
            $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"account_name\" value=\"" . $account_name . "\"/></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt1\" colspan=\"2\"><input type=\"submit\" name=\"search_fun\" value=\"Look up\"/></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "</table>" . "\n";
            $out .= "</form>" . "\n";
            $out .= "<br/>" . "\n";
            if( $update != "" ) 
            {
                $user_id = isset($_POST["user_id"]) && is_array($_POST["user_id"]) ? $_POST["user_id"] : "";
                $user_point = isset($_POST["user_points"]) && is_array($_POST["user_points"]) ? $_POST["user_points"] : "";
                if( $user_id != "" && $user_point != "" ) 
                {
                    $count = count($user_id);
                    for( $i = 0; $i < $count; $i++ ) 
                    {
                        $userid = is_int((int) $user_id[$i]) ? antiject((int) $user_id[$i]) : "";
                        $userpoint = is_int((int) $user_point[$i]) ? antiject((int) $user_point[$i]) : "";
                        if( $userid != "" && $userpoint != "" ) 
                        {
                            connectgamecpdb();
                            $select_user = "" . "SELECT user_points,user_account_id FROM gamecp_gamepoints WHERE user_id = '" . $userid . "'";
                            $select_result = mssql_query($select_user);
                            $user = mssql_fetch_array($select_result);
                            if( $user["user_points"] != $userpoint ) 
                            {
                                $update_points = "UPDATE gamecp_gamepoints SET user_points = '" . $userpoint . "' WHERE user_id = '" . $userid . "'";
                                if( !($update_points_result = mssql_query($update_points)) ) 
                                {
                                    $exit_process = 1;
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Unable to user game points!</p>";
                                }

                                gamecp_log(0, $userdata["username"], "ADMIN - MANAGE USERS - UPDATED - Account ID: " . $user["user_account_id"] . "" . " | New Points: " . $userpoint . " | Old Points: " . $user["user_points"], 0);
                            }

                            @mssql_free_result($select_result);
                        }

                    }
                    if( $exit_process != 1 ) 
                    {
                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">" . "\n";
                        $out .= "\t<tr>" . "\n";
                        $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\">Updated user game points</td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        $out .= "</table>" . "\n";
                        $out .= "<br/>" . "\n";
                    }

                }

            }

            if( $search_fun != "" ) 
            {
                if( $account_name == "" ) 
                {
                    $exit_process = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Please fill in a account name</p>";
                }
                else
                {
                    connectuserdb();
                    $sql = "SELECT Serial FROM tbl_UserAccount WHERE id = convert(binary,'" . $account_name . "')";
                    $result = mssql_query($sql);
                    $user_info = mssql_fetch_array($result);
                    $account_id = $user_info["Serial"];
                    @mssql_free_result($result);
                }

                if( $exit_process == 0 ) 
                {
                    if( $account_name != "" ) 
                    {
                        $search_query = " WHERE ";
                        $query_p2 .= " AND ";
                    }
                    else
                    {
                        $query_p2 .= "WHERE ";
                    }

                    if( $account_name != "" ) 
                    {
                        $search_query .= "" . " user_account_id = '" . $account_id . "'";
                    }

                }

            }
            else
            {
                $query_p2 .= " WHERE ";
            }

            if( $exit_process == 0 ) 
            {
                $out .= "<form method=\"post\">" . "\n";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\">ID</td>" . "\n";
                $out .= "\t\t<td class=\"thead\">Account Name</td>" . "\n";
                $out .= "\t\t<td class=\"thead\">Game Points</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                include("./includes/pagination/ps_pagination.php");
                connectgamecpdb();
                $query_p1 = "SELECT user_id, user_points, user_account_id FROM gamecp_gamepoints" . $search_query;
                $query_p2 .= "" . "user_id NOT IN ( SELECT TOP [OFFSET] user_id FROM gamecp_gamepoints" . $search_query . " ORDER BY user_id DESC) ORDER BY user_id DESC";
                $url = str_replace("&page_gen=" . $page_gen, "", $_SERVER["REQUEST_URI"]);
                $pager = new PS_Pagination($gamecp_dbconnect, $query_p1, $query_p2, 20, 10, $url);
                $rs = $pager->paginate();
                connectuserdb();
                while( $row = mssql_fetch_array($rs) ) 
                {
                    $sql = "SELECT convert(varchar,id) AS Name FROM tbl_UserAccount WHERE Serial = '" . $row["user_account_id"] . "'";
                    $result = mssql_query($sql);
                    $user_info = mssql_fetch_array($result);
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" style=\"text-align: center;\"><input type=\"hidden\" name=\"user_id[]\" value=\"" . $row["user_id"] . "\" />" . $row["user_id"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\">" . antiject($user_info["Name"]) . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"user_points[]\" value=\"" . $row["user_points"] . "\" /></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    @mssql_free_result($result);
                }
                if( 0 < mssql_num_rows($rs) ) 
                {
                    $out .= "<tr>" . "\n";
                    $out .= "\t<td class=\"alt2\" colspan=\"3\" style=\"text-align: center;\">" . $pager->renderFullNav() . "</td>" . "\n";
                    $out .= "</tr>" . "\n";
                    $out .= "<tr>" . "\n";
                    $out .= "\t<td class=\"alt2\" colspan=\"3\" style=\"text-align: center;\"><input type=\"hidden\" name=\"account_name\" value=\"" . $account_name . "\"/><input type=\"submit\" name=\"update\" value=\"Update Points\" /></td>" . "\n";
                    $out .= "</tr>" . "\n";
                }
                else
                {
                    $out .= "<tr>" . "\n";
                    $out .= "\t<td class=\"alt2\" colspan=\"3\" style=\"text-align: center;\">No users found</td>" . "\n";
                    $out .= "</tr>" . "\n";
                }

                $out .= "</table>" . "\n";
                $out .= "</form>" . "\n";
                @mssql_free_result($rs);
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


