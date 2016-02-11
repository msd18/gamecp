<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Server"]["Player List"] = $file;
}
else
{
    $lefttitle = "Top Players List";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        $gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
        $top_limit = 50;
        $max_pages = 10;
        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
        $out .= "<tr>";
        $out .= "<td class=\"thead\" style=\"padding: 4px; text-align: center;\" nowrap><b>Rank</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px; text-align: center;\" nowrap><b>Status</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px; text-align: center;\" nowrap><b>Race</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px; text-align: center;\" nowrap><b>Level</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Player Name</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px; text-align: center;\" nowrap><b>Class</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px; text-align: center;\" nowrap><b>Total Time</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px; text-align: center;\" nowrap><b>PVP Points</b></td>";
        $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Guild</b></td>";
        $out .= "</tr>";
        connectdatadb();
        include("./includes/pagination/ps_pagination.php");
        $query_p1 = "SELECT\n\tB.AccountSerial, B.Account, B.Serial, B.Class, B.LastConnTime, G.TotalPlayMin, B.Name, B.lv, B.race, G.PvpPoint, P.GuildName\n\tFROM\n\ttbl_base AS B\n\tINNER JOIN\n\ttbl_general AS G\n\tON B.Serial = G.Serial\n\tLEFT JOIN\n\ttbl_PvpRankToday AS P\n\tON B.Serial = P.Serial\t\n\tWHERE B.DCK = '0'";
        $query_p2 = " AND B.Serial NOT IN \n\t( SELECT TOP [OFFSET] B.Serial\n\tFROM \n\ttbl_base AS B\n\tINNER JOIN\n\ttbl_general AS G\n\tON B.Serial = G.Serial\n\tLEFT JOIN\n\ttbl_PvpRankToday AS P\n\tON B.Serial = P.Serial\n\tWHERE B.DCK = '0'\n\tORDER BY G.PvpPoint DESC,G.Serial DESC ) ORDER BY G.PvpPoint DESC,G.Serial DESC";
        if( $gen == 1 ) 
        {
            $i = 1;
        }
        else
        {
            $i += ($gen - 1) * $top_limit + 1;
        }

        $filename = $_GET["do"] . "_" . md5($query_p1);
        if( !($query_count = readCache($filename . ".cache", 5)) ) 
        {
            $query_count = mssql_query("SELECT COUNT(Serial) AS Count FROM tbl_base WHERE DCK = '0'");
            $query_count = mssql_fetch_array($query_count);
            $query_count = $query_count["Count"];
            writeCache($query_count, $filename . ".cache");
        }

        $pager = new PS_Pagination($data_dbconnect, $query_p1, $query_p2, $top_limit, $max_pages, "" . $script_name . "?do=" . $_GET["do"], $query_count);
        $rs = $pager->paginate();
        while( $row = mssql_fetch_array($rs) ) 
        {
            connectitemsdb();
            if( !($class_info = mssql_query("SELECT class_name FROM tbl_Classes WHERE class_code = '" . $row["Class"] . "'", $items_dbconnect)) ) 
            {
                exit( "Sorry, error occured. You have a problem with your Items DB -> tbl_Classes" );
            }

            $class_info = mssql_fetch_array($class_info);
            $t_login = $row["LastConnTime"];
            $t_cur = time();
            $t_maxlogin = $t_login + 2592000;
            connectuserdb();
            $user_select = "SELECT TOP 1 LastLogOffTime, LastLoginTime FROM tbl_UserAccount WHERE Serial = '" . $row["AccountSerial"] . "'";
            if( !($user_result = @mssql_query($user_select, $user_dbconnect)) ) 
            {
                $status = "offline";
            }
            else
            {
                if( !($user_fetch = mssql_fetch_array($user_result)) ) 
                {
                    $status = "offline";
                }
                else
                {
                    $status = "online";
                }

            }

            connectdatadb();
            $date = date("Ym");
            $select_log = "" . "SELECT TOP 1 CharacSerial FROM tbl_characterselect_log_" . $date . " WHERE AccountSerial = '" . $row["AccountSerial"] . "' ORDER BY ID DESC";
            if( !($select_result = @mssql_query($select_log, $data_dbconnect)) ) 
            {
                $status = "offline";
            }
            else
            {
                if( !($log_fetch = mssql_fetch_array($select_result)) ) 
                {
                    $status = "offline";
                }
                else
                {
                    $status = "online";
                }

            }

            $t_login = strtotime($user_fetch["LastLoginTime"]);
            $t_logout = strtotime($user_fetch["LastLogOffTime"]);
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
                    if( $log_fetch["CharacSerial"] == $row["Serial"] ) 
                    {
                        $status = "online";
                    }
                    else
                    {
                        $status = "offline";
                    }

                }

            }

            if( $row["race"] == 0 || $row["race"] == 1 ) 
            {
                $race = "<span style=\"color: #CC6699;\">Bell</span>";
            }
            else
            {
                if( $row["race"] == 2 || $row["race"] == 3 ) 
                {
                    $race = "<span style=\"color: #9933CC;\">Cora</span>";
                }
                else
                {
                    if( $row["race"] == 4 ) 
                    {
                        $race = "<span style=\"color: grey;\">Acc</span>";
                    }

                }

            }

            if( $row["GuildName"] == "" ) 
            {
                $row["GuildName"] = "*";
            }

            $out .= "<tr>";
            $out .= "<td class=\"alt2\" style=\"padding: 4px; text-align: center;\" width=\"5%\"><b>" . $i . "</b></td>";
            $out .= "<td class=\"alt2\" style=\"padding: 4px; text-align: center;\" width=\"5%\"><b><img src=\"./includes/images/" . $status . ".gif\" /></b></td>";
            $out .= "<td class=\"alt1\" style=\"padding: 4px; text-align: center;\" width=\"5%\"><b>" . $race . "</b></td>";
            $out .= "<td class=\"alt1\" style=\"padding: 4px; text-align: center;\" width=\"5%\"><b>" . $row["lv"] . "</b></td>";
            $out .= "<td class=\"alt1\" style=\"padding: 4px;\" width=\"40%\"><b>" . $row["Name"] . "</b></td>";
            $out .= "<td class=\"alt1\" style=\"padding: 4px; text-align: center;\" width=\"5%\"><b>" . $class_info["class_name"] . "</b></td>";
            $out .= "<td class=\"alt1\" style=\"padding: 4px; text-align: center;\" width=\"10%\"><b>" . number_format(round($row["TotalPlayMin"] / 60)) . " h</b></td>";
            $out .= "<td class=\"alt1\" style=\"padding: 4px; text-align: center;\" width=\"10%\"><b>" . number_format(round($row["PvpPoint"])) . "</b></td>";
            $out .= "<td class=\"alt1\" style=\"padding: 4px;\" width=\"20%\"><b>" . $row["GuildName"] . "</b></td>";
            $out .= "</tr>";
            $i++;
            @mssql_free_result($select_result);
            @mssql_free_result($user_result);
        }
        $out .= "<tr>";
        $out .= "<td colspan=\"9\" style=\"text-align: center;\">" . $pager->renderFullNav() . "</td>";
        $out .= "</tr>";
        $out .= "</table>";
        @mssql_free_result($rs);
    }
    else
    {
        $out .= $lang["invalid_page_load"];
    }

}


