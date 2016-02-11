<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Logs"]["Rented Items Logs"] = $file;
}
else
{
    $lefttitle = "ItemCharge/Rented Items Logs";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            connectdatadb();
            $result = mssql_query("SELECT TOP 100 \r\n\t\tB.Name, C.nSerial, C.nAvatorSerial, C.nItemCode_K, C.nItemCode_D, C.nItemCode_U, C.DCK, C.T, C.[dtGiveDate], C.[dtTakeDate]\r\n\t\tFROM tbl_ItemCharge AS C\r\n\t\tINNER JOIN\r\n\t\ttbl_base AS B\r\n\t\tON B.Serial = C.nAvatorSerial\r\n\t\tORDER BY C.nSerial DESC");
            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
            $out .= "<tr>";
            $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>nSerial</b></td>";
            $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>nAvatorSerial</b></td>";
            $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Name</b></td>";
            $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Item Name</b></td>";
            $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Item Amount</b></td>";
            $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>In Use?</b></td>";
            $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Give Time</b></td>";
            $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Take Time</b></td>";
            $out .= "<td class=\"thead\"style=\"padding: 4px;\" nowrap><b>Time</b></td>";
            $out .= "</tr>";
            connectitemsdb();
            while( $row = mssql_fetch_array($result) ) 
            {
                $iteminfo = itemcode("convert", $row["nItemCode_K"]);
                $items_query = mssql_query("SELECT item_id,item_name,item_code FROM " . getitemtablename($iteminfo["type"]) . " WHERE item_id = '" . $iteminfo["id"] . "'", $items_dbconnect);
                $items = mssql_fetch_array($items_query);
                $rented_code = $items["item_code"];
                $rented_name = $items["item_name"];
                mssql_free_result($items_query);
                $out .= "<tr>";
                $out .= "<td class=\"alt2\" nowrap>" . $row["nSerial"] . "</td>";
                $out .= "<td class=\"alt1\" nowrap>" . $row["nAvatorSerial"] . "</td>";
                $out .= "<td class=\"alt1\" nowrap>" . $row["Name"] . "</td>";
                $out .= "<td class=\"alt1\" nowrap>" . str_replace("_", " ", $rented_name) . "</td>";
                $out .= "<td class=\"alt1\" nowrap>" . $row["nItemCode_D"] . "</td>";
                $out .= "<td class=\"alt1\" nowrap>" . ($row["DCK"] == 1 ? "Yes" : "No") . "</td>";
                $out .= "<td class=\"alt1\" nowrap>" . $row["dtGiveDate"] . "</td>";
                $out .= "<td class=\"alt1\" nowrap>" . ($row["DCK"] == 1 ? $row["dtTakeDate"] : "-") . "</td>";
                $out .= "<td class=\"alt1\" nowrap>" . round($row["T"] / 3600) . " Hr</td>";
                $out .= "</td>";
            }
            $out .= "</table>";
            mssql_free_result($result);
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

function game_cp_30($input_type = "convert", $input, $type = false, $slot = false)
{
    $item = array(  );
    if( $input_type == "convert" ) 
    {
        $dechex = dechex($input);
        $game_cp_24 = strlen($dechex) - 4;
        $game_cp_25 = substr($dechex, 0, $game_cp_24);
        $item["id"] = hexdec($game_cp_25);
        $game_cp_26 = strlen($dechex) - strlen($game_cp_25) - 2;
        $game_cp_27 = substr($dechex, $game_cp_24, $game_cp_26);
        $item["type"] = hexdec($game_cp_27);
        $game_cp_28 = strlen($dechex) - strlen($game_cp_25) - strlen($game_cp_27);
        $game_cp_29 = substr($dechex, $game_cp_24 + $game_cp_26, $game_cp_28);
        $item["slot"] = hexdec($game_cp_29);
        return $item;
    }

    if( $input_type == "make" ) 
    {
        if( $type ) 
        {
            return 65536 * $input + $type * 256 + $slot;
        }

        return false;
    }

    return false;
}


