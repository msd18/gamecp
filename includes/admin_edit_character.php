<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Server Admin"]["Edit Character"] = $file;
}
else
{
    $lefttitle = "Character Edit";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $query_p1 = "";
            $search_query = "";
            $enable_exit = false;
            if( empty($page) ) 
            {
                $out .= "<form method=\"GET\" action=\"" . $script_name . "\">";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Look up character</b></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Character Name:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"character_name\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Character Serial:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"character_serial\" /></td>";
                $out .= "</tr>";
                $out .= "<td colspan=\"2\"><input type=\"hidden\" name=\"do\" value=\"" . $_GET["do"] . "\" /><input type=\"submit\" value=\"Edit character\" name=\"search_fun\" /></td>";
                $out .= "</tr>";
                $out .= "</table>";
                $out .= "</form>";
                if( $search_fun != "" ) 
                {
                    $character_name = isset($_GET["character_name"]) ? $_GET["character_name"] : "";
                    $character_serial = isset($_GET["character_serial"]) && is_int((int) $_GET["character_serial"]) ? (int) $_GET["character_serial"] : "";
                    if( $character_serial == "" && $character_name == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>You must enter a character name or serial to proceed</b></p>";
                    }

                    if( $enable_exit != true ) 
                    {
                        $out .= "<br/>" . "\n";
                        $out .= "<form method=\"post\">" . "\n";
                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                        $out .= "\t<tr>" . "\n";
                        $out .= "\t\t<td class=\"thead\" style=\"padding: 4px;\" colspan=\"2\" nowrap>User Information</td>" . "\n";
                        $out .= "\t</tr>" . "\n";
                        $out .= "" . "\n";
                        $character_name = antiject($character_name);
                        $character_serial = antiject($character_serial);
                        $search_query .= "WHERE ";
                        if( $character_name != "" ) 
                        {
                            $search_query .= "" . " B.Name = '" . $character_name . "'";
                        }

                        if( $character_serial != "" ) 
                        {
                            if( $character_name != "" ) 
                            {
                                $search_query .= " OR ";
                            }

                            $search_query .= "" . " B.Serial = '" . $character_serial . "'";
                        }

                        connectdatadb();
                        $query_p1 = "" . "SELECT \n\t\t\t\t\tB.Serial, B.Name, B.DCK, B.AccountSerial, B.Account, B.Slot, B.Race, B.Class, B.Lv, B.Dalant, B.Gold, B.Baseshape, G.PvpPoint, G.GuildSerial, G.TotalPlayMin, G.Map, G.Class0, G.Class1, G.Class2, G.ClassInitCnt, G.MaxLevel, G.Exp, G.LossExp, G.WM0, G.WM1, G.DM, G.PM\n\t\t\t\t\tFROM \n\t\t\t\t\ttbl_base AS B\n\t\t\t\t\tINNER JOIN\n\t\t\t\t\ttbl_general AS G\n\t\t\t\t\tON G.Serial = B.Serial\n\t\t\t\t\t" . $search_query;
                        if( !($result = mssql_query($query_p1, $data_dbconnect)) ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error fetching user info from the database</p>";
                            if( $config["security_enable_debug"] == 1 ) 
                            {
                                $out .= "<p>DEBUG(?):<br/>" . "\n";
                                $out .= mssql_get_last_message();
                                $out .= "</p>";
                            }

                        }

                        connectuserdb();
                        connectitemsdb();
                        while( $row = mssql_fetch_array($result) ) 
                        {
                            if( $row["DCK"] == true ) 
                            {
                                $dck_checked = "checked";
                            }
                            else
                            {
                                $dck_checked = "";
                            }

                            $class_query = "SELECT class_id, class_code, class_name FROM tbl_Classes WHERE class_name != ' '";
                            $class_query = mssql_query($class_query, $items_dbconnect);
                            $base_class = "<select name=\"base_class\">" . "\n";
                            $first_class = "<select name=\"first_class\">" . "\n";
                            $second_class = "<select name=\"second_class\">" . "\n";
                            $final_class = "<select name=\"final_class\">" . "\n";
                            $base_class .= "\t<option value=\"-1\">Not yet selected</option>" . "\n";
                            $first_class .= "\t<option value=\"-1\">Not yet selected</option>" . "\n";
                            $second_class .= "\t<option value=\"-1\">Not yet selected</option>" . "\n";
                            while( $classinfo = mssql_fetch_array($class_query) ) 
                            {
                                $class_code_id = 0 - 1;
                                if( $row["Class"] == $classinfo["class_code"] ) 
                                {
                                    $class_selected = "selected";
                                    $class_code_id = $classinfo["class_id"];
                                }
                                else
                                {
                                    $class_selected = "";
                                }

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

                                $race2 = strtolower(substr($classinfo["class_code"], 2, 12));
                                if( $race2 == "b0" ) 
                                {
                                    $base_class .= "\t\t<option value=\"" . $classinfo["class_id"] . "\"" . ($classinfo["class_id"] == $row["Class0"] ? " selected" : "") . ">" . $classinfo["class_name"] . "</option>" . "\n";
                                }

                                if( $race2 != "b0" ) 
                                {
                                    $first_class .= "\t\t<option value=\"" . $classinfo["class_id"] . "\"" . ($classinfo["class_id"] == $row["Class1"] ? " selected" : "") . ">" . $classinfo["class_name"] . "</option>" . "\n";
                                }

                                if( $race2 != "b0" ) 
                                {
                                    $second_class .= "\t\t<option value=\"" . $classinfo["class_id"] . "\"" . ($classinfo["class_id"] == $row["Class2"] ? " selected" : "") . ">" . $classinfo["class_name"] . "</option>" . "\n";
                                }

                                $final_class .= "\t\t<option value=\"" . $classinfo["class_id"] . "\"" . ($classinfo["class_code"] == $row["Class"] ? " selected" : "") . ">" . $classinfo["class_name"] . "</option>" . "\n";
                            }
                            @mssql_free_result($class_query);
                            $final_class .= "</select>" . "\n";
                            $second_class .= "</select>" . "\n";
                            $first_class .= "</select>" . "\n";
                            $base_class .= "</select>" . "\n";
                            $race = "\t\t<select name=\"race\">" . "\n";
                            $races = array( "Belleto Male", "Belleto Female", "Cora Male", "Cora Female", "Accretian" );
                            for( $i = 0; $i < 5; $i++ ) 
                            {
                                if( $row["Race"] == $i ) 
                                {
                                    $race_selected = "selected";
                                }
                                else
                                {
                                    $race_selected = "";
                                }

                                $race .= "\t\t<option value=\"" . $i . "\"" . $race_selected . ">" . $races[$i] . "</option>" . "\n";
                            }
                            $race .= "\t\t</select>" . "\n";
                            connectuserdb();
                            $account_query = "SELECT serial FROM tbl_UserAccount WHERE id = convert(binary,'" . $row["Account"] . "')";
                            $account_result = mssql_query($account_query, $user_dbconnect);
                            $account = mssql_fetch_array($account_result);
                            $account["serial"] = !empty($account["serial"]) ? $account["serial"] : "No such account";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Serial</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\" width=\"100%\">" . $row["Serial"] . "</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>DCK</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"dck\" type=\"checkbox\" " . $dck_checked . "/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Name</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"name\" type=\"text\" value=\"" . $row["Name"] . "\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Account Serial</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"account_serial\" type=\"text\" value=\"" . $row["AccountSerial"] . "\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Account</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"account\" type=\"text\" value=\"" . $row["Account"] . "\"/> (Serial: " . $account["serial"] . ")</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Slot</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"slot\" type=\"text\" value=\"" . $row["Slot"] . "\" size=\"1\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Level</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"level\" type=\"text\" value=\"" . $row["Lv"] . "\" size=\"1\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Max Level</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"MaxLevel\" type=\"text\" value=\"" . $row["MaxLevel"] . "\" size=\"1\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Exp</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"Exp\" type=\"text\" value=\"" . $row["Exp"] . "\" size=\"5\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Lost Exp</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"LossExp\" type=\"text\" value=\"" . $row["LossExp"] . "\" size=\"1\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Map</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\">" . "\n";
                            $out .= "\t\t\t<select name=\"map\">" . "\n";
                            for( $i = 0; $i < 26; $i++ ) 
                            {
                                if( $i == $row["Map"] ) 
                                {
                                    $selected = " selected=\"selected\"";
                                }
                                else
                                {
                                    $selected = "";
                                }

                                $out .= "\t\t\t<option value=\"" . $i . "\"" . $selected . ">" . game_cp_2($i) . "</option>" . "\n";
                            }
                            $out .= "\t\t\t</select>" . "\n";
                            $out .= "\t\t</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Race</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\">" . $race . "</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Base Class</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\">" . $base_class . "</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>1st Sub Class</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\">" . $first_class . " - This class is your 1st up</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>2nd Sub Class</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\">" . $second_class . " - This is NOT the 2nd up class. It is an un-used class that allows REAL cross-classing</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Final Class</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\">" . $final_class . " - This is the final or 2nd class up. If no 1st class is selected, this is the base class</td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Gold</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"gold\" type=\"text\" value=\"" . $row["Gold"] . "\" size=\"12\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Dalant</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"dalant\" type=\"text\" value=\"" . $row["Dalant"] . "\" size=\"12\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Melee PT</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"melee_pt\" type=\"text\" value=\"" . $row["WM0"] . "\" size=\"12\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Range PT</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"melee_pt\" type=\"text\" value=\"" . $row["WM1"] . "\" size=\"12\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Defence PT</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"melee_pt\" type=\"text\" value=\"" . $row["DM"] . "\" size=\"12\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Shield PT</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"melee_pt\" type=\"text\" value=\"" . $row["PM"] . "\" size=\"12\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Base Shape</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"baseshape\" type=\"text\" value=\"" . $row["Baseshape"] . "\" size=\"6\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>PVP Points</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"pvppoint\" type=\"text\" value=\"" . $row["PvpPoint"] . "\" size=\"6\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Guild Serial</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"guildserial\" type=\"text\" value=\"" . $row["GuildSerial"] . "\" size=\"6\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td class=\"alt2\" style=\"padding: 4px; font-weight: bold;\" nowrap>Total Play Min</td>" . "\n";
                            $out .= "\t\t<td class=\"alt1\"><input name=\"totalplaymin\" type=\"text\" value=\"" . $row["TotalPlayMin"] . "\" size=\"6\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                            $out .= "<input type=\"hidden\" name=\"char_serial\" value=\"" . $row["Serial"] . "\"/>" . "\n";
                        }
                        if( mssql_num_rows($result) == 0 ) 
                        {
                            $out .= "<tr>" . "\n";
                            $out .= "<td colspan=\"2\" class=\"alt1\" style=\"text-align: center;\">No such character found</td>" . "\n";
                            $out .= "</tr>" . "\n";
                        }
                        else
                        {
                            $out .= "\t<tr>" . "\n";
                            $out .= "\t\t<td colspan=\"2\"><input type=\"submit\" name=\"page\" value=\"Update\"/></td>" . "\n";
                            $out .= "\t</tr>" . "\n";
                        }

                        @mssql_free_result($result);
                        $out .= "</table>" . "\n";
                        $out .= "</form>" . "\n";
                    }

                    gamecp_log(0, $userdata["username"], "" . "ADMIN - CHARACTER EDIT - Searched for: " . $character_name . " or " . $character_serial, 1);
                    return 1;
                }

            }
            else
            {
                if( $page == "Update" ) 
                {
                    $char_serial = isset($_POST["char_serial"]) ? $_POST["char_serial"] : "";
                    $dck = isset($_POST["dck"]) ? "True" : "False";
                    $name = isset($_POST["name"]) ? $_POST["name"] : "";
                    $account_serial = isset($_POST["account_serial"]) ? $_POST["account_serial"] : "";
                    $account = isset($_POST["account"]) ? $_POST["account"] : "";
                    $map = isset($_POST["map"]) && is_numeric($_POST["map"]) ? $_POST["map"] : "";
                    $slot = isset($_POST["slot"]) ? $_POST["slot"] : "";
                    $race = isset($_POST["race"]) ? $_POST["race"] : "";
                    $base_class = isset($_POST["base_class"]) ? $_POST["base_class"] : "";
                    $first_class = isset($_POST["first_class"]) ? $_POST["first_class"] : "";
                    $second_class = isset($_POST["second_class"]) ? $_POST["second_class"] : "";
                    $final_class = isset($_POST["final_class"]) ? $_POST["final_class"] : "";
                    $level = isset($_POST["level"]) ? $_POST["level"] : "";
                    $gold = isset($_POST["gold"]) ? $_POST["gold"] : "";
                    $dalant = isset($_POST["dalant"]) ? $_POST["dalant"] : "";
                    $baseshape = isset($_POST["baseshape"]) ? $_POST["baseshape"] : "";
                    $maxlevel = isset($_POST["MaxLevel"]) ? $_POST["MaxLevel"] : "";
                    $exp = isset($_POST["Exp"]) && is_numeric($_POST["Exp"]) ? antiject($_POST["Exp"]) : "";
                    $lossexp = isset($_POST["LossExp"]) && is_numeric($_POST["LossExp"]) ? $_POST["LossExp"] : "";
                    $pvppoint = isset($_POST["pvppoint"]) && is_numeric($_POST["pvppoint"]) ? $_POST["pvppoint"] : "";
                    $guildserial = isset($_POST["guildserial"]) && is_numeric($_POST["guildserial"]) ? $_POST["guildserial"] : "-1";
                    $totalplaymin = isset($_POST["totalplaymin"]) && is_numeric($_POST["totalplaymin"]) ? $_POST["totalplaymin"] : "0";
                    $name = antiject($name);
                    $account_serial = antiject($account_serial);
                    $account = antiject($account);
                    $map = antiject($map);
                    $slot = antiject($slot);
                    $race = antiject($race);
                    $base_class = antiject($base_class);
                    $first_class = antiject($first_class);
                    $second_class = antiject($second_class);
                    $final_class = antiject($final_class);
                    $level = antiject($level);
                    $gold = antiject($gold);
                    $dalant = antiject($dalant);
                    $maxlevel = antiject($maxlevel);
                    $baseshape = antiject($baseshape);
                    $pvppoint = antiject($pvppoint);
                    $guildserial = antiject($guildserial);
                    $totalplaymin = antiject($totalplaymin);
                    $stage1_exit = false;
                    $stage2_exit = false;
                    if( $char_serial == "" || $name == "" || $account_serial == "" || $account == "" || $map == "" || $slot == "" || $race == "" || $final_class == "" || $base_class == "" || $final_class == "" || $second_class == "" || $level == "" || $gold == "" || $dalant == "" || $baseshape == "" || $pvppoint == "" || $guildserial == "" || $totalplaymin == "" || $maxlevel == "" || $exp == "" || $lossexp == "" ) 
                    {
                        $stage1_exit = true;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Make sure you have filled in all the fields</p>";
                    }
                    else
                    {
                        if( !is_numeric($level) || !is_numeric($gold) || !is_numeric($dalant) || !is_numeric($account_serial) || !is_numeric($baseshape) || !is_numeric($maxlevel) || !is_numeric($exp) || !is_numeric($lossexp) ) 
                        {
                            $stage1_exit = true;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Ensure that all numeric values are numeric only!</p>";
                        }

                    }

                    if( 2000000000 < $dalant ) 
                    {
                        $dalant = 2000000000;
                    }

                    if( 500000 < $gold ) 
                    {
                        $gold = 500000;
                    }

                    connectitemsdb();
                    $class_query = "SELECT class_id, class_code, class_name FROM tbl_Classes WHERE class_name != ' '";
                    $class_query = mssql_query($class_query, $items_dbconnect);
                    while( $classinfo = mssql_fetch_array($class_query) ) 
                    {
                        if( $first_class == "-1" ) 
                        {
                            if( $classinfo["class_id"] == $base_class ) 
                            {
                                $final_class = $classinfo["class_code"];
                            }

                        }
                        else
                        {
                            if( $first_class != "-1" && $final_class == $base_class ) 
                            {
                                if( $classinfo["class_id"] == $first_class ) 
                                {
                                    $final_class = $classinfo["class_code"];
                                }

                            }
                            else
                            {
                                if( $classinfo["class_id"] == $final_class ) 
                                {
                                    $final_class = $classinfo["class_code"];
                                    break;
                                }

                            }

                        }

                    }
                    mssql_free_result($class_query);
                    if( $stage1_exit != true ) 
                    {
                        connectdatadb();
                        $update_query = "" . "UPDATE tbl_base SET DCK = '" . $dck . "', Name = '" . $name . "', AccountSerial = '" . $account_serial . "', Account = '" . $account . "', Slot = '" . $slot . "', Race = '" . $race . "', Class = '" . $final_class . "', Lv = '" . $level . "', Gold = '" . $gold . "', Dalant = '" . $dalant . "', Baseshape = '" . $baseshape . "' WHERE Serial = '" . $char_serial . "'";
                        $result = mssql_query($update_query) or $update2_query = "" . "UPDATE tbl_general SET PvpPoint = '" . $pvppoint . "', GuildSerial = '" . $guildserial . "', TotalPlayMin = '" . $totalplaymin . "', Map = '" . $map . "', Class0 = '" . $base_class . "', Class1 = '" . $first_class . "', Class2 = '" . $second_class . "', MaxLevel = '" . $maxlevel . "', Exp = '" . $exp . "', LossExp = '" . $lossexp . "' WHERE Serial = '" . $char_serial . "'";
                        $result = mssql_query($update2_query) or $write_log = "" . "Name: " . $name . " | Account Serial: " . $account_serial . " | Account: " . $account . " | Level: " . $level . " | MaxLevel: " . $maxlevel . " | Gold: " . $gold . " | Dalant: " . $dalant . " | PVP Point: " . $pvppoint . " | Total Play Min: " . $totalplaymin . " | Base Class: " . $base_class . " | First Sub Class: " . $first_class . " | Second Sub Class: " . $second_class . " | Final Class: " . $final_class;
                        $update_dalant = "215155" + $dalant;
                        $delete_npc = "" . "DELETE FROM tbl_NpcData WHERE Serial = '" . $char_serial . "'";
                        $result2 = mssql_query($delete_npc) or $stage2_exit = true;
						
						if( $stage2_exit != true ) 
{
    $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully updated the character " . $name . "</p>";
    header("" . "Refresh: 2; " . $script_name . "?do=" . $_GET["do"] . "&search_fun=Edit+character&character_serial=" . $char_serial);
}
else
{
    $out .= "<p style=\"text-align: center; font-weight: bold;\">Unable to update the character, query failed</p>";
}

                        gamecp_log(0, $userdata["username"], "" . "ADMIN - CHARACTER EDIT - Updated: " . $name, 1);
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
            $out .= $lang["no_permission"];
            return 1;
        }

    }
    else
    {
        $out .= $lang["invalid_page_load"];
    }

}


