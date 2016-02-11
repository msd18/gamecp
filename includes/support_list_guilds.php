<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Support"]["Guild List"] = $file;
}
else
{
    $lefttitle = "Guild List";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_GET["page"]) || isset($_POST["page"]) ? isset($_GET["page"]) ? $_GET["page"] : $_POST["page"] : "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $enable_exit = false;
            $last_month = @mktime(0, 0, 0, 7, 1, 2009);
            if( empty($page) ) 
            {
                $out .= "<form method=\"get\" action=\"" . $script_name . "?do=support_desk\">" . "\n";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Options</b></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt1\">View In-Active:</td>" . "\n";
                $out .= "\t\t<td class=\"alt2\"><input type=\"checkbox\" name=\"inactive\" " . (isset($_GET["inactive"]) ? "checked" : "") . "/></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>";
                $out .= "\t\t<td colspan=\"2\"><input type=\"hidden\" value=\"" . $_GET["do"] . "\" name=\"do\" /><input type=\"submit\" value=\"Submit\" name=\"search_fun\" /></td>";
                $out .= "\t</tr>";
                $out .= "</table>" . "\n";
                $out .= "</form>" . "\n";
                $inactive = isset($_GET["inactive"]) ? true : false;
                connectdatadb();
                if( !($result = mssql_query("SELECT TOP 500 id, serial, MemberCount, Grade FROM tbl_Guild WHERE DCK = 0 ORDER BY MemberCount DESC")) ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error! Failed in getting the guild info</p>";
                }

                $sql_rows = @mssql_num_rows($result);
                $out .= "<br/>";
                $out .= "\t<script type=\"text/javascript\" src=\"checkall.js\"></script>" . "\n";
                $out .= "\t<form name=\"user_list\" method=\"post\">" . "\n";
                $out .= "\t<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td colspan=\"7\" style=\"font-size: 80%;\">* When deleting in-active guilds, a server reboot is mandatory for the deletions to be recognized as guilds are cached by the Zone server by default</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap><b>#</b></td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap><b>Guild Serial</b></td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap><b>Guild Name</b></td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap><b>Member Count</b></td>" . "\n";
                if( $inactive ) 
                {
                    $out .= "\t\t<td class=\"thead\" nowrap><b>Last Connect Time</b></td>" . "\n";
                    $out .= "\t\t<td class=\"thead\" width=\"2%\"><input name=\"allbox\" id=\"checkAll\" onclick=\"checkAllFields(1);\" type=\"checkbox\" name=\"check_all\" value=\"0\"/></td>" . "\n";
                }
                else
                {
                    $out .= "\t\t<td class=\"thead\" nowrap><b>Grade</b></td>" . "\n";
                }

                $out .= "\t</tr>" . "\n";
                $x = 0;
                while( $row = mssql_fetch_array($result) ) 
                {
                    if( $inactive ) 
                    {
                        $char_result = mssql_query("SELECT B.Serial, B.Name, B.AccountSerial, B.Account, B.LastConnTime FROM tbl_base AS B INNER JOIN tbl_general as G ON B.Serial = G.Serial WHERE G.GuildSerial = '" . $row["serial"] . "' ORDER BY B.LastConnTime DESC");
                        $lastconntime = 0;
                        while( $char = mssql_fetch_array($char_result) ) 
                        {
                            if( $lastconntime < $char["LastConnTime"] ) 
                            {
                                $lastconntime = $char["LastConnTime"];
                            }

                        }
                        mssql_free_result($char_result);
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
                        if( $lastconntime < $last_month || $row["MemberCount"] < 8 ) 
                        {
                            $out .= "\t<tr id=\"tr_" . $x . "\" class=\"alt1\">" . "\n";
                            $out .= "\t\t<td nowrap>" . $x . "</td>" . "\n";
                            $out .= "\t\t<td nowrap><a href=\"index.php?guild_serial=" . $row["serial"] . "&do=support_guild_search&search_fun=Search\">" . $row["serial"] . "</a></td>" . "\n";
                            $out .= "\t\t<td nowrap>" . $row["id"] . "</td>" . "\n";
                            $out .= "\t\t<td nowrap>" . $row["MemberCount"] . "</td>" . "\n";
                            $out .= "\t\t<td nowrap>" . date("M d Y h:iA", $lastconntime) . "</td>" . "\n";
                            $out .= "\t\t<td><input type=\"checkbox\" name=\"ban_serial[]\" class=\"boxes\" onclick=\"JavaScript: checkAllFields(2); highlight(this,'tr_" . $x . "');\" value=\"" . $row["serial"] . "\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $x++;
                        }

                    }
                    else
                    {
                        $out .= "\t<tr>" . "\n";
                        $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap>" . $x . "</td>" . "\n";
                        $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap><a href=\"index.php?guild_serial=" . $row["serial"] . "&do=support_guild_search&search_fun=Search\">" . $row["serial"] . "</a></td>" . "\n";
                        $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap>" . $row["id"] . "</td>" . "\n";
                        $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap>" . $row["MemberCount"] . "</td>" . "\n";
                        $out .= "\t\t<td style=\"font-weight: bold;\" class=\"alt1\" nowrap>" . $row["Grade"] . "</td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        $x++;
                    }

                }
                if( $inactive ) 
                {
                    $out .= "\t\t\t\t<tr>" . "\n";
                    $out .= "\t\t\t\t\t<td class=\"alt2\" colspan=\"6\" style=\"text-align: right;\">" . "\n";
                    $out .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"delete_guilds\"/><input type=\"submit\" name=\"ban_users\" value=\"Delete [0] Selected\" id=\"removeChecked\"/>" . "\n";
                    $out .= "\t\t\t\t\t</td>" . "\n";
                    $out .= "\t\t\t\t</tr>" . "\n";
                }

                $out .= "</table>";
                $out .= "</form>";
                mssql_free_result($result);
                return 1;
            }

            if( $page == "delete_guilds" ) 
            {
                $ban_serial = isset($_POST["ban_serial"]) && is_array($_POST["ban_serial"]) ? $_POST["ban_serial"] : "";
                $do_process = 1;
                $exit_process = false;
                $exit_text = "";
                if( !is_array($ban_serial) ) 
                {
                    $do_process = 0;
                    $out .= "<p>Failed: No IDEA WHY!</p>";
                }

                if( $do_process == 1 ) 
                {
                    if( $ban_serial == "" ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have not provided a user serial<br/>";
                    }

                }
                else
                {
                    $exit_process = true;
                }

                if( !$exit_process ) 
                {
                    foreach( $ban_serial as $serial ) 
                    {
                        $serial = is_int((int) $serial) ? antiject((int) $serial) : 0;
                        if( $serial != "" ) 
                        {
                            connectdatadb();
                            $users_select = "" . "SELECT serial FROM tbl_guild WHERE serial = '" . $serial . "' and dck = 0";
                            if( !($users_result = mssql_query($users_select)) ) 
                            {
                                $exit_process = true;
                                $exit_text .= "&raquo; SQL Error while trying to obtain guild info";
                            }
                            else
                            {
                                if( mssql_num_rows($users_result) <= 0 ) 
                                {
                                    $exit_process = true;
                                    $exit_text .= "&raquo; No such guild serial (#" . $serial . ") found in the database";
                                }
                                else
                                {
                                    $insert_sql = "" . "UPDATE tbl_Guild  SET DCK = 1, deleteid = id, id = '*' + CONVERT(varchar, Serial) WHERE    serial = '" . $serial . "'";
                                    if( !($insert_result = @mssql_query($insert_sql)) ) 
                                    {
                                        $exit_process = true;
                                        $exit_text .= "&raquo; This guild (#" . $serial . ") has already been banned<br/>";
                                    }
                                    else
                                    {
                                        $exit_process = true;
                                        $exit_text .= "<b>&raquo; Successfully deleted the guild: " . $serial . "</b><br/>";
                                        gamecp_log(4, $userdata["username"], "" . "SUPPORT - GUILD LIST - DELETED - Guild Serial: " . $serial, 1);
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


