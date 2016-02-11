<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Support"]["Guild Search"] = $file;
}
else
{
    $lefttitle = "Guild Search";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_GET["page"]) || isset($_POST["page"]) ? isset($_GET["page"]) ? $_GET["page"] : $_POST["page"] : "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $enable_exit = false;
            if( empty($page) ) 
            {
                $out .= "<form method=\"get\" action=\"" . $script_name . "?do=support_desk\">" . "\n";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Look up a guild</b></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt1\">Guild Serial:</td>" . "\n";
                $out .= "\t\t<td class=\"alt2\"><input type=\"text\" name=\"guild_serial\" /></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt1\">Guild Name:</td>" . "\n";
                $out .= "\t\t<td class=\"alt2\"><input type=\"text\" name=\"guild_name\" /></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt1\">Guild Delete Name:</td>" . "\n";
                $out .= "\t\t<td class=\"alt2\"><input type=\"text\" name=\"guild_delete_name\" /></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>";
                $out .= "\t\t<td colspan=\"2\"><input type=\"hidden\" value=\"" . $_GET["do"] . "\" name=\"do\" /><input type=\"submit\" value=\"Search\" name=\"search_fun\" /></td>";
                $out .= "\t</tr>";
                $out .= "</table>" . "\n";
                $out .= "</form>" . "\n";
                if( $search_fun != "" ) 
                {
                    $guild_serial = isset($_GET["guild_serial"]) && is_int((int) $_GET["guild_serial"]) ? antiject((int) $_GET["guild_serial"]) : 0;
                    $guild_name = isset($_GET["guild_name"]) ? antiject($_GET["guild_name"]) : "";
                    $guild_delete_name = isset($_GET["guild_delete_name"]) ? antiject($_GET["guild_delete_name"]) : "";
                    if( $guild_serial == 0 && $guild_name == "" && $guild_delete_name == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>Guild name or serial is required</b></p>";
                    }

                    $search = "";
                    if( $enable_exit != true ) 
                    {
                        if( $guild_serial != 0 ) 
                        {
                            $search .= "" . " serial = '" . $guild_serial . "' ";
                        }

                        if( $guild_name != "" ) 
                        {
                            $search .= $search != "" ? " AND " : "";
                            if( !preg_match("/%/", $guild_name) ) 
                            {
                                $search .= "" . " id = '" . $guild_name . "' ";
                            }
                            else
                            {
                                $search .= "" . " id LIKE '" . $guild_name . "' ";
                            }

                        }

                        if( $guild_delete_name != "" ) 
                        {
                            $search .= $search != "" ? " AND " : "";
                            if( !preg_match("/%/", $guild_delete_name) ) 
                            {
                                $search .= "" . " deleteid = '" . $guild_delete_name . "' ";
                            }
                            else
                            {
                                $search .= "" . " deleteid LIKE '" . $guild_delete_name . "' ";
                            }

                        }

                        connectdatadb();
                        if( !($result = mssql_query("" . "SELECT id, serial, MemberCount, MasterSerial, Dalant, Gold FROM tbl_Guild WHERE " . $search)) ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error! Failed in getting the guild info</p>";
                        }

                        $sql_rows = @mssql_num_rows($result);
                        $out .= "<br/>";
                        $out .= "<script type=\"text/javascript\" src=\"checkall.js\"></script>" . "\n";
                        while( $row = mssql_fetch_array($result) ) 
                        {
                            $out .= "\t<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px\" nowrap><b>Guild Serial </b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px\" nowrap><b>Guild Name</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px\" nowrap><b>Member Count</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px\" nowrap><b>Gold</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px\" nowrap><b>Money</b></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap><a href=\"index.php?guild_serial=" . $row["serial"] . "&do=support_guild_search&search_fun=Search\">" . $row["serial"] . "</a></td>" . "\n";
                            $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap>" . $row["id"] . "</td>" . "\n";
                            $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap>" . $row["MemberCount"] . "</td>" . "\n";
                            $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap>" . number_format($row["Gold"]) . "</td>" . "\n";
                            $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap>" . number_format($row["Dalant"]) . "</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt2\" colspan=\"5\">Guild Members</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td style=\"font-weight: bold; padding: 4px;\" class=\"alt1\" colspan=\"5\">" . "\n";
                            $char_result = mssql_query("SELECT B.Serial, B.Name, B.AccountSerial, B.Account, B.LastConnTime FROM tbl_base AS B INNER JOIN tbl_general as G ON B.Serial = G.Serial WHERE G.GuildSerial = '" . $row["serial"] . "' ORDER BY B.LastConnTime DESC");
                            $out .= "\t\t\t<form name=\"user_list\" method=\"post\">" . "\n";
                            $out .= "\t\t\t<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" style=\"border: 0;\" width=\"100%\">" . "\n";
                            $out .= "\t\t\t\t<tr>" . "\n";
                            $out .= "\t\t\t\t\t<td class=\"thead\">Account Serial</td>" . "\n";
                            $out .= "\t\t\t\t\t<td class=\"thead\">Account Name</td>" . "\n";
                            $out .= "\t\t\t\t\t<td class=\"thead\">Char Serial</td>" . "\n";
                            $out .= "\t\t\t\t\t<td class=\"thead\">Char Name</td>" . "\n";
                            $out .= "\t\t\t\t\t<td class=\"thead\">Last Conn Time</td>" . "\n";
                            $out .= "\t\t\t\t\t<td class=\"thead\" width=\"2%\"><input name=\"allbox\" id=\"checkAll\" onclick=\"checkAllFields(1);\" type=\"checkbox\" name=\"check_all\" value=\"0\"/></td>" . "\n";
                            $out .= "\t\t\t\t</tr>" . "\n";
                            for( $x = 0; $char = mssql_fetch_array($char_result); $x++ ) 
                            {
                                $class = $char["Serial"] == $row["MasterSerial"] ? "alt1 highlight" : "alt1";
                                $lastconntime = $char["LastConnTime"];
                                if( 0 < $lastconntime ) 
                                {
                                    if( mb_strlen($lastconntime) <= 9 ) 
                                    {
                                        $lastconntime = "0" . $lastconntime;
                                        $prepend_etime = "20";
                                    }
                                    else
                                    {
                                        $prepend_etime = "20";
                                    }

                                    $lastconntime = str_split($lastconntime, 2);
                                    $lastconntime = @mktime($lastconntime[3], $lastconntime[4], 0, @ltrim($lastconntime[1], "0"), $lastconntime[2], $prepend_etime . $lastconntime[0]);
                                    $lastconntimex = $lastconntime;
                                    if( isset($config["gamecp_logs_url"]) && !empty($config["gamecp_logs_url"]) && $config["gamecp_logs_url"] != " " ) 
                                    {
                                        $generate_item_url = $config["gamecp_logs_url"] . "?";
                                        $generate_item_url .= "y=" . date("Y", $lastconntime) . "&";
                                        $generate_item_url .= "m=" . date("m", $lastconntime) . "&";
                                        $generate_item_url .= "d=" . date("d", $lastconntime) . "&";
                                        $generate_item_url .= "h=" . date("G", $lastconntime) . "&";
                                        $generate_item_url .= "serial=" . $row["Serial"];
                                        $lastconntimex = date("M d Y h:iA", $lastconntimex);
                                        $lastconntime = "<a href=\"" . $generate_item_url . "\" target=\"logs\">" . $lastconntimex . "</a>";
                                    }
                                    else
                                    {
                                        $lastconntime = date("M d Y h:iA", $lastconntimex);
                                    }

                                }
                                else
                                {
                                    $lastconntime = "--";
                                }

                                $out .= "\t\t\t\t<tr id=\"tr_" . $x . "\" class=\"" . $class . "\">" . "\n";
                                $out .= "\t\t\t\t\t<td><a href=\"" . $script_name . "?do=support_desk&amp;account_serial=" . $char["AccountSerial"] . "&amp;search_fun=Search\">" . $char["AccountSerial"] . "</a></td>" . "\n";
                                $out .= "\t\t\t\t\t<td>" . $char["Account"] . "</td>" . "\n";
                                $out .= "\t\t\t\t\t<td>" . $char["Serial"] . "</td>" . "\n";
                                $out .= "\t\t\t\t\t<td>" . $char["Name"] . "</td>" . "\n";
                                $out .= "\t\t\t\t\t<td style=\"font-size: 10px;\">" . $lastconntime . "</td>" . "\n";
                                $out .= "\t\t\t\t\t<td><input type=\"checkbox\" name=\"ban_serial[]\" class=\"boxes\" onclick=\"JavaScript: checkAllFields(2); highlight(this,'tr_" . $x . "');\" value=\"" . $char["AccountSerial"] . "\"/></td>" . "\n";
                                $out .= "\t\t\t\t</tr>" . "\n";
                            }
                            mssql_free_result($char_result);
                            $out .= "\t\t\t\t<tr>" . "\n";
                            $out .= "\t\t\t\t\t<td class=\"alt2\" colspan=\"6\" style=\"text-align: right;\">" . "\n";
                            $out .= "\t\t\t\t\t\t<b>Ban Rason:</b> <select name=\"ban_reason\">" . "\n";
                            for( $i = 0; $i < $reasons_count; $i++ ) 
                            {
                                $out .= "\t\t\t\t\t\t\t<option>" . $ban_reasons[$i] . "</option>" . "\n";
                            }
                            $out .= "\t\t\t\t\t\t</select>" . "\n";
                            $out .= "\t\t\t\t\t\t<b>Period:</b> " . "\n";
                            $out .= "\t\t\t<select name=\"ban_period\">";
                            $out .= "\t\t\t\t<option value=\"2\">2 Hrs</option>";
                            $out .= "\t\t\t\t<option value=\"3\">3 Hrs</option>";
                            $out .= "\t\t\t\t<option value=\"4\">4 Hrs</option>";
                            $out .= "\t\t\t\t<option value=\"12\">12 Hrs</option>";
                            $out .= "\t\t\t\t<option value=\"24\">1 Day</option>";
                            $out .= "\t\t\t\t<option value=\"48\">2 Days</option>";
                            $out .= "\t\t\t\t<option value=\"72\">3 Days</option>";
                            $out .= "\t\t\t\t<option value=\"720\">1 Month</option>";
                            $out .= "\t\t\t\t<option value=\"999\">Forever</option>";
                            $out .= "\t\t\t</select>";
                            $out .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"ban_users\"/><input type=\"submit\" name=\"ban_users\" value=\"Ban [0] Selected\" id=\"removeChecked\"/>" . "\n";
                            $out .= "\t\t\t\t\t</td>" . "\n";
                            $out .= "\t\t\t\t</tr>" . "\n";
                            $out .= "\t\t\t</table>";
                            $out .= "\t\t\t</form>" . "\n";
                            $out .= "\t\t</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "</table>" . "\n";
                            $out .= "<br/>" . "\n";
                        }
                        mssql_free_result($result);
                        gamecp_log(0, $userdata["username"], "" . "SUPPORT - GUILD SEARCH - Searched for: " . $guild_name . " or " . $guild_serial, 1);
                        return 1;
                    }

                }

            }
            else
            {
                if( $page == "ban_users" ) 
                {
                    $ban_serial = isset($_POST["ban_serial"]) ? $_POST["ban_serial"] : "";
                    $ban_period = isset($_POST["ban_period"]) && is_int((int) $_POST["ban_period"]) ? antiject((int) $_POST["ban_period"]) : 119988;
                    $ban_period = 119988 < $ban_period ? "119988" : $ban_period;
                    $ban_reason = isset($_POST["ban_reason"]) ? antiject($_POST["ban_reason"]) : "";
                    $do_process = 1;
                    $exit_process = false;
                    $exit_text = "";
                    if( !is_array($ban_serial) && !is_int($ban_period) && $ban_reason == "" ) 
                    {
                        $do_process = 0;
                        $exit_process = true;
                        $exit_text .= "&raquo; Failed: No IDEA WHY!";
                    }

                    if( $do_process == 1 ) 
                    {
                        if( $ban_serial == "" ) 
                        {
                            $exit_process = true;
                            $exit_text .= "&raquo; You have not provided a user serial<br/>";
                        }

                        if( $ban_period == "" ) 
                        {
                            $exit_process = true;
                            $exit_text .= "&raquo; You must provide a ban period (i.e. 119988)<br/>";
                        }

                        if( $ban_reason == "" ) 
                        {
                            $exit_process = true;
                            $exit_text .= "&raquo; You must provide a reason for this ban<br/>";
                        }

                    }

                    if( $exit_process != 1 ) 
                    {
                        foreach( $ban_serial as $serial ) 
                        {
                            $serial = is_int((int) $serial) ? antiject((int) $serial) : 0;
                            if( $serial != 0 ) 
                            {
                                connectuserdb();
                                $users_select = "" . "SELECT Serial FROM tbl_UserAccount WHERE Serial = '" . $serial . "'";
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
                                        $exit_text .= "&raquo; No such account serial (#" . $serial . ") found in the database";
                                    }
                                    else
                                    {
                                        $insert_sql = "" . "INSERT INTO tbl_UserBan (nAccountSerial, nPeriod, nKind, szReason, GMWriter) VALUES ('" . $serial . "', '" . $ban_period . "', '0', '" . $ban_reason . "','" . $userdata["username"] . "')";
                                        if( !($insert_result = @mssql_query($insert_sql)) ) 
                                        {
                                            $exit_process = true;
                                            $exit_text .= "&raquo; This account (#" . $serial . ") has already been banned<br/>";
                                        }
                                        else
                                        {
                                            $exit_process = true;
                                            $exit_text .= "<b>&raquo; Successfully banned the account: " . $serial . "</b><br/>";
                                            gamecp_log(4, $userdata["username"], "" . "ADMIN - MANAGE BANS - ADDED - Account Serial: " . $serial, 1);
                                        }

                                    }

                                }

                            }

                        }
                    }

                    if( $exit_process == 1 ) 
                    {
                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                        $out .= "\t\t<tr>" . "\n";
                        $out .= "\t\t\t<td class=\"alt2\">" . "\n";
                        $out .= "\t\t\t\t" . $exit_text . "\n";
                        $out .= "\t\t\t</td>" . "\n";
                        $out .= "\t\t</tr>" . "\n";
                        $out .= "</table>" . "\n";
                        header("Refresh: 1; URL=" . $script_name . "?do=" . $_GET["do"]);
                        return 1;
                    }

                }
                else
                {
                    $out .= $lang["invalid_page_id"];
                    return 1;
                }

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


