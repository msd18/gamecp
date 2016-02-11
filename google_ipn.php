<?php 
define("IN_GAMECP_SALT58585", true);
include("./gamecp_common.php");
require_once("./includes/google_library/googleresponse.php");
require_once("./includes/google_library/googlemerchantcalculations.php");
require_once("./includes/google_library/googleresult.php");
require_once("./includes/google_library/googlerequest.php");
define("RESPONSE_HANDLER_ERROR_LOG_FILE", "googleerror.log");
define("RESPONSE_HANDLER_LOG_FILE", "googlemessage.log");
if( !isset($config["google_merchant_id"]) && !isset($config["google_merchant_key"]) && !isset($config["google_server_type"]) && !isset($config["google_currency"]) ) 
{
    exit( "Missing configuration info" );
}

$merchant_id = $config["google_merchant_id"];
$merchant_key = $config["google_merchant_key"];
$server_type = $config["google_server_type"];
$currency = $config["google_currency"];
$Gresponse = new GoogleResponse($merchant_id, $merchant_key);
$Grequest = new GoogleRequest($merchant_id, $merchant_key, $server_type, $currency);
$Gresponse->SetLogFiles(RESPONSE_HANDLER_ERROR_LOG_FILE, RESPONSE_HANDLER_LOG_FILE, L_ALL);
$xml_response = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");
if( get_magic_quotes_gpc() ) 
{
    $xml_response = stripslashes($xml_response);
}

list($root, $data) = $Gresponse->GetParsedXML($xml_response);
$Gresponse->SetMerchantAuthentication($merchant_id, $merchant_key);
$status = $Gresponse->HttpAuthentication();
if( !$status ) 
{
    gamecp_log(5, "Google", "GOOGLE - Authentication Failure");
    exit( "authentication failed" );
}

