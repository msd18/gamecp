<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Server Admin"]["Give Item"] = $file;
}
else
{
    $lefttitle = "Server Admin - Give Item";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            if( isset($_POST["page"]) || isset($_GET["page"]) ) 
            {
                $page = isset($_POST["page"]) ? $_POST["page"] : $_GET["page"];
            }
            else
            {
                $page = "";
            }

            $character_serial = isset($_POST["character_serial"]) || isset($_GET["character_serial"]) ? isset($_POST["character_serial"]) ? $_POST["character_serial"] : $_GET["character_serial"] : "";
            $character_serial = is_int((int) $character_serial) ? antiject((int) $character_serial) : "";
            $character_name = isset($_POST["character_name"]) || isset($_GET["character_name"]) ? isset($_POST["character_name"]) ? $_POST["character_name"] : $_GET["character_name"] : "";
            $character_name = trim(antiject($character_name));
            $out .= "<form method=\"GET\" action=\"" . $script_name . "?do=" . $_GET["do"] . "\">" . "\n";
            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Search for a character</b></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt1\">Character Serial:</td>";
            $out .= "\t\t<td class=\"alt2\"><input type=\"text\" name=\"character_serial\" value=\"" . ($character_serial != 0 ? $character_serial : "") . "\"/></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt1\">Character Name:</td>";
            $out .= "\t\t<td class=\"alt2\"><input type=\"text\" name=\"character_name\"  value=\"" . $character_name . "\"/></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td colspan=\"2\"><input type=\"hidden\" name=\"page\" value=\"useritems\" /><input type=\"hidden\" name=\"do\" value=\"" . $_GET["do"] . "\" /><input type=\"submit\" value=\"Search\" name=\"submit\" /></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "</table>" . "\n";
            $out .= "</form>" . "\n";
            if( $page == "" ) 
            {
                $out .= "<p style=\"text-align: center; font-weight: bold;\">Please search for a character name or serial above</p>";
                return 1;
            }

            if( $page == "useritems" ) 
            {
                $page_exit = false;
                if( $character_serial == 0 && $character_name == "" ) 
                {
                    $page_exit = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Sorry, we require either a character serial or name to be entered</p>";
                }

                if( !$page_exit ) 
                {
                    connectdatadb();
                    $sql_search = $character_serial != 0 ? "Serial = '" . $character_serial . "'" : "Name = '" . $character_name . "'";
                    $sql_charserial = "" . "SELECT TOP 1 Name, Serial FROM tbl_base WHERE " . $sql_search;
                    if( !($result_charserial = mssql_query($sql_charserial)) ) 
                    {
                        $page_exit = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL ERROR! Cannot check character serial or name</p>";
                        if( $config["security_enable_debug"] == 1 ) 
                        {
                            mssql_free_result($result_charserial);
                            exit( "DEBUG ON! Problems while trying to get the characer info" . "<br/>\n" . "SQL DEBUG: " . mssql_get_last_message() );
                        }

                    }
                    else
                    {
                        $char_info = mssql_fetch_array($result_charserial);
                        $character_serial = $char_info["Serial"];
                        $character_name = antiject($char_info["Name"]);
                    }

                    mssql_free_result($result_charserial);
                    if( $character_serial == "" ) 
                    {
                        $page_exit = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Sorry, we are unable to the character</p>";
                    }

                    $out .= "<h3>Character name: " . $character_name . "</h3>";
                    if( !$page_exit ) 
                    {
                        $sql_itemlist = "SELECT top 32 nSerial, nItemCode_K, nItemCode_D, nItemCode_U, dtGiveDate, dtTakeDate from tbl_ItemCharge where nAvatorSerial = '" . $character_serial . "' and DCK = 0 order by dtGiveDate DESC";
                        if( !($result_itemlist = mssql_query($sql_itemlist)) ) 
                        {
                            $page_exit = true;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL ERROR! Cannot get item list (undelivered) for this character</p>";
                            if( $config["security_enable_debug"] == 1 ) 
                            {
                                mssql_free_result($result_charserial);
                                exit( "DEBUG ON! Cannot get (undelivered) item list for this character" . "<br/>\n" . "SQL DEBUG: " . mssql_get_last_message() );
                            }

                        }

                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                        $out .= "\t<tr>" . "\n";
                        $out .= "\t\t<td class=\"alt2\" colspan=\"6\">Waiting Item List</td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        $out .= "\t<tr>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Serial</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Item Name</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Amount</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Upgrade</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Give Date</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Take Date</td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        if( 0 < mssql_num_rows($result_itemlist) ) 
                        {
                            connectitemsdb();
                            while( $urow = mssql_fetch_array($result_itemlist) ) 
                            {
                                $k_value = $urow["nItemCode_K"];
                                $slot = 0;
                                $kn = 0;
                                for( $n = 9; $n < $item_tbl_num; $n++ ) 
                                {
                                    $item_id = ($k_value - $n * (256 + $slot + 1)) / 65536;
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
                                    $item_name = str_replace("_", " ", $items["item_name"]);
                                }
                                else
                                {
                                    $item_name = "Not found in DB - " . $item_id . ":" . $kn;
                                }

                                $item_code = $items["item_code"];
                                @mssql_free_result($items_query);
                                $base_code = 268435455;
                                $u_value = $urow["nItemCode_U"];
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
                                        $upgrades .= "<img src=\"./includes/images/talics/t-" . sprintf("%02d", $talic_id) . ".png\" width=\"12\"/>";
                                    }
                                    $bgc = " style=\"background-color: #10171f;\"";
                                }
                                else
                                {
                                    $upgrades = "";
                                    $bgc = "";
                                }

                                $out .= "\t<tr>" . "\n";
                                $out .= "\t\t<td class=\"alt2\" nowrap>" . $urow["nSerial"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" nowrap>" . $item_name . " (" . $item_code . ")</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" nowrap>" . $urow["nItemCode_D"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\"" . $bgc . " nowrap>" . $upgrades . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" nowrap>" . date("d/m/Y h:i A", strtotime(preg_replace("/:[0-9][0-9][0-9]/", "", $urow["dtGiveDate"]))) . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" nowrap>" . date("d/m/Y h:i A", strtotime(preg_replace("/:[0-9][0-9][0-9]/", "", $urow["dtTakeDate"]))) . "</td>" . "\n";
                                $out .= "\t</tr>" . "\n";
                            }
                        }
                        else
                        {
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt1\" colspan=\"6\" style=\"text-align: center;\">No items waiting for delivery for this character</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                        }

                        $out .= "</table>" . "\n";
                        mssql_free_result($result_itemlist);
                        $out .= "<br/>" . "\n";
                        $out .= "<form action=\"?do=" . $_GET["do"] . "\" method=\"post\" id=\"form1\">" . "\n";
                        $out .= "<table id=\"myTable\" class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                        $out .= "\t<tr>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>#</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Item Name</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Item Code</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Amount</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Upgrade</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap>Rental Period</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\" nowrap></td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        $out .= "\t<input type=\"hidden\" id=\"id\" value=\"1\" />" . "\n";
                        $out .= "\t<tr id=\"row0\">" . "\n";
                        $out .= "\t\t<td class=\"alt2\" nowrap>0</td>" . "\n";
                        $out .= "\t\t<td class=\"alt1\" nowrap><div id=\"results_div0\">-1</div></td>" . "\n";
                        $out .= "\t\t<td class=\"alt1\" nowrap><input type=\"text\" size=\"5\" name=\"item_code[]\" onchange=\"check_itemname('item_code0', 'results_div0');\" id=\"item_code0\" /></td>" . "\n";
                        $out .= "\t\t<td class=\"alt1\" nowrap><input type=\"text\" size=\"1\" name=\"item_amount[]\" value=\"0\"/></td>" . "\n";
                        $out .= "\t\t<td class=\"alt1\" nowrap><select name=\"item_ups[]\"><option value=\"0\">+0</option><option value=\"1\">+1</option><option value=\"2\">+2</option><option value=\"3\">+3</option><option value=\"4\">+4</option><option value=\"5\">+5</option><option value=\"6\">+6</option><option value=\"7\">+7</option></select>/<select name=\"item_slots[]\"><option value=\"0\">0</option><option value=\"1\">1</option><option value=\"2\">2</option><option value=\"3\">3</option><option value=\"4\">4</option><option value=\"5\">5</option><option value=\"6\">6</option><option value=\"7\">7</option></select> <select name=\"item_talic[]\"><option value=\"1\">No Talic</option><option value=\"2\">Rebirth</option><option value=\"3\">Mercy</option><option value=\"4\">Grace</option><option value=\"5\">Glory</option><option value=\"6\">Guard</option><option value=\"7\">Belief</option><option value=\"8\">Sacred Flame</option><option value=\"9\">Wisdom</option><option value=\"10\">Favor</option><option value=\"11\">Hatred</option><option value=\"12\">Chaos</option><option value=\"13\">Darkness</option><option value=\"14\">Destruction</option><option value=\"15\">Ignorant</option></select></td>" . "\n";
                        $out .= "\t\t<td class=\"alt1\" nowrap><input type=\"text\" size=\"5\" name=\"item_rental_time[]\" value=\"0\"/></td>" . "\n";
                        $out .= "\t\t<td class=\"alt1\" nowrap></td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        $out .= "\t<tr id=\"last\">" . "\n";
                        $out .= "\t\t<td colspan=\"6\"><input type=\"hidden\" name=\"page\" value=\"give_item\" /><input type=\"hidden\" name=\"character_serial\" value=\"" . $character_serial . "\" /><input type=\"submit\" value=\"Submit\" name=\"submit\"><input type=\"reset\" value=\"Reset\" name=\"reset\"></td>" . "\n";
                        $out .= "\t\t<td style=\"text-align: right;\" nowrap><a href=\"#\" onClick=\"addFormField(); return false;\">Add More Items</a></td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        $out .= "</table>" . "\n";
                        $out .= "</form>" . "\n";
                        $out .= "<br/>" . "\n";
                        connectdatadb();
                        $sql_itemlist = "SELECT top 32 nSerial, nItemCode_K, nItemCode_D, nItemCode_U, dtGiveDate, dtTakeDate from tbl_ItemCharge where nAvatorSerial = '" . $character_serial . "' and DCK = 1 order by dtGiveDate DESC";
                        if( !($result_itemlist = mssql_query($sql_itemlist)) ) 
                        {
                            $page_exit = true;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL ERROR! Cannot get item list (undelivered) for this character</p>";
                            if( $config["security_enable_debug"] == 1 ) 
                            {
                                mssql_free_result($result_charserial);
                                exit( "DEBUG ON! Cannot get (undelivered) item list for this character" . "<br/>\n" . "SQL DEBUG: " . mssql_get_last_message() );
                            }

                        }

                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                        $out .= "\t<tr>" . "\n";
                        $out .= "\t\t<td class=\"alt2\" colspan=\"6\">Delivered Item List</td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        $out .= "\t<tr>" . "\n";
                        $out .= "\t\t<td class=\"thead\">Serial</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\">Item Name</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\">Amount</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\">Upgrade</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\">Give Date</td>" . "\n";
                        $out .= "\t\t<td class=\"thead\">Take Date</td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        if( 0 < mssql_num_rows($result_itemlist) ) 
                        {
                            connectitemsdb();
                            while( $urow = mssql_fetch_array($result_itemlist) ) 
                            {
                                $k_value = $urow["nItemCode_K"];
                                $slot = 0;
                                $kn = 0;
                                for( $n = 9; $n < $item_tbl_num; $n++ ) 
                                {
                                    $item_id = ($k_value - $n * (256 + $slot + 1)) / 65536;
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
                                    $item_name = str_replace("_", " ", $items["item_name"]);
                                }
                                else
                                {
                                    $item_name = "Not found in DB - " . $item_id . ":" . $kn;
                                }

                                $item_code = $items["item_code"];
                                @mssql_free_result($items_query);
                                $base_code = 268435455;
                                $u_value = $urow["nItemCode_U"];
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

                                $out .= "\t<tr>" . "\n";
                                $out .= "\t\t<td class=\"alt2\" nowrap>" . $urow["nSerial"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" nowrap>" . $item_name . " (" . $item_code . ")</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" nowrap>" . $urow["nItemCode_D"] . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\"" . $bgc . " nowrap>" . $upgrades . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" nowrap>" . date("d/m/Y h:i A", strtotime(preg_replace("/:[0-9][0-9][0-9]/", "", $urow["dtGiveDate"]))) . "</td>" . "\n";
                                $out .= "\t\t<td class=\"alt1\" nowrap>" . date("d/m/Y h:i A", strtotime(preg_replace("/:[0-9][0-9][0-9]/", "", $urow["dtTakeDate"]))) . "</td>" . "\n";
                                $out .= "\t</tr>" . "\n";
                            }
                        }
                        else
                        {
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt1\" colspan=\"6\" style=\"text-align: center;\">There are no items charged and delivered for this character</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                        }

                        $out .= "</table>" . "\n";
                        mssql_free_result($result_itemlist);
                        return 1;
                    }

                }

            }
            else
            {
                if( $page == "give_item" ) 
                {
                    $page_exit = false;
                    $process_exit = false;
                    $pexit_message = "";
                    $base_code = 268435455;
                    $item_code_x = isset($_POST["item_code"]) ? $_POST["item_code"] : "";
                    $item_amount_x = isset($_POST["item_amount"]) ? $_POST["item_amount"] : "";
                    $item_ups_x = isset($_POST["item_ups"]) ? $_POST["item_ups"] : "";
                    $item_slots_x = isset($_POST["item_slots"]) ? $_POST["item_slots"] : "";
                    $item_talic_x = isset($_POST["item_talic"]) ? $_POST["item_talic"] : "";
                    $item_rental_time_x = isset($_POST["item_rental_time"]) ? $_POST["item_rental_time"] : "";
                    if( $item_code_x == "" || $item_amount_x == "" || $item_amount_x == "" || $item_ups_x == "" || $item_slots_x == "" || $item_talic_x == "" || $item_rental_time_x == "" ) 
                    {
                        $page_exit = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Sorry, you have left a field empty</p>";
                    }

                    connectdatadb();
                    $char_sql = "" . "SELECT Name FROM tbl_base WHERE Serial = '" . $character_serial . "'";
                    if( !($char_result = mssql_query($char_sql)) ) 
                    {
                        $page_exit = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL ERROR! Cannot get character data/p>";
                        if( $config["security_enable_debug"] == 1 ) 
                        {
                            mssql_free_result($result_charserial);
                            exit( "DEBUG ON! Cannot get character data with provided serial" . "<br/>\n" . "SQL DEBUG: " . mssql_get_last_message() );
                        }

                    }

                    if( mssql_num_rows($char_result) <= 0 ) 
                    {
                        $page_exit = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Sorry, the character serial (#" . $character_serial . ") you have selected does not exist!</p>";
                    }

                    $itemcode_count = count($item_code_x);
                    $itemamount_count = count($item_amount_x);
                    $itemups_count = count($item_ups_x);
                    $itemslots_count = count($item_slots_x);
                    $itemtalic_count = count($item_talic_x);
                    $itemrentaltime_count = count($item_rental_time_x);
                    if( $itemcode_count != $itemamount_count || $itemcode_count != $itemups_count || $itemcode_count != $itemslots_count || $itemcode_count != $itemtalic_count || $itemcode_count != $itemrentaltime_count ) 
                    {
                        $page_exit = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Error! Seems you are missing a field somewhere (firefox html glitch?)</p>";
                    }

                    if( !$page_exit ) 
                    {
                        for( $i = 0; $i < $itemcode_count; $i++ ) 
                        {
                            $item_code = !empty($item_code_x[$i]) ? antiject($item_code_x[$i]) : "";
                            $item_amount = !empty($item_amount_x[$i]) && is_int((int) $item_amount_x[$i]) ? antiject($item_amount_x[$i]) : 0;
                            $item_ups = !empty($item_ups_x[$i]) && is_int((int) $item_ups_x[$i]) ? antiject($item_ups_x[$i]) : 0;
                            $item_slots = !empty($item_slots_x[$i]) && is_int((int) $item_slots_x[$i]) ? antiject($item_slots_x[$i]) : 0;
                            $item_talic = !empty($item_talic_x[$i]) && is_int((int) $item_talic_x[$i]) ? antiject($item_talic_x[$i]) : 1;
                            $item_rental_time = !empty($item_rental_time_x[$i]) && is_int((int) $item_rental_time_x[$i]) ? antiject($item_rental_time_x[$i]) : 0;
                            if( $item_code == "" && $item_amount == "" && $item_ups == "" && $item_slots == "" && $item_talic == "" && $item_rental_time == "" ) 
                            {
                                $page_exit = true;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">Sorry, you have left a field empty</p>";
                            }

                            if( !$page_exit ) 
                            {
                                $item_kind = game_cp_4($item_code);
                                connectitemsdb();
                                $itemdata_query = mssql_query("SELECT item_code, item_name, item_id FROM " . getitemtablename($item_kind) . " WHERE item_code = '" . $item_code . "'", $items_dbconnect);
                                $itemdata = mssql_fetch_array($itemdata_query);
                                if( 0 < mssql_num_rows($itemdata_query) ) 
                                {
                                    $item_id = $itemdata["item_id"];
                                }
                                else
                                {
                                    $item_id = 0 - 1;
                                    $process_exit = true;
                                    $pexit_message .= "Invalid ITEM CODE provided: " . $item_code . " (this item has not been added!)<br/>";
                                }

                                mssql_free_result($itemdata_query);
                                if( !$process_exit ) 
                                {
                                    if( 99 < $item_amount ) 
                                    {
                                        $item_amount = 99;
                                    }
                                    else
                                    {
                                        if( $item_amount < 0 ) 
                                        {
                                            $item_amount = 0;
                                        }

                                    }

                                    if( $item_slots < $item_ups ) 
                                    {
                                        $item_ups = $item_slots;
                                    }

                                    $item_db_code = 65536 * $item_id + $item_kind * 256 + 0;
                                    $km_allorArray = array( 0, 1, 2, 3, 4, 5, 6, 7 );
                                    if( !in_array($item_kind, $km_allorArray) ) 
                                    {
                                        $item_upgrade = $base_code;
                                        if( $item_amount == 0 ) 
                                        {
                                            $item_amount = 1;
                                        }

                                    }
                                    else
                                    {
                                        $item_amount = 0;
                                        if( $item_talic == 1 ) 
                                        {
                                            $item_upgrade = $base_code + ($base_code + 1) * $item_slots;
                                        }
                                        else
                                        {
                                            $item_upgrade = ($base_code + ($base_code + 1) * $item_slots) - $item_talic * (pow(16, $item_ups) - 1) / 15;
                                        }

                                    }

                                    if( 0 < $item_rental_time ) 
                                    {
                                        $column_append = ", T";
                                        $value_append = "" . ", '" . $item_rental_time . "'";
                                    }
                                    else
                                    {
                                        $column_append = "";
                                        $value_append = "";
                                    }

                                    connectdatadb();
                                    $sql_insert_itemcharge = "INSERT tbl_ItemCharge ( nAvatorSerial, nItemCode_K, nItemCode_D, nItemCode_U" . $column_append . "" . " )VALUES( '" . $character_serial . "', '" . $item_db_code . "', '" . $item_amount . "', '" . $item_upgrade . "'" . $value_append . " )";
                                    if( !($result_inser_itemcharge = mssql_query($sql_insert_itemcharge)) ) 
                                    {
                                        $process_exit = true;
                                        $pexit_message .= "Error while inserting item, " . $item_code . " . This item was not added.<br/>";
                                    }

                                    gamecp_log(2, $userdata["username"], "" . "ADMIN - GIVE ITEM - Character Serial: " . $character_serial . " | Item Code: " . $item_code . " [" . $item_ups . "]/" . $item_slots . " [talic: " . $item_talic . "]", 1);
                                }

                            }

                        }
                        if( $process_exit ) 
                        {
                            $out .= "<p style=\"text-align: center;\">" . $pexit_message . "</p>";
                            return 1;
                        }

                        header("Location: ?do=" . $_GET["do"] . "&page=useritems&character_serial=" . $character_serial . "&submit=Search");
                        return 1;
                    }

                }
                else
                {
                    if( $page == "getcode" ) 
                    {
                        connectitemsdb();
                        $item_code = isset($_GET["itemcode"]) ? antiject(trim($_GET["itemcode"])) : "";
                        if( $item_code != "" ) 
                        {
                            if( $item_code != "-" ) 
                            {
                                $kn = game_cp_4($item_code);
                                $items_query = mssql_query("SELECT TOP 1 item_name FROM " . getitemtablename($kn) . "" . " WHERE item_code LIKE '" . $item_code . "'", $items_dbconnect);
                                $items = mssql_fetch_array($items_query);
                                if( $items["item_name"] != "" ) 
                                {
                                    echo "<b>" . str_replace("_", " ", $items["item_name"]) . "</b>";
                                }
                                else
                                {
                                    echo "<b>No such item for: " . $item_code . "</b><br/>";
                                }

                                @mssql_free_result($items_query);
                            }
                            else
                            {
                                echo "-1";
                            }

                        }
                        else
                        {
                            echo "<b>No code provided</b>";
                        }

                        exit();
                    }

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


