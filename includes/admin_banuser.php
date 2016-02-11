<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Server Admin"]["Manage Bans"] = $file;
}
else
{
    $lefttitle = "Support Desk - Admin User Ban Management";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $max_pages = 10;
            $top_limit = 60;
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_POST["search_fun"]) ? $_POST["search_fun"] : "";
            $enable_exit = false;
            $enable_account = false;
            $query_p2 = "";
            $search_query = "";
            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"50%\" align=\"center\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\"><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "\">View Ban List</a></td>" . "\n";
            $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\"><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "&page=addedit\">Ban User</a></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "</table>" . "\n";
            if( empty($page) ) 
            {
                connectuserdb();
                $out .= "<form method=\"post\">" . "\n";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" colspan=\"2\">Look up a banned user</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt2\" width=\"1%\" nowrap>Account Name: </td>" . "\n";
                $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"account_name\"/></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt2\" width=\"1%\" nowrap>Account Serial: </td>" . "\n";
                $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"account_serial\"/></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt2\" colspan=\"2\" nowrap><input type=\"submit\" name=\"search_fun\" value=\"Search\"/></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "</table>" . "\n";
                $out .= "</form>" . "\n";
                if( $search_fun != "" ) 
                {
                    $account_serial = isset($_POST["account_serial"]) && is_int((int) $_POST["account_serial"]) ? antiject((int) $_POST["account_serial"]) : 0;
                    $account_name = isset($_POST["account_name"]) ? antiject($_POST["account_name"]) : "";
                    $chat_ban = isset($_POST["chat_ban"]) ? antiject($_POST["chat_ban"]) : "";
                    if( $account_serial == 0 && $account_name == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">You must enter a account name or serial to do a search</p>";
                    }

                    if( $enable_exit === false ) 
                    {
                        $search_query = " WHERE ";
                        if( $account_name != "" && $account_serial == 0 ) 
                        {
                            $search_query .= "" . " U.id = convert(binary,'" . $account_name . "')";
                        }
                        else
                        {
                            $search_query .= "" . " U.Serial = '" . $account_serial . "'";
                        }

                        $query_p2 .= " AND ";
                    }
                    else
                    {
                        $query_p2 = " WHERE ";
                    }

                }
                else
                {
                    $query_p2 = " WHERE ";
                }

                $query_p1 = "SELECT\n\t\t\t\t\tCONVERT(varchar, U.id) AS username, B.nAccountSerial, B.dtStartDate, B.nPeriod, B.nKind, B.szReason, B.GMWriter, U.Serial\n\t\t\t\t\tFROM\n\t\t\t\t\ttbl_UserBan AS B\n\t\t\t\t\tINNER JOIN\n\t\t\t\t\ttbl_UserAccount AS U\n\t\t\t\t\tON U.serial = B.nAccountSerial\n\t\t\t\t\t" . $search_query;
                $query_p2 .= "U.Serial NOT IN\n\t\t\t( SELECT TOP [OFFSET] U.Serial\n\t\t\tFROM\n\t\t\ttbl_UserBan AS B\n\t\t\tINNER JOIN\n\t\t\ttbl_UserAccount AS U\n\t\t\tON U.serial = B.nAccountSerial\n\t\t\t" . $search_query . "\n\t\t\tORDER BY B.dtStartDate DESC) ORDER BY B.dtStartDate DESC";
                include("./includes/pagination/ps_pagination.php");
                $pager = new PS_Pagination($user_dbconnect, $query_p1, $query_p2, $top_limit, $max_pages, "" . $script_name . "?do=" . $_GET["do"]);
                $rs = $pager->paginate();
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\" nowrap>Serial</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Start Date</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Account Name</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Period</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\" nowrap>Chat Ban</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Reason</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\" nowrap>Game Master</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\" colspan=\"2\" nowrap>Options</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                while( $row = mssql_fetch_array($rs) ) 
                {
                    $gmwriter = antiject($row["GMWriter"]);
                    if( $gmwriter == "WS0" ) 
                    {
                        $gmwriter = "-";
                        $row["szReason"] = "Auto-banned by FireGuard";
                    }

                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"text-align: center;\" nowrap>" . $row["Serial"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . $row["dtStartDate"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . antiject($row["username"]) . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . $row["nPeriod"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\" nowrap>" . ($row["nKind"] == 1 ? "Yes " : "No") . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . antiject($row["szReason"]) . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . antiject($gmwriter) . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\" nowrap><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "&page=addedit&edit_ban_serial=" . $row["Serial"] . "\" style=\"text-decoration: none;\">Edit</a></td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\" nowrap><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "&page=delete&ban_serial=" . $row["Serial"] . "\" style=\"text-decoration: none;\">Delete</a></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }
                if( mssql_num_rows($rs) <= 0 ) 
                {
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"alt1\" colspan=\"9\" style=\"text-align: center; font-weight: bold;\">No banned accounts found.</td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                }
                else
                {
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"alt2\" colspan=\"9\" style=\"text-align: center; font-weight: bold;\">" . $pager->renderFullNav() . "</td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                }

                @mssql_free_result($rs);
                $out .= "</table>" . "\n";
                return 1;
            }

            if( $page == "addedit" ) 
            {
                connectuserdb();
                $display_form = true;
                $do_process = 0;
                $exit_process = false;
                $exit_text = "";
                $add_submit = isset($_POST["add_submit"]) ? 1 : 0;
                $edit_submit = isset($_POST["edit_submit"]) ? 1 : 0;
                if( isset($_POST["edit_ban_serial"]) || isset($_GET["edit_ban_serial"]) ) 
                {
                    $edit_ban_serial = isset($_POST["edit_ban_serial"]) ? $_POST["edit_ban_serial"] : $_GET["edit_ban_serial"];
                    if( !is_numeric($edit_ban_serial) ) 
                    {
                        $edit_ban_serial = "";
                    }

                }
                else
                {
                    $edit_ban_serial = "";
                }

                $ban_serial = isset($_POST["ban_serial"]) && is_numeric($_POST["ban_serial"]) ? antiject($_POST["ban_serial"]) : 0;
                $ban_period = isset($_POST["ban_period"]) && is_numeric($_POST["ban_period"]) ? antiject($_POST["ban_period"]) : 119988;
                $ban_period = 119988 < $ban_period ? "119988" : $ban_period;
                $ban_reason = isset($_POST["ban_reason"]) ? antiject($_POST["ban_reason"]) : "";
                $ban_chat = isset($_POST["ban_chat"]) ? antiject($_POST["ban_chat"]) : 0;
                if( $add_submit == 1 || $edit_submit == 1 ) 
                {
                    $do_process = 1;
                }

                if( $edit_ban_serial != "" ) 
                {
                    $page_mode = "edit_submit";
                    $submit_name = "Update Account";
                    $this_mode_title = "Edit Banned Account";
                    $disable = " disabled";
                    if( $do_process == 0 ) 
                    {
                        $select_sql = "" . "SELECT nPeriod, szReason, nKind FROM tbl_UserBan WHERE nAccountSerial = '" . $edit_ban_serial . "'";
                        if( !($select_result = mssql_query($select_sql)) ) 
                        {
                            $display_form = false;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error occured while trying to obtain account data</p>";
                        }
                        else
                        {
                            if( 0 < mssql_num_rows($select_result) ) 
                            {
                                $info = mssql_fetch_array($select_result);
                                $ban_period = $info["nPeriod"];
                                $ban_reason = $info["szReason"];
                                $ban_chat = $info["nKind"];
                            }
                            else
                            {
                                $display_form = false;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">No such user found</p>";
                            }

                        }

                        @mssql_free_result($select_result);
                    }

                    $ban_serial = $edit_ban_serial;
                }
                else
                {
                    $page_mode = "add_submit";
                    $submit_name = "Ban Account";
                    $this_mode_title = "New Account Ban";
                    $disable = "";
                }

                if( $do_process == 1 ) 
                {
                    if( $ban_serial == 0 ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have not provided a user serial<br/>";
                    }

                    if( $ban_period == 0 ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You must provide a ban period (i.e. 119988)<br/>";
                    }

                    if( $ban_reason == "" ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You must provide a reason for this ban<br/>";
                    }

                    if( $exit_process != true ) 
                    {
                        $users_select = "" . "SELECT Serial FROM tbl_UserAccount WHERE Serial = '" . $ban_serial . "'";
                        if( !($users_result = mssql_query($users_select)) ) 
                        {
                            $exit_process = true;
                            $exit_text .= "&raquo; SQL Error while trying to obtain account info";
                        }
                        else
                        {
                            if( mssql_num_rows($users_result) <= 0 ) 
                            {
                                $exit_process = true;
                                $exit_text .= "&raquo; No such account serial found in the database";
                            }

                        }

                    }

                }

                if( $exit_process != 1 ) 
                {
                    if( $add_submit == 1 ) 
                    {
                        $insert_sql = "" . "INSERT INTO tbl_UserBan (nAccountSerial, nPeriod, nKind, szReason, GMWriter) VALUES ('" . $ban_serial . "', '" . $ban_period . "', '" . $ban_chat . "', '" . $ban_reason . "','" . $userdata["username"] . "')";
                        if( !($insert_result = @mssql_query($insert_sql)) ) 
                        {
                            $exit_process = true;
                            $exit_text .= "&raquo; This account has already been banned";
                        }
                        else
                        {
                            $exit_process = true;
                            $exit_text .= "<b>&raquo; Successfully banned the account: " . $ban_serial . "</b>";
                            gamecp_log(4, $userdata["username"], "" . "ADMIN - MANAGE BANS - ADDED - Account Serial: " . $ban_serial, 1);
                        }

                    }
                    else
                    {
                        if( $edit_submit == 1 ) 
                        {
                            $update_sql = "" . "UPDATE tbl_UserBan SET nPeriod = '" . $ban_period . "', szReason = '" . $ban_reason . "', nKind = '" . $ban_chat . "' WHERE nAccountSerial = '" . $edit_ban_serial . "'";
                            if( !($update_result = mssql_query($update_sql, $user_dbconnect)) ) 
                            {
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to update user ban</p>";
                            }
                            else
                            {
                                if( 0 < mssql_rows_affected($user_dbconnect) ) 
                                {
                                    $exit_process = true;
                                    $exit_text .= "&raquo; Successfully updated the banned account: " . $edit_ban_serial;
                                    gamecp_log(4, $userdata["username"], "" . "ADMIN - MANAGE BANS - UPDATED - Account Serial: " . $edit_ban_serial, 1);
                                }
                                else
                                {
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">No such banned account found</p>";
                                }

                            }

                        }

                    }

                }

                if( $exit_process == 1 ) 
                {
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td>" . "\n";
                    $out .= "\t\t\t\t" . $exit_text . "\n";
                    $out .= "\t\t\t</td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                    $out .= "</table>" . "\n";
                }

                if( $display_form == true ) 
                {
                    $out .= "<form method=\"post\">" . "\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"thead\" colspan=\"2\">" . $this_mode_title . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Account Serial:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"ban_serial\" value=\"" . ($ban_serial != 0 ? $ban_serial : "") . "\"" . $disable . " /></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Chat Ban:</td>" . "\n";
                    $checked_yes = $ban_chat == 1 ? " checked" : "";
                    $checked_no = $ban_chat == 0 ? " checked" : "";
                    $out .= "\t\t<td class=\"alt1\">Yes <input type=\"radio\" name=\"ban_chat\" value=\"1\" " . $checked_yes . "/> No <input type=\"radio\" name=\"ban_chat\" value=\"0\" " . $checked_no . "/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Period:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\">";
                    $out .= "\t\t\t<select name=\"ban_period\">";
                    $out .= "\t\t\t\t<option value=\"2\"" . ($ban_period == 2 ? " selected=\"seelcted\"" : "") . ">2 Hrs</option>";
                    $out .= "\t\t\t\t<option value=\"3\"" . ($ban_period == 3 ? " selected=\"seelcted\"" : "") . ">3 Hrs</option>";
                    $out .= "\t\t\t\t<option value=\"4\"" . ($ban_period == 4 ? " selected=\"seelcted\"" : "") . ">4 Hrs</option>";
                    $out .= "\t\t\t\t<option value=\"12\"" . ($ban_period == 12 ? " selected=\"seelcted\"" : "") . ">12 Hrs</option>";
                    $out .= "\t\t\t\t<option value=\"24\"" . ($ban_period == 24 ? " selected=\"seelcted\"" : "") . ">1 Day</option>";
                    $out .= "\t\t\t\t<option value=\"48\"" . ($ban_period == 48 ? " selected=\"seelcted\"" : "") . ">2 Days</option>";
                    $out .= "\t\t\t\t<option value=\"72\"" . ($ban_period == 72 ? " selected=\"seelcted\"" : "") . ">3 Days</option>";
                    $out .= "\t\t\t\t<option value=\"720\"" . ($ban_period == 720 ? " selected=\"seelcted\"" : "") . ">1 Month</option>";
                    $out .= "\t\t\t\t<option value=\"119988\"" . (720 < $ban_period ? " selected=\"seelcted\"" : "") . ">Forever</option>";
                    $out .= "\t\t\t</select>";
                    $out .= "\t\t</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Reason:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\">" . "\n";
                    $out .= "\t\t\t<select name=\"ban_reason\">" . "\n";
                    for( $i = 0; $i < $reasons_count; $i++ ) 
                    {
                        if( $ban_reasons[$i] == $ban_reason ) 
                        {
                            $selected = " selected=\"selected\"";
                        }
                        else
                        {
                            $selected = "";
                        }

                        $out .= "\t\t\t\t<option" . $selected . ">" . $ban_reasons[$i] . "</option>" . "\n";
                    }
                    $out .= "\t\t\t</select>" . "\n";
                    $out .= "\t\t</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" colspan=\"2\" nowrap>" . "\n";
                    $out .= "\t\t\t<input name=\"page\" type=\"hidden\" value=\"addedit\"/>" . "\n";
                    $out .= "\t\t\t<input name=\"" . $page_mode . "\" type=\"submit\" value=\"" . $submit_name . "\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "</table>" . "\n";
                    $out .= "</form>" . "\n";
                    return 1;
                }

            }
            else
            {
                if( $page == "delete" ) 
                {
                    $ban_serial = isset($_GET["ban_serial"]) && is_int((int) $_GET["ban_serial"]) ? antiject((int) $_GET["ban_serial"]) : "";
                    if( $ban_serial == "" ) 
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">No such banned account found</p>";
                        return 1;
                    }

                    connectuserdb();
                    $user_query = mssql_query("" . "SELECT convert(varchar,id) as Account FROM tbl_UserBan AS B INNER JOIN tbl_UserAccount AS U ON B.nAccountSerial = U.Serial WHERE B.nAccountSerial = '" . $ban_serial . "'");
                    $user_info = mssql_fetch_array($user_query);
                    if( mssql_num_rows($user_query) <= 0 ) 
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">No such banned account found</p>";
                    }
                    else
                    {
                        $out .= "<form method=\"post\">" . "\n";
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Are you sure you want to UNBAN the Account: <u>" . antiject($user_info["Account"]) . "</u> (Serial: " . $ban_serial . ")?</p>" . "\n";
                        $out .= "<p style=\"text-align: center;\"><input type=\"hidden\" name=\"ban_serial\" value=\"" . $ban_serial . "\"/><input type=\"hidden\" name=\"page\" value=\"delete_user\"/><input type=\"submit\" name=\"yes\" value=\"Yes\"/> <input type=\"submit\" name=\"no\" value=\"No\"/></p>";
                        $out .= "</form>";
                    }

                    @mssql_free_result($user_query);
                    return 1;
                }

                if( $page == "delete_user" ) 
                {
                    $yes = isset($_POST["yes"]) ? "1" : "0";
                    $no = isset($_POST["no"]) ? "1" : "0";
                    if( isset($_POST["ban_serial"]) && is_int((int) $_POST["ban_serial"]) ) 
                    {
                        $ban_serial = antiject((int) $_POST["ban_serial"]);
                    }
                    else
                    {
                        $ban_serial = "";
                    }

                    if( $no != 1 && $ban_serial != "" ) 
                    {
                        connectuserdb();
                        $user_query = mssql_query("" . "SELECT convert(varchar,id) as Account, U.Serial FROM tbl_UserBan AS B INNER JOIN tbl_UserAccount AS U ON B.nAccountSerial = U.Serial WHERE B.nAccountSerial = '" . $ban_serial . "'");
                        $user = mssql_fetch_array($user_query);
                        if( mssql_num_rows($user_query) <= 0 ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">No such banned account found</p>";
                        }
                        else
                        {
                            $cquery = mssql_query("DELETE FROM tbl_UserBan WHERE nAccountSerial = " . $ban_serial);
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully unbanned the account: " . antiject($user["Account"]) . " (Serial: " . $user["Serial"] . ")</p>";
                            gamecp_log(5, $userdata["username"], "ADMIN - MANAGE BANS - UNBAN - Account Name:  " . antiject($user["Account"]) . " | Serial: " . $user["Serial"], 1);
                        }

                        @mssql_free_result($user_query);
                        return 1;
                    }

                    header("" . "Location: " . $script_name . "?do=" . $_GET["do"]);
                    return 1;
                }

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


