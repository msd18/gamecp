<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Donations"]["Buy Rented Items"] = $file;
}
else
{
    $datetime = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( $isuser ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $lefttitle = "Buy Rented Items";
            $time = date("F j Y G:i");
            $base_code = 268435455;
            if( empty($page) ) 
            {
                $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">" . number_format($userdata["points"], 2) . "</span> Game Points</p>" . "\n";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr align=\"center\">" . "\n";
                $out .= "\t\t<td class=\"tcat\" align=\"center\"></td>" . "\n";
                $out .= "\t\t<td class=\"tcat\" align=\"center\">Jade Name</td>" . "\n";
                $out .= "\t\t<td class=\"tcat\" align=\"center\">Time</td>" . "\n";
                $out .= "\t\t<td class=\"tcat\" align=\"center\">Price</td>" . "\n";
                $out .= "\t\t<td class=\"tcat\" align=\"center\"></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                connectgamecpdb();
                $select_items = "SELECT rented_id, rented_name, rented_k, rented_u, rented_d, rented_time, rented_desc, rented_price FROM gamecp_rented_items ORDER BY rented_name DESC";
                if( !($result_items = mssql_query($select_items)) ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Error in Query</p>";
                }

                connectitemsdb();
                while( $row = mssql_fetch_array($result_items) ) 
                {
                    $iteminfo = itemcode("convert", $row["rented_k"]);
                    $items_query = mssql_query("SELECT item_code FROM " . getitemtablename($iteminfo["type"]) . " WHERE item_id = '" . $iteminfo["id"] . "'", $items_dbconnect);
                    $items = mssql_fetch_array($items_query);
                    $item_code = $items["item_code"];
                    $image_path = glob("" . "./includes/images/items/" . $item_code . "{.jpg,.JPG,.gif,.GIF,.png,.PNG}", GLOB_BRACE);
                    if( file_exists($image_path[0]) ) 
                    {
                        $item_image = $image_path[0];
                    }
                    else
                    {
                        $item_image = "./includes/images/items/unknown.gif";
                    }

                    if( $userdata["points"] < $row["rented_price"] ) 
                    {
                        $disable_button = " DISABLED";
                    }
                    else
                    {
                        $disable_button = "";
                    }

                    $out .= "<form method=\"post\">" . "\n";
                    $out .= "<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" align=\"center\"><img src=\"" . $item_image . "\" width=\"34\" height=\"34\"/></td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\"><span style=\"font-weight: bold; font-size: 14px;\">" . str_replace("''", "'", $row["rented_name"]) . "</span><br/><span style=\"color:#AFAFAF\">" . str_replace("''", "'", $row["rented_desc"]) . "</span></td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" align=\"center\">" . round($row["rented_time"] / 3600) . " Hr</td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" align=\"center\">" . number_format($row["rented_price"]) . " GP</td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" align=\"center\"><input type=\"hidden\" value=\"select\" name=\"page\"/><input type=\"hidden\" value=\"" . $row["rented_id"] . "\" name=\"item_id\"/><input type=\"submit\" name=\"buy\" value=\"Buy Now\"" . $disable_button . "/></td>" . "\n";
                    $out .= "</tr>" . "\n";
                    $out .= "</form>" . "\n";
                }
                if( mssql_num_rows($result_items) <= 0 ) 
                {
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"alt1\" colspan=\"6\" style=\"text-align: center; font-weight: bold;\">No rented items available for purchase</td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                }

                mssql_free_result($result_items);
                $out .= "</table>" . "\n";
                return 1;
            }

            if( $page == "select" ) 
            {
                $exit_buy = 0;
                $item_id = isset($_POST["item_id"]) && is_int((int) $_POST["item_id"]) ? (int) $_POST["item_id"] : "";
                if( $item_id == "" ) 
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid Item ID provided</p>" . "\n";
                }

                $t_login = strtotime($userdata["lastlogintime"]);
                $t_logout = strtotime($userdata["lastlogofftime"]);
                $t_cur = time();
                if( $t_login <= $t_logout ) 
                {
                    $status = "offline";
                }
                else
                {
                    $status = "online";
                }

                if( $status == "online" ) 
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot buy items when logged into the game!<br/>If you have logged out and yet see this message, log back in and properly log out again (click the log out button!).</p>" . "\n";
                }

                if( $exit_buy != 1 ) 
                {
                    connectgamecpdb();
                    $item_sql = "" . "SELECT rented_name as item_name, rented_d as item_amount, rented_custom_amount as item_custom_amount, rented_u as item_upgrade, rented_k as item_dbcode, rented_price as item_price FROM gamecp_rented_items WHERE rented_id = '" . $item_id . "'";
                    if( !($item_result = mssql_query($item_sql)) ) 
                    {
                        $exit_buy = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain item information</p>";
                    }

                    if( !($item = mssql_fetch_array($item_result)) ) 
                    {
                        $exit_buy = 1;
                        $out .= $lang["no_such_item"];
                    }
                    else
                    {
                        $item_name = $item["item_name"];
                        $item_price = $item["item_price"];
                        $item_dbcode = $item["item_dbcode"];
                        $item_amount = $item["item_amount"];
                        $item_custom_amount = $item["item_custom_amount"];
                        $item_upgrade = $item["item_upgrade"];
                        $item_final_price = $item_price;
                        $item_price = $item_final_price;
                        if( $item_name == "" || $item_price < 0 || $item_dbcode == "" || $item_upgrade == "" ) 
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid data supplied by the database, contact the admin.</p>";
                        }

                        if( $userdata["points"] < $item_price ) 
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have enough of game points to purchase this item.</p>";
                        }

                    }

                    @mssql_free_result($item_result);
                }

                connectdatadb();
                $char_sql = "SELECT Serial, Name, Lv, Race FROM tbl_base WHERE DCK = 0 AND AccountSerial = '" . $userdata["serial"] . "'";
                if( !($char_result = mssql_query($char_sql)) ) 
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to character information</p>";
                }

                while( $row = mssql_fetch_array($char_result) ) 
                {
                    $chars[] = $row;
                }
                @mssql_free_result($char_result);
                if( !($num_chars = @count($chars)) ) 
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have any characters on your account</p>";
                }

                if( $exit_buy == 0 ) 
                {
                    $lefttitle .= " - Buying an Item";
                    $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">" . number_format($userdata["points"], 2) . "</span> Game Points</p>" . "\n";
                    $u_value = $item_upgrade;
                    $item_slots = $u_value;
                    $item_slots = $item_slots - $base_code;
                    $item_slots = $item_slots / ($base_code + 1);
                    $upgrades = "";
                    $ceil_slots = ceil($item_slots);
                    $slots_code = $base_code + ($base_code + 1) * $ceil_slots;
                    $slots = $ceil_slots;
                    if( 0 < $ceil_slots ) 
                    {
                        $u_value = dechex($u_value);
                        $item_ups = $u_value[0];
                        $slots = 0;
                        $u_value = strrev($u_value);
                        for( $m = 0; $m < $item_ups; $m++ ) 
                        {
                            $talic_id = hexdec($u_value[$m]);
                            $upgrades .= "<img src=\"./includes/images/talics2/t-" . sprintf("%02d", $talic_id) . ".png\" width=\"12\"/>";
                        }
                        $bgc = " style=\"background-color: #10171f;\"";
                    }
                    else
                    {
                        $upgrades = "";
                        $bgc = "";
                    }

                    $colspan = 4;
                    if( $upgrades == "" ) 
                    {
                        $colspan -= 1;
                    }

                    if( $item_amount == "" ) 
                    {
                        $colspan -= 1;
                    }
                    else
                    {
                        if( $ceil_slots <= 0 && $item_custom_amount == 1 ) 
                        {
                            $item_raw_amount = $item_amount;
                            $single_item_price = ceil($item_price / $item_raw_amount);
                            $item_amount = "<select name=\"item_amount\" id=\"amount_1\" onChange=\"calculate_amount(1,'" . $item_price . "','" . $item_amount . "','" . $userdata["points"] . "');\">" . "\n";
                            for( $p = 1; $p < 6; $p++ ) 
                            {
                                $max_amount = floor($p * $single_item_price);
                                if( $userdata["points"] < $max_amount ) 
                                {
                                    continue;
                                }

                                if( $p == $item_raw_amount ) 
                                {
                                    $select = " selected=\"selected\"";
                                }
                                else
                                {
                                    $select = "";
                                }

                                $item_amount .= "\t<option" . $select . ">" . $p . "</option>" . "\n";
                            }
                            $item_amount .= "</select>";
                        }

                    }

                    $out .= "<form method=\"post\">" . "\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"thead\" colspan=\"2\">Buying the Item</td>" . "\n";
                    if( $upgrades != "" ) 
                    {
                        $out .= "\t\t<td class=\"thead\">Upgrade</td>" . "\n";
                    }

                    if( $item_amount != "" ) 
                    {
                        $out .= "\t\t<td class=\"thead\">Amount</td>" . "\n";
                    }

                    $out .= "\t\t<td class=\"thead\">Price</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"font-weight: bold;\" colspan=\"2\">" . $item_name . "</td>" . "\n";
                    if( $upgrades != "" ) 
                    {
                        $out .= "\t\t<td class=\"alt1\" style=\"" . $bgc . "\">" . $upgrades . "</td>" . "\n";
                    }

                    if( $item_amount != "" ) 
                    {
                        $out .= "\t\t<td class=\"alt1\">" . $item_amount . "</td>" . "\n";
                    }

                    $out .= "\t\t<td class=\"alt1\" style=\"font-weight: bold;\" id=\"price_1\">" . number_format($item["item_price"], 2) . " GP</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"text-align: right;\" colspan=\"" . $colspan . "\">Total GP after purchase:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"font-weight: bold;\" id=\"gpafter_1\">" . number_format($userdata["points"] - $item_final_price, 2) . " GP</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "</table>" . "\n";
                    $char_select = "<select name=\"char_serial\">" . "\n";
                    foreach( $chars as $char ) 
                    {
                        $is_char = true;
                        $char_select .= "<option value=\"" . $char["Serial"] . "\">Lv: " . $char["Lv"] . " - " . $char["Name"] . "</option>" . "\n";
                    }
                    $char_select .= "</select>" . "\n";
                    if( $is_char == false ) 
                    {
                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">" . "\n";
                        $out .= "\t<tr>" . "\n";
                        $out .= "\t\t<td class=\"alt2\">No characters have been found</td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        $out .= "</table>";
                        return NULL;
                    }

                    $out .= "<br/>" . "\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\">Which character are you buying for?</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" style=\"text-align: right;\">" . $char_select . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" colspan=\"2\" style=\"text-align: right;\"><input type=\"hidden\" name=\"page\" value=\"buy_item\"/><input type=\"hidden\" name=\"item_id\" value=\"" . $item_id . "\"/><input type=\"submit\" name=\"submit\" value=\"Buy Now!\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "</table>";
                    $out .= "</form>" . "\n";
                    return 1;
                }

            }
            else
            {
                if( $page == "buy_item" ) 
                {
                    $exit_buy = 0;
                    $empty_slot = "-1";
                    $item_id = isset($_POST["item_id"]) && is_int((int) $_POST["item_id"]) ? antiject((int) $_POST["item_id"]) : "";
                    $item_post_amount = isset($_POST["item_amount"]) && is_int((int) $_POST["item_amount"]) ? antiject((int) $_POST["item_amount"]) : "";
                    $char_serial = isset($_POST["char_serial"]) && is_int((int) $_POST["char_serial"]) ? antiject((int) $_POST["char_serial"]) : "";
                    if( $item_id == "" ) 
                    {
                        $exit_buy = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid Item ID provided</p>" . "\n";
                    }

                    if( $char_serial == 0 - 1 ) 
                    {
                        $exit_buy = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">No such character found</p>" . "\n";
                    }

                    $t_login = strtotime($userdata["lastlogintime"]);
                    $t_logout = strtotime($userdata["lastlogofftime"]);
                    $t_cur = time();
                    if( $t_login <= $t_logout ) 
                    {
                        $status = "offline";
                    }
                    else
                    {
                        $status = "online";
                    }

                    if( $status == "online" ) 
                    {
                        $exit_buy = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot buy items when logged into the game!<br/>If you have logged out and yet see this message, log back in and properly log out again (click the log out button!).</p>" . "\n";
                    }

                    if( $exit_buy != 1 ) 
                    {
                        connectgamecpdb();
                        $item_sql = "" . "SELECT rented_time, rented_name as item_name, rented_d as item_amount, rented_custom_amount as item_custom_amount, rented_u as item_upgrade, rented_k as item_dbcode, rented_price as item_price FROM gamecp_rented_items WHERE rented_id = '" . $item_id . "'";
                        if( !($item_result = mssql_query($item_sql)) ) 
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain item information</p>";
                        }

                        if( !($item = mssql_fetch_array($item_result)) ) 
                        {
                            $exit_buy = 1;
                            $out .= $lang["no_such_item"];
                        }
                        else
                        {
                            $item_name = $item["item_name"];
                            $item_price = $item["item_price"];
                            $item_dbcode = $item["item_dbcode"];
                            $item_amount = $item["item_amount"];
                            $item_custom_amount = $item["item_custom_amount"];
                            $item_upgrade = $item["item_upgrade"];
                            $item_final_price = $item_price;
                            $item_price = $item_final_price;
                            $item_duration = $item["rented_time"];
                            if( $item_name == "" || $item_price < 0 || $item_dbcode == "" || $item_upgrade == "" ) 
                            {
                                $exit_buy = 1;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid data supplied by the database, contact the admin.</p>";
                            }

                            if( $userdata["points"] < $item_price ) 
                            {
                                $exit_buy = 1;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have enough of game points to purchase this item.</p>";
                            }

                        }

                        @mssql_free_result($item_result);
                    }

                    if( isset($item_amount) && $item_amount != "" && $item_upgrade == $base_code && $item_custom_amount == 1 && $item_post_amount != "" ) 
                    {
                        $single_price = ceil($item_price / $item_amount);
                        $item_price = ceil($single_price * $item_post_amount);
                        $item_amount = $item_post_amount;
                        if( 99 < $item_post_amount || $item_post_amount < 1 ) 
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">You have supplied an invalid amount.</p>";
                        }

                        if( $userdata["points"] < $item_price ) 
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have enough of game points to make this purchase.</p>";
                        }

                    }

                    if( $exit_buy == 0 ) 
                    {
                        $bags = "";
                        for( $i = 0; $i < 100; $i++ ) 
                        {
                            if( $i != 0 ) 
                            {
                                $bags .= ",";
                            }

                            $bags .= "" . "I.K" . $i;
                        }
                        connectdatadb();
                        $inven_select = "" . "SELECT " . $bags . " FROM \n\t\t\t\ttbl_inven AS I\n\t\t\t\t\tINNER JOIN\n\t\t\t\ttbl_base AS B\n\t\t\t\t\tON B.Serial = I.Serial\n\t\t\t\tWHERE \n\t\t\t\t\tI.Serial = '" . $char_serial . "'\n\t\t\t\tAND\n\t\t\t\t\tB.AccountSerial = '" . $userdata["serial"] . "'";
                        if( !($inven_result = mssql_query($inven_select)) ) 
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain inventory information</p>";
                        }

                        $inven = mssql_fetch_array($inven_result);
                        for( $i = 0; $i < 100; $i++ ) 
                        {
                            if( $inven["" . "K" . $i] == "-1" ) 
                            {
                                $empty_slot = $i;
                                break;
                            }

                        }
                        if( @mssql_num_rows($inven_result) <= 0 ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">No such character found</p>";
                            return 1;
                        }

                        if( $empty_slot == "-1" ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">No empty slots found in your characters inventory</p>";
                            return 1;
                        }

                        if( $item_upgrade == "" ) 
                        {
                            $item_upgrade = $base_code;
                        }

                        if( $item_amount == "" ) 
                        {
                            $item_amount = 0;
                        }

                        $update_inven = "INSERT INTO tbl_ItemCharge (nAvatorSerial,nItemCode_K,nItemCode_D,nItemCode_U,S,T) VALUES (\"" . $char_serial . "\", \"" . $item_dbcode . "\", \"" . $item_amount . "\", \"268435455\", \"0\", \"" . $item_duration . "\")";
                        if( !($inven_result = mssql_query($update_inven)) ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to update your characters inventory.</p>";
                            gamecp_log(1, $userdata["username"], "GAMECP - ITEM SHOP - Failed to update inventory", 1);
                        }
                        else
                        {
                            $time = time();
                            $total_gp = $userdata["points"] - $item_price;
                            connectgamecpdb();
                            $redeem_insert = "INSERT INTO gamecp_redeem_log (redeem_account_id,redeem_char_id,redeem_price,redeem_item_id,redeem_item_name,redeem_item_amount,redeem_item_dbcode,redeem_total_gp,redeem_time) VALUES ('" . antiject($userdata["serial"]) . "', '" . antiject($char_serial) . "', '" . antiject($item_final_price) . "', '-1', '" . antiject($item_name . " (" . $item_duration / 3600 . " Hr)") . "', '" . antiject($item_amount) . "', '" . antiject($item_dbcode) . "', '" . $total_gp . "', '" . $time . "')";
                            if( !($redeem_result = mssql_query($redeem_insert)) ) 
                            {
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to insert a redeem log.</p>";
                                gamecp_log(1, $userdata["username"], "GAMECP - ITEM SHOP - Failed to insert a redeem log", 1);
                            }
                            else
                            {
                                $update_gp = "" . "UPDATE gamecp_gamepoints SET user_points = user_points-" . $item_price . " WHERE user_account_id = '" . $userdata["serial"] . "'";
                                if( !($updategp_result = mssql_query($update_gp)) ) 
                                {
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to upate your game points.</p>";
                                    gamecp_log(1, $userdata["username"], "" . "GAMECP - RENTED ITEM - Failed to update Game Points: -" . $item_price, 1);
                                }
                                else
                                {
                                    $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">" . number_format($total_gp, 2) . "</span> Game Points</p>" . "\n";
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully purchased the item: " . $item_name . "</p>";
                                }

                            }

                        }

                        @mssql_free_result($inven_result);
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
            $out .= $lang["must_be_logged_in_view"];
            return 1;
        }

    }
    else
    {
        $out .= $lang["invalid_page_load"];
    }

}

function game_cp_31($input_type = "convert", $input, $type = false, $slot = false)
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


