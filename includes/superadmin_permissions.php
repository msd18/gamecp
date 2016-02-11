<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Super Admin"]["<b>Permissions</b>"] = $file;
}
else
{
    $lefttitle = "Super Admin - Game CP Permissions";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        $super_admin = explode(",", $admin["super_admin"]);
        $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
        if( $isuser == true && in_array($userdata["username"], $super_admin) ) 
        {
            $out .= "<p>";
            $out .= "<a href=\"" . $script_name . "?do=" . $_GET["do"] . "\">View Users</a> | <a href=\"" . $script_name . "?do=" . $_GET["do"] . "&page=add\">Add User</a>";
            $out .= "</p>";
            if( $page == "" ) 
            {
                connectgamecpdb();
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>ID</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Username</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Serial</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Permissions</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Options</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $permission_query = "SELECT admin_id, admin_serial, admin_permission FROM gamecp_permissions";
                if( !($permission_result = mssql_query($permission_query, $gamecp_dbconnect)) ) 
                {
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td colspan=\"3\">SQL Query Error has occured</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }

                while( $row = mssql_fetch_array($permission_result) ) 
                {
                    connectuserdb();
                    $user_result = mssql_query("SELECT convert(varchar,id) AS username FROM tbl_UserAccount WHERE Serial = '" . $row["admin_serial"] . "'", $user_dbconnect);
                    $user = mssql_fetch_array($user_result);
                    $user["username"] = antiject($user["username"]);
                    $module_names = "";
                    $explode = explode(",", $row["admin_permission"]);
                    $fileinfo = readCache("fileinfo.cache", 86400);
                    $fileinfo = explode("\n", $fileinfo);
                    $setmodules = true;
                    foreach( $explode as $module ) 
                    {
                        if( $module_names != "" ) 
                        {
                            $module_names .= ",";
                        }

                        foreach( $fileinfo as $moduleinfo ) 
                        {
                            $info = explode(",", $moduleinfo);
                            $module_name = $module . ".php";
                            if( $info[0] == $module_name ) 
                            {
                                $module_names .= $info[2];
                            }

                        }
                    }
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" valign=\"top\" nowrap>" . $row["admin_id"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" nowrap>" . $user["username"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" nowrap>" . $row["admin_serial"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" nowrap>" . str_replace(",", "<br/>", $module_names) . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" nowrap><a href=\"" . $script_name . "?do=" . $_GET["do"] . "&page=edit&serial=" . $row["admin_serial"] . "\">Edit User</a> | <a href=\"" . $script_name . "?do=" . $_GET["do"] . "&page=delete&serial=" . $row["admin_serial"] . "\">Delete User</a></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }
                if( mssql_num_rows($permission_result) <= 0 ) 
                {
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td colspan=\"5\" class=\"alt1\" style=\"text-align: center; font-weight: bold;\">No users have been given special permission</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }

                $out .= "</table>" . "\n";
                @mssql_free_result($permission_result);
                return 1;
            }

            if( $page == "edit" ) 
            {
                $admin_files = "";
                $support_files = "";
                if( isset($_POST["serial"]) ) 
                {
                    $serial = $_POST["serial"];
                }
                else
                {
                    if( isset($_GET["serial"]) ) 
                    {
                        $serial = $_GET["serial"];
                    }
                    else
                    {
                        $serial = "";
                    }

                }

                if( !is_numeric($serial) ) 
                {
                    $serial = "";
                }

                if( $serial == "" ) 
                {
                    $out .= "Invalid serial given";
                    return 1;
                }

                connectgamecpdb();
                $permission_query = "SELECT admin_serial, admin_permission FROM gamecp_permissions WHERE admin_serial = '" . $serial . "'";
                if( !($permission_result = mssql_query($permission_query, $gamecp_dbconnect)) ) 
                {
                    $out .= "Unable to find given serial";
                }
                else
                {
                    $row = mssql_fetch_array($permission_result);
                    $permissions = explode(",", $row["admin_permission"]);
                    connectuserdb();
                    $user_result = mssql_query("SELECT convert(varchar,id) AS username FROM tbl_UserAccount WHERE Serial = '" . $row["admin_serial"] . "'", $user_dbconnect);
                    $user = mssql_fetch_array($user_result);
                    $user["username"] = antiject($user["username"]);
                    $phpEx = "php";
                    $fileinfo = readCache("fileinfo.cache", 86400);
                    $fileinfo = explode("\n", $fileinfo);
                    foreach( $fileinfo as $files ) 
                    {
                        $file = explode(",", $files);
                        if( !in_array($file[0], $dont_allow) ) 
                        {
                            if( preg_match("/^admin_.*?\\." . $phpEx . "" . "\$/", $file[0]) ) 
                            {
                                $file[0] = substr($file[0], 0, strrpos($file[0], "."));
                                if( in_array($file[0], $permissions) ) 
                                {
                                    $selected_admin = " checked=\"checked\"";
                                }
                                else
                                {
                                    $selected_admin = "";
                                }

                                $admin_files .= "<input type=\"checkbox\" name=\"pages[]\" value=\"" . $file[0] . "\"" . $selected_admin . "> - " . $file[2] . "<br/>\n";
                            }

                            if( preg_match("/^support_.*?\\." . $phpEx . "" . "\$/", $file[0]) ) 
                            {
                                $file[0] = substr($file[0], 0, strrpos($file[0], "."));
                                if( in_array($file[0], $permissions) ) 
                                {
                                    $selected_support = " checked=\"checked\"";
                                }
                                else
                                {
                                    $selected_support = "";
                                }

                                $support_files .= "<input type=\"checkbox\" name=\"pages[]\" value=\"" . $file[0] . "\"" . $selected_support . "> - " . $file[2] . "<br/>\n";
                            }

                        }

                    }
                    $out .= "<form method=\"post\">" . "\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td valign=\"top\" class=\"alt2\"><b>Serial</b></td>" . "\n";
                    $out .= "\t\t<td valign=\"top\" class=\"alt1\">" . $serial . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td valign=\"top\" class=\"alt2\"><b>Name</b></td>" . "\n";
                    $out .= "\t\t<td valign=\"top\" class=\"alt1\">" . $user["username"] . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td valign=\"top\" class=\"alt2\"><b>Admin Permissions</b></td>" . "\n";
                    $out .= "\t\t<td valign=\"top\" class=\"alt2\"><b>Support Permissions</b></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td valign=\"top\" class=\"alt1\">" . $admin_files . "</td>" . "\n";
                    $out .= "\t\t<td valign=\"top\" class=\"alt1\">" . $support_files . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td valign=\"top\" class=\"alt2\" colspan=\"2\" style=\"text-align: center;\"><input type=\"hidden\" name=\"serial\" value=\"" . $serial . "\"/><input type=\"hidden\" name=\"page\" value=\"edit_update\"/><input type=\"submit\" name=\"submit\" value=\"Edit User\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "</table>" . "\n";
                    $out .= "</form>" . "\n";
                }

                @mssql_free_result($user_result);
                @mssql_free_result($permission_result);
                return 1;
            }

            if( $page == "edit_update" ) 
            {
                $listpages = "";
                if( isset($_POST["serial"]) ) 
                {
                    $serial = $_POST["serial"];
                }
                else
                {
                    $serial = "";
                }

                if( !is_numeric($serial) ) 
                {
                    $serial = "";
                }

                if( isset($_POST["pages"]) ) 
                {
                    $pages = $_POST["pages"];
                    for( $i = 0; $i < count($pages); $i++ ) 
                    {
                        if( $i == 0 ) 
                        {
                            $listpages .= "";
                        }
                        else
                        {
                            $listpages .= ",";
                        }

                        $listpages .= $pages[$i];
                    }
                }

                if( $serial == "" ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid serial givenM</p>";
                    return 1;
                }

                connectgamecpdb();
                $query_update = "UPDATE gamecp_permissions SET admin_permission = '" . $listpages . "' WHERE admin_serial = '" . $serial . "'";
                if( !($permission_result = mssql_query($query_update, $gamecp_dbconnect)) ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Failed to update this user due to an SQL error</p>";
                    return 1;
                }

                $out .= "<p style=\"text-align: center; font-weight: bold;\">This user has been updated!</p>";
                header("Refresh: 1; URL=" . $script_name . "?do=" . $_GET["do"]);
                gamecp_log(2, $userdata["username"], "" . "SUPER ADMIN - PERMISSIONS - EDITED: User serial " . $serial, 1);
                return 1;
            }

            if( $page == "add" ) 
            {
                $support_files = "";
                $admin_files = "";
                $phpEx = "php";
                $fileinfo = readCache("fileinfo.cache", 86400);
                $fileinfo = explode("\n", $fileinfo);
                foreach( $fileinfo as $files ) 
                {
                    $file = explode(",", $files);
                    if( !in_array($file[0], $dont_allow) ) 
                    {
                        if( preg_match("/^admin_.*?\\." . $phpEx . "" . "\$/", $file[0]) ) 
                        {
                            $file[0] = substr($file[0], 0, strrpos($file[0], "."));
                            $admin_files .= "<input type=\"checkbox\" name=\"pages[]\" value=\"" . $file[0] . "\"> - " . $file[2] . "<br/>\n";
                        }

                        if( preg_match("/^support_.*?\\." . $phpEx . "" . "\$/", $file[0]) ) 
                        {
                            $file[0] = substr($file[0], 0, strrpos($file[0], "."));
                            $support_files .= "<input type=\"checkbox\" name=\"pages[]\" value=\"" . $file[0] . "\"> - " . $file[2] . "<br/>\n";
                        }

                    }

                }
                $out .= "<form method=\"post\">" . "\n";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt2\"><b>Account Serial</b></td>" . "\n";
                $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"serials\" value=\"\"/></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"alt2\"><i>OR</i> <b>Account Name</b></td>" . "\n";
                $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"name\" value=\"\"/></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td valign=\"top\" class=\"alt2\"><b>Admin Permissions</b></td>" . "\n";
                $out .= "\t\t<td valign=\"top\" class=\"alt2\"><b>Support Permissions</b></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td valign=\"top\" class=\"alt1\">" . $admin_files . "</td>" . "\n";
                $out .= "\t\t<td valign=\"top\" class=\"alt1\">" . $support_files . "</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td valign=\"top\" class=\"alt1\" colspan=\"2\" style=\"text-align: center;\"><input type=\"hidden\" name=\"page\" value=\"add_user\"/><input type=\"submit\" name=\"submit\" value=\"Add User\"/></td>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "</table>" . "\n";
                $out .= "</form>" . "\n";
                return 1;
            }

            if( $page == "add_user" ) 
            {
                $serial = isset($_POST["serials"]) ? $_POST["serials"] : "";
                $name = isset($_POST["name"]) ? antiject($_POST["name"]) : "";
                $listpages = "";
                $search = "";
                if( isset($_POST["pages"]) ) 
                {
                    $pages = $_POST["pages"];
                    for( $i = 0; $i < count($pages); $i++ ) 
                    {
                        if( $i == 0 ) 
                        {
                            $listpages .= "";
                        }
                        else
                        {
                            $listpages .= ",";
                        }

                        $listpages .= $pages[$i];
                    }
                }
                else
                {
                    $listpages = "";
                }

                if( $serial == "" && $name == "" ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You must enter an account username or serial</p>";
                    return 1;
                }

                if( $serial != "" ) 
                {
                    $search .= "Serial = '" . $serial . "'";
                }

                if( $name != "" ) 
                {
                    if( $serial != "" ) 
                    {
                        $search .= " OR ";
                    }

                    $search .= "ID = convert(binary,'" . $name . "')";
                }

                connectuserdb();
                $user_result = mssql_query("" . "SELECT TOP 1 Serial FROM tbl_UserAccount WHERE " . $search);
                $user = mssql_fetch_array($user_result);
                $user_serial = antiject($user["Serial"]);
                if( $user_serial == "" ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">This user does not exist, please try again.</p>";
                }
                else
                {
                    connectgamecpdb();
                    $select_sql = "" . "SELECT admin_serial FROM gamecp_permissions WHERE admin_serial = '" . $user_serial . "'";
                    if( !($select_result = mssql_query($select_sql, $gamecp_dbconnect)) ) 
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while doing user check</p>";
                    }
                    else
                    {
                        if( mssql_rows_affected($gamecp_dbconnect) ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Error! Duplicate user entry.</p>";
                        }
                        else
                        {
                            $insert_query = "" . "INSERT INTO gamecp_permissions (admin_serial, admin_permission) VALUES ('" . $user_serial . "','" . $listpages . "')";
                            if( !($insert_result = mssql_query($insert_query)) ) 
                            {
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">Unable to add this user</p>";
                            }
                            else
                            {
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">User has been added to the database.</p>";
                                gamecp_log(2, $userdata["username"], "" . "SUPER ADMIN - PERMISSIONS - ADDED: User " . $user_serial, 1);
                            }

                        }

                    }

                }

                @mssql_free_result($user_result);
                return 1;
            }

            if( $page == "delete" ) 
            {
                if( isset($_POST["serial"]) ) 
                {
                    $serial = $_POST["serial"];
                }
                else
                {
                    if( isset($_GET["serial"]) ) 
                    {
                        $serial = $_GET["serial"];
                    }
                    else
                    {
                        $serial = "";
                    }

                }

                if( $serial != "" ) 
                {
                    connectuserdb();
                    $user_result = mssql_query("SELECT convert(varchar,id) AS username FROM tbl_UserAccount WHERE Serial = '" . $serial . "'", $user_dbconnect);
                    $user = mssql_fetch_array($user_result);
                    $user["username"] = antiject($user["username"]);
                    if( $user["username"] != "" ) 
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Are you sure you want the delete the user: <u>" . $user["username"] . "</u>?</p>" . "\n";
                        $out .= "<form method=\"post\">" . "\n";
                        $out .= "<p style=\"text-align: center;\"><input type=\"hidden\" name=\"serial\" value=\"" . $serial . "\"/><input type=\"hidden\" name=\"page\" value=\"delete_user\"/><input type=\"submit\" name=\"yes\" value=\"Yes\"/> <input type=\"submit\" name=\"no\" value=\"No\"/></p>";
                    }
                    else
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">This user does not exist, please try again.</p>";
                    }

                    @mssql_free_result($user_result);
                    return 1;
                }

            }
            else
            {
                if( $page == "delete_user" ) 
                {
                    $yes = isset($_POST["yes"]) ? "1" : "0";
                    $no = isset($_POST["no"]) ? "1" : "0";
                    if( isset($_POST["serial"]) && is_numeric($_POST["serial"]) ) 
                    {
                        $serial = antiject($_POST["serial"]);
                    }
                    else
                    {
                        $serial = "";
                    }

                    if( $no != 1 && $serial != "" ) 
                    {
                        $delete_query = "" . "DELETE FROM gamecp_permissions WHERE admin_serial = '" . $serial . "'";
                        if( !($delete_result = mssql_query($delete_query)) ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Unable to DELETE this user</p>";
                            return 1;
                        }

                        $out .= "<p style=\"text-align: center; font-weight: bold;\">User has been successfully deleted</p>";
                        return 1;
                    }

                    header("" . "Location: " . $script_name . "?do=" . $_GET["do"]);
                    return 1;
                }

                $out .= $lang["page_not_found"];
                return 1;
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


