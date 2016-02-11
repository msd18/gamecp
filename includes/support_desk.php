<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Support"]["User Information"] = $file;
}
else
{
    $lefttitle = "Support Desk - User Information";
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
                $out .= "<form method=\"get\" action=\"" . $script_name . "?do=support_desk\">";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Look up a user</b></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Account Serial:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_serial\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Account name:<br/><span style=\"font-size: 9px;\">Use % as a wild card. <b>DO NOT MAKE THE SEARCH TOO GENERAL!</b></span></td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_name\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">E-Mail:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_email\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Last Logged Ip:<br/><span style=\"font-size: 9px;\">Use % as a wild card. <b>DO NOT MAKE THE SEARCH TOO GENERAL!</b></span></td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_ip\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">FG Password:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"fg_pw\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">FG Answer:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"fg_ans\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Bank Password:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"bank_pw\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Bank Answer:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"bank_answer\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td colspan=\"2\"><input type=\"hidden\" value=\"" . $_GET["do"] . "\" name=\"do\" /><input type=\"submit\" value=\"Search\" name=\"search_fun\" /></td>";
                $out .= "</tr>";
                $out .= "</table>";
                $out .= "</form>";
                if( $search_fun != "" ) 
                {
                    $out .= "<br/><br/>";
                    $account_serial = isset($_GET["account_serial"]) && is_int((int) $_GET["account_serial"]) ? antiject((int) $_GET["account_serial"]) : 0;
                    $account_name = isset($_GET["account_name"]) ? antiject($_GET["account_name"]) : "";
                    $account_email = isset($_GET["account_email"]) ? antiject($_GET["account_email"]) : "";
                    $account_ip = isset($_GET["account_ip"]) ? antiject($_GET["account_ip"]) : "";
                    $bank_answer = isset($_GET["bank_answer"]) ? antiject($_GET["bank_answer"]) : "";
                    $bank_pw = isset($_GET["bank_pw"]) ? antiject($_GET["bank_pw"]) : "";
                    $fg_pw = isset($_GET["fg_pw"]) ? antiject($_GET["fg_pw"]) : "";
                    $fg_ans = isset($_GET["fg_ans"]) ? antiject($_GET["fg_ans"]) : "";
                    $or_s = "";
                    $or_e = "";
                    $or_i = "";
                    $or_fg = "";
                    $or_an = "";
                    $account_ser = "";
                    $email_add = "";
                    $account_add = "";
                    $ip_add = "";
                    $fgpw_add = "";
                    $fgan_add = "";
                    if( $account_serial == 0 && $account_name == "" && $account_email == "" && $account_ip == "" && $bank_pw == "" && $bank_answer == "" && $fg_pw == "" && $fg_ans == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>Sorry, make sure you filled in either the name or email for the account</b></p>";
                    }

                    if( $account_serial != "" && !is_numeric($account_serial) ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>Sorry, invalid account serial</b></p>";
                    }

                    if( $account_email != "" && !isemail($account_email) ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>Sorry, invalid email address entered</b></p>";
                    }

                    if( $account_name != "" ) 
                    {
                        if( !preg_match("/%/", $account_name) ) 
                        {
                            $account_add = "P.id = CONVERT(binary,\"" . $account_name . "\")";
                        }
                        else
                        {
                            $account_add = "P.id LIKE CONVERT(binary,'" . $account_name . "')";
                        }

                    }

                    if( $account_serial != 0 ) 
                    {
                        if( $account_name != "" ) 
                        {
                            $or_s = " OR ";
                        }

                        $account_ser = "U.serial = \"" . $account_serial . "\"";
                    }

                    if( $account_email != "" ) 
                    {
                        if( $account_name != "" || $account_serial != 0 ) 
                        {
                            $or_e = " OR ";
                        }

                        $email_add = "P.Email LIKE \"" . $account_email . "\"";
                    }

                    if( $account_ip != "" ) 
                    {
                        if( $account_name != "" || $account_serial != 0 || $account_email != "" ) 
                        {
                            $or_i = " OR ";
                        }

                        if( !preg_match("/%/", $account_ip) ) 
                        {
                            $ip_add = "U.lastconnectip = \"" . $account_ip . "\"";
                        }
                        else
                        {
                            $ip_add = "U.lastconnectip LIKE '" . $account_ip . "'";
                        }

                    }

                    if( $fg_pw != "" ) 
                    {
                        if( $account_name != "" || $account_serial != 0 || $account_email != "" ) 
                        {
                            $or_fg = " OR ";
                        }

                        if( !preg_match("/%/", $fg_pw) ) 
                        {
                            $fgpw_add = "U.uilock_pw = CONVERT(binary,\"" . $fg_pw . "\")";
                        }
                        else
                        {
                            $fgpw_add = "U.uilock_pw LIKE CONVERT(binary,'" . $fg_pw . "')";
                        }

                    }

                    if( $fg_ans != "" ) 
                    {
                        if( $account_name != "" || $account_serial != 0 || $account_email != "" || $fg_pw != "" ) 
                        {
                            $or_an = " OR ";
                        }

                        if( !preg_match("/%/", $fg_pw) ) 
                        {
                            $fgan_add = "U.uilock_hintanswer = CONVERT(binary,\"" . $fg_ans . "\")";
                        }
                        else
                        {
                            $fgan_add = "U.uilock_hintanswer LIKE CONVERT(binary,'" . $fg_ans . "')";
                        }

                    }

                    if( $bank_answer != "" || $bank_pw != "" ) 
                    {
                        connectdatadb();
                        $searchme = "";
                        if( $bank_answer != "" ) 
                        {
                            $searchme .= "HintAnswer = '" . $bank_answer . "'";
                        }

                        if( $bank_pw != "" ) 
                        {
                            if( $searchme != "" ) 
                            {
                                $searchme .= " AND ";
                            }

                            $searchme .= "TrunkPass = CONVERT(binary,'" . $bank_pw . "')";
                        }

                        $bank_result = mssql_query("" . "SELECT\n\t\t\t\t\t\tAccountSerial\n\t\t\t\t\t\tFROM\n\t\t\t\t\t\ttbl_AccountTrunk\n\t\t\t\t\t\tWHERE " . $searchme, $data_dbconnect);
                        $bank_serial = "";
                        while( $bank = mssql_fetch_row($bank_result) ) 
                        {
                            if( $bank_serial != "" ) 
                            {
                                $bank_serial .= ",";
                            }

                            $bank_serial .= $bank[0];
                        }
                        mssql_free_result($bank_result);
                        if( $bank_serial != "" ) 
                        {
                            if( $account_name != "" || $account_serial != 0 || $account_email != "" || $account_ip != "" ) 
                            {
                                $or_s = " OR ";
                            }

                            $account_ser = "U.serial IN (" . $bank_serial . ")";
                        }
                        else
                        {
                            $enable_exit = true;
                            $out .= "<p align='center'><b>Sorry, no results could be found</b></p>";
                        }

                    }

                    if( $enable_exit != true ) 
                    {
                        $account_name = antiject($account_name);
                        $account_email = antiject($account_email);
                        connectuserdb();
                        $query_text = "SELECT TOP 400\n\t\t\t\t\tU.serial, U.createtime, U.createip, U.lastconnectip, U.lastlogofftime, id = CAST(P.id as varbinary), password = CAST(P.password as varbinary), P.Email, U.uilock, uilock_pw = CAST(U.uilock_pw AS varchar(255)), uilock_hintanswer = CAST(U.uilock_hintanswer AS varchar(255)), convert(varchar, U.uilock_update) AS uilock_update\n\t\t\t\t\tFROM\n\t\t\t\t\t\t" . TABLE_LUACCOUNT . " AS P\n\t\t\t\t\tINNER JOIN\n\t\t\t\t\t\ttbl_UserAccount AS U\n\t\t\t\t\tON U.id = P.id\n\t\t\t\t\tWHERE " . $account_add . $or_s . $account_ser . $or_e . $email_add . $or_i . $ip_add . $or_fg . $fgpw_add . $or_an . $fgan_add . " ORDER BY U.lastlogofftime DESC";
                        $result = mssql_query($query_text, $user_dbconnect);
                        $sql_rows = @mssql_num_rows($result);
                        if( 1 < $sql_rows ) 
                        {
                            $out .= "<script type=\"text/javascript\" src=\"checkall.js\"></script>" . "\n";
                        }

                        $out .= "<form name=\"user_list\" method=\"post\">" . "\n";
                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                        $out .= "</tr>" . "\n";
                        $out .= "<tr>" . "\n";
                        $out .= "<td colspan=\"10\">For performance purposes, results listed are limited the top 250 returned results</td>" . "\n";
                        $out .= "</tr>" . "\n";
                        $out .= "<tr>" . "\n";
                        $out .= "<td class=\"thead\"style=\"padding: 4px\" nowrap><b>Account Serial</b></td>" . "\n";
                        $out .= "<td class=\"thead\"style=\"padding: 4px\" nowrap><b>Username</b></td>" . "\n";
                        $out .= "<td class=\"thead\"style=\"padding: 4px\" nowrap><b>Password</b></td>" . "\n";
                        $out .= "<td class=\"thead\"style=\"padding: 4px\" nowrap><b>E-Mail</b></td>" . "\n";
                        $out .= "<td class=\"thead\"style=\"padding: 4px\" nowrap><b>Create Date</b></td>" . "\n";
                        $out .= "<td class=\"thead\"style=\"padding: 4px\" nowrap><b>Last logoff Date</b></td>" . "\n";
                        $out .= "<td class=\"thead\"style=\"padding: 4px\" nowrap><b>Create IP</b></td>" . "\n";
                        $out .= "<td class=\"thead\"style=\"padding: 4px\" nowrap><b>Last Connect IP</b></td>" . "\n";
                        if( 1 < $sql_rows ) 
                        {
                            $out .= "<td class=\"thead\"style=\"padding: 4px\" nowrap><b>View More</b></td>" . "\n";
                            $out .= "<td class=\"thead\"style=\"padding: 4px; text-align: center;\" nowrap><input name=\"allbox\" id=\"checkAll\" onclick=\"checkAllFields(1);\" type=\"checkbox\" name=\"check_all\" value=\"0\"/></td>" . "\n";
                        }

                        $x = 0;
                        connectdatadb();
                        while( $row = mssql_fetch_array($result) ) 
                        {
                            $username = $row["id"];
                            $password = $row["password"];
                            $uilock_pw = antiject($row["uilock_pw"]);
                            $bank_result = mssql_query("SELECT\n\t\t\t\t\t\tAccountSerial, DCK, CONVERT(varbinary,TrunkPass) as BankPass, HintAnswer\n\t\t\t\t\t\tFROM\n\t\t\t\t\t\ttbl_AccountTrunk\n\t\t\t\t\t\tWHERE AccountSerial = '" . $row["serial"] . "'", $data_dbconnect);
                            $bank = mssql_fetch_array($bank_result);
                            $BankPass = $bank["BankPass"];
                            if( empty($BankPass) ) 
                            {
                                $BankPass = "*";
                            }

                            if( !in_array($userdata["username"], $super_admin) ) 
                            {
                                $password = "*";
                                $uilock_pw = "*";
                                ${$BankPass} = "*";
                            }

                            if( 1 < $sql_rows ) 
                            {
                                $single_user = " class=\"extra_" . $row["serial"] . "\" style=\"display: none;\"";
                                $colspan_single = 9;
                                $class_single = "alt1";
                                $single_serial = "";
                                $username = make_link($row["serial"], $username);
                            }
                            else
                            {
                                $single_user = "";
                                $colspan_single = 7;
                                $class_single = "alt2";
                                $single_serial = $row["serial"];
                            }

                            $onetwo = $x % 2 ? "one" : "two";
                            $out .= "<tr id=\"tr_" . $x . "\" class=\"alt1\">" . "\n";
                            $out .= "\t<td style=\"font-weight: bold;\" nowrap>" . $row["serial"] . "</td>";
                            $out .= "\t<td style=\"font-weight: bold;\" nowrap>" . $username . "</td>";
                            $out .= "\t<td nowrap>" . $password . "</td>";
                            $out .= "\t<td nowrap>" . $row["Email"] . "</td>" . "\n";
                            $out .= "\t<td nowrap>" . $row["createtime"] . "</td>" . "\n";
                            $out .= "\t<td nowrap>" . $row["lastlogofftime"] . "</td>" . "\n";
                            $out .= "\t<td nowrap>" . $row["createip"] . "</td>" . "\n";
                            $out .= "\t<td nowrap>" . $row["lastconnectip"] . "</td>" . "\n";
                            if( 1 < $sql_rows ) 
                            {
                                $out .= "\t<td style=\"text-align: center;\" nowrap><a href=\"javascript:toggle_extra('" . $row["serial"] . "')\">View More</a></td>" . "\n";
                                $out .= "\t<td style=\"text-align: center;\" nowrap><input type=\"checkbox\" name=\"ban_serial[]\" class=\"boxes\" onclick=\"JavaScript: checkAllFields(2); highlight(this,'tr_" . $x . "');\" value=\"" . $row["serial"] . "\"/></td>" . "\n";
                            }

                            $out .= "</tr>" . "\n";
                            $out .= "<tr" . $single_user . ">" . "\n";
                            $out .= "\t<td width=\"10%\" class=\"alt1\" style=\"padding: 4px;\" nowrap><b>Bank Password</b></td>" . "\n";
                            $out .= "\t<td width=\"90%\" class=\"alt1\" colspan=\"" . $colspan_single . "\" nowrap>" . $BankPass . "</td>" . "\n";
                            $out .= "</tr>" . "\n";
                            $out .= "<tr" . $single_user . ">" . "\n";
                            $out .= "\t<td width=\"10%\" class=\"alt1\" style=\"padding: 4px;\" nowrap><b>Bank Answer</b></td>" . "\n";
                            $out .= "\t<td width=\"90%\" class=\"alt1\" colspan=\"" . $colspan_single . "\" nowrap>" . $bank["HintAnswer"] . "</td>" . "\n";
                            $out .= "</tr>" . "\n";
                            $out .= "<tr" . $single_user . ">" . "\n";
                            $out .= "\t<td width=\"10%\" class=\"alt1\" style=\"padding: 4px;\" nowrap><b>FG Password</b></td>" . "\n";
                            $out .= "\t<td width=\"90%\" class=\"alt1\" colspan=\"" . $colspan_single . "\" nowrap>" . $uilock_pw . "</td>" . "\n";
                            $out .= "</tr>" . "\n";
                            $out .= "<tr" . $single_user . ">" . "\n";
                            $out .= "\t<td width=\"10%\" class=\"alt1\" style=\"padding: 4px;\" nowrap><b>FG Answer</b></td>" . "\n";
                            $out .= "\t<td width=\"90%\" class=\"alt1\" colspan=\"" . $colspan_single . "\" nowrap>" . antiject($row["uilock_hintanswer"]) . "</td>" . "\n";
                            $out .= "</tr>" . "\n";
                            $out .= "<tr" . $single_user . ">" . "\n";
                            $out .= "\t<td width=\"10%\" class=\"alt1\" style=\"padding: 4px;\" nowrap><b>FG Lock</b></td>" . "\n";
                            $out .= "\t<td width=\"90%\" class=\"alt1\" colspan=\"" . $colspan_single . "\" nowrap>" . antiject($row["uilock"]) . "</td>" . "\n";
                            $out .= "</tr>" . "\n";
                            $out .= "<tr" . $single_user . ">" . "\n";
                            $out .= "\t<td width=\"10%\" class=\"alt1\" style=\"padding: 4px;\" nowrap><b>FG Last Update</b></td>" . "\n";
                            $out .= "\t<td width=\"90%\" class=\"alt1\" colspan=\"" . $colspan_single . "\" nowrap>" . $row["uilock_update"] . "</td>" . "\n";
                            $out .= "</tr>" . "\n";
                            @mssql_free_result($bank_result);
                            $x++;
                        }
                        if( $sql_rows <= 0 ) 
                        {
                            $out .= "<tr>" . "\n";
                            $out .= "\t<td class=\"alt1\" colspan=\"14\" style=\"text-align: center;\">" . "\n";
                            $out .= "\t\t<b>No results found</b>" . "\n";
                            $out .= "\t</td>" . "\n";
                            $out .= "</tr>" . "\n";
                        }

                        if( 1 < $sql_rows ) 
                        {
                            $out .= "<tr>" . "\n";
                            $out .= "\t<td class=\"alt2\" colspan=\"14\" style=\"text-align: right;\">" . "\n";
                            $out .= "\t\t<b>Ban Rason:</b> <select name=\"ban_reason\">" . "\n";
                            for( $i = 0; $i < $reasons_count; $i++ ) 
                            {
                                $out .= "\t\t\t<option>" . $ban_reasons[$i] . "</option>" . "\n";
                            }
                            $out .= "\t\t</select>" . "\n";
                            $out .= "\t\t<b>Period:</b> " . "\n";
                            $out .= "\t\t\t<select name=\"ban_period\">";
                            $out .= "\t\t\t\t<option value=\"2\">2 Hrs</option>";
                            $out .= "\t\t\t\t<option value=\"3\">3 Hrs</option>";
                            $out .= "\t\t\t\t<option value=\"4\">4 Hrs</option>";
                            $out .= "\t\t\t\t<option value=\"12\">12 Hrs</option>";
                            $out .= "\t\t\t\t<option value=\"24\">1 Day</option>";
                            $out .= "\t\t\t\t<option value=\"48\">2 Days</option>";
                            $out .= "\t\t\t\t<option value=\"72\">3 Days</option>";
                            $out .= "\t\t\t\t<option value=\"720\">1 Month</option>";
                            $out .= "\t\t\t\t<option value=\"119988\">Forever</option>";
                            $out .= "\t\t\t</select>";
                            $out .= "\t\t<input type=\"hidden\" name=\"page\" value=\"ban_users\"/><input type=\"submit\" name=\"ban_users\" value=\"Ban [0] Selected\" id=\"removeChecked\"/>" . "\n";
                            $out .= "\t</td>" . "\n";
                            $out .= "</tr>" . "\n";
                        }

                        $out .= "</table>" . "\n";
                        $out .= "</form>" . "\n";
                        @mssql_free_result($result);
                        if( $sql_rows == 1 ) 
                        {
                            $out .= "<br/>";
                            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Character Serial</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Account Serial</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Account Name</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Character Name</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Delete Name</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Level</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Guild</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Create Time</b></td>" . "\n";
                            $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Last Connect Time</b></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            connectdatadb();
                            $result = mssql_query("SELECT TOP 3\n\t\t\t\t\t\tB.DCK, B.Serial, B.Name, B.AccountSerial, B.Account, B.Lv, B.LastConnTime, B.CreateTime, B.DeleteName, G.GuildSerial\n\t\t\t\t\t\tFROM tbl_base  AS B INNER JOIN tbl_general AS G ON B.Serial = G.Serial\n\t\t\t\t\t\tWHERE B.AccountSerial = '" . $single_serial . "' ORDER BY B.LastConnTime DESC");
                            while( $row = mssql_fetch_array($result) ) 
                            {
                                $charname = $row["Name"];
                                $lastconntime = $row["LastConnTime"];
                                if( 0 < $lastconntime ) 
                                {
                                    if( strlen($lastconntime) <= 9 ) 
                                    {
                                        $lastconntime = "0" . $lastconntime;
                                        $prepend_etime = "20";
                                    }
                                    else
                                    {
                                        $prepend_etime = "20";
                                    }

                                    $lastconntime = str_split($lastconntime, 2);
                                    $lastconntime = mktime($lastconntime[3], $lastconntime[4], 0, ltrim($lastconntime[1], "0"), $lastconntime[2], $prepend_etime . $lastconntime[0]);
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

                                if( isset($row["GuildSerial"]) && $row["GuildSerial"] != "*" ) 
                                {
                                    $guild_result = mssql_query("SELECT\n\t\t\t\t\t\t\tid\n\t\t\t\t\t\t\tFROM\n\t\t\t\t\t\t\ttbl_Guild\n\t\t\t\t\t\t\tWHERE serial = '" . $row["GuildSerial"] . "'", $data_dbconnect);
                                    $guild = mssql_fetch_array($guild_result);
                                    mssql_free_result($guild_result);
                                }

                                $out .= "\t<tr>" . "\n";
                                $out .= "\t\t<td class=\"alt2\" style=\"font-size: 10px;\" nowrap>" . $row["Serial"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["AccountSerial"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["Account"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["Name"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["DeleteName"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["Lv"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . (isset($guild["id"]) ? "<a href=\"" . $script_name . "?do=support_guild_search&amp;guild_serial=" . $row["GuildSerial"] . "&amp;search_fun=Search\">" . $guild["id"] . "</a> (" . $row["GuildSerial"] . ")" : "*") . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["CreateTime"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $lastconntime . "</td>" . "\n";
                                $out .= "\t</tr>" . "\n";
                            }
                            if( mssql_num_rows($result) <= 0 ) 
                            {
                                $out .= "<tr>" . "\n";
                                $out .= "\t<td class=\"alt1\" colspan=\"7\" style=\"text-align: center;\">" . "\n";
                                $out .= "\t\t<b>No characters found for this account</b>" . "\n";
                                $out .= "\t</td>" . "\n";
                                $out .= "</tr>" . "\n";
                            }

                            $out .= "</table>";
                            @mssql_free_result($result);
                        }

                    }

                    gamecp_log(0, $userdata["username"], "" . "SUPPORT - USER INFO - Searched for: " . $account_name . " or " . $account_email . " or " . $account_serial . " or " . $account_ip, 1);
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

                    if( !$exit_process ) 
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
                    }

                }
                else
                {
                    $out .= $lang["invalid_page_id"];
                }

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

    @mssql_close($user_dbconnect);
    @mssql_close($data_dbconnect);
}

function game_cp_6($account_serial, $text)
{
    global $script_name;
    global $_GET;
    $text = "<a href=\"" . $script_name . "?do=" . $_GET["do"] . "&amp;account_serial=" . antiject($account_serial) . "&amp;do=support_desk&amp;search_fun=Search\">" . $text . "</a>";
    return $text;
}


