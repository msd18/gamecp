<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Item Shop Admin"]["Manage Rented Items"] = $file;
}
else
{
    $lefttitle = "Rented Items";
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $exit_cat = false;
            $page = isset($_POST["page"]) || isset($_GET["page"]) ? isset($_POST["page"]) ? antiject($_POST["page"]) : antiject($_GET["page"]) : "";
            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"50%\" align=\"center\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\"><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "\">View Items</a></td>" . "\n";
            $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\"><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "&page=addedit\">Add Item</a></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "</table>" . "\n";
            $out .= "<br/>" . "\n";
            if( $page == "" ) 
            {
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\" nowrap>ID</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Item Name</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Item Code</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Amount</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Duration</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Price</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" colspan=\"2\" nowrap>Options</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                connectgamecpdb();
                $select_items = "SELECT rented_id, rented_name, rented_k, rented_d, rented_time, rented_price FROM gamecp_rented_items";
                if( !($result_items = mssql_query($select_items)) ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Queer Error</p>";
                }

                connectitemsdb();
                while( $row = mssql_fetch_array($result_items) ) 
                {
                    $iteminfo = itemcode("convert", $row["rented_k"]);
                    $items_query = mssql_query("SELECT item_code FROM " . getitemtablename($iteminfo["type"]) . " WHERE item_id = '" . $iteminfo["id"] . "'", $items_dbconnect);
                    $items = mssql_fetch_array($items_query);
                    mssql_free_result($items_query);
                    $item_code = $items["item_code"];
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"text-align: center;\" nowrap>" . $row["rented_id"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . $row["rented_name"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . $item_code . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . $row["rented_d"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . round($row["rented_time"] / 3600) . " Hr</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap>" . number_format($row["rented_price"]) . " GP</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" nowrap> <a href=\"?do=" . $do . "&page=addedit&rented_id=" . $row["rented_id"] . "\">Edit</a> / <a href=\"?do=" . $do . "&page=delete&rented_id=" . $row["rented_id"] . "\">Delete</a> </td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }
                $out .= "</table>" . "\n";
                return 1;
            }

            if( $page == "addedit" ) 
            {
                $display_form = true;
                $do_process = 0;
                $exit_process = false;
                $exit_text = "";
                $add_submit = isset($_POST["add_submit"]) ? 1 : 0;
                $edit_submit = isset($_POST["edit_submit"]) ? 1 : 0;
                $rented_id = isset($_POST["rented_id"]) || isset($_GET["rented_id"]) ? isset($_POST["rented_id"]) ? antiject($_POST["rented_id"]) : antiject($_GET["rented_id"]) : "";
                $rented_code = isset($_POST["rented_code"]) ? antiject($_POST["rented_code"]) : "";
                $rented_name = isset($_POST["rented_name"]) ? antiject($_POST["rented_name"]) : "";
                $rented_desc = isset($_POST["rented_desc"]) ? antiject($_POST["rented_desc"]) : "";
                $rented_d = isset($_POST["rented_d"]) && is_numeric($_POST["rented_d"]) ? antiject($_POST["rented_d"]) : "";
                $rented_time = isset($_POST["rented_time"]) && is_numeric($_POST["rented_time"]) ? antiject($_POST["rented_time"]) : "";
                $rented_price = isset($_POST["rented_price"]) && is_numeric($_POST["rented_price"]) ? antiject($_POST["rented_price"]) : "";
                $rented_custom_amount = isset($_POST["rented_custom_amount"]) ? 1 : 0;
                if( 9000 < $rented_time ) 
                {
                    $rented_time = 9000;
                }

                if( 99 < $rented_d ) 
                {
                    $rented_d = 99;
                }

                if( $add_submit == 1 || $edit_submit == 1 ) 
                {
                    $do_process = 1;
                }

                if( $rented_id != "" ) 
                {
                    $page_mode = "edit_submit";
                    $submit_name = "Update Rented Item";
                    $this_mode_title = "Editing A Rented Item";
                    if( $do_process == 0 ) 
                    {
                        connectgamecpdb();
                        $select_data = "" . "SELECT rented_custom_amount, rented_id, rented_name, rented_k, rented_d, rented_desc, rented_time, rented_price FROM gamecp_rented_items WHERE rented_id = '" . $rented_id . "'";
                        if( !($data = mssql_query($select_data)) ) 
                        {
                            $exit_cat = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain rented item information</p>";
                        }

                        if( $exit_cat == 0 ) 
                        {
                            if( !($item = mssql_fetch_array($data)) ) 
                            {
                                $display_form = 0;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid item id supplied</p>";
                            }
                            else
                            {
                                $rented_name = antiject($item["rented_name"]);
                                $rented_desc = antiject($item["rented_desc"]);
                                $rented_k = antiject($item["rented_k"]);
                                $rented_d = antiject($item["rented_d"]);
                                $rented_time = antiject($item["rented_time"]);
                                $rented_price = antiject($item["rented_price"]);
                                $rented_custom_amount = antiject($item["rented_custom_amount"]);
                                connectitemsdb();
                                $iteminfo = itemcode("convert", $rented_k);
                                $items_query = mssql_query("SELECT item_id,item_code FROM " . getitemtablename($iteminfo["type"]) . " WHERE item_id = '" . $iteminfo["id"] . "'", $items_dbconnect);
                                $items = mssql_fetch_array($items_query);
                                $rented_code = $items["item_code"];
                                $rented_k = itemcode("make", $items["item_id"], $iteminfo["type"], 1);
                                mssql_free_result($items_query);
                                $rented_time = round($rented_time / 3600);
                            }

                        }

                        mssql_free_result($data);
                    }

                }
                else
                {
                    $page_mode = "add_submit";
                    $submit_name = "Add Rented Item";
                    $this_mode_title = "Adding a rented item";
                    $disable = "";
                }

                if( $do_process == 1 ) 
                {
                    connectitemsdb();
                    $items_query = mssql_query("SELECT item_code,item_id,item_name FROM " . getitemtablename(game_cp_4($rented_code)) . " WHERE item_code = '" . $rented_code . "'", $items_dbconnect);
                    $items = mssql_fetch_array($items_query);
                    $item_code = $items["item_code"];
                    $item_name = str_replace("_", " ", $items["item_name"]);
                    $item_id = $items["item_id"];
                    $rented_k = itemcode("make", $item_id, game_cp_4($rented_code), 1);
                    if( @mssql_num_rows($items_query) <= 0 ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have entered an invalid Item Code<br/>";
                    }

                    mssql_free_result($items_query);
                    if( $rented_name == "" ) 
                    {
                        $rented_name = $item_name;
                    }

                    if( $rented_d == "" ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have left the amount value blank or it was invalid<br/>";
                    }

                    if( $rented_time == "" ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have left the time value blank or it was invalid<br/>";
                    }
                    else
                    {
                        if( $rented_time <= 0 ) 
                        {
                            $exit_process = true;
                            $exit_text .= "&raquo; Time/Duration needs to be greater than 0!<br/>";
                        }
                        else
                        {
                            $rented_time = round($rented_time * 3600);
                        }

                    }

                    if( $rented_price == "" ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have left the price value blank or it was invalid<br/>";
                    }

                    if( $rented_desc == "" ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have left the description value blank or it was invalid<br/>";
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
                else
                {
                    connectgamecpdb();
                    if( $add_submit == 1 ) 
                    {
                        $insert_sql = "" . "INSERT INTO gamecp_rented_items (rented_custom_amount,rented_name,rented_k,rented_d,rented_time,rented_desc,rented_price) VALUES ('" . $rented_custom_amount . "','" . $rented_name . "','" . $rented_k . "','" . $rented_d . "','" . $rented_time . "','" . $rented_desc . "','" . $rented_price . "')";
                        if( !($insert_result = @mssql_query($insert_sql)) ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to rented item</p>";
                            if( $is_superadmin == 1 ) 
                            {
                                $out .= "<p>DEBUG(?): " . mssql_get_last_message() . "\n";
                                $out .= "<br/>SQL: " . $insert_sql;
                                $out .= "</p>";
                            }

                        }
                        else
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully added the rented item " . $rented_name . "</p>";
                            gamecp_log(0, $userdata["username"], "" . "ADMIN - RENTED ITEMS - ADDED - Item Name: " . $rented_name, 1);
                            $display_form = false;
                        }

                    }
                    else
                    {
                        if( $edit_submit == 1 ) 
                        {
                            $update_sql = "" . "UPDATE gamecp_rented_items SET rented_custom_amount = '" . $rented_custom_amount . "', rented_name = '" . $rented_name . "', rented_k = '" . $rented_k . "', rented_d = '" . $rented_d . "', rented_time = '" . $rented_time . "', rented_desc = '" . $rented_desc . "', rented_price = '" . $rented_price . "' WHERE rented_id = '" . $rented_id . "'";
                            if( !($update_result = mssql_query($update_sql, $gamecp_dbconnect)) ) 
                            {
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to update rented item</p>";
                                if( $is_superadmin == 1 ) 
                                {
                                    $out .= "<p>DEBUG(?): " . mssql_get_last_message() . "\n";
                                    $out .= "<br/>SQL: " . $update_sql;
                                    $out .= "</p>";
                                }

                            }
                            else
                            {
                                if( 0 < mssql_rows_affected($gamecp_dbconnect) ) 
                                {
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully updated the rented item: " . $rented_name . "</p>";
                                    $display_form = false;
                                    gamecp_log(0, $userdata["username"], "" . "ADMIN - RENTED ITEMS - UPDATED - Item ID: " . $rented_id, 1);
                                }
                                else
                                {
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">No rented item(s) found in the database</p>";
                                }

                            }

                        }

                    }

                }

                if( $display_form == true ) 
                {
                    $out .= "<form method=\"post\">" . "\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"thead\" colspan=\"2\">" . $this_mode_title . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Item Code:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"rented_code\" value=\"" . $rented_code . "\" size=\"4\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Item Name:<br/><small>Leave blank for auto name</small></td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"rented_name\" value=\"" . $rented_name . "\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Item Description:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"rented_desc\" value=\"" . $rented_desc . "\" size=\"25\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Item Price:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"rented_price\" value=\"" . $rented_price . "\" size=\"1\"/> GP</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Item Duration:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"rented_time\" value=\"" . $rented_time . "\" size=\"1\"/> Hr</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Item Amount:<br/><small>Just set this to 1</small></td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"rented_d\" value=\"" . $rented_d . "\" size=\"1\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Stackable?:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"checkbox\" name=\"rented_custom_amount\" value=\"1\" " . ($rented_custom_amount == 1 ? "checked" : "") . "/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" colspan=\"2\" nowrap>" . "\n";
                    $out .= "\t\t\t<input name=\"rented_id\" type=\"hidden\" value=\"" . $rented_id . "\"/>" . "\n";
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
                    $item_id = isset($_GET["rented_id"]) && is_numeric($_GET["rented_id"]) ? $_GET["rented_id"] : "";
                    if( $item_id == "" ) 
                    {
                        $out .= $lang["no_such_item"];
                        return 1;
                    }

                    connectgamecpdb();
                    $item_query = mssql_query("" . "SELECT rented_name as item_name,rented_id as item_id FROM gamecp_rented_items WHERE rented_id = '" . $item_id . "'");
                    $item_info = mssql_fetch_array($item_query);
                    if( mssql_num_rows($item_query) <= 0 ) 
                    {
                        $out .= $lang["no_such_item"];
                    }
                    else
                    {
                        $out .= "<form method=\"post\">" . "\n";
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Are you sure you want the delete the Rented Item: <u>" . $item_info["item_name"] . "</u>?</p>" . "\n";
                        $out .= "<p style=\"text-align: center;\"><input type=\"hidden\" name=\"item_id\" value=\"" . $item_id . "\"/><input type=\"hidden\" name=\"page\" value=\"delete_item\"/><input type=\"submit\" name=\"yes\" value=\"Yes\"/> <input type=\"submit\" name=\"no\" value=\"No\"/></p>";
                        $out .= "</form>";
                    }

                    @mssql_free_result($item_query);
                    return 1;
                }

                if( $page == "delete_item" ) 
                {
                    $yes = isset($_POST["yes"]) ? "1" : "0";
                    $no = isset($_POST["no"]) ? "1" : "0";
                    if( isset($_POST["item_id"]) && is_numeric($_POST["item_id"]) ) 
                    {
                        $item_id = antiject($_POST["item_id"]);
                    }
                    else
                    {
                        $item_id = "";
                    }

                    if( $no != 1 && $item_id != "" ) 
                    {
                        connectgamecpdb();
                        $item_query = mssql_query("" . "SELECT rented_id as item_id,rented_name as item_name FROM gamecp_rented_items WHERE rented_id = '" . $item_id . "'", $gamecp_dbconnect);
                        $item = mssql_fetch_array($item_query);
                        if( mssql_num_rows($item_query) <= 0 ) 
                        {
                            $out .= $lang["no_such_item"];
                        }
                        else
                        {
                            $cquery = mssql_query("DELETE FROM gamecp_rented_items WHERE rented_id = " . $item_id);
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Deleted the rented item name: " . $item["item_name"] . " (#" . $item["item_id"] . ")</p>";
                            gamecp_log(3, $userdata["username"], "ADMIN - RENTED ITEMS - DELETED - Item Name:  " . $item["item_name"] . " | Item ID: " . $item["item_id"], 1);
                        }

                        @mssql_free_result($item_query);
                        return 1;
                    }

                    header("" . "Location: " . $script_name . "?do=" . $_GET["do"]);
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

function game_cp_23($input_type = "convert", $input, $type = false, $slot = false)
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


