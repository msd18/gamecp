<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Server Admin"]["Vote Sites"] = $file;
}
else
{
    $lefttitle = "Vote for Game Points Sites";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"50%\" align=\"center\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\"><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "\">View Sites</a></td>" . "\n";
            $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\"><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "&page=addedit\">Add Site</a></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "</table>" . "\n";
            if( empty($page) ) 
            {
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td class=\"thead\" style=\"text-align: center;\" nowrap>ID</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Site Name</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Site Image</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" nowrap>Reset Time</td>" . "\n";
                $out .= "\t\t<td class=\"thead\" colspan=\"2\" nowrap>Options</td>" . "\n";
                $out .= "\t</tr>" . "\n";
                connectgamecpdb();
                $sites_sql = "SELECT vote_id, vote_site_name, vote_site_url, vote_site_image, vote_reset_time, vote_count FROM gamecp_vote_sites";
                if( !($sites_result = @mssql_query($sites_sql)) ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to get sites information</p>";
                    if( $is_superadmin == 1 ) 
                    {
                        $out .= "<p>DEBUG(?): " . mssql_get_last_message() . "\n";
                        $out .= "<br/>SQL: " . $sites_sql;
                        $out .= "</p>";
                    }

                }

                while( $row = mssql_fetch_array($sites_result) ) 
                {
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" valign=\"top\" style=\"text-align: center;\" nowrap>" . $row["vote_id"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" nowrap>" . $row["vote_site_name"] . "</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" style=\"text-align: center;\" width=\"1\" nowrap><a href=\"" . $row["vote_site_url"] . "\"><img src=\"" . $row["vote_site_image"] . "\" width=\"50\" /></a></td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" valign=\"top\" nowrap>" . $row["vote_reset_time"] . "s</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\" nowrap><a href=\"" . $script_name . "?do=" . $_GET["do"] . "&page=addedit&vote_id=" . $row["vote_id"] . "\"  style=\"text-decoration: none;\">Edit</a></td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\" nowrap><a href=\"" . $script_name . "?do=" . $_GET["do"] . "&page=delete&vote_id=" . $row["vote_id"] . "\"  style=\"text-decoration: none;\">Delete</a></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }
                if( mssql_num_rows($sites_result) <= 0 ) 
                {
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"alt1\" colspan=\"7\" style=\"text-align: center; font-weight: bold;\">No vote for gp sites have been added.</td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                }

                $out .= "</table>";
                @mssql_free_result($sites_result);
                return 1;
            }

            if( $page == "addedit" ) 
            {
                connectgamecpdb();
                $display_form = true;
                $do_process = 0;
                $exit_process = false;
                $exit_text = "";
                $add_submit = isset($_POST["add_submit"]) ? 1 : 0;
                $edit_submit = isset($_POST["edit_submit"]) ? 1 : 0;
                if( isset($_POST["vote_id"]) || isset($_GET["vote_id"]) ) 
                {
                    $vote_id = isset($_POST["vote_id"]) && is_int((int) $_POST["vote_id"]) ? (int) $_POST["vote_id"] : (int) $_GET["vote_id"];
                    if( !is_numeric($vote_id) ) 
                    {
                        $vote_id = "";
                    }

                }
                else
                {
                    $vote_id = "";
                }

                $vote_site_name = isset($_POST["vote_site_name"]) ? antiject($_POST["vote_site_name"]) : "";
                $vote_site_url = isset($_POST["vote_site_url"]) ? antiject($_POST["vote_site_url"]) : "";
                $vote_site_image = isset($_POST["vote_site_image"]) ? antiject($_POST["vote_site_image"]) : "";
                $vote_reset_time = isset($_POST["vote_reset_time"]) ? antiject($_POST["vote_reset_time"]) : "43200";
                if( $add_submit == 1 || $edit_submit == 1 ) 
                {
                    $do_process = 1;
                }

                if( $vote_id != "" ) 
                {
                    $page_mode = "edit_submit";
                    $submit_name = "Update Site";
                    $this_mode_title = "Edit Site";
                    $disable = " disabled";
                    if( $do_process == 0 ) 
                    {
                        $select_sql = "" . "SELECT vote_id, vote_site_name, vote_site_url, vote_site_image, vote_reset_time, vote_count FROM gamecp_vote_sites WHERE vote_id = '" . $vote_id . "'";
                        if( !($select_result = mssql_query($select_sql)) ) 
                        {
                            $display_form = false;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error occured while trying to site info</p>";
                        }
                        else
                        {
                            if( 0 < mssql_num_rows($select_result) ) 
                            {
                                $info = mssql_fetch_array($select_result);
                                $vote_site_name = $info["vote_site_name"];
                                $vote_site_url = $info["vote_site_url"];
                                $vote_site_image = $info["vote_site_image"];
                                $vote_reset_time = $info["vote_reset_time"];
                            }
                            else
                            {
                                $display_form = false;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">No such site found</p>";
                            }

                        }

                        @mssql_free_result($select_result);
                    }

                }
                else
                {
                    $page_mode = "add_submit";
                    $submit_name = "Add site";
                    $this_mode_title = "Adding a new vote site";
                    $disable = "";
                }

                if( $do_process == 1 ) 
                {
                    if( $vote_site_name == "" ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have not filled in a name for this site<br/>";
                    }

                    if( $vote_reset_time == "" ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have not filled in a reset time (in seconds)<br/>";
                    }

                    if( !is_numeric($vote_reset_time) ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; Invalid reset time given<br/>";
                    }

                    if( !is_valid_url($vote_site_url) ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have entered an invalid website url (make sure you include http://)<br/>";
                    }

                    if( !is_valid_url($vote_site_image) ) 
                    {
                        $exit_process = true;
                        $exit_text .= "&raquo; You have entered an invalid image url (make sure you include http://)<br/>";
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
                    if( $add_submit == 1 ) 
                    {
                        $insert_sql = "" . "INSERT INTO gamecp_vote_sites (vote_site_name, vote_site_url, vote_site_image, vote_reset_time) VALUES ('" . $vote_site_name . "', '" . $vote_site_url . "', '" . $vote_site_image . "', '" . $vote_reset_time . "')";
                        if( !($insert_result = @mssql_query($insert_sql)) ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to add vote site</p>";
                            if( $is_superadmin == 1 ) 
                            {
                                $out .= "<p>DEBUG(?): " . mssql_get_last_message() . "\n";
                                $out .= "<br/>SQL: " . $sites_sql;
                                $out .= "</p>";
                            }

                        }
                        else
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully added the site " . $vote_site_name . "</p>";
                            gamecp_log(0, $userdata["username"], "" . "ADMIN - VOTE SITES - ADDED - Site Name: " . $vote_site_name, 1);
                            $display_form = false;
                        }

                    }
                    else
                    {
                        if( $edit_submit == 1 ) 
                        {
                            $update_sql = "" . "UPDATE gamecp_vote_sites SET vote_site_name = '" . $vote_site_name . "', vote_site_url = '" . $vote_site_url . "', vote_site_image = '" . $vote_site_image . "', vote_reset_time = '" . $vote_reset_time . "' WHERE vote_id = '" . $vote_id . "'";
                            if( !($update_result = mssql_query($update_sql, $gamecp_dbconnect)) ) 
                            {
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to update vote site</p>";
                                if( $is_superadmin == 1 ) 
                                {
                                    $out .= "<p>DEBUG(?): " . mssql_get_last_message() . "\n";
                                    $out .= "<br/>SQL: " . $sites_sql;
                                    $out .= "</p>";
                                }

                            }
                            else
                            {
                                if( 0 < mssql_rows_affected($gamecp_dbconnect) ) 
                                {
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully updated site: " . $vote_site_name . "</p>";
                                    $display_form = false;
                                    gamecp_log(0, $userdata["username"], "" . "ADMIN - VOTE SITES - UPDATED - Site ID: " . $vote_id, 1);
                                }
                                else
                                {
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">No vote site found in the database</p>";
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
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Site Name:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"vote_site_name\" value=\"" . $vote_site_name . "\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Reset Time:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"vote_reset_time\" value=\"" . $vote_reset_time . "\"/> (seconds)</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Site URL:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"vote_site_url\" value=\"" . $vote_site_url . "\" size=\"50\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" width=\"1\" nowrap>Site Image URL:</td>" . "\n";
                    $out .= "\t\t<td class=\"alt1\"><input type=\"text\" name=\"vote_site_image\" value=\"" . $vote_site_image . "\"  size=\"50\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"alt2\" colspan=\"2\" nowrap>" . "\n";
                    $out .= "\t\t\t<input name=\"vote_id\" type=\"hidden\" value=\"" . $vote_id . "\"/>" . "\n";
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
                    $vote_id = isset($_GET["vote_id"]) && is_numeric($_GET["vote_id"]) ? $_GET["vote_id"] : "";
                    if( $vote_id == "" ) 
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">No such site found</p>";
                        return 1;
                    }

                    connectgamecpdb();
                    $site_query = mssql_query("" . "SELECT vote_site_name FROM gamecp_vote_sites WHERE vote_id = '" . $vote_id . "'");
                    $site_info = mssql_fetch_array($site_query);
                    if( mssql_num_rows($site_query) <= 0 ) 
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">No such banned account found</p>";
                    }
                    else
                    {
                        $out .= "<form method=\"post\">" . "\n";
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Are you sure you want to DELETE the site: <u>" . antiject($site_info["vote_site_name"]) . "</u> (ID: " . $vote_id . ")?</p>" . "\n";
                        $out .= "<p style=\"text-align: center;\"><input type=\"hidden\" name=\"vote_id\" value=\"" . $vote_id . "\"/><input type=\"hidden\" name=\"page\" value=\"delete_site\"/><input type=\"submit\" name=\"yes\" value=\"Yes\"/> <input type=\"submit\" name=\"no\" value=\"No\"/></p>";
                        $out .= "</form>";
                    }

                    @mssql_free_result($site_query);
                    return 1;
                }

                if( $page == "delete_site" ) 
                {
                    $yes = isset($_POST["yes"]) ? "1" : "0";
                    $no = isset($_POST["no"]) ? "1" : "0";
                    if( isset($_POST["vote_id"]) && is_numeric($_POST["vote_id"]) ) 
                    {
                        $vote_id = antiject($_POST["vote_id"]);
                    }
                    else
                    {
                        $vote_id = "";
                    }

                    if( $no != 1 && $vote_id != "" ) 
                    {
                        connectgamecpdb();
                        $site_query = mssql_query("" . "SELECT vote_site_name FROM gamecp_vote_sites WHERE vote_id = '" . $vote_id . "'");
                        $site = mssql_fetch_array($site_query);
                        if( mssql_num_rows($site_query) <= 0 ) 
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">No such site found</p>";
                        }
                        else
                        {
                            $cquery = mssql_query("DELETE FROM gamecp_vote_sites WHERE vote_id = " . $vote_id);
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully deleted the site: " . antiject($site["vote_site_name"]) . " (ID: " . $vote_id . ")</p>";
                            gamecp_log(2, $userdata["username"], "ADMIN - VOTE SITES - DELETED - Site Name:  " . antiject($site["vote_site_name"]) . " | ID: " . $vote_id, 1);
                        }

                        @mssql_free_result($site_query);
                        return 1;
                    }

                    header("" . "Location: " . $script_name . "?do=" . $_GET["do"]);
                    return 1;
                }

                $out .= $lang["invalid_page_id"];
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

function game_cp_3($url)
{
    if( !preg_match("~^https?://~i", $url) ) 
    {
        return false;
    }

    return true;
}