switch( $root ) 
{
    case "request-received":
        break;
    case "error":
        break;
    case "diagnosis":
        break;
    case "checkout-redirect":
        break;
    case "merchant-calculation-callback":
        $merchant_calc = new GoogleMerchantCalculations($currency);
        $addresses = get_arr_result($data[$root]["calculate"]["addresses"]["anonymous-address"]);
        foreach( $addresses as $curr_address ) 
        {
            $curr_id = $curr_address["id"];
            $country = $curr_address["country-code"]["VALUE"];
            $city = $curr_address["city"]["VALUE"];
            $region = $curr_address["region"]["VALUE"];
            $postal_code = $curr_address["postal-code"]["VALUE"];
            if( isset($data[$root]["calculate"]["shipping"]) ) 
            {
                $shipping = get_arr_result($data[$root]["calculate"]["shipping"]["method"]);
                foreach( $shipping as $curr_ship ) 
                {
                    $name = $curr_ship["name"];
                    $price = 12;
                    $shippable = "true";
                    $merchant_result = new GoogleResult($curr_id);
                    $merchant_result->SetShippingDetails($name, $price, $shippable);
                    if( $data[$root]["calculate"]["tax"]["VALUE"] == "true" ) 
                    {
                        $amount = 15;
                        $merchant_result->SetTaxDetails($amount);
                    }

                    if( isset($data[$root]["calculate"]["merchant-code-strings"]["merchant-code-string"]) ) 
                    {
                        $codes = get_arr_result($data[$root]["calculate"]["merchant-code-strings"]["merchant-code-string"]);
                        foreach( $codes as $curr_code ) 
                        {
                            $coupons = new GoogleCoupons("true", $curr_code["code"], 5, "test2");
                            $merchant_result->AddCoupons($coupons);
                        }
                    }

                    $merchant_calc->AddResult($merchant_result);
                }
            }
            else
            {
                $merchant_result = new GoogleResult($curr_id);
                if( $data[$root]["calculate"]["tax"]["VALUE"] == "true" ) 
                {
                    $amount = 15;
                    $merchant_result->SetTaxDetails($amount);
                }

                $codes = get_arr_result($data[$root]["calculate"]["merchant-code-strings"]["merchant-code-string"]);
                foreach( $codes as $curr_code ) 
                {
                    $coupons = new GoogleCoupons("true", $curr_code["code"], 5, "test2");
                    $merchant_result->AddCoupons($coupons);
                }
                $merchant_calc->AddResult($merchant_result);
            }

        }
        $Gresponse->ProcessMerchantCalculations($merchant_calc);
        break;
    case "new-order-notification":
        $credits = @calculate_credits($config["donations_credit_muntiplier"], $config["donations_number_of_pay_options"], $config["donations_start_price"], $config["donations_start_credits"], @round($data[$root]["order-total"]["VALUE"], 2));
        $orderNumber = $data[$root]["google-order-number"]["VALUE"];
        $buyerId = $data[$root]["buyer-id"]["VALUE"];
        $userId = $data[$root]["shopping-cart"]["items"]["item"]["merchant-private-item-data"]["VALUE"];
        $priceTotal = number_format($data[$root]["order-total"]["VALUE"], 2, ".", "");
        $userPoints = $credits;
        $buyerEmail = $data[$root]["buyer-billing-address"]["email"]["VALUE"];
        $buyerName = $data[$root]["buyer-billing-address"]["contact-name"]["VALUE"];
        $buyerAddress = $data[$root]["buyer-billing-address"]["address1"]["VALUE"];
        $postalCode = $data[$root]["buyer-billing-address"]["postal-code"]["VALUE"];
        connectuserdb();
        $user_info_sql = "SELECT convert(varchar,id) AS AccountName FROM tbl_UserAccount WHERE Serial = '" . $userId . "'";
        if( !($user_info_result = mssql_query($user_info_sql)) ) 
        {
            gamecp_log(0, $custom, "GOOGLE - ERROR - Unable to find or query this user id");
        }

        $user = mssql_fetch_array($user_info_result);
        if( $user["AccountName"] != "" ) 
        {
            $user_name = antiject($user["AccountName"]);
        }
        else
        {
            $user_name = $userId;
        }

        connectgamecpdb();
        $orderid_query = mssql_query("SELECT google_order_id FROM gamecp_google_payments WHERE google_order_id=\"" . $orderId . "\"");
        if( mssql_num_rows($orderid_query) <= 0 ) 
        {
            $google_query = "INSERT INTO gamecp_google_payments (google_order_id, google_order_price, google_order_points, google_buyer_id, google_buyer_email, google_account_serial, google_buyer_name, google_buyer_address, google_buyer_postal_code, google_order_state, google_time) VALUES ('" . $orderNumber . "', '" . $priceTotal . "', '" . $userPoints . "', '" . $buyerId . "', '" . antiject($buyerEmail) . "', '" . antiject($userId) . "', '" . antiject($buyerName) . "', '" . antiject($buyerAddress) . "', '" . antiject($postalCode) . "', '1', '" . time() . "')";
            mssql_query($google_query);
            gamecp_log(0, $user_name, "" . "GOOGLE - NEW ORDER - ID: " . $orderNumber . " | E-Mail: " . $buyerEmail . " | Price: \$" . $priceTotal, 1);
        }
        else
        {
            gamecp_log(0, $user_name, "" . "GOOGLE - DUPLICATE ORDER - ID: " . $orderNumber . " | E-Mail: " . $buyerEmail . " | Price: \$" . $priceTotal, 1);
        }

        $Gresponse->SendAck();
        break;
    case "authorization-amount-notification":
        $fp = fopen("test.txt", "a");
        fwrite($fp, "authorization-amount-notification" . "\n");
        fclose($fp);
        break;
    case "order-state-change-notification":
        $new_financial_state = $data[$root]["new-financial-order-state"]["VALUE"];
        $new_fulfillment_order = $data[$root]["new-fulfillment-order-state"]["VALUE"];
        $orderId = $data[$root]["google-order-number"]["VALUE"];
        connectgamecpdb();
        $select_order = mssql_query("" . "SELECT google_order_price, google_order_points, google_order_id, google_account_serial, google_buyer_email FROM gamecp_google_payments WHERE google_order_id = '" . $orderId . "'");
        if( 1 <= mssql_num_rows($select_order) ) 
        {
            $google = @mssql_fetch_array($select_order);
            $Email = $google["google_buyer_email"];
            connectuserdb();
            $user_info_sql = "SELECT convert(varchar,id) AS AccountName FROM tbl_UserAccount WHERE Serial = '" . $google["google_account_serial"] . "'";
            if( !($user_info_result = mssql_query($user_info_sql)) ) 
            {
                gamecp_log(0, $custom, "GOOGLE - ERROR - Unable to find or query this user id [CHARGE]");
            }

            $user = mssql_fetch_array($user_info_result);
            if( $user["AccountName"] != "" ) 
            {
                $user_name = antiject($user["AccountName"]);
            }
            else
            {
                $user_name = $google["google_account_serial"];
            }

            switch( $new_financial_state ) 
            {
                case "REVIEWING":
                    break;
                case "CHARGEABLE":
                    break;
                case "CHARGING":
                    break;
                case "CHARGED":
                    break;
                case "PAYMENT_DECLINED":
                    gamecp_log(3, $user_name, "" . "GOOGLE - PAYMENT DECLINED - ID: " . $orderId . " | EMail: " . $Email);
                    break;
                case "CANCELLED":
                    gamecp_log(3, $user_name, "" . "GOOGLE - CANCELLED - ID: " . $orderId . " | EMail: " . $Email);
                    break;
                case "CANCELLED_BY_GOOGLE":
                    $Grequest->SendBuyerMessage($data[$root]["google-order-number"]["VALUE"], "Sorry, your order is cancelled by Google", true);
                    gamecp_log(3, $user_name, "" . "GOOGLE - CANCELLED BY GOOGLE - ID: " . $orderId);
                    break;
                default:
                    break;
            }
        }
        else
        {
            connectgamecpdb();
            gamecp_log(4, "Google", "" . "GOOGLE - ORDER ERROR - ID: " . $orderId . " | Cannot find order in database!");
            $Gresponse->SendAck();
        }

    case "charge-amount-notification":
        $Gresponse->SendAck();
        break;
    case "chargeback-amount-notification":
        $orderId = $data[$root]["google-order-number"]["VALUE"];
        $amount = $data[$root]["total-chargeback-amount"]["VALUE"];
        connectgamecpdb();
        $select_order = mssql_query("" . "SELECT google_order_price, google_order_points, google_order_id, google_account_serial, google_buyer_email FROM gamecp_google_payments WHERE google_order_id = '" . $orderId . "'");
        if( 1 <= mssql_num_rows($select_order) ) 
        {
            $google = @mssql_fetch_array($select_order);
            connectuserdb();
            $user_info_sql = "SELECT convert(varchar,id) AS AccountName FROM tbl_UserAccount WHERE Serial = '" . $google["google_account_serial"] . "'";
            if( !($user_info_result = mssql_query($user_info_sql)) ) 
            {
                gamecp_log(0, $custom, "GOOGLE - ERROR - Unable to find or query this user id [CHARGE]");
            }

            $user = mssql_fetch_array($user_info_result);
            if( $user["AccountName"] != "" ) 
            {
                $user_name = antiject($user["AccountName"]);
            }
            else
            {
                $user_name = $google["google_account_serial"];
            }

            $EMail = $google["google_buyer_email"];
            gamecp_log(5, $user_name, "" . "GOOGLE - CHARGEBACK - ID: " . $orderId . " | EMail: " . $EMail . " | Amount: " . $amount);
        }
        else
        {
            connectgamecpdb();
            gamecp_log(5, "Google", "" . "GOOGLE - ORDER CHARGEBACK ERROR - ID: " . $orderId . " | Cannot find order in database!");
        }

        $Gresponse->SendAck();
        break;
    case "refund-amount-notification":
        $orderId = $data[$root]["google-order-number"]["VALUE"];
        $amount = $data[$root]["total-chargeback-amount"]["VALUE"];
        connectgamecpdb();
        $select_order = mssql_query("" . "SELECT google_order_price, google_order_points, google_order_id, google_account_serial, google_buyer_email FROM gamecp_google_payments WHERE google_order_id = '" . $orderId . "'");
        if( 1 <= mssql_num_rows($select_order) ) 
        {
            $google = @mssql_fetch_array($select_order);
            connectuserdb();
            $user_info_sql = "SELECT convert(varchar,id) AS AccountName FROM tbl_UserAccount WHERE Serial = '" . $google["google_account_serial"] . "'";
            if( !($user_info_result = mssql_query($user_info_sql)) ) 
            {
                gamecp_log(0, $custom, "GOOGLE - ERROR - Unable to find or query this user id [CHARGE]");
            }

            $user = mssql_fetch_array($user_info_result);
            if( $user["AccountName"] != "" ) 
            {
                $user_name = antiject($user["AccountName"]);
            }
            else
            {
                $user_name = $google["google_account_serial"];
            }

            $EMail = $google["google_buyer_email"];
            gamecp_log(5, $user_name, "" . "GOOGLE - REFUND - ID: " . $orderId . " | EMail: " . $EMail . " | Amount: " . $amount);
        }
        else
        {
            connectgamecpdb();
            gamecp_log(5, "Google", "" . "GOOGLE - ORDER REFUND ERROR - ID: " . $orderId . " | Cannot find order in database!");
        }

        $Gresponse->SendAck();
        break;
    case "risk-information-notification":
        $orderId = $data[$root]["google-order-number"]["VALUE"];
        $buyerIP = $data[$root]["risk-information"]["ip-address"]["VALUE"];
        $buyerAge = $data[$root]["risk-information"]["buyer-account-age"]["VALUE"];
        $google_query = "" . "UPDATE gamecp_google_payments SET google_buyer_ip = '" . $buyerIP . "', google_buyer_account_age = '" . $buyerAge . "' WHERE google_order_id = '" . $orderId . "'";
        mssql_query($google_query);
        $Gresponse->SendAck();
        break;
    default:
        $Gresponse->SendBadRequestStatus("Invalid or not supported Message");
        break;
}
switch( $new_fulfillment_order ) 
{
    case "NEW":
        break;
    case "PROCESSING":
        break;
    case "DELIVERED":
        gamecp_log(0, $user_name, "" . "GOOGLE - SUCCESSFUL ORDER - ID: " . $orderId, 1);
        connectgamecpdb();
        $google_query = "" . "UPDATE gamecp_google_payments SET google_order_state = '3' WHERE google_order_id = '" . $orderId . "'";
        mssql_query($google_query);
        $userPoints = @calculate_credits($config["donations_credit_muntiplier"], $config["donations_number_of_pay_options"], $config["donations_start_price"], $config["donations_start_credits"], @round($google["google_order_price"], 2));
        $totalusers_query = @mssql_query("SELECT user_points FROM gamecp_gamepoints WHERE user_account_id=\"" . @trim($google["google_account_serial"]) . "\"");
        if( mssql_num_rows($totalusers_query) == 0 ) 
        {
            $points_in = "INSERT INTO gamecp_gamepoints (user_account_id, user_points) VALUES (\"" . $google["google_account_serial"] . "\", \"" . $userPoints . "\")";
            mssql_query($points_in);
            gamecp_log(0, $user_name, "" . "GOOGLE - ADDED POINTS - INSERT - ID: " . $orderId . " | Points: " . $userPoints);
        }
        else
        {
            $points_in = "UPDATE gamecp_gamepoints SET user_points=user_points+" . $userPoints . " WHERE user_account_id=\"" . $google["google_account_serial"] . "\"";
            mssql_query($points_in);
            gamecp_log(0, $user_name, "" . "GOOGLE - ADDED POINTS - UPDATE - ID: " . $orderId . " | Points: " . $userPoints);
        }

        break;
    case "WILL_NOT_DELIVER":
        gamecp_log(4, $user_name, "" . "GOOGLE - NOT DELIVERED - ID: " . $orderId . " | EMail: " . $Email . " | Might be an error in your setup!");
        break;
    default:
        break;
}
break;
function get_arr_result($child_node)
{
    $result = array(  );
    if( isset($child_node) ) 
    {
        if( is_associative_array($child_node) ) 
        {
            $result[] = $child_node;
        }
        else
        {
            foreach( $child_node as $curr_node ) 
            {
                $result[] = $curr_node;
            }
        }

    }

    return $result;
}

function is_associative_array($var)
{
    return is_array($var) && !is_numeric(implode("", array_keys($var)));
}


