<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Item Shop Admin"]["Manage Categories"] = $file;
}
else
{
    $lefttitle = "Item Shop Admin - Manage Categories";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        $menu_array = array(  );
        $prepend = "";
        connectgamecpdb();
        $query = mssql_query("SELECT cat_name, cat_id, cat_sub_id FROM gamecp_shop_categories", $gamecp_dbconnect);
        while( $row = mssql_fetch_assoc($query) ) 
        {
            $menu_array[$row["cat_id"]] = array( "name" => $row["cat_name"], "parent" => $row["cat_sub_id"], "id" => $row["cat_id"] );
            $prepend[$row["cat_id"]] = "";
        }
        @mssql_free_result($query);
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $cat_id = isset($_GET["cat_id"]) && is_int((int) $_GET["cat_id"]) ? antiject((int) $_GET["cat_id"]) : "";
            $exit_stage = 0;
            $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"50%\" align=\"center\">" . "\n";
            $out .= "\t<tr>" . "\n";
            $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\"><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "\">View Categories</a></td>" . "\n";
            $out .= "\t\t<td class=\"alt1\" style=\"text-align: center;\"><a href=\"./" . $script_name . "?do=" . $_GET["do"] . "&page=addedit\">New Category</a></td>" . "\n";
            $out .= "\t</tr>" . "\n";
            $out .= "</table>" . "\n";
            $out .= "<br/>" . "\n";
            if( empty($page) ) 
            {
                $sub_cat_id = isset($_GET["sub_cat_id"]) && is_int((int) $_GET["sub_cat_id"]) ? (int) $_GET["sub_cat_id"] : 0;
                if( isset($_POST["submit"]) ) 
                {
                    $cat_ids = isset($_POST["cat_id"]) && is_array($_POST["cat_id"]) ? $_POST["cat_id"] : "";
                    $cat_order = isset($_POST["cat_order"]) && is_array($_POST["cat_order"]) ? $_POST["cat_order"] : "";
                    if( $cat_ids != "" && $cat_order != "" ) 
                    {
                        $count = count($cat_ids);
                        for( $i = 0; $i < $count; $i++ ) 
                        {
                            $catorder = is_int((int) $cat_order[$i]) ? antiject((int) $cat_order[$i]) : "";
                            $catids = is_int((int) $cat_ids[$i]) ? antiject((int) $cat_ids[$i]) : "";
                            if( $catorder != "" && $catids != "" ) 
                            {
                                $update_order = "UPDATE gamecp_shop_categories SET cat_order = '" . $catorder . "' WHERE cat_id = '" . $catids . "'";
                                if( !($cat_order_result = mssql_query($update_order)) ) 
                                {
                                    $exit_stage = 1;
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Unable to update category order!</p>";
                                }

                            }

                        }
                        gamecp_log(0, $userdata["username"], "ADMIN - MANAGE CATEGORIES - ORDER - Changed category order", 1);
                    }

                }

                $cat_sql = "" . "SELECT cat_id, cat_name, cat_sub_id, cat_order FROM gamecp_shop_categories WHERE cat_sub_id = '" . $sub_cat_id . "' ORDER BY cat_order ASC";
                if( !($cat_result = mssql_query($cat_sql)) ) 
                {
                    $exit_stage = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain category information</p>";
                }

                if( $exit_stage == 0 ) 
                {
                    $cat = array(  );
                    while( $row = mssql_fetch_array($cat_result) ) 
                    {
                        $cat[] = $row;
                    }
                    mssql_free_result($cat_result);
                    $total_categories = count($cat);
                    $out .= "<form method=\"post\">" . "\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">" . "\n";
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"thead\" style=\"text-align: center;\" nowrap>#</td>" . "\n";
                    $out .= "\t\t\t<td class=\"thead\" nowrap>Category Name</td>" . "\n";
                    $out .= "\t\t\t<td class=\"thead\" nowrap>Order</td>" . "\n";
                    $out .= "\t\t\t<td class=\"thead\" style=\"text-align: center;\" colspan=\"2\" nowrap>Options</td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                    for( $i = 0; $i < $total_categories; $i++ ) 
                    {
                        $out .= "\t\t<tr>" . "\n";
                        $out .= "\t\t\t<td class=\"alt2\" style=\"text-align: center;\" nowrap>" . $cat[$i]["cat_id"] . "</td>" . "\n";
                        $out .= "\t\t\t<td class=\"alt1\" nowrap><a href=\"" . $script_name . "?do=" . $_GET["do"] . "&sub_cat_id=" . $cat[$i]["cat_id"] . "\">" . $cat[$i]["cat_name"] . " &raquo;</a></td>" . "\n";
                        $out .= "\t\t\t<td class=\"alt1\" nowrap><input type=\"hidden\" name=\"cat_id[]\" value=\"" . $cat[$i]["cat_id"] . "\" /><input type=\"text\" name=\"cat_order[]\" value=\"" . $cat[$i]["cat_order"] . "\" size=\"1\" /></td>" . "\n";
                        $out .= "\t\t\t<td class=\"alt1\" style=\"text-align: center;\" nowrap><a href=\"" . $script_name . "?do=" . $_GET["do"] . "&page=addedit&edit_cat_id=" . $cat[$i]["cat_id"] . "\">Edit Category</a></td>" . "\n";
                        $out .= "\t\t\t<td class=\"alt1\" style=\"text-align: center;\" nowrap><a href=\"" . $script_name . "?do=" . $_GET["do"] . "&page=delete&cat_id=" . $cat[$i]["cat_id"] . "\">Delete Category</a></td>" . "\n";
                        $out .= "\t\t</tr>" . "\n";
                    }
                    if( $total_categories != 0 ) 
                    {
                        $out .= "\t\t<tr>" . "\n";
                        $out .= "\t\t\t<td colspan=\"5\" style=\"text-align: right;\">" . "\n";
                        $out .= "\t\t\t\t<input type=\"submit\" name=\"submit\" value=\"Update Order\" />" . "\n";
                        $out .= "\t\t\t</td>" . "\n";
                        $out .= "\t\t</tr>" . "\n";
                    }
                    else
                    {
                        $out .= "\t\t<tr>" . "\n";
                        $out .= "\t\t\t<td colspan=\"5\" style=\"text-align: center; font-weight: bold;\" class=\"alt1\">" . "\n";
                        $out .= "\t\t\t\tNo categories found in the database" . "\n";
                        $out .= "\t\t\t</td>" . "\n";
                        $out .= "\t\t</tr>" . "\n";
                    }

                    $out .= "</table>" . "\n";
                    $out .= "</form>" . "\n";
                }

                @mssql_free_result($cat_result);
                return 1;
            }

            if( $page == "addedit" ) 
            {
                $add_submit = isset($_POST["add_submit"]) ? 1 : 0;
                $edit_submit = isset($_POST["edit_submit"]) ? 1 : 0;
                $exit_process = 0;
                $exit_text = "";
                $display_form = 1;
                $do_process = 0;
                if( isset($_POST["edit_cat_id"]) || isset($_GET["edit_cat_id"]) ) 
                {
                    $edit_cat_id = isset($_POST["edit_cat_id"]) && is_int((int) $_POST["edit_cat_id"]) ? (int) $_POST["edit_cat_id"] : (int) $_GET["edit_cat_id"];
                    if( !is_numeric($edit_cat_id) ) 
                    {
                        $edit_cat_id = 0;
                    }

                }
                else
                {
                    $edit_cat_id = 0;
                }

                $cat_name = isset($_POST["cat_name"]) ? antiject($_POST["cat_name"]) : "";
                $cat_description = isset($_POST["cat_description"]) ? antiject($_POST["cat_description"]) : "";
                $cat_order = isset($_POST["cat_order"]) && is_int((int) $_POST["cat_order"]) ? antiject((int) $_POST["cat_order"]) : "";
                $cat_sub_id = isset($_POST["cat_sub_id"]) && is_int((int) $_POST["cat_sub_id"]) ? antiject((int) $_POST["cat_sub_id"]) : "";
                if( $add_submit == 1 || $edit_submit == 1 ) 
                {
                    $do_process = 1;
                }

                if( $edit_cat_id != 0 ) 
                {
                    $page_mode = "edit_submit";
                    $submit_name = "Update Category";
                    $this_mode_title = "Editing a category";
                    if( $do_process == 0 ) 
                    {
                        connectgamecpdb();
                        $select_sql = "" . "SELECT cat_id, cat_name, cat_sub_id, cat_description, cat_order FROM gamecp_shop_categories WHERE cat_id = '" . $edit_cat_id . "'";
                        if( !($result = mssql_query($select_sql)) ) 
                        {
                            $exit_stage = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain category information</p>";
                        }

                        if( $exit_stage == 0 ) 
                        {
                            if( !($cat = mssql_fetch_array($result)) ) 
                            {
                                $display_form = 0;
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid cat id supplied</p>";
                            }
                            else
                            {
                                $cat_name = $cat["cat_name"];
                                $cat_sub_id = $cat["cat_sub_id"];
                                $cat_description = $cat["cat_description"];
                                $cat_order = $cat["cat_order"];
                            }

                        }

                    }

                }
                else
                {
                    $page_mode = "add_submit";
                    $submit_name = "Add Category";
                    $this_mode_title = "Adding a new Category";
                }

                if( $do_process == 1 ) 
                {
                    if( $cat_name == "" ) 
                    {
                        $exit_process = 1;
                        $exit_text .= "&raquo; Category Name was left blank<br/>";
                    }

                    if( $cat_order == 0 ) 
                    {
                        $exit_process = 1;
                        $exit_text .= "&raquo; Category ORDER was left blank<br/>";
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
                        $display_form = 0;
                        if( $add_submit == 1 ) 
                        {
                            $insert_sql = "" . "INSERT INTO gamecp_shop_categories (cat_name, cat_sub_id, cat_description, cat_order) VALUES ('" . $cat_name . "', '" . $cat_sub_id . "', '" . $cat_description . "', '" . $cat_order . "')";
                            if( !($query_insert = mssql_query($insert_sql)) ) 
                            {
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error, cannot add category to the database</p>";
                            }
                            else
                            {
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully added the new category!!</p>";
                                gamecp_log(0, $userdata["username"], "" . "ADMIN - MANAGE CATEGORIES - ADDED - New category: " . $cat_name, 1);
                            }

                        }
                        else
                        {
                            if( $edit_submit == 1 ) 
                            {
                                $update_sql = "" . "UPDATE gamecp_shop_categories SET cat_name = '" . $cat_name . "', cat_sub_id = '" . $cat_sub_id . "', cat_description = '" . $cat_description . "', cat_order = '" . $cat_order . "' WHERE cat_id = '" . $edit_cat_id . "'";
                                if( !($query_insert = mssql_query($update_sql)) ) 
                                {
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error, cannot update category to the database</p>";
                                }
                                else
                                {
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully updated the new category!!</p>";
                                    gamecp_log(0, $userdata["username"], "" . "ADMIN - MANAGE CATEGORIES - UPDATE - Category: " . $cat_name, 1);
                                }

                            }

                        }

                    }

                }

                if( $display_form == 1 ) 
                {
                    connectgamecpdb();
                    generate_menu($menu_array, 0, "", $prepend);
                    $subcategory_list = $options;
                    $out .= "<form method=\"post\">" . "\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">" . "\n";
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"thead\" colspan=\"2\">" . $this_mode_title . "</td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"alt2\">Category Name</td>" . "\n";
                    $out .= "\t\t\t<td class=\"alt2\"><input type=\"text\" name=\"cat_name\" value=\"" . $cat_name . "\" /></td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"alt2\">Category Description</td>" . "\n";
                    $out .= "\t\t\t<td class=\"alt2\"><input type=\"text\" name=\"cat_description\" value=\"" . $cat_description . "\" size=\"50\" /></td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"alt2\">Category Order</td>" . "\n";
                    $out .= "\t\t\t<td class=\"alt2\"><input type=\"text\" name=\"cat_order\" value=\"" . $cat_order . "\" size=\"2\" /></td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td class=\"alt2\">Parent Category</td>" . "\n";
                    $out .= "\t\t\t<td class=\"alt2\">" . "\n";
                    $out .= "\t\t\t\t<select name=\"cat_sub_id\">" . "\n";
                    $out .= "\t\t\t\t\t<option value=\"0\" style=\"background-color: #D3D3D3;\">No parent category</option>" . "\n";
                    $out .= $subcategory_list . "\n";
                    $out .= "\t\t\t\t</select>" . "\n";
                    $out .= "\t\t\t</td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td  colspan=\"2\">" . "\n";
                    $out .= "\t\t\t\t<input type=\"hidden\" name=\"cat_id\" value=\"" . $edit_cat_id . "\" /><input type=\"submit\" name=\"" . $page_mode . "\" value=\"" . $submit_name . "\" />" . "\n";
                    $out .= "\t\t\t</td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                    $out .= "</table>" . "\n";
                    $out .= "</form>" . "\n";
                    return 1;
                }

            }
            else
            {
                if( $page == "delete" ) 
                {
                    $cat_id = isset($_GET["cat_id"]) && is_int((int) $_GET["cat_id"]) ? antiject((int) $_GET["cat_id"]) : "";
                    if( $cat_id == "" ) 
                    {
                        $out .= $lang["invalid_serial"];
                        return 1;
                    }

                    connectgamecpdb();
                    $cat_query = mssql_query("" . "SELECT cat_name,cat_id FROM gamecp_shop_categories WHERE cat_id = '" . $cat_id . "'");
                    $cat_info = mssql_fetch_array($cat_query);
                    if( mssql_num_rows($cat_query) <= 0 ) 
                    {
                        $out .= $lang["invalid_serial"];
                    }
                    else
                    {
                        $out .= "<form method=\"post\">" . "\n";
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Are you sure you want the delete the cat: <u>" . $cat_info["cat_name"] . "</u>?</p>" . "\n";
                        $out .= "<p style=\"text-align: center;\"><input type=\"hidden\" name=\"cat_id\" value=\"" . $cat_id . "\"/><input type=\"hidden\" name=\"page\" value=\"delete_cat\"/><input type=\"submit\" name=\"yes\" value=\"Yes\"/> <input type=\"submit\" name=\"no\" value=\"No\"/></p>";
                        $out .= "</form>";
                    }

                    @mssql_free_result($cat_query);
                    return 1;
                }

                if( $page == "delete_cat" ) 
                {
                    $yes = isset($_POST["yes"]) ? "1" : "0";
                    $no = isset($_POST["no"]) ? "1" : "0";
                    if( isset($_POST["cat_id"]) && is_int((int) $_POST["cat_id"]) ) 
                    {
                        $cat_id = antiject((int) $_POST["cat_id"]);
                    }
                    else
                    {
                        $cat_id = 0;
                    }

                    if( $no != 1 && $cat_id != 0 ) 
                    {
                        connectgamecpdb();
                        $cat_query = mssql_query("" . "SELECT cat_id,cat_name FROM gamecp_shop_categories WHERE cat_id = '" . $cat_id . "'", $gamecp_dbconnect);
                        $cat = mssql_fetch_array($cat_query);
                        if( mssql_num_rows($cat_query) <= 0 ) 
                        {
                            $out .= $lang["invalid_serial"];
                            return 1;
                        }

                        $cquery = mssql_query("DELETE FROM gamecp_shop_categories WHERE cat_id = " . $cat_id);
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Deleted the cat name: " . $cat["cat_name"] . " (#" . $cat["cat_id"] . ")</p>";
                        gamecp_log(3, $userdata["username"], "ADMIN - MANAGE CATEGORIES - DELETED - Category Name:  " . $cat["cat_name"] . " | Cat ID: " . $cat["cat_id"], 1);
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


