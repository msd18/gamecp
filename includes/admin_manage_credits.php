<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Item Shop Admin"]["Manage Credits"] = $file;
}
else
{
    $lefttitle = "Item Shop Admin - Manage Credits";
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
            $account_id = false;
            $user_pass = false;
            $account_namex = false;
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
                $user_id = isset($_POST["user_id"]) && is_array($_GET["user_id"]) ? $_POST["user_id"] : "";
                $user_point = isset($_POST["user_points"]) && is_array($_GET["user_points"]) ? $_POST["user_points"] : "";
                if( $user_id != "" && $user_point != "" ) 
                {
                    $count = count($user_id);
                    for( $i = 0; $i < $count; $i++ ) 
                    {
                        $userid = is_int((int) $user_id[$i]) ? antiject((int) $user_id[$i]) : "";
                        $userpoint = is_int((int) $user_point[$i]) ? antiject($user_point[$i]) : "";
                        if( $userid != "" && $userpoint != "" ) 
                        {
                            connectgamecpdb();
                            $select_user = "" . "SELECT userId,cashBalance FROM BillCrux.dbo.tblUserInfo WHERE userId = '" . $userid . "'";
                            $select_result = mssql_query($select_user);
                            $user = mssql_fetch_array($select_result);
                            if( $user["cashBalance"] != $userpoint ) 
                            {
                                $update_points = "UPDATE BillCrux.dbo.tblUserInfo SET cashBalance = '" . $user_point[$i] . "' WHERE userId = '" . $user_id[$i] . "'";
                                if( !($update_points_result = mssql_query($update_points)) ) 
                                {
                                    $exit_process = 1;
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Unable to user game points!</p>";
                                }

                                gamecp_log(0, $userdata["username"], "ADMIN - MANAGE CREDITS - UPDATED - Account ID: " . $user["userId"] . "" . " | New Points: " . $userpoint . " | Old Points: " . $user["cashBalance"], 0);
                            }

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
                    if( $account_name[0] == "!" ) 
                    {
                        $sql = "SELECT \r\n\t\t\t\tAccountName = CAST(id AS varchar(255)), Serial, Pass = CAST(PW as varchar(255)) \r\n\t\t\t\tFROM \r\n\t\t\t\ttbl_StaffAccount\r\n\t\t\t\tWHERE id = convert(binary,'" . $account_name . "')";
                        $result = mssql_query($sql);
                        $user_info = mssql_fetch_array($result);
                        $account_id = $user_info["Serial"];
                        $user_pass = $user_info["Pass"];
                        $account_namex = antiject($user_info["AccountName"]);
                        @mssql_free_result($result);
                    }
                    else
                    {
                        $sql = "SELECT \r\n\t\t\t\tAccountName = CAST(L.id AS varchar(255)), U.Serial, Pass = CAST(L.Password as varchar(255)) \r\n\t\t\t\tFROM \r\n\t\t\t\t" . TABLE_LUACCOUNT . " AS L\r\n\t\t\t\tINNER JOIN\r\n\t\t\t\ttbl_UserAccount AS U\r\n\t\t\t\tON L.id = U.id\r\n\t\t\t\tWHERE U.id = convert(binary,'" . $account_name . "')";
                        $result = mssql_query($sql);
                        $user_info = mssql_fetch_array($result);
                        $account_id = $user_info["Serial"];
                        $user_pass = $user_info["Pass"];
                        $account_namex = antiject($user_info["AccountName"]);
                        @mssql_free_result($result);
                    }

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
                        $search_query .= "" . " B.userId = '" . $account_namex . "'";
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
                $query_p1 = "SELECT B.cashBalance, B.userId\r\n\t\t\tFROM \r\n\t\t\tBillCrux.dbo.tblUser AS U\r\n\t\t\tINNER JOIN\r\n\t\t\tBillCrux.dbo.tblUserInfo AS B\r\n\t\t\tON B.userNumber = U.userNumber\r\n\t\t\t" . $search_query;
                $query_p2 .= "" . "B.userNumber NOT IN ( SELECT TOP [OFFSET] B.userNumber \r\n\t\t\tFROM \r\n\t\t\tBillCrux.dbo.tblUser AS U\r\n\t\t\tINNER JOIN\r\n\t\t\tBillCrux.dbo.tblUserInfo AS B\r\n\t\t\tON B.userNumber = U.userNumber\r\n\t\t\t" . $search_query . " ORDER BY B.userId DESC) ORDER BY B.userNumber DESC";
                $url = str_replace("&page_gen=" . $page_gen, "", $_SERVER["REQUEST_URI"]);
                $pager = new PS_Pagination($gamecp_dbconnect, $query_p1, $query_p2, 20, 10, $url);
                if( !($rs = $pager->paginate()) ) 
                {
                    if( $config["security_enable_debug"] == 1 ) 
                    {
                        exit( "Error! While trying to get the character name" . "<br/>\n" . "SQL DEBUG: " . mssql_get_last_message() );
                    }

                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Error! Seems there is a problem with the SQL Statement</p>";
                    $rs = false;
                }

                connectuserdb();
                while( $row = mssql_fetch_array($rs) ) 
                {
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" style=\"text-align: center;\"><input type=\"hidden\" name=\"user_id[]\" value=\"" . $row["userId"] . "\" />" . $row["userId"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\">" . antiject($row["userId"]) . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"user_points[]\" value=\"" . $row["cashBalance"] . "\" /></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
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
                    if( $account_id && $user_pass && $account_namex ) 
                    {
                        $account_id = antiject($account_id);
                        $user_pass = antiject($user_pass);
                        $billingchk = "SELECT userNumber FROM BillCrux.dbo.tblUser WHERE userId = '" . $account_id . "'";
                        $exist = mssql_query($billingchk);
                        if( mssql_num_rows($exist) == 0 ) 
                        {
                            $bill_sql = "INSERT INTO BillCrux.dbo.tblUser \r\n\t\t\t\t\t\t(userId,userPwd,cpId,userTypeId,UserStatusId,gameServiceId) VALUES \r\n\t\t\t\t\t\t('" . $account_id . "',(CONVERT(binary, '" . $user_pass . "')),1,6,1,6);";
                            $bill_query = mssql_query($bill_sql) or exit( "Cannot insert bill user id" );
                            $get_billid = "SELECT userNumber FROM BillCrux.dbo.tblUser WHERE userId = '" . $account_id . "'";
                            $billid_query = mssql_query($get_billid) or exit( "Cannot get bill user id" );
                            $billid = mssql_fetch_row($billid_query);
                            $insert_billinfo = "INSERT INTO BillCrux.dbo.tblUserInfo (userNumber,userId,userPwd,cpId,userTypeId,UserStatusId,gameServiceId,cashBalance) VALUES \r\n\t\t\t\t\t\t('" . $billid[0] . "','" . $account_namex . "',(CONVERT(binary, '" . $user_pass . "')),1,1,1,6,0);";
                            $insert_billinfo = mssql_query($insert_billinfo) or exit( "Cannot insert bill info" );
                            @mssql_free_result($billid_query);
                        }

                    }

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


