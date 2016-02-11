<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Account"]["Change Password"] = $file;
}
else
{
    $lefttitle = "Change Account Password";
    if( $this_script == $script_name && $isuser ) 
    {
        $page = isset($_GET["page"]) ? $_GET["page"] : "";
        $show_form = true;
        if( $page == "comf" ) 
        {
            connectuserdb();
            $oldpassword = antiject($_REQUEST["oldpassword"]);
            $newpassword = antiject($_REQUEST["newpassword"]);
            $comfpassword = antiject($_REQUEST["comfpassword"]);
            $exit = false;
            $error_msg = "";
            if( eregi("[^a-zA-Z0-9_-]", $oldpassword) || eregi("[^a-zA-Z0-9_-]", $newpassword) || eregi("[^a-zA-Z0-9_-]", $comfpassword) ) 
            {
                $error_msg .= "Invalid password. Passwords can only contain letters numbers and underscores or dashes.<br/>";
                $exit = true;
            }

            if( $newpassword != $comfpassword ) 
            {
                $error_msg .= "New password and comfirm password fields must match.<br/>";
                $exit = true;
            }

            if( empty($newpassword) || empty($comfpassword) || empty($oldpassword) ) 
            {
                $error_msg .= "You left a blank field.<br/>";
                $exit = true;
            }

            if( strlen($newpassword) < 4 || 12 < strlen($newpassword) ) 
            {
                $error_msg .= "Your password must be between  4 and  12 characters long.<br/>";
                $exit = true;
            }

            $query_result = mssql_query("SELECT Password FROM " . TABLE_LUACCOUNT . " WHERE ID=CONVERT(binary,\"" . $userdata["username"] . "\")");
            $query = mssql_fetch_array($query_result);
            if( trim($query["Password"]) != $oldpassword ) 
            {
                $error_msg .= "Your old password did not match the one you specified<br/>";
                $exit = true;
            }

            if( $query[2] == $newpassword ) 
            {
                $error_msg .= "Your old password and new password are the same.<br/>";
                $exit = true;
            }

            if( !$exit ) 
            {
                $newpassword = ereg_replace("" . ";\$", "", $newpassword);
                $newpassword = ereg_replace("\\\\", "", $newpassword);
                $oldpassword = ereg_replace("" . ";\$", "", $oldpassword);
                $oldpassword = ereg_replace("\\\\", "", $oldpassword);
                $comfpassword = ereg_replace("" . ";\$", "", $comfpassword);
                $comfpassword = ereg_replace("\\\\", "", $comfpassword);
                $newpassword = antiject($newpassword);
                $oldpassword = antiject($oldpassword);
                $comfpassword = antiject($comfpassword);
                $sql = "UPDATE " . TABLE_LUACCOUNT . " SET Password = CONVERT(binary,\"" . $newpassword . "\") WHERE ID = CONVERT(binary,\"" . $userdata["username"] . "\")";
                if( !($query_result = mssql_query($sql)) ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Query Failed! Enabling Debugging for Admins (if allowed)</p>";
                    if( $config["security_enable_debug"] == 1 ) 
                    {
                        $out .= "<p>DEBUG(?):<br/>" . "\n";
                        $out .= mssql_get_last_message();
                        $out .= "</p>";
                    }

                }
                else
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Your password has been sucessfully changed.</p>";
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You are now required to re-login with your new password.</p>";
                    $show_form = false;
                    gamecp_log(1, $userdata["username"], "GAMECP - CHANGE PASSWORD", 1);
                    $_SESSION = array(  );
                    setcookie("gamecp_userdata", "", time() - 3600);
                    if( isset($_COOKIE["gamecp_userdata"]) ) 
                    {
                        unset($_COOKIE["gamecp_userdata"]);
                    }

                    @session_destroy();
                }

            }
            else
            {
                $out .= "<p style=\"text-align: center; font-weight: bold; color: red;\">" . $error_msg . "</p>";
            }

        }

        if( $show_form ) 
        {
            $out .= "<form method=\"POST\" action=\"" . $script_name . "?do=user_changepassword&amp;page=comf\">" . "\n";
            $out .= "<b>Passwords must be between 4 and 12 characters long using numbers and letters only! Passwords are CaSe SenSiTiVe!!</b>" . "\n";
            $out .= "<table border=\"0\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td><strong>Old Password: </td>" . "\n";
            $out .= "\t\t<td><input type=\"password\" name=\"oldpassword\"></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td><strong>New Password: </td>" . "\n";
            $out .= "\t\t<td><input type=\"password\" name=\"newpassword\"></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td><strong>Comfirm Password: </td>" . "\n";
            $out .= "\t\t<td><input type=\"password\" name=\"comfpassword\"></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Change Password\"></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "</table>" . "\n";
            $out .= "</form>" . "\n";
        }

    }

}


