<?php 

class GoogleMerchantCalculations
{
    public $results_arr = NULL;
    public $currency = NULL;
    public $schema_url = "http://checkout.google.com/schema/2";

    public function GoogleMerchantCalculations($currency = "USD")
    {
        $this->results_arr = array(  );
        $this->currency = $currency;
    }

    public function AddResult($results)
    {
        $this->results_arr[] = $results;
    }

    public function GetXML()
    {
        require_once("xml-processing/gc_xmlbuilder.php");
        $xml_data = new gc_XmlBuilder();
        $xml_data->Push("merchant-calculation-results", array( "xmlns" => $this->schema_url ));
        $xml_data->Push("results");
        foreach( $this->results_arr as $result ) 
        {
            if( $result->shipping_name != "" ) 
            {
                $xml_data->Push("result", array( "shipping-name" => $result->shipping_name, "address-id" => $result->address_id ));
                $xml_data->Element("shipping-rate", $result->ship_price, array( "currency" => $this->currency ));
                $xml_data->Element("shippable", $result->shippable);
            }
            else
            {
                $xml_data->Push("result", array( "address-id" => $result->address_id ));
            }

            if( $result->tax_amount != "" ) 
            {
                $xml_data->Element("total-tax", $result->tax_amount, array( "currency" => $this->currency ));
            }

            if( count($result->coupon_arr) != 0 || count($result->giftcert_arr) != 0 ) 
            {
                $xml_data->Push("merchant-code-results");
                foreach( $result->coupon_arr as $curr_coupon ) 
                {
                    $xml_data->Push("coupon-result");
                    $xml_data->Element("valid", $curr_coupon->coupon_valid);
                    $xml_data->Element("code", $curr_coupon->coupon_code);
                    $xml_data->Element("calculated-amount", $curr_coupon->coupon_amount, array( "currency" => $this->currency ));
                    $xml_data->Element("message", $curr_coupon->coupon_message);
                    $xml_data->Pop("coupon-result");
                }
                foreach( $result->giftcert_arr as $curr_gift ) 
                {
                    $xml_data->Push("gift-result");
                    $xml_data->Element("valid", $curr_gift->gift_valid);
                    $xml_data->Element("code", $curr_gift->gift_code);
                    $xml_data->Element("calculated-amount", $curr_gift->gift_amount, array( "currency" => $this->currency ));
                    $xml_data->Element("message", $curr_gift->gift_message);
                    $xml_data->Pop("gift-result");
                }
                $xml_data->Pop("merchant-code-results");
            }

            $xml_data->Pop("result");
        }
        $xml_data->Pop("results");
        $xml_data->Pop("merchant-calculation-results");
        return $xml_data->GetXML();
    }

}


