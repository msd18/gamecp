<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Account"]["Change Char Name"] = $file;
}
else
{
    if( $this_script == $script_name && $isuser ) 
    {
        $lefttitle = "Change Character Name";
        $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
        $echodata = "";
        if( $page == "" ) 
        {
            $count = 0;
            if( !($chars = getcharacters($userdata["serial"])) ) 
            {
                $out .= "<p style=\"text-align: center; font-weight: bold;\">According to our database, you have no characters on your account</p>";
                return 1;
            }

            if( $userdata["points"] < $config["specialgp_charname"] ) 
            {
                $out .= "<strong><font color=\"#e90000\">Not enough gamepoints. It costs " . $config["specialgp_charname"] . " Game Points and you have " . $userdata["points"] . ".</font></strong><br><br><strong>Change your name to anything that has Admin or GM in it will get you banned. DONT do it!</strong><br><br>";
                $out .= "<form method=\"POST\" action=\"" . $script_name . "?do=" . $_GET["do"] . "&amp;page=comf\">";
                $out .= "<table border=\"0\">" . "<tr><td><strong>Character</strong></td><td><select name=\"oldchar\">";
                $chardata = getcharacters($userdata["serial"]);
                if( !empty($chardata) ) 
                {
                    foreach( $chardata as $character ) 
                    {
                        $out .= "<option value=\"" . $character["Name"] . "\">" . $character["Name"];
                    }
                }

                $out .= "</select></td></tr>" . "<tr><td><strong>New Character Name: </strong></td><td> <input type=\"text\" name=\"newname\"></td></tr>" . "<tr><td><strong>Comfirm Name: </strong></td><td> <input type=\"text\" name=\"comfname\"></td></tr>" . "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" disabled value=\"Change Name\"></td></tr>" . "</table></form>";
                return 1;
            }

            if( empty($chars) ) 
            {
                $out .= "<center><strong>No characters made</strong></center>";
                return 1;
            }

            $out .= "<strong>It costs " . $config["specialgp_charname"] . " Game Points to change your account name.</strong><br><br><strong>Change your name to anything that has Admin or GM in it will get you banned. DONT do it!</strong><br><br>";
            $out .= "<form method=\"POST\" action=\"" . $script_name . "?do=" . $_GET["do"] . "&amp;page=comf\">";
            $out .= "<table border=\"0\">" . "<tr><td><strong>Character</strong></td><td><select name=\"oldchar\">";
            $chardata = getcharacters($userdata["serial"]);
            if( !empty($chardata) ) 
            {
                foreach( $chardata as $character ) 
                {
                    $out .= "<option value=\"" . $character["Name"] . "\">" . $character["Name"];
                }
            }

            $out .= "</select></td></tr>" . "<tr><td><strong>New Character Name: </strong></td><td> <input type=\"text\" name=\"newname\"></td></tr>" . "<tr><td><strong>Comfirm Name: </strong></td><td> <input type=\"text\" name=\"comfname\"></td></tr>" . "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Change Name\"></td></tr>" . "</table></form>";
            return 1;
        }

        if( $page == "comf" ) 
        {
            $oldchar = antiject($_REQUEST["oldchar"]);
            $newname = antiject($_REQUEST["newname"]);
            $comfname = antiject($_REQUEST["comfname"]);
            if( eregi("[^a-zA-Z0-9]", $newname) || eregi("[^a-zA-Z0-9]", $comfname) ) 
            {
                $echodata .= "Invalid name. Names can only contain letters and numbers.";
            }
            else
            {
                if( $newname != $comfname ) 
                {
                    $echodata .= "New Character Name and Comfirm Name fields must match.";
                }
                else
                {
                    if( empty($newname) || empty($comfname) ) 
                    {
                        $echodata .= "You left a blank field.";
                    }
                    else
                    {
                        if( !isusers($userdata["username"], $oldchar) ) 
                        {
                            $echodata .= "This character does not belong to you or name has already been changed.";
                        }
                        else
                        {
                            if( $userdata["points"] < $config["specialgp_charname"] ) 
                            {
                                $echodata .= "You do not have enough Game Points for this";
                            }
                            else
                            {
                                if( strlen($newname) < 4 || 15 < strlen($newname) ) 
                                {
                                    $echodata .= "Character name must be 4 to 15 characters long";
                                }
                                else
                                {
                                    $oldchar = ereg_replace("" . ";\$", "", $oldchar);
                                    $oldchar = ereg_replace("\\\\", "", $oldchar);
                                    $newname = ereg_replace("" . ";\$", "", $newname);
                                    $newname = ereg_replace("\\\\", "", $newname);
                                    $comfname = ereg_replace("" . ";\$", "", $comfname);
                                    $comfname = ereg_replace("\\\\", "", $comfname);
                                    $oldchar = antiject($oldchar);
                                    $newname = antiject($newname);
                                    if( game_cp_14($newname) ) 
                                    {
                                        $echodata .= "A character with this name already exists";
                                    }
                                    else
                                    {
                                        $chardata = game_cp_15($oldchar);
                                        write_log($chardata["Serial"], $oldchar, $newname, $_SERVER["REMOTE_ADDR"]);
                                        connectdatadb();
                                        mssql_query("UPDATE tbl_base SET Name = '" . $newname . "' WHERE AccountSerial = '" . $userdata["serial"] . "' AND Name = '" . $oldchar . "'");
                                        $echodata .= "Your Character name has been sucessfully changed to " . $newname . ".";
                                        $creditsleft = $userdata["points"] - $config["specialgp_charname"];
                                        connectgamecpdb();
                                        mssql_query("UPDATE gamecp_gamepoints SET user_points=\"" . $creditsleft . "\" WHERE user_account_id=\"" . $userdata["serial"] . "\"");
                                        gamecp_log(1, $userdata["username"], "GAMECP - CHANGE CHAR NAME - Char Serial: " . $chardata["Serial"] . "" . " | Old Name: " . $oldchar . " | New Name: " . $newname . " | GP: -" . $config["specialgp_charname"], 1);
                                    }

                                }

                            }

                        }

                    }

                }

            }

            $out .= "<center>" . $echodata . "<br><a href=\"" . $script_name . "?do=" . $_GET["do"] . "\">Return</a></center>";
        }

    }

}


