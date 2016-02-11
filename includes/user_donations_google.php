<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Donations"]["Purchase GP (Google)"] = $file;
}
else
{
    $lefttitle = "Purchase Game Points (Google)";
    if( $this_script == $script_name ) 
    {
        $exit_stage = 0;
        if( isset($isuser) && $isuser == true ) 
        {
            require_once("./includes/google_library/googlecart.php");
            require_once("./includes/google_library/googleitem.php");
            require_once("./includes/google_library/googleshipping.php");
            require_once("./includes/google_library/googletax.php");
            $merchant_id = $config["google_merchant_id"];
            $merchant_key = $config["google_merchant_key"];
            $server_type = $config["google_server_type"];
            $currency = $config["google_currency"];
            $tottime = time() - $userdata["createtime"];
            if( $tottime <= 604800 || $userdata["createtime"] == "" ) 
            {
            }

            $username = isset($_POST["username"]) ? $_POST["username"] : "";
            connectuserdb();
            if( $username == "" ) 
            {
                $custom = $userdata["serial"];
            }
            else
            {
                if( eregi("[^a-zA-Z0-9_-]", $username) ) 
                {
                    $custom = $userdata["serial"];
                }
                else
                {
                    $username = ereg_replace("" . ";\$", "", $username);
                    $username = ereg_replace("\\\\", "", $username);
                    $username = antiject($username);
                    $select_query = "" . "SELECT Serial, convert(varchar,id) AS Name FROM tbl_UserAccount WHERE id = convert(binary,'" . $username . "')";
                    if( !($result = mssql_query($select_query)) ) 
                    {
                        $exit_stage = 1;
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to query the database</p>";
                    }

                    if( $exit_stage == 0 ) 
                    {
                        $data = mssql_fetch_array($result);
                        $username = antiject($data["Name"]);
                        $custom = $data["Serial"];
                        if( $custom == "" ) 
                        {
                            $username = "[USER NOT FOUND]";
                            $custom = $userdata["serial"];
                        }

                    }

                    @mssql_free_result($result);
                }

            }

            $custom = antiject($custom);
            if( $exit_stage == 0 ) 
            {
                $out .= "<center>" . "\n";
                $out .= "<h2>Your account currently has <span style=\"color: #8F92E8;\">" . number_format($userdata["points"]) . "</span> Game Points</h2><br/>" . "\n";
                $out .= "<form method=\"post\" action=\"" . $script_name . "?do=" . $_GET["do"] . "\">" . "\n";
                $out .= "\tIf your purchasing Item Mall Credits for a friend, enter their <b><u>username</u></b> below.<br /><br />" . "\n";
                $out .= "\t<input type=\"text\" name=\"username\"> <input type=\"submit\" class=\"submit\" value=\"Change\">" . "\n";
                $out .= "</form>" . "\n";
                $out .= "</center>" . "\n";
                $out .= "<br>" . "\n";
                calculate_credits($config["donations_credit_muntiplier"], $config["donations_number_of_pay_options"], $config["donations_start_price"], $config["donations_start_credits"], false);
                $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"60%\" align=\"center\">" . "\n";
                if( $username != "" ) 
                {
                    $out .= "\t\t<tr>" . "\n";
                    $out .= "\t\t\t<td colspan=\"5\" style=\"text-align: center; font-size: 15px;\">You are purchasing these credits for <b>" . $username . "</b></td>" . "\n";
                    $out .= "\t\t</tr>" . "\n";
                }

                $out .= "\t\t<tr>" . "\n";
                $out .= "\t\t\t<td class=\"thead\">Price</td>" . "\n";
                $out .= "\t\t\t<td class=\"thead\">Credits</td>" . "\n";
                $out .= "\t\t\t<td class=\"thead\">Bonus</td>" . "\n";
                $out .= "\t\t\t<td class=\"thead\" style=\"text-align: center;\">Total</td>" . "\n";
                $out .= "\t\t\t<td class=\"thead\" style=\"text-align: center;\">Buy Now!</td>" . "\n";
                $out .= "\t\t</tr>" . "\n";
                for( $i = 1; $i < count($c_price); $i++ ) 
                {
                    $cart = new GoogleCart($merchant_id, $merchant_key, $server_type, $currency);
                    $item = new GoogleItem($c_total[$i] . " Game Points", $c_total[$i] . " Game Points for the Item Shop", 1, $c_price[$i]);
                    $item->SetMerchantPrivateItemData($custom);
                    $item->SetEmailDigitalDelivery("true");
                    $cart->AddItem($item);
                    $bgcolor = $i % 2 ? "alt1" : "alt2";
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td class=\"" . $bgcolor . "\" style=\"text-align: center;\">\$" . number_format($c_price[$i], 2, ".", "") . "</td>" . "\n";
                    $out .= "\t\t<td class=\"" . $bgcolor . "\" style=\"text-align: center;\">" . number_format($c_credits[$i]) . "</td>" . "\n";
                    $out .= "\t\t<td class=\"" . $bgcolor . "\" style=\"text-align: center;\">" . number_format($c_bonus[$i]) . "</td>" . "\n";
                    $out .= "\t\t<td class=\"" . $bgcolor . "\" style=\"text-align: center;\"><b>" . number_format($c_total[$i]) . "</td>" . "\n";
                    $out .= "\t\t<td class=\"" . $bgcolor . "\" style=\"text-align: center;\">" . $cart->CheckoutButtonNowCode("small", true, "en_US", false, "trans") . "</td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }
                $out .= "\t</table>";
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


