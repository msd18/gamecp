<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Donations"]["Item Redeem Logs"] = $file;
}
else
{
    $lefttitle = "Item Redeem Logs";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        $max_pages = 10;
        $top_limit = 25;
        if( $isuser ) 
        {
            $gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
            $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">" . number_format($userdata["points"], 2) . "</span> Game Points</p>" . "\n";
            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\" nowrap>#</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Date</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Item Name</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Character Name</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>Item Price</td>" . "\n";
            $out .= "\t\t<td class=\"thead\" nowrap>GP After Purchase</td>" . "\n";
            $out .= "\t</tr>" . "\n";
            connectgamecpdb();
            $query_p1 = "SELECT \r\n\t\tR.redeem_char_id, R.redeem_price, R.redeem_item_id, R.redeem_total_gp, R.redeem_time, R.redeem_item_name, I.item_name, I.item_delete\r\n\t\tFROM \r\n\t\t\tgamecp_redeem_log AS R \r\n\t\t\tLEFT JOIN \r\n\t\t\tgamecp_shop_items AS I\r\n\t\t\tON R.redeem_item_id = I.item_id\r\n\t\tWHERE R.redeem_account_id = '" . $userdata["serial"] . "'";
            $query_p2 = " AND R.redeem_id NOT IN ( SELECT TOP [OFFSET] R.redeem_id FROM \r\n\t\t\tgamecp_redeem_log AS R \r\n\t\t\tLEFT JOIN \r\n\t\t\tgamecp_shop_items AS I\r\n\t\t\t\tON R.redeem_item_id = I.item_id\r\n\t\t\tWHERE R.redeem_account_id = '" . $userdata["serial"] . "' ORDER BY R.redeem_id DESC) ORDER BY R.redeem_id DESC";
            include("./includes/pagination/ps_pagination.php");
            $pager = new PS_Pagination($gamecp_dbconnect, $query_p1, $query_p2, $top_limit, $max_pages, "" . $script_name . "?do=" . $_GET["do"]);
            $rs = $pager->paginate();
            if( $gen == 1 ) 
            {
                $i = 1;
            }
            else
            {
                $i += ($gen - 1) * $top_limit + 1;
            }

            connectdatadb();
            while( $row = mssql_fetch_array($rs) ) 
            {
                $char_result = mssql_query("SELECT Name,DCK FROM tbl_base WHERE Serial = '" . $row["redeem_char_id"] . "'");
                $char = mssql_fetch_array($char_result);
                $char_name = $char["Name"] != "" ? $char["Name"] : "Unknown";
                if( $char["DCK"] == 1 ) 
                {
                    $char_name = "<i>" . $char_name . "</i>";
                }

                if( $row["item_delete"] == 1 ) 
                {
                    $item_name = "<i>" . $row["item_name"] . "</i>";
                }
                else
                {
                    if( $row["redeem_item_name"] != "" ) 
                    {
                        $item_name = $row["redeem_item_name"];
                    }
                    else
                    {
                        $item_name = "<i>Unknown</i>";
                    }

                }

                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt2\" style=\"text-align: center;\" nowrap>" . $i . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . date("d/m/y h:i:s A", $row["redeem_time"]) . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . $item_name . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . $char_name . "</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . number_format($row["redeem_price"], 2) . " GP</td>" . "\n";
                $out .= "\t\t<td class=\"alt1\" nowrap>" . number_format($row["redeem_total_gp"], 2) . " GP</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                mssql_free_result($char_result);
                $i++;
            }
            if( mssql_num_rows($rs) <= 0 ) 
            {
                $out .= "\t\t<tr>" . "\n";
                $out .= "\t\t\t<td class=\"alt1\" colspan=\"6\" style=\"text-align: center; font-weight: bold;\">No redeem logs found for your account.</td>" . "\n";
                $out .= "\t\t</tr>" . "\n";
            }
            else
            {
                $out .= "\t\t<tr>" . "\n";
                $out .= "\t\t\t<td class=\"alt2\" colspan=\"6\" style=\"text-align: center; font-weight: bold;\">" . $pager->renderFullNav() . "</td>" . "\n";
                $out .= "\t\t</tr>" . "\n";
            }

            $out .= "</table>";
            @mssql_free_result($rs);
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


