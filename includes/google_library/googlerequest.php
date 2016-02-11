<?php 
define("PHP_SAMPLE_CODE_VERSION", "v1.2.5");
define("ENTER", "\r\n");
define("DOUBLE_ENTER", ENTER . ENTER);
define("GOOGLE_MESSAGE_LENGTH", 254);
define("GOOGLE_REASON_LENGTH", 140);

class GoogleRequest
{
    public $merchant_id = NULL;
    public $merchant_key = NULL;
    public $currency = NULL;
    public $server_url = NULL;
    public $schema_url = NULL;
    public $base_url = NULL;
    public $checkout_url = NULL;
    public $checkout_diagnose_url = NULL;
    public $request_url = NULL;
    public $request_diagnose_url = NULL;
    public $merchant_checkout = NULL;
    public $proxy = array(  );
    public $certPath = "";
    public $log = NULL;

    public function GoogleRequest($id, $key, $server_type = "sandbox", $currency = "USD")
    {
        $this->merchant_id = $id;
        $this->merchant_key = $key;
        $this->currency = $currency;
        if( strtolower($server_type) == "sandbox" ) 
        {
            $this->server_url = "https://sandbox.google.com/checkout/";
        }
        else
        {
            $this->server_url = "https://checkout.google.com/";
        }

        $this->schema_url = "http://checkout.google.com/schema/2";
        $this->base_url = $this->server_url . "api/checkout/v2/";
        $this->request_url = $this->base_url . "request/Merchant/" . $this->merchant_id;
        $this->merchant_checkout = $this->base_url . "merchantCheckout/Merchant/" . $this->merchant_id;
        ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . ".");
        require_once("googlelog.php");
        $this->log = new GoogleLog("", "", L_OFF);
    }

    public function SetLogFiles($errorLogFile, $messageLogFile, $logLevel = L_ERR_RQST)
    {
        $this->log = new GoogleLog($errorLogFile, $messageLogFile, $logLevel);
    }

    public function SetCertificatePath($certPath)
    {
        $this->certPath = $certPath;
    }

    public function SendServer2ServerCart($xml_cart, $die = true)
    {
        list($status, $body) = $this->SendReq($this->merchant_checkout, $this->GetAuthenticationHeaders(), $xml_cart);
        if( $status != 200 ) 
        {
            return array( $status, $body );
        }

        ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . ".");
        require_once("xml-processing/gc_xmlparser.php");
        $xml_parser = new gc_xmlparser($body);
        $root = $xml_parser->GetRoot();
        $data = $xml_parser->GetData();
        $this->log->logRequest("Redirecting to: " . $data[$root]["redirect-url"]["VALUE"]);
        header("Location: " . $data[$root]["redirect-url"]["VALUE"]);
        if( $die ) 
        {
            exit( $data[$root]["redirect-url"]["VALUE"] );
        }

        return array( 200, $data[$root]["redirect-url"]["VALUE"] );
    }

    public function SendChargeOrder($google_order, $amount = "")
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <charge-order xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">";
        if( $amount != "" ) 
        {
            $postargs .= "<amount currency=\"" . $this->currency . "\">" . $amount . "</amount>";
        }

        $postargs .= "</charge-order>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendRefundOrder($google_order, $amount, $reason, $comment = "")
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <refund-order xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">" . "<reason>" . $reason . "</reason>";
        if( $amount != 0 ) 
        {
            $postargs .= "<amount currency=\"" . $this->currency . "\">" . htmlentities($amount) . "</amount>";
        }

        $postargs .= "<comment>" . htmlentities($comment) . "</comment>\n                  </refund-order>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendCancelOrder($google_order, $reason, $comment)
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <cancel-order xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">\n                  <reason>" . substr(htmlentities(strip_tags($reason)), 0, GOOGLE_REASON_LENGTH) . "</reason>\n                  <comment>" . substr(htmlentities(strip_tags($comment)), 0, GOOGLE_REASON_LENGTH) . "</comment>\n                  </cancel-order>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendTrackingData($google_order, $carrier, $tracking_no)
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <add-tracking-data xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">\n                  <tracking-data>\n                  <carrier>" . htmlentities($carrier) . "</carrier>\n                  <tracking-number>" . $tracking_no . "</tracking-number>\n                  </tracking-data>\n                  </add-tracking-data>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendMerchantOrderNumber($google_order, $merchant_order, $timeout = false)
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <add-merchant-order-number xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">\n                  <merchant-order-number>" . $merchant_order . "</merchant-order-number>\n                  </add-merchant-order-number>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs, $timeout);
    }

    public function SendBuyerMessage($google_order, $message, $send_mail = "true", $timeout = false)
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <send-buyer-message xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">\n                  <message>" . substr(htmlentities(strip_tags($message)), 0, GOOGLE_MESSAGE_LENGTH) . "</message>\n                  <send-email>" . strtolower($send_mail) . "</send-email>\n                  </send-buyer-message>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs, $timeout);
    }

    public function SendProcessOrder($google_order)
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <process-order xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\"/> ";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendDeliverOrder($google_order, $carrier = "", $tracking_no = "", $send_mail = "true")
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <deliver-order xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">";
        if( $carrier != "" && $tracking_no != "" ) 
        {
            $postargs .= "<tracking-data>\n                  <carrier>" . htmlentities($carrier) . "</carrier>\n            <tracking-number>" . htmlentities($tracking_no) . "</tracking-number>\n                  </tracking-data>";
        }

        $postargs .= "<send-email>" . strtolower($send_mail) . "</send-email>\n                  </deliver-order>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendArchiveOrder($google_order)
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <archive-order xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\"/>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendUnarchiveOrder($google_order)
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <unarchive-order xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\"/>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendShipItems($google_order, $items_list = array(  ), $send_mail = "true")
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <ship-items xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">" . "<item-shipping-information-list>\n";
        foreach( $items_list as $item ) 
        {
            $postargs .= "<item-shipping-information>\n                      <item-id>\n                        <merchant-item-id>" . $item->merchant_item_id . "</merchant-item-id>\n                     </item-id>\n";
            if( count($item->tracking_data_list) ) 
            {
                $postargs .= "<tracking-data-list>\n";
                foreach( $item->tracking_data_list as $tracking_data ) 
                {
                    $postargs .= "<tracking-data>\n                            <carrier>" . htmlentities($tracking_data["carrier"]) . "</carrier>\n                            <tracking-number>" . $tracking_data["tracking-number"] . "</tracking-number>\n                          </tracking-data>\n";
                }
                $postargs .= "</tracking-data-list>\n";
            }

            $postargs .= "</item-shipping-information>\n";
        }
        $postargs .= "</item-shipping-information-list>\n" . "<send-email>" . strtolower($send_mail) . "</send-email>\n                  </ship-items>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendBackorderItems($google_order, $items_list = array(  ), $send_mail = "true")
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <backorder-items xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">";
        $postargs .= "<item-ids>";
        foreach( $items_list as $item ) 
        {
            $postargs .= "<item-id>\n                        <merchant-item-id>" . $item->merchant_item_id . "</merchant-item-id>\n                      </item-id>";
        }
        $postargs .= "</item-ids>";
        $postargs .= "<send-email>" . strtolower($send_mail) . "</send-email>\n                    </backorder-items>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendCancelItems($google_order, $items_list = array(  ), $reason, $comment = "", $send_mail = "true")
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <cancel-items xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">";
        $postargs .= "<item-ids>";
        foreach( $items_list as $item ) 
        {
            $postargs .= "<item-id>\n                        <merchant-item-id>" . $item->merchant_item_id . "</merchant-item-id>\n                      </item-id>";
        }
        $postargs .= "</item-ids>";
        $postargs .= "<send-email>" . strtolower($send_mail) . "</send-email>\n                  <reason>" . substr(htmlentities(strip_tags($reason)), 0, GOOGLE_REASON_LENGTH) . "</reason>\n                  <comment>" . substr(htmlentities(strip_tags($comment)), 0, GOOGLE_REASON_LENGTH) . "</comment>\n                  </cancel-items>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendReturnItems($google_order, $items_list = array(  ), $send_mail = "true")
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <return-items xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">";
        $postargs .= "<item-ids>";
        foreach( $items_list as $item ) 
        {
            $postargs .= "<item-id>\n                        <merchant-item-id>" . $item->merchant_item_id . "</merchant-item-id>\n                      </item-id>";
        }
        $postargs .= "</item-ids>";
        $postargs .= "<send-email>" . strtolower($send_mail) . "</send-email>\n                    </return-items>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function SendResetItemsShippingInformation($google_order, $items_list = array(  ), $send_mail = "true")
    {
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n                  <reset-items-shipping-information xmlns=\"" . $this->schema_url . "\" google-order-number=\"" . $google_order . "\">";
        $postargs .= "<item-ids>";
        foreach( $items_list as $item ) 
        {
            $postargs .= "<item-id>\n                        <merchant-item-id>" . $item->merchant_item_id . "</merchant-item-id>\n                      </item-id>";
        }
        $postargs .= "</item-ids>";
        $postargs .= "<send-email>" . strtolower($send_mail) . "</send-email>\n                    </reset-items-shipping-information>";
        return $this->SendReq($this->request_url, $this->GetAuthenticationHeaders(), $postargs);
    }

    public function GetAuthenticationHeaders()
    {
        $headers = array(  );
        $headers[] = "Authorization: Basic " . base64_encode($this->merchant_id . ":" . $this->merchant_key);
        $headers[] = "Content-Type: application/xml; charset=UTF-8";
        $headers[] = "Accept: application/xml; charset=UTF-8";
        $headers[] = "User-Agent: GC-PHP-Sample_code (" . PHP_SAMPLE_CODE_VERSION . "/ropu)";
        return $headers;
    }

    public function SetProxy($proxy = array(  ))
    {
        if( is_array($proxy) && count($proxy) ) 
        {
            $this->proxy["host"] = $proxy["host"];
            $this->proxy["port"] = $proxy["port"];
        }

    }

    public function SendReq($url, $header_arr, $postargs, $timeout = false)
    {
        $session = curl_init($url);
        $this->log->LogRequest($postargs);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_HTTPHEADER, $header_arr);
        curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
        curl_setopt($session, CURLOPT_HEADER, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        if( !empty($this->certPath) && file_exists($this->certPath) ) 
        {
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($session, CURLOPT_CAINFO, $this->certPath);
        }
        else
        {
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        }

        if( is_array($this->proxy) && count($this->proxy) ) 
        {
            curl_setopt($session, CURLOPT_PROXY, $this->proxy["host"] . ":" . $this->proxy["port"]);
        }

        if( $timeout != false ) 
        {
            curl_setopt($session, CURLOPT_TIMEOUT, $timeout);
        }

        $response = curl_exec($session);
        if( curl_errno($session) ) 
        {
            $this->log->LogError(curl_error($session));
            return array( "CURL_ERR", curl_error($session) );
        }

        curl_close($session);
        $heads = $this->parse_headers($response);
        $body = $this->get_body_x($response);
        $status_code = array(  );
        preg_match("/\\d\\d\\d/", $heads[0], $status_code);
        switch( $status_code[0] ) 
        {
            case 200:
                $this->log->LogResponse($response);
                return array( 200, $body );
            case 503:
                $this->log->LogError($response);
                return array( 503, htmlentities($body) );
            case 403:
                $this->log->LogError($response);
                return array( 403, htmlentities($body) );
            case 400:
                $this->log->LogError($response);
                return array( 400, htmlentities($body) );
            default:
                $this->log->LogError($response);
                return array( "ERR", htmlentities($body) );
        }
    }

    public function parse_headers($message)
    {
        $head_end = strpos($message, DOUBLE_ENTER);
        $headers = $this->get_headers_x(substr($message, 0, $head_end + strlen(DOUBLE_ENTER)));
        if( !is_array($headers) || empty($headers) ) 
        {
            return null;
        }

        if( !preg_match("%[HTTP/\\d\\.\\d] (\\d\\d\\d)%", $headers[0], $status_code) ) 
        {
            return null;
        }

        switch( $status_code[1] ) 
        {
            case "200":
                $parsed = $this->parse_headers(substr($message, $head_end + strlen(DOUBLE_ENTER)));
                return is_null($parsed) ? $headers : $parsed;
            default:
                return $headers;
        }
    }

    public function get_headers_x($heads, $format = 0)
    {
        $fp = explode(ENTER, $heads);
        foreach( $fp as $header ) 
        {
            if( $header == "" ) 
            {
                $eoheader = true;
                break;
            }

            $header = trim($header);
            if( $format == 1 ) 
            {
                $key = array_shift(explode(":", $header));
                if( $key == $header ) 
                {
                    $headers[] = $header;
                }
                else
                {
                    $headers[$key] = substr($header, strlen($key) + 2);
                }

                unset($key);
            }
            else
            {
                $headers[] = $header;
            }

        }
        return $headers;
    }

    public function get_body_x($heads)
    {
        $fp = explode(DOUBLE_ENTER, $heads, 2);
        return $fp[1];
    }

}


class GoogleShipItem
{
    public $merchant_item_id = NULL;
    public $tracking_data_list = NULL;
    public $tracking_no = NULL;

    public function GoogleShipItem($merchant_item_id, $tracking_data_list = array(  ))
    {
        $this->merchant_item_id = $merchant_item_id;
        $this->tracking_data_list = $tracking_data_list;
    }

    public function AddTrackingData($carrier, $tracking_no)
    {
        if( $carrier != "" && $tracking_no != "" ) 
        {
            $this->tracking_data_list[] = array( "carrier" => $carrier, "tracking-number" => $tracking_no );
        }

    }

}


