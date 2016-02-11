<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Support"]["Monster List"] = $file;
}
else
{
    $lefttitle = "Monster List";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
            $page_gen = isset($_GET["page_gen"]) ? $_GET["page_gen"] : "1";
            $search_fun = isset($_GET["search_fun"]) ? $_GET["search_fun"] : "";
            $enable_exit = false;
            $query_p2 = "";
            $search_query = "";
            if( empty($page) ) 
            {
                $out .= "<form method=\"GET\" action=\"" . $script_name . "\">";
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\">" . "\n";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" colspan=\"2\" style=\"padding: 4px;\"><b>Look up monster code/name</b></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Monster Code:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"item_code\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"alt1\">Monster Name:</td>";
                $out .= "<td class=\"alt2\"><input type=\"text\" name=\"item_name\" /></td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td colspan=\"2\"><input type=\"hidden\" name=\"do\" value=\"" . $_GET["do"] . "\" /><input type=\"submit\" value=\"Search\" name=\"search_fun\" /></td>";
                $out .= "</tr>";
                $out .= "</table>";
                $out .= "</form>";
                connectitemsdb();
                if( $search_fun != "" ) 
                {
                    $item_code = isset($_GET["item_code"]) ? $_GET["item_code"] : "";
                    $item_name = isset($_GET["item_name"]) ? $_GET["item_name"] : "";
                    if( $item_code == "" && $item_name == "" ) 
                    {
                        $enable_exit = true;
                        $out .= "<p align='center'><b>Sorry, make sure you filled in either the mob name or code</b></p>";
                    }

                    if( $enable_exit != true ) 
                    {
                        $item_code = antiject($item_code);
                        $item_name = antiject($item_name);
                        if( $item_name != "" || $item_code != "" ) 
                        {
                            $search_query = " WHERE ";
                            $query_p2 .= " AND ";
                        }
                        else
                        {
                            $query_p2 .= "WHERE ";
                        }

                        if( $item_code != "" ) 
                        {
                            if( !preg_match("/%/", $item_code) ) 
                            {
                                $search_query .= "" . " item_code = '" . $item_code . "'";
                            }
                            else
                            {
                                $search_query .= "" . " item_code LIKE '" . $item_code . "'";
                            }

                        }

                        if( $item_name != "" ) 
                        {
                            if( $item_code != "" ) 
                            {
                                $search_query .= " OR ";
                            }

                            $item_name = str_replace(" ", "_", $item_name);
                            if( !preg_match("/%/", $item_name) ) 
                            {
                                $search_query .= "" . " item_name = '" . $item_name . "'";
                            }
                            else
                            {
                                $search_query .= "" . " item_name LIKE '" . $item_name . "'";
                            }

                        }

                    }
                    else
                    {
                        $query_p2 .= " WHERE ";
                    }

                    gamecp_log(0, $userdata["username"], "" . "ADMIN - Monster LIST - Searched for:  " . $item_code . " or " . $item_name, 0);
                }
                else
                {
                    $query_p2 .= " WHERE ";
                }

                include("./includes/pagination/ps_pagination.php");
                $query_p1 = "SELECT item_id, item_code, item_name FROM tbl_code_monstercharacter" . $search_query;
                $query_p2 .= "" . "item_id NOT IN ( SELECT TOP [OFFSET] item_id FROM tbl_code_monstercharacter " . $search_query . " ORDER BY item_id) ORDER BY item_id ASC";
                $url = str_replace("&page_gen=" . $page_gen, "", $_SERVER["REQUEST_URI"]);
                $pager = new PS_Pagination($items_dbconnect, $query_p1, $query_p2, 50, 10, $url);
                $rs = $pager->paginate();
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
                $out .= "<tr>";
                $out .= "<td colspan=\"3\" nowrap>Total number of mobs found: " . number_format($pager->totalResults()) . "</td>";
                $out .= "</tr>";
                $out .= "<tr>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Monster ID</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Monster Code</b></td>";
                $out .= "<td class=\"thead\" style=\"padding: 4px;\" nowrap><b>Monster Name</b></td>";
                $out .= "</tr>";
                while( $row = mssql_fetch_array($rs) ) 
                {
                    $out .= "<tr>";
                    $out .= "<td class=\"alt2\" nowrap>" . $row["item_id"] . "</td>";
                    $out .= "<td class=\"alt1\" nowrap>" . $row["item_code"] . "</td>";
                    $out .= "<td class=\"alt1\" nowrap>" . str_replace("_", " ", $row["item_name"]) . "</td>";
                    $out .= "</td>";
                }
                $out .= "</table>";
                $out .= "<br/>";
                $out .= $pager->renderFullNav();
                @mssql_free_result($rs);
            }
            else
            {
                $out .= $lang["invalid_page_id"];
            }

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


