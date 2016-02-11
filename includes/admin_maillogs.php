<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Logs"]["Mail Logs"] = $file;
}
else
{
    $lefttitle = "Support Desk - Mail Logs";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            if( empty($page) ) 
            {
                $char_sendname = isset($_GET["char_sendname"]) ? $_GET["char_sendname"] : "";
                $char_recvname = isset($_GET["char_recvname"]) ? $_GET["char_recvname"] : "";
                $char_content = isset($_GET["char_content"]) ? $_GET["char_content"] : "";
                $out .= "<form method=\"get\" action=\"" . $script_name . "?do=admin_maillogs\">";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Look up a user</b></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Send Name:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"char_sendname\" value=\"" . $char_sendname . "\"/></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Recieve Name:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"char_recvname\" value=\"" . $char_recvname . "\"/></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Content:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"char_content\" value=\"" . $char_content . "\"/></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td colspan=\"2\"><input type=\"submit\" value=\"Search\" name=\"search_fun\" /></td>";
                $out .= "</tr>";
                $out .= "</table>";
                $out .= "<input type=\"hidden\" name=\"do\" value=\"admin_maillogs\"/>";
                $out .= "</form>";
                $out .= "<br/>" . "\n";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>sendserial</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>date</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>sendname</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>recvname</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>title</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>content</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>k</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>d</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>u</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\"><b>gold</b></td>";
                $out .= "</tr>";
                if( $search_fun != "" ) 
                {
                    $char_sendname = isset($_GET["char_sendname"]) ? $_GET["char_sendname"] : "";
                    $char_recvname = isset($_GET["char_recvname"]) ? $_GET["char_recvname"] : "";
                    $char_content = isset($_GET["char_content"]) ? $_GET["char_content"] : "";
                    $enable_exit = false;
                    if( $char_sendname == "" && $char_recvname == "" && $char_content == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p style=\"text-align:center;\"><b>Enter a send or recieve character name or content</b></p>";
                    }

                    if( $enable_exit != true ) 
                    {
                        if( $char_sendname != "" ) 
                        {
                            $char_sendname = ereg_replace("" . ";\$", "", $char_sendname);
                            $char_sendname = ereg_replace("\\\\", "", $char_sendname);
                            $search_query = " AND mail_sendname = '" . $char_sendname . "' ORDER BY mail_id DESC";
                        }
                        else
                        {
                            if( $char_recvname != "" ) 
                            {
                                $char_recvname = ereg_replace("" . ";\$", "", $char_recvname);
                                $char_recvname = ereg_replace("\\\\", "", $char_recvname);
                                $search_query = " AND mail_recvname = '" . $char_recvname . "' ORDER BY mail_id DESC";
                            }
                            else
                            {
                                $char_content = ereg_replace("" . ";\$", "", $char_content);
                                $char_content = ereg_replace("\\\\", "", $char_content);
                                if( !preg_match("/%/", $char_content) ) 
                                {
                                    $search_query = " AND mail_content LIKE '%" . $char_content . "%' ORDER BY mail_id DESC";
                                }
                                else
                                {
                                    $search_query = " AND mail_content LIKE '" . $char_content . "' ORDER BY mail_id DESC";
                                }

                            }

                        }

                        gamecp_log(0, $userdata["username"], "" . "ADMIN - MAIL LOGS - Searched for: " . $char_sendname . " or " . $char_recvname . " or " . $char_content, 1);
                    }

                }
                else
                {
                    $search_query = "";
                }

                include("./includes/pagination/ps_pagination.php");
                connectdatadb();
                $query_text = "SELECT mail_date, mail_dck, mail_sendserial, mail_sendname, mail_recvname, mail_title, mail_content, mail_k, mail_u, mail_d, mail_gold FROM tbl_MailLogs WHERE mail_dck = 0 " . $search_query;
                if( $search_query == "" ) 
                {
                    $query_p2 = " AND mail_id NOT IN ( SELECT TOP [OFFSET] mail_id FROM tbl_MailLogs WHERE mail_dck = 0 ORDER BY mail_id DESC) ORDER BY mail_id DESC";
                    $pager = new PS_Pagination($data_dbconnect, $query_text, $query_p2, 35, 5, "" . $script_name . "?do=admin_maillogs");
                    $rs = $pager->paginate();
                    $out .= "Total number of logs found: " . number_format($pager->totalResults());
                }
                else
                {
                    $rs = mssql_query($query_text);
                }

                connectitemsdb();
                while( $row = mssql_fetch_array($rs) ) 
                {
                    $i++;
                    $k_value = $row["mail_k"];
                    $u_value = $row["mail_u"];
                    if( "-1" < $k_value ) 
                    {
                        $slot = 0;
                        $kn = 0;
                        for( $n = 0; $n < $item_tbl_num; $n++ ) 
                        {
                            $item_id = ($k_value - $n * (256 + $slot)) / 65536;
                            if( $item_id == $k_value ) 
                            {
                                $kn = $n;
                            }

                        }
                        $item_id = ceil($item_id);
                        $kn = floor(($k_value - $item_id * 65536) / 256);
                        $items_query = mssql_query("SELECT item_code, item_id, item_name FROM " . getitemtablename($kn) . "" . " WHERE item_id = '" . $item_id . "'", $items_dbconnect);
                        $items = mssql_fetch_array($items_query);
                        if( $items["item_name"] != "" ) 
                        {
                            $item_id = str_replace("_", " ", $items["item_name"]);
                        }
                        else
                        {
                            $item_id = "Not found in DB - " . $item_id . ":" . $kn;
                        }

                        $item_code = $items["item_code"];
                        @mssql_free_result($items_query);
                    }
                    else
                    {
                        $item_id = $k_value;
                        $item_code = "-";
                    }

                    $base_code = 268435455;
                    $item_slots = $u_value;
                    $item_slots = $item_slots - $base_code;
                    $item_slots = $item_slots / ($base_code + 1);
                    $upgrades = "";
                    $ceil_slots = ceil($item_slots);
                    $slots_code = $base_code + ($base_code + 1) * $ceil_slots;
                    $slots = $ceil_slots;
                    if( 0 < $ceil_slots && "-1" < $k_value ) 
                    {
                        if( $slots_code != $u_value ) 
                        {
                            for( $m = 1; $m <= 7; $m++ ) 
                            {
                                $item_ups = $u_value;
                                $item_ups = ($base_code + ($base_code + 1) * $ceil_slots) - $item_ups;
                                $talic_id = (pow(16, $m) - 1) / 15;
                                $talic_id = ceil($item_ups / $talic_id);
                                $raw_item_u = ceil($base_code + ($base_code + 1) * $ceil_slots) - $talic_id * (pow(16, $m) - 1) / 15;
                                if( $raw_item_u == $u_value ) 
                                {
                                    $ups = $m;
                                    $slots = $ceil_slots - $ups;
                                    for( $k = 0; $k < $ups; $k++ ) 
                                    {
                                        $upgrades .= "<img src=\"./includes/images/talics/t-" . sprintf("%02d", 16 - $talic_id) . ".png\" width=\"10\"/>";
                                    }
                                }

                            }
                        }

                        for( $j = 0; $j < $slots; $j++ ) 
                        {
                            $upgrades .= "<img src=\"./includes/images/talics/t-00.png\" width=\"10\"/>";
                        }
                        $bgc = "background-color: #10171f;";
                    }
                    else
                    {
                        $upgrades = "No Upgrades";
                        $bgc = "";
                    }

                    if( $row["mail_date"] == "" ) 
                    {
                        $row["mail_date"] = "No Date";
                    }

                    $out .= "<tr>";
                    $out .= "<td class=\"alt2\" style=\"text-align: center; font-size: 9px;\" width=\"2\" nowrap>" . $row["mail_sendserial"] . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 9px;\" nowrap>" . $row["mail_date"] . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 9px;\" nowrap>" . $row["mail_sendname"] . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 9px;\" nowrap>" . $row["mail_recvname"] . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 9px;\" nowrap>" . $row["mail_title"] . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 9px;\" nowrap>";
                    if( trim($row["mail_content"]) != "" ) 
                    {
                        $out .= "<a href=\"#\" id=\"c" . $i . "-show\" class=\"showLink\" onclick=\"showHide('c" . $i . "'); return false;\">[show content]</a>";
                        $out .= "<div id=\"c" . $i . "\" class=\"more\">" . $row["mail_content"];
                        $out .= "<br/><a href=\"#\" id=\"c" . $i . "-hide\" class=\"hideLink\" onclick=\"showHide('c" . $i . "');return false;\">[hide content]</a></div>";
                    }
                    else
                    {
                        $out .= "<i>Empty</i>";
                    }

                    $out .= "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 9px;\" nowrap>" . $item_id . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 9px;\" nowrap>" . $row["mail_d"] . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 9px; " . $bgc . "\" nowrap>" . $upgrades . "</td>";
                    $out .= "<td class=\"alt1\" style=\"font-size: 9px;\" nowrap>" . $row["mail_gold"] . "</td>";
                    $out .= "</td>";
                }
                if( mssql_num_rows($rs) <= 0 ) 
                {
                    $out .= "<tr>" . "\n";
                    $out .= "<td colspan=\"10\" style=\"text-align: center;\" class=\"alt1\">No mail logs found</td>" . "\n";
                    $out .= "</tr>" . "\n";
                }

                $out .= "</table>";
                $out .= "<br/>";
                if( $search_query == "" ) 
                {
                    $out .= $pager->renderFullNav();
                }

                @mssql_free_result($rs);
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


