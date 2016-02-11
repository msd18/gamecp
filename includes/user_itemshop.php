<?php

if ( !empty( $setmodules ) )
{
    $file = basename( __FILE__ );
    $module['Donations']['Item Shop'] = $file;
    return;
}
else
{
    $lefttitle = "Item Shop";
    $time = date( "F j Y G:i" );
    if ( $this_script == $script_name )
    {
        if ( $isuser == true )
        {
            $config['shop_discount'] = 0;
            $page = isset( $_REQUEST['page'] ) ? antiject( $_REQUEST['page'] ) : "";
            $cat_id = isset( $_GET['cat_id'] ) ? $_GET['cat_id'] : 0;
            $cat_id = isset( $cat_id ) && intval( $cat_id ) ? antiject( $cat_id ) : "0";
            $cat_id = preg_replace( sql_regcase( "/(from|select|insert|delete|update|where|drop table|show tables|#|\\*|--|\\\\)/" ), "", $cat_id );
            $today = time( );
            $base_code = 268435455;
            $exit_cat = 0;
            $race = "";
            $query_p2 = "";
            $j = 0;
            $k = 0;
            $sub_name = "";
            $is_char = false;
            if ( isset( $_POST['order'], $_POST['sort'] ) )
            {
                $sortorder_data = $_POST['order'].chr( 255 ).$_POST['sort'];
                setcookie( "sortorder", $sortorder_data, time( ) + 31104000 );
                $order = antiject( $_POST['order'] );
                $sort = antiject( $_POST['sort'] );
            }
            else
            {
                if ( isset( $_COOKIE['sortorder'] ) )
                {
                    $sortorder = explode( chr( 255 ), $_COOKIE['sortorder'] );
                    $order = $sortorder[0];
                    $sort = $sortorder[1];
                }
                else
                {
                    $order = isset( $config['shop_order_by'] ) ? $config['shop_order_by'] : "1";
                    $sort = isset( $config['shop_sort'] ) ? $config['shop_sort'] : "1";
                }
            }
            $order_raw = $order;
            $sort_raw = $sort;
            if ( $order == 1 )
            {
                $order = "item_name";
            }
            else if ( $order == 2 )
            {
                $order = "item_price";
            }
            else if ( $order == 3 )
            {
                $order = "item_race";
            }
            else if ( $order == 4 )
            {
                $order = "item_buy_count";
            }
            else if ( $order == 5 )
            {
                $order = "item_date_added";
            }
            else if ( $order == 6 )
            {
                $order = "item_date_updated";
            }
            else if ( $order == 7 )
            {
                $order = "item_dbcode";
            }
            else
            {
                $order = "item_name";
            }
            if ( $sort == 1 )
            {
                $sort = "ASC";
            }
            else
            {
                $sort = "DESC";
            }
            function get_nav( $catid, $nav = array( ) )
            {
                global $gamecp_dbconnect;
                global $script_name;
                if ( $catid != 0 )
                {
                    $select_cat = "SELECT cat_sub_id, cat_name FROM gamecp_shop_categories WHERE cat_id = '{$catid}'";
                    if ( !( $cat_result = mssql_query( $select_cat, $gamecp_dbconnect ) ) )
                    {
                        return "failed: ".mssql_get_last_message( );
                    }
                    if ( $cat = mssql_fetch_array( $cat_result ) )
                    {
                        $nav[] = " / <a href=\"".$script_name."?do=".$_GET['do']."&cat_id=".$catid."\" style=\"text-decoration: none;\">".$cat['cat_name']."</a>";
                        @mssql_free_result( $cat_result );
                        return get_nav( $cat['cat_sub_id'], $nav );
                    }
                    @mssql_free_result( $cat_result );
                    return "";
                }
                if ( is_array( $nav ) )
                {
                    $nav = array_reverse( $nav );
                    $list_cats = "";
                    foreach ( $nav as $rev )
                    {
                        $list_cats .= $rev;
                    }
                    return $list_cats;
                }
            }
            $config['shop_order_by'] = isset( $config['shop_order_by'] ) ? $config['shop_order_by'] : "item_name";
            $order = $order == "" ? $config['shop_order_by'] : $order;
            $config['shop_sort'] = isset( $config['shop_sort'] ) ? $config['shop_sort'] : "ASC";
            $short = $sort == "" ? $config['shop_sort'] : $sort;
            connectuserdb( );
            if ( empty( $page ) )
            {
                $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">".number_format( $userdata['points'], 2 )."</span> Game Points</p>"."\n";
                connectgamecpdb( );
                $cat_sql = "SELECT cat_id, cat_sub_id, cat_name, cat_description FROM gamecp_shop_categories WHERE cat_sub_id = '".$cat_id."' ORDER BY cat_order, cat_name, cat_id DESC";
                if ( !( $cat_result = mssql_query( $cat_sql ) ) )
                {
                    $exit_cat = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain category information</p>";
                }
                if ( $exit_cat == 0 )
                {
                    $cat = array( );
                    while ( $row = mssql_fetch_array( $cat_result ) )
                    {
                        $cat[] = $row;
                    }
                    $total_categories = count( $cat );
                    if ( $cat_id == "" )
                    {
                        $cat_id = 0;
                    }
                    if ( $cat_id != "0" )
                    {
                        $by_category = " AND item_cat_id = '".$cat_id."'";
                    }
                    else
                    {
                        $by_category = "";
                    }
                    include( "./includes/pagination/ps_pagination.php" );
                    $query_p1 = "SELECT item_id,item_name,item_dbcode,item_image_url,item_amount,item_upgrade,item_description,item_price,item_buy_count,item_date_added,item_date_updated,item_race FROM gamecp_shop_items WHERE item_delete = 0 AND item_status = 1 {$by_category}";
                    $query_p2 = " AND item_id NOT IN ( SELECT TOP [OFFSET] item_id FROM gamecp_shop_items WHERE item_delete = 0 AND item_status = 1 {$by_category} ORDER BY {$order} {$sort}) ORDER BY {$order} {$sort}";
                    $page_gen = isset( $_REQUEST['page_gen'] ) ? $_REQUEST['page_gen'] : "0";
                    $url = str_replace( "&page_gen=".$page_gen, "", $_SERVER['REQUEST_URI'] );
                    $pager = new PS_Pagination( $gamecp_dbconnect, $query_p1, $query_p2, 20, 10, $url );
                    $rs = $pager->paginate( );
                    $nav = get_nav( $cat_id, $nav = "" );
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">"."\n";
                    $out .= "\t<tr>"."\n";
                    $out .= "\t\t<td class=\"thead\" colspan=\"2\" style=\"font-size: 12px;\"><a href=\"".$script_name."?do=".$_GET['do']."\" style=\"text-decoration: none;\">Categories</a>".$nav."</td>"."\n";
                    $out .= "\t</tr>"."\n";
                    if ( 0 < $total_categories )
                    {
                    }
                    $i = 0;
                    while ( $i < $total_categories )
                    {
                        $sub_sql = "SELECT cat_id, cat_sub_id, cat_name FROM gamecp_shop_categories WHERE cat_sub_id = '".$cat[$i]['cat_id']."' ORDER BY cat_name DESC";
                        if ( !( $sub_result = mssql_query( $sub_sql, $gamecp_dbconnect ) ) )
                        {
                        }
                        else
                        {
                            while ( $sub = mssql_fetch_array( $sub_result ) )
                            {
                                if ( $sub['cat_sub_id'] == $cat[$i]['cat_id'] )
                                {
                                    if ( $k != 0 )
                                    {
                                        $sub_name .= ", ";
                                    }
                                    $sub_name .= "<a href=\"".$script_name."?do=".$_GET['do']."&cat_id=".$sub['cat_id']."\" style=\"text-decoration: none;\">".$sub['cat_name']."</a>";
                                    ++$k;
                                }
                            }
                        }
                        if ( $j % 2 == 0 )
                        {
                            $out .= "\t<tr>"."\n";
                            $out .= "\t\t<td width=\"50%\" valign=\"top\" class=\"alt1\">&#187; <a href=\"".$script_name."?do=".$_GET['do']."&cat_id=".$cat[$i]['cat_id']."\" style=\"font-size: 13px; text-decoration: none; font-weight: bold;\">".$cat[$i]['cat_name']."</a><br/> ".$sub_name."</td>"."\n";
                        }
                        else
                        {
                            $out .= "\t\t<td width=\"50%\" valign=\"top\" class=\"alt1\">&#187; <a href=\"".$script_name."?do=".$_GET['do']."&cat_id=".$cat[$i]['cat_id']."\" style=\"font-size: 13px; text-decoration: none; font-weight: bold;\">".$cat[$i]['cat_name']."</a><br/> ".$sub_name."</td>"."\n";
                            $out .= "\t</tr>"."\n";
                        }
                        ++$j;
                        $sub_name = "";
                        $k = 0;
                        ++$i;
                    }
                    if ( 0 < $total_categories && $j % 2 )
                    {
                        $out .= "<td class=\"alt1\"></td>"."\n";
                        $out .= "\t</tr>"."\n";
                    }
                    $out .= "</table>"."\n";
                    $order_array = array( "", "Name", "Price", "Race", "Popularity", "Date Added", "Date Updated", "Item Group" );
                    $sort_array = array( "", "Ascending", "Descending" );
                    $out .= "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">"."\n";
                    $out .= "\t<tr>"."\n";
                    $out .= "\t\t<td style=\"text-align: right;\">"."\n";
                    $out .= "\t\t\t<form method=\"post\">"."\n";
                    $out .= "\t\t\tOrder By: <select name=\"order\">"."\n";
                    $f = 1;
                    while ( $f < count( $order_array ) )
                    {
                        if ( $order_raw == $f )
                        {
                            $selected = " selected=\"selected\"";
                        }
                        else
                        {
                            $selected = "";
                        }
                        $out .= "\t\t\t\t\t\t<option value=\"".$f."\"".$selected.">".$order_array[$f]."</option>"."\n";
                        ++$f;
                    }
                    $out .= "\t\t\t\t\t  </select> "."\n";
                    $out .= "\t\t\t\t\t  <select name=\"sort\">"."\n";
                    $s = 1;
                    while ( $s < count( $sort_array ) )
                    {
                        if ( $sort_raw == $s )
                        {
                            $selected = " selected=\"selected\"";
                        }
                        else
                        {
                            $selected = "";
                        }
                        $out .= "\t\t\t\t\t\t<option value=\"".$s."\"".$selected.">".$sort_array[$s]."</option>"."\n";
                        ++$s;
                    }
                    $out .= "\t\t\t\t\t  </select> "."\n";
                    $out .= "\t\t\t<input type=\"submit\" name=\"Change\" value=\"Change\"/>"."\n";
                    $out .= "\t\t\t</form>"."\n";
                    $out .= "\t\t</td>"."\n";
                    $out .= "\t</tr>"."\n";
                    $out .= "</table>"."\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">"."\n";
                    $out .= "\t<tr>"."\n";
                    $out .= "\t\t<td class=\"thead\">Item List</td>"."\n";
                    $out .= "\t</tr>"."\n";
                    connectitemsdb( );
                    while ( $item = mssql_fetch_array( $rs ) )
                    {
                        $u_value = $item['item_upgrade'];
                        $item_slots = $u_value;
                        $item_slots = $item_slots - $base_code;
                        $item_slots = $item_slots / ( $base_code + 1 );
                        $upgrades = "";
                        $ceil_slots = ceil( $item_slots );
                        $slots_code = $base_code + ( $base_code + 1 ) * $ceil_slots;
                        $slots = $ceil_slots;
                        if ( 0 < $ceil_slots )
                        {
                            $u_value = dechex( $u_value );
                            $item_ups = $u_value[0];
                            $slots = 0;
                            $u_value = strrev( $u_value );
                            $m = 0;
                            while ( $m < $item_ups )
                            {
                                $talic_id = hexdec( $u_value[$m] );
                                $upgrades .= "<img src=\"./includes/images/talics/t-".sprintf( "%02d", $talic_id ).".png\" width=\"12\"/>";
                                ++$m;
                            }
                            $bgc = " style=\"background-color: #10171f;\"";
                        }
                        else
                        {
                            $upgrades = "";
                            $bgc = "";
                        }
                        if ( $today < $item['item_date_added'] + 172800 )
                        {
                            $new = " <span style=\"color: red;\">*NEW*</span>";
                        }
                        else
                        {
                            $new = "";
                        }
                        $item_price = $item['item_price'];
                        $item_final_price = $item_price;
                        if ( $item['item_race'] == 1 )
                        {
                            $race = "<span style=\"color: #CC6699;\">Bellato</span>";
                        }
                        else if ( $item['item_race'] == 2 )
                        {
                            $race = "<span style=\"color: #9933CC;\">Cora</span>";
                        }
                        else if ( $item['item_race'] == 3 )
                        {
                            $race = "<span style=\"color: grey;\">Accreatian</span>";
                        }
                        else if ( $item['item_race'] == 4 )
                        {
                            $race = "<span style=\"color: #CC6699;\">Bellato</span> & <span style=\"color: #9933CC;\">Cora</span>";
                        }
                        else
                        {
                            $race = "All Races";
                        }
                        if ( $userdata['points'] < $item_final_price )
                        {
                            $disable_button = " DISABLED";
                        }
                        else
                        {
                            $disable_button = "";
                        }
                        if ( $item['item_image_url'] != " " )
                        {
                            $item_image = $item['item_image_url'];
                        }
                        else
                        {
                            $k_value = $item['item_dbcode'];
                            $slot = 0;
                            $kn = 0;
                            $n = 9;
                            while ( $n < $item_tbl_num )
                            {
                                $item_id = ( $k_value - $n * ( 256 + $slot ) ) / 65536;
                                if ( $item_id == $k_value )
                                {
                                    $kn = $n;
                                }
                                ++$n;
                            }
                            $item_id = ceil( $item_id );
                            $kn = floor( ( $k_value - $item_id * 65536 ) / 256 );
                            $items_query = mssql_query( "SELECT item_code FROM ".GetItemTableName( $kn )." WHERE item_id = '{$item_id}'", $items_dbconnect );
                            $items = mssql_fetch_array( $items_query );
                            $item_code = $items['item_code'];
                            $image_path = glob( "./includes/images/items/{$item_code}{.jpg,.JPG,.gif,.GIF,.png,.PNG}", GLOB_BRACE );
                            if ( file_exists( $image_path[0] ) )
                            {
                                $item_image = $image_path[0];
                            }
                            else
                            {
                                $item_image = "./includes/images/items/unknown.gif";
                            }
                            @mssql_free_result( $items_query );
                        }
                        $out .= "\t\t<tr>"."\n";
                        $out .= ( "\t\t\t<td class=\"alt1\" colspan=\"".( $total_categories + 1 ) )."\">"."\n";
                        $out .= "\t\t\t\t<div class=\"panel\">"."\n";
                        $out .= "\t\t\t\t<form method=\"post\">"."\n";
                        $out .= "\t\t\t\t<table width=\"100%\" cellpadding=\"6\" cellspacing=\"1\">"."\n";
                        $out .= "\t\t\t\t\t<tr>"."\n";
                        $out .= "\t\t\t\t\t\t<td width=\"1\" valign=\"top\" nowrap><img src=\"".$item_image."\"/></td>"."\n";
                        $out .= "\t\t\t\t\t\t<td valign=\"top\">"."\n";
                        $out .= "\t\t\t\t\t\t<span style=\"font-size: 14px; font-weight: bold;\">".$item['item_name'].$new."</span><br/>"."\n";
                        $out .= "\t\t\t\t\t\t<span style=\"font-size: 13px;\">".$item['item_description']."<br/>"."\n";
                        $out .= "\t\t\t\t\t\tRace: ".$race."</span><br/>"."\n";
                        if ( 0 < $item['item_amount'] )
                        {
                            $out .= "\t\t\t\t\t\tAmount: ".$item['item_amount']."<br/>"."\n";
                        }
                        $out .= "\t\t\t\t\t\tTimes bought: ".number_format( $item['item_buy_count'] )."<br/>"."\n";
                        if ( $upgrades != "" )
                        {
                            $out .= "\t\t\t\t\t\t".$upgrades."<br/>"."\n";
                        }
                        $out .= "\t\t\t\t\t\t</td>"."\n";
                        $out .= "\t\t\t\t\t\t<td width=\"1%\" style=\"text-align: right; font-size: 14px;\" valign=\"top\" nowrap=\"nowrap\">";
                        $out .= "<b>".number_format( $item_final_price, 2, ".", "" )."</b> GP<br/>";
                        $out .= "<br/><input type=\"submit\" name=\"submit\" value=\"Buy now!\"".$disable_button."/></td>"."\n";
                        $out .= "\t\t\t\t\t</tr>"."\n";
                        $out .= "\t\t\t\t</table>"."\n";
                        $out .= "\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"buy\"/><input type=\"hidden\" name=\"item_id\" value=\"".$item['item_id']."\"/>"."\n";
                        $out .= "\t\t\t\t</form>"."\n";
                        $out .= "\t\t\t\t</div>"."\n";
                        $out .= "\t\t\t</td>"."\n";
                        $out .= "\t\t</tr>"."\n";
                    }
                    if ( mssql_num_rows( $rs ) <= 0 )
                    {
                        $out .= "\t\t<tr>"."\n";
                        $out .= ( "\t\t\t<td class=\"alt1\" colspan=\"".( $total_categories + 1 ) )."\" style=\"text-align: center; font-weight: bold;\">No items were found in this category</td>"."\n";
                        $out .= "\t\t</tr>"."\n";
                    }
                    else
                    {
                        $out .= "\t\t<tr>"."\n";
                        $out .= ( "\t\t\t<td class=\"alt2\" colspan=\"".( $total_categories + 1 ) )."\" style=\"text-align: center; font-weight: bold;\">".$pager->renderFullNav( )."</td>"."\n";
                        $out .= "\t\t</tr>"."\n";
                    }
                    $out .= "</table>"."\n";
                    @mssql_free_result( $rs );
                }
                mssql_free_result( $cat_result );
            }
            else if ( $page == "buy" )
            {
                $exit_buy = 0;
                $item_id = isset( $_POST['item_id'] ) && intval( $_POST['item_id'] ) ? $_POST['item_id'] : "";
                if ( $item_id == "" )
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid Item ID provided</p>"."\n";
                }
                $t_login = strtotime( $userdata['lastlogintime'] );
                $t_logout = strtotime( $userdata['lastlogofftime'] );
                $t_cur = time( );
                $t_maxlogin = $t_login + 3600;
                if ( $t_login <= $t_logout )
                {
                    $status = "offline";
                }
                else
                {
                    $status = "online";
                }
                if ( $status == "online" )
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot buy items when logged into the game!<br/>If you have logged out and yet see this message, log back in and properly log out again (click the log out button!).</p>"."\n";
                }
                if ( $exit_buy != 1 )
                {
                    connectgamecpdb( );
                    $item_sql = "SELECT item_name, item_amount, item_custom_amount, item_upgrade, item_dbcode, item_price, item_race FROM gamecp_shop_items WHERE  item_delete = 0 AND item_status = 1 AND item_id = '{$item_id}'";
                    if ( !( $item_result = mssql_query( $item_sql ) ) )
                    {
                        $exit_buy = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain item information</p>";
                    }
                    if ( !( $item = mssql_fetch_array( $item_result ) ) )
                    {
                        $exit_buy = 1;
                        $out .= $lang['no_such_item'];
                    }
                    else
                    {
                        $item_name = $item['item_name'];
                        $item_price = $item['item_price'];
                        $item_dbcode = $item['item_dbcode'];
                        $item_amount = $item['item_amount'] < 1 ? 1 : $item['item_amount'];
                        $item_custom_amount = $item['item_custom_amount'];
                        $item_upgrade = $item['item_upgrade'];
                        $item_race = $item['item_race'];
                        $item_final_price = $item_price;
                        $item_price = $item_final_price;
                        if ( $item_name == "" || $item_price < 0 || $item_dbcode == "" || $item_upgrade == "" )
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid data supplied by the database, contact the admin.</p>";
                        }
                        if ( $userdata['points'] < $item_price )
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have enough of game points to purchase this item.</p>";
                        }
                    }
                    @mssql_free_result( $item_result );
                }
                connectdatadb( );
                $char_sql = "SELECT Serial, Name, Lv, Race FROM tbl_base WHERE DCK = 0 AND AccountSerial = '".$userdata['serial']."'";
                if ( !( $char_result = mssql_query( $char_sql ) ) )
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to character information</p>";
                }
                while ( $row = mssql_fetch_array( $char_result ) )
                {
                    $chars[] = $row;
                }
                @mssql_free_result( $char_result );
                if ( !( $num_chars = @count( $chars ) ) )
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have any characters on your account</p>";
                }
                if ( $exit_buy == 0 )
                {
                    $lefttitle .= " - Buying an Item";
                    if ( $item['item_race'] == 1 )
                    {
                        $race = "<span style=\"color: #CC6699;\">Bellato</span>";
                    }
                    else if ( $item['item_race'] == 2 )
                    {
                        $race = "<span style=\"color: #9933CC;\">Cora</span>";
                    }
                    else if ( $item['item_race'] == 3 )
                    {
                        $race = "<span style=\"color: grey;\">Accreatian</span>";
                    }
                    else if ( $item['item_race'] == 4 )
                    {
                        $race = "<span style=\"color: #CC6699;\">Bellato</span> & <span style=\"color: #9933CC;\">Cora</span>";
                    }
                    else
                    {
                        $race = "All Races";
                    }
                    $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">".number_format( $userdata['points'], 2 )."</span> Game Points</p>"."\n";
                    $u_value = $item_upgrade;
                    $item_slots = $u_value;
                    $item_slots = $item_slots - $base_code;
                    $item_slots = $item_slots / ( $base_code + 1 );
                    $upgrades = "";
                    $ceil_slots = ceil( $item_slots );
                    $slots_code = $base_code + ( $base_code + 1 ) * $ceil_slots;
                    $slots = $ceil_slots;
                    if ( 0 < $ceil_slots )
                    {
                        $u_value = dechex( $u_value );
                        $item_ups = $u_value[0];
                        $slots = 0;
                        $u_value = strrev( $u_value );
                        $m = 0;
                        while ( $m < $item_ups )
                        {
                            $talic_id = hexdec( $u_value[$m] );
                            $upgrades .= "<img src=\"./includes/images/talics/t-".sprintf( "%02d", $talic_id ).".png\" width=\"12\"/>";
                            ++$m;
                        }
                        $bgc = " background-color: #10171f;";
                    }
                    else
                    {
                        $upgrades = "";
                        $bgc = "";
                    }
                    $colspan = 4;
                    if ( $upgrades == "" )
                    {
                        $colspan -= 1;
                    }
                    if ( $item_amount == "" )
                    {
                        $colspan -= 1;
                    }
                    else if ( $ceil_slots <= 0 && $item_custom_amount == 1 )
                    {
                        $item_raw_amount = $item_amount;
                        $single_item_price = ceil( $item_price / $item_raw_amount );
                        $item_amount = "<select name=\"item_amount\" id=\"amount_1\" onChange=\"calculate_amount(1,'".$item_price."','".$item_amount."','".$userdata['points']."');\">"."\n";
                        $p = 1;
                        while ( $p < 100 )
                        {
                            $max_amount = floor( $p * $single_item_price );
                            if ( $userdata['points'] < $max_amount )
                            {
                                continue;
                            }
                            if ( $p == $item_raw_amount )
                            {
                                $select = " selected=\"selected\"";
                            }
                            else
                            {
                                $select = "";
                            }
                            $item_amount .= "\t<option".$select.">".$p."</option>"."\n";
                            ++$p;
                        }
                        $item_amount .= "</select>";
                    }
                    $out .= "<form method=\"post\">"."\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">"."\n";
                    $out .= "\t<tr>"."\n";
                    $out .= "\t\t<td class=\"thead\">Buying the Item</td>"."\n";
                    if ( $upgrades != "" )
                    {
                        $out .= "\t\t<td class=\"thead\">Upgrade</td>"."\n";
                    }
                    if ( $item_amount != "" )
                    {
                        $out .= "\t\t<td class=\"thead\">Amount</td>"."\n";
                    }
                    $out .= "\t\t<td class=\"thead\">Race</td>"."\n";
                    $out .= "\t\t<td class=\"thead\">Price</td>"."\n";
                    $out .= "\t</tr>"."\n";
                    $out .= "\t<tr>"."\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"font-weight: bold;\">".$item_name."</td>"."\n";
                    if ( $upgrades != "" )
                    {
                        $out .= "\t\t<td class=\"alt1\" style=\"".$bgc."\">".$upgrades."</td>"."\n";
                    }
                    if ( $item_amount != "" )
                    {
                        $out .= "\t\t<td class=\"alt1\">".$item_amount."</td>"."\n";
                    }
                    $out .= "\t\t<td class=\"alt1\">".$race."</td>"."\n";
                    $out .= "\t\t<td class=\"alt1\" style=\"font-weight: bold;\" id=\"price_1\">".number_format( $item['item_price'], 2 )." GP</td>"."\n";
                    $out .= "\t</tr>"."\n";
                    $out .= "\t<tr>"."\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"text-align: right;\" colspan=\"".$colspan."\">Total GP after purchase:</td>"."\n";
                    $out .= "\t\t<td class=\"alt2\" style=\"font-weight: bold;\" id=\"gpafter_1\">".number_format( $userdata['points'] - $item_final_price, 2 )." GP</td>"."\n";
                    $out .= "\t</tr>"."\n";
                    $out .= "</table>"."\n";
                    $char_select = "<select name=\"char_serial\">"."\n";
                    foreach ( $chars as $char )
                    {
                        if ( $item_race == 1 && ( $char['Race'] == 0 || $char['Race'] == 1 ) )
                        {
                            $is_char = true;
                            $char_select .= "<option value=\"".$char['Serial']."\">Lv: ".$char['Lv']." - ".$char['Name']."</option>"."\n";
                        }
                        else if ( $item_race == 2 && ( $char['Race'] == 2 || $char['Race'] == 3 ) )
                        {
                            $is_char = true;
                            $char_select .= "<option value=\"".$char['Serial']."\">Lv: ".$char['Lv']." - ".$char['Name']."</option>"."\n";
                        }
                        else if ( $item_race == 3 && $char['Race'] == 4 )
                        {
                            $is_char = true;
                            $char_select .= "<option value=\"".$char['Serial']."\">Lv: ".$char['Lv']." - ".$char['Name']."</option>"."\n";
                        }
                        else if ( $item_race == 4 && ( $char['Race'] == 0 || $char['Race'] == 1 || $char['Race'] == 2 || $char['Race'] == 3 ) )
                        {
                            $is_char = true;
                            $char_select .= "<option value=\"".$char['Serial']."\">Lv: ".$char['Lv']." - ".$char['Name']."</option>"."\n";
                        }
                        else if ( $item_race == 0 )
                        {
                            $is_char = true;
                            $char_select .= "<option value=\"".$char['Serial']."\">Lv: ".$char['Lv']." - ".$char['Name']."</option>"."\n";
                        }
                    }
                    $char_select .= "</select>"."\n";
                    if ( $is_char == false )
                    {
                        $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">"."\n";
                        $out .= "\t<tr>"."\n";
                        $out .= "\t\t<td class=\"alt2\">No characters for the ".$race." race has been found</td>"."\n";
                        $out .= "\t</tr>"."\n";
                        $out .= "</table>";
                        return;
                    }
                    $out .= "<br/>"."\n";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\" align=\"center\">"."\n";
                    $out .= "\t<tr>"."\n";
                    $out .= "\t\t<td class=\"alt2\">Which character are you buying for?</td>"."\n";
                    $out .= "\t\t<td class=\"alt1\" style=\"text-align: right;\">".$char_select."</td>"."\n";
                    $out .= "\t</tr>"."\n";
                    $out .= "\t<tr>"."\n";
                    $out .= "\t\t<td class=\"alt2\" colspan=\"2\" style=\"text-align: right;\"><input type=\"hidden\" name=\"page\" value=\"buy_item\"/><input type=\"hidden\" name=\"item_id\" value=\"".$item_id."\"/><input type=\"submit\" name=\"submit\" value=\"Buy Now!\"/></td>"."\n";
                    $out .= "\t</tr>"."\n";
                    $out .= "</table>";
                    $out .= "</form>"."\n";
                }
            }
            else if ( $page == "buy_item" )
            {
                $exit_buy = 0;
                $empty_slot = "-1";
                $item_id = isset( $_POST['item_id'] ) && intval( $_POST['item_id'] ) ? antiject( $_POST['item_id'] ) : "";
                $item_post_amount = isset( $_POST['item_amount'] ) && intval( $_POST['item_amount'] ) ? antiject( $_POST['item_amount'] ) : "";
                $char_serial = isset( $_POST['char_serial'] ) && intval( $_POST['char_serial'] ) ? antiject( $_POST['char_serial'] ) : "";
                if ( $item_id == "" )
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid Item ID provided</p>"."\n";
                }
                if ( $char_serial == 0 - 1 )
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">No such character found</p>"."\n";
                }
                $t_login = strtotime( $userdata['lastlogintime'] );
                $t_logout = strtotime( $userdata['lastlogofftime'] );
                $t_cur = time( );
                $t_maxlogin = $t_login + 3600;
                if ( $t_login <= $t_logout )
                {
                    $status = "offline";
                }
                else
                {
                    $status = "online";
                }
                if ( $status == "online" )
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot buy items when logged into the game!<br/>If you have logged out and yet see this message, log back in and properly log out again (click the log out button!).</p>"."\n";
                }
                if ( $exit_buy != 1 )
                {
                    connectgamecpdb( );
                    $item_sql = "SELECT item_name, item_amount, item_custom_amount, item_upgrade, item_dbcode, item_price FROM gamecp_shop_items WHERE item_delete = 0 AND item_status = 1 AND item_id = '{$item_id}'";
                    if ( !( $item_result = mssql_query( $item_sql ) ) )
                    {
                        $exit_buy = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain item information</p>";
                    }
                    if ( !( $item = mssql_fetch_array( $item_result ) ) )
                    {
                        $exit_buy = 1;
                        $out .= $lang['no_such_item'];
                    }
                    else
                    {
                        $item_name = $item['item_name'];
                        $item_price = $item['item_price'];
                        $item_dbcode = $item['item_dbcode'];
                        $item_amount = $item['item_amount'] < 1 ? 1 : $item['item_amount'];
                        $item_custom_amount = $item['item_custom_amount'];
                        $item_upgrade = $item['item_upgrade'];
                        $item_final_price = $item_price;
                        $item_price = $item_final_price;
                        if ( $item_name == "" || $item_price < 0 || $item_dbcode == "" || $item_upgrade == "" )
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Invalid data supplied by the database, contact the admin.</p>";
                        }
                        if ( $userdata['points'] < $item_price )
                        {
                            $exit_buy = 1;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have enough of game points to purchase this item.</p>";
                        }
                    }
                    @mssql_free_result( $item_result );
                }
                if ( isset( $item_amount ) && $item_amount != "" && $item_upgrade == $base_code && $item_custom_amount == 1 && $item_post_amount != "" )
                {
                    $single_price = ceil( $item_price / $item_amount );
                    $item_price = ceil( $single_price * $item_post_amount );
                    $item_amount = $item_post_amount;
                    if ( 99 < $item_post_amount || $item_post_amount < 1 )
                    {
                        $exit_buy = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">You have supplied an invalid amount.</p>";
                    }
                    if ( $userdata['points'] < $item_price )
                    {
                        $exit_buy = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have enough of game points to make this purchase.</p>";
                    }
                }
                if ( $exit_buy == 0 )
                {
                    $bags = "";
                    $i = 0;
                    while ( $i < 100 )
                    {
                        if ( $i != 0 )
                        {
                            $bags .= ",";
                        }
                        $bags .= "I.K{$i}";
                        ++$i;
                    }
                    connectdatadb( );
                    $inven_select = "SELECT {$bags} FROM\r\n\t\t\t\ttbl_inven AS I\r\n\t\t\t\t\tINNER JOIN\r\n\t\t\t\ttbl_base AS B\r\n\t\t\t\t\tON B.Serial = I.Serial\r\n\t\t\t\tWHERE\r\n\t\t\t\t\tI.Serial = '{$char_serial}'\r\n\t\t\t\tAND\r\n\t\t\t\t\tB.AccountSerial = '".$userdata['serial']."'";
                    if ( !( $inven_result = mssql_query( $inven_select ) ) )
                    {
                        $exit_buy = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to obtain inventory information</p>";
                    }
                    $inven = mssql_fetch_array( $inven_result );
                    $i = 0;
                    while ( $i < 100 )
                    {
                        if ( $inven["K{$i}"] == "-1" )
                        {
                            $empty_slot = $i;
                            break;
                        }
                        ++$i;
                    }
                    if ( mssql_num_rows( $inven_result ) <= 0 )
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">No such character found</p>";
                    }
                    else
                    {
                        if ( $empty_slot == "-1" )
                        {
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">No empty slots found in your characters inventory</p>";
                        }
                        else
                        {
                            if ( $item_upgrade == "" )
                            {
                                $item_upgrade = $base_code;
                            }
                            if ( $item_amount == "" )
                            {
                                $item_amount = 0;
                            }
                            $update_inven = "INSERT tbl_ItemCharge ( nAvatorSerial, nItemCode_K, nItemCode_D, nItemCode_U )\r\n\t\t\t\t\t\t\tVALUES( '{$char_serial}', '{$item_dbcode}', '{$item_amount}', '{$item_upgrade}')";
                            if ( !( $inven_result = mssql_query( $update_inven ) ) )
                            {
                                $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to update your characters inventory.</p>";
                                gamecp_log( 1, $userdata['username'], "GAMECP - ITEM SHOP - Failed to update inventory", 1 );
                            }
                            else
                            {
                                $time = time( );
                                $total_gp = $userdata['points'] - $item_price;
                                connectgamecpdb( );
                                $redeem_insert = "INSERT INTO gamecp_redeem_log (redeem_account_id,redeem_char_id,redeem_price,redeem_item_id,redeem_item_name,redeem_item_amount,redeem_item_dbcode,redeem_total_gp,redeem_time) VALUES ('".antiject( $userdata['serial'] )."', '".antiject( $char_serial )."', '".antiject( $item_price )."', '".antiject( $item_id )."', '".antiject( $item_name )."', '".antiject( $item_amount )."', '".antiject( $item_dbcode )."', '".$total_gp."', '".$time."')";
                                if ( !( $redeem_result = mssql_query( $redeem_insert ) ) )
                                {
                                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to insert a redeem log.</p>";
                                    gamecp_log( 1, $userdata['username'], "GAMECP - ITEM SHOP - Failed to insert a redeem log", 1 );
                                }
                                else
                                {
                                    $update_gp = "UPDATE gamecp_gamepoints SET user_points = user_points-{$item_price} WHERE user_account_id = '".$userdata['serial']."'";
                                    if ( !( $updategp_result = mssql_query( $update_gp ) ) )
                                    {
                                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error trying to upate your game points.</p>";
                                        gamecp_log( 1, $userdata['username'], "GAMECP - ITEM SHOP - Failed to update Game Points: -{$item_price}", 1 );
                                    }
                                    else
                                    {
                                        $update_buycount = "UPDATE gamecp_shop_items SET item_buy_count = item_buy_count+1 WHERE item_id = '".$item_id."'";
                                        $update_buycount_result = @mssql_query( $update_buycount );
                                        $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">".number_format( $total_gp, 2 )."</span> Game Points</p>"."\n";
                                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully purchased the item: ".$item_name."</p>";
                                    }
                                }
                            }
                            @mssql_free_result( $inven_result );
                        }
                    }
                }
            }
            else if ( $page == "ban" )
            {
                $out .= "<p style=\"text-align: center; font-weight: bold;\">You have a blocked account, as a prevention method we have blocked all of you're accounts from making item purchases. Please contact an Administrator to get this resolved.</p>";
            }
            else
            {
                $out .= $lang['invalid_page_id'];
            }
        }
        else
        {
            $out .= $lang['no_permission'];
        }
    }
    else
    {
        $out .= $lang['invalid_page_load'];
    }
}
?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              