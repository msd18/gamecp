<?php 

class GoogleResponse
{
    public $merchant_id = NULL;
    public $merchant_key = NULL;
    public $schema_url = NULL;
    public $log = NULL;
    public $response = NULL;
    public $root = "";
    public $data = array(  );
    public $xml_parser = NULL;

    public function GoogleResponse($id = null, $key = null)
    {
        $this->merchant_id = $id;
        $this->merchant_key = $key;
        $this->schema_url = "http://checkout.google.com/schema/2";
        ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . ".");
        require_once("googlelog.php");
        $this->log = new GoogleLog("", "", L_OFF);
    }

    public function SetMerchantAuthentication($id, $key)
    {
        $this->merchant_id = $id;
        $this->merchant_key = $key;
    }

    public function SetLogFiles($errorLogFile, $messageLogFile, $logLevel = L_ERR_RQST)
    {
        $this->log = new GoogleLog($errorLogFile, $messageLogFile, $logLevel);
    }

    public function HttpAuthentication($headers = null, $die = true)
    {
        if( !is_null($headers) ) 
        {
            $_SERVER = $headers;
        }

        if( isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"]) ) 
        {
            $compare_mer_id = $_SERVER["PHP_AUTH_USER"];
            $compare_mer_key = $_SERVER["PHP_AUTH_PW"];
        }
        else
        {
            if( isset($_SERVER["HTTP_AUTHORIZATION"]) ) 
            {
                list($compare_mer_id, $compare_mer_key) = explode(":", base64_decode(substr($_SERVER["HTTP_AUTHORIZATION"], strpos($_SERVER["HTTP_AUTHORIZATION"], " ") + 1)));
            }
            else
            {
                if( isset($_SERVER["Authorization"]) ) 
                {
                    list($compare_mer_id, $compare_mer_key) = explode(":", base64_decode(substr($_SERVER["Authorization"], strpos($_SERVER["Authorization"], " ") + 1)));
                }
                else
                {
                    $this->SendFailAuthenticationStatus("Failed to Get Basic Authentication Headers", $die);
                    return false;
                }

            }

        }

        if( $compare_mer_id != $this->merchant_id || $compare_mer_key != $this->merchant_key ) 
        {
            $this->SendFailAuthenticationStatus("Invalid Merchant Id/Key Pair", $die);
            return false;
        }

        return true;
    }

    public function ProcessMerchantCalculations($merchant_calc)
    {
        $this->SendOKStatus();
        $result = $merchant_calc->GetXML();
        echo $result;
    }

    public function ProcessNewOrderNotification()
    {
        $this->SendAck();
    }

    public function ProcessRiskInformationNotification()
    {
        $this->SendAck();
    }

    public function ProcessOrderStateChangeNotification()
    {
        $this->SendAck();
    }

    public function ProcessChargeAmountNotification()
    {
        $this->SendAck();
    }

    public function ProcessRefundAmountNotification()
    {
        $this->SendAck();
    }

    public function ProcessChargebackAmountNotification()
    {
        $this->SendAck();
    }

    public function ProcessAuthorizationAmountNotification()
    {
        $this->SendAck();
    }

    public function SendOKStatus()
    {
        header("HTTP/1.0 200 OK");
    }

    public function SendFailAuthenticationStatus($msg = "401 Unauthorized Access", $die = true)
    {
        $this->log->logError($msg);
        header("WWW-Authenticate: Basic realm=\"GoogleCheckout PHPSample Code\"");
        header("HTTP/1.0 401 Unauthorized");
        if( $die ) 
        {
            exit( $msg );
        }

        echo $msg;
    }

    public function SendBadRequestStatus($msg = "400 Bad Request", $die = true)
    {
        $this->log->logError($msg);
        header("HTTP/1.0 400 Bad Request");
        if( $die ) 
        {
            exit( $msg );
        }

        echo $msg;
    }

    public function SendServerErrorStatus($msg = "500 Internal Server Error", $die = true)
    {
        $this->log->logError($msg);
        header("HTTP/1.0 500 Internal Server Error");
        if( $die ) 
        {
            exit( $msg );
        }

        echo $msg;
    }

    public function SendAck($die = true)
    {
        $this->SendOKStatus();
        $acknowledgment = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . "<notification-acknowledgment xmlns=\"" . $this->schema_url . "\"/>";
        $this->log->LogResponse($acknowledgment);
        if( $die ) 
        {
            exit( $acknowledgment );
        }

        echo $acknowledgment;
    }

    public function GetParsedXML($request = null)
    {
        if( !is_null($request) ) 
        {
            $this->log->LogRequest($request);
            $this->response = $request;
            ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . ".");
            require_once("xml-processing/gc_xmlparser.php");
            $this->xml_parser = new gc_xmlparser($request);
            $this->root = $this->xml_parser->GetRoot();
            $this->data = $this->xml_parser->GetData();
        }

        return array( $this->root, $this->data );
    }

}


