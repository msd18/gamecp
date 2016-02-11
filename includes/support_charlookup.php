<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Support"]["Char Lookup"] = $file;
}
else
{
    $lefttitle = "Support Desk - Character Look Up";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $enable_exit = false;
            if( empty($page) ) 
            {
                $out .= "<form method=\"get\" action=\"" . $script_name . "?do=support_charlookup\">";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Look up a user</b></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Account Name:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_name\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Account Serial:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_serial\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Character Serial:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_charserial\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Character Name:<br/><span style=\"font-size: 9px;\">Use % as a wild card. <b>DO NOT MAKE THE SEARCH TOO GENERAL!</b></span></td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_char\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Delete Name:<br/><span style=\"font-size: 9px;\">Use % as a wild card. <b>DO NOT MAKE THE SEARCH TOO GENERAL!</b></span></td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"account_delchar\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td colspan=\"2\"><input type=\"hidden\" name=\"do\" value=\"" . $_GET["do"] . "\"><input type=\"submit\" value=\"Look Up\" name=\"search_fun\" /></td>";
                $out .= "</tr>";
                $out .= "</table>";
                $out .= "</form>";
                if( $search_fun != "" ) 
                {
                    $out .= "<br/><br/>";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                    $out .= "<tr>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Character Serial</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Account Serial</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Account Name</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Character Name</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Delete Name</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Level</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Guild</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Base Class</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>1st Class</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>2nd Class</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>3rd Class</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>X Y Z</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Create Time</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Last Connect Time</b></td>";
                    $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Options</b></td>";
                    $out .= "</tr>";
                    $account_serial = is_int((int) $_GET["account_serial"]) ? antiject((int) $_GET["account_serial"]) : "";
                    $account_name = isset($_GET["account_name"]) ? $_GET["account_name"] : "";
                    $account_char = isset($_GET["account_char"]) ? $_GET["account_char"] : "";
                    $account_delchar = isset($_GET["account_delchar"]) ? $_GET["account_delchar"] : "";
                    $account_charserial = is_int((int) $_GET["account_charserial"]) ? antiject((int) $_GET["account_charserial"]) : "";
                    if( $account_serial == 0 && $account_name == "" && $account_char == "" && $account_delchar == "" && $account_charserial == 0 ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>Sorry, make sure you filled in either the name or account serial or character name or deleted char name</b></p>";
                        $out .= "</table>";
                    }

                    if( $account_name != "" ) 
                    {
                        connectuserdb();
                        $query_result = mssql_query("SELECT serial FROM tbl_UserAccount WHERE id = CONVERT(binary,\"" . $account_name . "\")");
                        $query = mssql_fetch_array($query_result);
                        $account_serial = $query["serial"];
                        @mssql_free_result($query_result);
                        $out .= mssql_get_last_message();
                    }

                    if( $account_char != "" ) 
                    {
                        if( !preg_match("/%/", $account_char) ) 
                        {
                            $search_query = "B.Name = '" . $account_char . "'";
                        }
                        else
                        {
                            $search_query = "B.Name LIKE '" . str_replace("'", "''", $account_char) . "'";
                        }

                    }
                    else
                    {
                        if( $account_delchar != "" ) 
                        {
                            if( !preg_match("/%/", $account_delchar) ) 
                            {
                                $search_query = "B.DeleteName = '" . $account_delchar . "'";
                            }
                            else
                            {
                                $search_query = "B.DeleteName LIKE '" . str_replace("'", "''", $account_delchar) . "'";
                            }

                        }
                        else
                        {
                            if( $account_charserial != 0 ) 
                            {
                                $search_query = "B.Serial = '" . $account_charserial . "'";
                            }
                            else
                            {
                                $search_query = "B.AccountSerial = '" . $account_serial . "'";
                            }

                        }

                    }

                    if( $enable_exit != true ) 
                    {
                        $account_serial = antiject($account_serial);
                        $account_name = antiject($account_name);
                        connectdatadb();
                        $result = mssql_query("" . "SELECT\r\n\t\t\t\t\tB.DCK, B.Serial, B.Name, B.AccountSerial, B.Account, B.Lv, B.LastConnTime, B.CreateTime, B.DeleteName, B.Class, G.Class0, G.Class1, G.Class2, G.ClassInitCnt, G.GuildSerial, G.X, G.Y, G.Z\r\n\t\t\t\t\t\tFROM tbl_base AS B\r\n\t\t\t\t\tINNER JOIN\r\n\t\t\t\t\t\ttbl_general AS G\r\n\t\t\t\t\tON B.Serial = G.Serial\r\n\t\t\t\t\tWHERE " . $search_query . " ORDER BY B.LastConnTime DESC");
                        while( $row = mssql_fetch_array($result) ) 
                        {
                            $charname = $row["Name"];
                            if( $row["DCK"] == 1 ) 
                            {
                                $delete_restore = "Delete | <u><b><a href=\"" . $_SERVER["REQUEST_URI"] . "&page=restore&charserial=" . $row["Serial"] . "\">Restore</a></b></u>";
                            }
                            else
                            {
                                $delete_restore = "<u><b><a href=\"" . $_SERVER["REQUEST_URI"] . "&page=delete&charserial=" . $row["Serial"] . "\">Delete</a></b></u> | Restore";
                            }

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

                            if( isset($row["GuildSerial"]) && $row["GuildSerial"] != "*" ) 
                            {
                                connectdatadb();
                                $guild_result = mssql_query("SELECT\r\n\t\t\t\t\t\t\tid\r\n\t\t\t\t\t\t\tFROM\r\n\t\t\t\t\t\t\ttbl_Guild\r\n\t\t\t\t\t\t\tWHERE serial = '" . $row["GuildSerial"] . "'", $data_dbconnect);
                                $guild = mssql_fetch_array($guild_result);
                                mssql_free_result($guild_result);
                            }

                            connectitemsdb();
                            $class_query = "SELECT class_id, class_code, class_name FROM tbl_Classes WHERE class_name != ' '";
                            $class_query = @mssql_query($class_query, $items_dbconnect) or exit( "Error! Items DB doesn't seem to contain the classes table" );
                            $base_class = "";
                            $first_class = "";
                            $second_class = "";
                            $third_class = "";
                            while( $classinfo = mssql_fetch_array($class_query) ) 
                            {
                                $race = substr($classinfo["class_code"], 0, 1);
                                if( $race == "B" ) 
                                {
                                    $classinfo["class_name"] = "Bell " . $classinfo["class_name"];
                                }
                                else
                                {
                                    if( $race == "A" ) 
                                    {
                                        $classinfo["class_name"] = "Accretian " . $classinfo["class_name"];
                                    }
                                    else
                                    {
                                        if( $race == "C" ) 
                                        {
                                            $classinfo["class_name"] = "Cora " . $classinfo["class_name"];
                                        }

                                    }

                                }

                                $base_class = $base_class == "" ? $row["Class0"] == $classinfo["class_id"] ? $classinfo["class_name"] : "" : $base_class;
                                $first_class = $first_class == "" ? $row["Class1"] == $classinfo["class_id"] ? $classinfo["class_name"] : "" : $first_class;
                                $second_class = $second_class == "" ? $row["Class"] == $classinfo["class_code"] ? $classinfo["class_name"] : "" : $second_class;
                                $third_class = $third_class == "" ? $row["Class2"] == $classinfo["class_id"] ? $classinfo["class_name"] : "" : $third_class;
                            }
                            @mssql_free_result($class_query);
                            $xyz = $row["X"] . " " . $row["Y"] . " " . $row["Z"];
                            if( !preg_match("/\\./", $xyz) ) 
                            {
                                $xyz = "<span style=\"color: red; font-weight: bold;\">" . $xyz . "</span>";
                            }

                            $out .= "<tr>";
                            $out .= "<td class=\"alt2\" style=\"font-size: 10px;\" nowrap>" . $row["Serial"] . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["AccountSerial"] . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . game_cp_14($row["AccountSerial"], $row["Account"]) . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["Name"] . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["DeleteName"] . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["Lv"] . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . (isset($guild["id"]) ? "<a href=\"" . $script_name . "?do=support_guild_search&amp;guild_serial=" . $row["GuildSerial"] . "&amp;search_fun=Search\">" . $guild["id"] . "</a> (" . $row["GuildSerial"] . ")" : "*") . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $base_class . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $first_class . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $second_class . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $third_class . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $xyz . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $row["CreateTime"] . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px;\" nowrap>" . $lastconntime . "</td>";
                            $out .= "<td class=\"alt1\" style=\"font-size: 10px; text-align: center;\" nowrap>" . $delete_restore . "</td>";
                            $out .= "</tr>";
                        }
                        @mssql_free_result($result);
                        $out .= "</table>";
                        gamecp_log(0, $userdata["username"], "" . "ADMIN - CHAR LOOK UP - Searched for: " . $account_name . " or " . $account_serial . " or " . $account_char . " or " . $account_delchar, 1);
                        return 1;
                    }

                }

            }
            else
            {
                if( $page == "delete" ) 
                {
                    connectdatadb();
                    $charserial = is_int((int) $_GET["charserial"]) ? antiject((int) $_GET["charserial"]) : 0;
                    if( $charserial != 0 ) 
                    {
                        $cquery = mssql_query("UPDATE tbl_base SET deletename=name  WHERE serial = " . $charserial);
                        $cquery = mssql_query("UPDATE tbl_base SET name='*'+cast(serial as varchar)  WHERE serial = " . $charserial);
                        $cquery = mssql_query("UPDATE tbl_base SET DCK = 1,  Arrange = 1, DeleteTime = getdate()  WHERE Serial = " . $charserial);
                        gamecp_log(4, $userdata["username"], "" . "ADMIN - DELETE CHARACTER - Character Serial: " . $charserial, 1);
                    }

                    header("Location: ./" . $script_name . "?account_name=" . $_GET["account_name"] . "&account_serial=" . $_GET["account_serial"] . "&account_charserial=" . $_GET["account_charserial"] . "&account_char=" . $_GET["account_char"] . "&account_delchar=" . $_GET["account_delchar"] . "&do=support_charlookup&search_fun=Look+Up");
                    return 1;
                }

                if( $page == "restore" ) 
                {
                    connectdatadb();
                    $charserial = isset($_GET["charserial"]) && is_int((int) $_GET["charserial"]) ? (int) $_GET["charserial"] : 0;
                    if( $charserial != 0 ) 
                    {
                        $cquery = mssql_query("UPDATE tbl_base SET name=deletename  WHERE serial = " . $charserial) or $fail = true;
						
						if( !$fail ) 
{
    $cquery = mssql_query("UPDATE tbl_base SET deletename='*'  WHERE serial = " . $charserial);
    $cquery = mssql_query("UPDATE tbl_base SET DCK=0 WHERE serial = " . $charserial);
}

                        gamecp_log(3, $userdata["username"], "" . "ADMIN - RESTORE CHARACTER - Character Serial: " . $charserial, 1);
                    }

                    header("Location: ./" . $script_name . "?account_name=" . $_GET["account_name"] . "&account_serial=" . $_GET["account_serial"] . "&account_charserial=" . $_GET["account_charserial"] . "&account_char=" . $_GET["account_char"] . "&account_delchar=" . $_GET["account_delchar"] . "&do=support_charlookup&search_fun=Look+Up");
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

function game_cp_13($account_serial, $text)
{
    global $script_name;
    global $_GET;
    $text = "<a href=\"" . $script_name . "?do=" . $_GET["do"] . "&amp;account_serial=" . antiject($account_serial) . "&amp;do=support_desk&amp;search_fun=Search\">" . $text . "</a>";
    return $text;
}


