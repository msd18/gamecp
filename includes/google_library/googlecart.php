<?php 
define("MAX_DIGITAL_DESC", 1024);

class GoogleCart
{
    public $merchant_id = NULL;
    public $merchant_key = NULL;
    public $variant = false;
    public $currency = NULL;
    public $server_url = NULL;
    public $schema_url = NULL;
    public $base_url = NULL;
    public $checkout_url = NULL;
    public $checkout_diagnose_url = NULL;
    public $request_url = NULL;
    public $request_diagnose_url = NULL;
    public $cart_expiration = "";
    public $merchant_private_data = "";
    public $edit_cart_url = "";
    public $continue_shopping_url = "";
    public $request_buyer_phone = "";
    public $merchant_calculated_tax = "";
    public $merchant_calculations_url = "";
    public $accept_merchant_coupons = "";
    public $accept_gift_certificates = "";
    public $rounding_mode = NULL;
    public $rounding_rule = NULL;
    public $analytics_data = NULL;
    public $item_arr = NULL;
    public $shipping_arr = NULL;
    public $default_tax_rules_arr = NULL;
    public $alternate_tax_tables_arr = NULL;
    public $xml_data = NULL;
    public $googleAnalytics_id = false;
    public $thirdPartyTackingUrl = false;
    public $thirdPartyTackingParams = array(  );
    public $multiple_tags = array( "flat-rate-shipping" => array(  ), "merchant-calculated-shipping" => array(  ), "pickup" => array(  ), "parameterized-url" => array(  ), "url-parameter" => array(  ), "item" => array(  ), "us-state-area" => array( "tax-area" ), "us-zip-area" => array( "tax-area" ), "us-country-area" => array( "tax-area" ), "postal-area" => array( "tax-area" ), "alternate-tax-table" => array(  ), "world-area" => array( "tax-area" ), "default-tax-rule" => array(  ), "alternate-tax-rule" => array(  ), "gift-certificate-adjustment" => array(  ), "coupon-adjustment" => array(  ), "coupon-result" => array(  ), "gift-certificate-result" => array(  ), "method" => array(  ), "anonymous-address" => array(  ), "result" => array(  ), "string" => array(  ) );
    public $ignore_tags = array( "xmlns" => true, "checkout-shopping-cart" => true, "merchant-private-data" => true, "merchant-private-item-data" => true );

    public function GoogleCart($id, $key, $server_type = "sandbox", $currency = "USD")
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
        $this->checkout_url = $this->base_url . "checkout/Merchant/" . $this->merchant_id;
        $this->checkoutForm_url = $this->base_url . "checkoutForm/Merchant/" . $this->merchant_id;
        $this->item_arr = array(  );
        $this->shipping_arr = array(  );
        $this->alternate_tax_tables_arr = array(  );
    }

    public function SetCartExpiration($cart_expire)
    {
        $this->cart_expiration = $cart_expire;
    }

    public function SetMerchantPrivateData($data)
    {
        $this->merchant_private_data = $data;
    }

    public function SetEditCartUrl($url)
    {
        $this->edit_cart_url = $url;
    }

    public function SetContinueShoppingUrl($url)
    {
        $this->continue_shopping_url = $url;
    }

    public function SetRequestBuyerPhone($req)
    {
        $this->request_buyer_phone = $this->_GetBooleanValue($req, "false");
    }

    public function SetMerchantCalculations($url, $tax_option = "false", $coupons = "false", $gift_cert = "false")
    {
        $this->merchant_calculations_url = $url;
        $this->merchant_calculated_tax = $this->_GetBooleanValue($tax_option, "false");
        $this->accept_merchant_coupons = $this->_GetBooleanValue($coupons, "false");
        $this->accept_gift_certificates = $this->_GetBooleanValue($gift_cert, "false");
    }

    public function AddItem($google_item)
    {
        $this->item_arr[] = $google_item;
    }

    public function AddShipping($ship)
    {
        $this->shipping_arr[] = $ship;
    }

    public function AddDefaultTaxRules($rules)
    {
        $this->default_tax_table = true;
        $this->default_tax_rules_arr[] = $rules;
    }

    public function AddAlternateTaxTables($tax)
    {
        $this->alternate_tax_tables_arr[] = $tax;
    }

    public function AddRoundingPolicy($mode, $rule)
    {
        switch( $mode ) 
        {
            case "UP":
            case "DOWN":
            case "CEILING":
            case "HALF_UP":
            case "HALF_DOWN":
            case "HALF_EVEN":
                $this->rounding_mode = $mode;
                break;
            default:
                break;
        }
        switch( $rule ) 
        {
            case "PER_LINE":
            case "TOTAL":
                $this->rounding_rule = $rule;
                break;
            default:
                break;
        }
    }

    public function SetAnalyticsData($data)
    {
        $this->analytics_data = $data;
    }

    public function AddGoogleAnalyticsTracking($GA_id)
    {
        $this->googleAnalytics_id = $GA_id;
    }

    public function AddThirdPartyTracking($url, $tracking_param_types = array(  ))
    {
        $this->thirdPartyTackingUrl = $url;
        $this->thirdPartyTackingParams = $tracking_param_types;
    }

    public function GetXML()
    {
        require_once("xml-processing/gc_xmlbuilder.php");
        $xml_data = new gc_XmlBuilder();
        $xml_data->Push("checkout-shopping-cart", array( "xmlns" => $this->schema_url ));
        $xml_data->Push("shopping-cart");
        if( $this->cart_expiration != "" ) 
        {
            $xml_data->Push("cart-expiration");
            $xml_data->Element("good-until-date", $this->cart_expiration);
            $xml_data->Pop("cart-expiration");
        }

        $xml_data->Push("items");
        foreach( $this->item_arr as $item ) 
        {
            $xml_data->Push("item");
            $xml_data->Element("item-name", $item->item_name);
            $xml_data->Element("item-description", $item->item_description);
            $xml_data->Element("unit-price", $item->unit_price, array( "currency" => $this->currency ));
            $xml_data->Element("quantity", $item->quantity);
            if( $item->merchant_private_item_data != "" ) 
            {
                if( is_a($item->merchant_private_item_data, "merchantprivate") ) 
                {
                    $item->merchant_private_item_data->AddMerchantPrivateToXML($xml_data);
                }
                else
                {
                    $xml_data->Element("merchant-private-item-data", $item->merchant_private_item_data);
                }

            }

            if( $item->merchant_item_id != "" ) 
            {
                $xml_data->Element("merchant-item-id", $item->merchant_item_id);
            }

            if( $item->tax_table_selector != "" ) 
            {
                $xml_data->Element("tax-table-selector", $item->tax_table_selector);
            }

            if( $item->item_weight != "" && $item->numeric_weight !== "" ) 
            {
                $xml_data->EmptyElement("item-weight", array( "unit" => $item->item_weight, "value" => $item->numeric_weight ));
            }

            if( $item->digital_content ) 
            {
                $xml_data->push("digital-content");
                if( !empty($item->digital_url) ) 
                {
                    $xml_data->element("description", substr($item->digital_description, 0, MAX_DIGITAL_DESC));
                    $xml_data->element("url", $item->digital_url);
                    if( !empty($item->digital_key) ) 
                    {
                        $xml_data->element("key", $item->digital_key);
                    }

                }
                else
                {
                    $xml_data->element("email-delivery", $this->_GetBooleanValue($item->email_delivery, "true"));
                }

                $xml_data->pop("digital-content");
            }

            $xml_data->Pop("item");
        }
        $xml_data->Pop("items");
        if( $this->merchant_private_data != "" ) 
        {
            if( is_a($this->merchant_private_data, "merchantprivate") ) 
            {
                $this->merchant_private_data->AddMerchantPrivateToXML($xml_data);
            }
            else
            {
                $xml_data->Element("merchant-private-data", $this->merchant_private_data);
            }

        }

        $xml_data->Pop("shopping-cart");
        $xml_data->Push("checkout-flow-support");
        $xml_data->Push("merchant-checkout-flow-support");
        if( $this->edit_cart_url != "" ) 
        {
            $xml_data->Element("edit-cart-url", $this->edit_cart_url);
        }

        if( $this->continue_shopping_url != "" ) 
        {
            $xml_data->Element("continue-shopping-url", $this->continue_shopping_url);
        }

        if( 0 < count($this->shipping_arr) ) 
        {
            $xml_data->Push("shipping-methods");
        }

        foreach( $this->shipping_arr as $ship ) 
        {
            if( $ship->type == "flat-rate-shipping" || $ship->type == "merchant-calculated-shipping" ) 
            {
                $xml_data->Push($ship->type, array( "name" => $ship->name ));
                $xml_data->Element("price", $ship->price, array( "currency" => $this->currency ));
                $shipping_restrictions = $ship->shipping_restrictions;
                if( isset($shipping_restrictions) ) 
                {
                    $xml_data->Push("shipping-restrictions");
                    if( $shipping_restrictions->allow_us_po_box === true ) 
                    {
                        $xml_data->Element("allow-us-po-box", "true");
                    }
                    else
                    {
                        $xml_data->Element("allow-us-po-box", "false");
                    }

                    if( $shipping_restrictions->allowed_restrictions ) 
                    {
                        $xml_data->Push("allowed-areas");
                        if( $shipping_restrictions->allowed_country_area != "" ) 
                        {
                            $xml_data->EmptyElement("us-country-area", array( "country-area" => $shipping_restrictions->allowed_country_area ));
                        }

                        foreach( $shipping_restrictions->allowed_state_areas_arr as $current ) 
                        {
                            $xml_data->Push("us-state-area");
                            $xml_data->Element("state", $current);
                            $xml_data->Pop("us-state-area");
                        }
                        foreach( $shipping_restrictions->allowed_zip_patterns_arr as $current ) 
                        {
                            $xml_data->Push("us-zip-area");
                            $xml_data->Element("zip-pattern", $current);
                            $xml_data->Pop("us-zip-area");
                        }
                        if( $shipping_restrictions->allowed_world_area === true ) 
                        {
                            $xml_data->EmptyElement("world-area");
                        }

                        for( $i = 0; $i < count($shipping_restrictions->allowed_country_codes_arr); $i++ ) 
                        {
                            $xml_data->Push("postal-area");
                            $country_code = $shipping_restrictions->allowed_country_codes_arr[$i];
                            $postal_pattern = $shipping_restrictions->allowed_postal_patterns_arr[$i];
                            $xml_data->Element("country-code", $country_code);
                            if( $postal_pattern != "" ) 
                            {
                                $xml_data->Element("postal-code-pattern", $postal_pattern);
                            }

                            $xml_data->Pop("postal-area");
                        }
                        $xml_data->Pop("allowed-areas");
                    }

                    if( $shipping_restrictions->excluded_restrictions ) 
                    {
                        if( !$shipping_restrictions->allowed_restrictions ) 
                        {
                            $xml_data->EmptyElement("allowed-areas");
                        }

                        $xml_data->Push("excluded-areas");
                        if( $shipping_restrictions->excluded_country_area != "" ) 
                        {
                            $xml_data->EmptyElement("us-country-area", array( "country-area" => $shipping_restrictions->excluded_country_area ));
                        }

                        foreach( $shipping_restrictions->excluded_state_areas_arr as $current ) 
                        {
                            $xml_data->Push("us-state-area");
                            $xml_data->Element("state", $current);
                            $xml_data->Pop("us-state-area");
                        }
                        foreach( $shipping_restrictions->excluded_zip_patterns_arr as $current ) 
                        {
                            $xml_data->Push("us-zip-area");
                            $xml_data->Element("zip-pattern", $current);
                            $xml_data->Pop("us-zip-area");
                        }
                        for( $i = 0; $i < count($shipping_restrictions->excluded_country_codes_arr); $i++ ) 
                        {
                            $xml_data->Push("postal-area");
                            $country_code = $shipping_restrictions->excluded_country_codes_arr[$i];
                            $postal_pattern = $shipping_restrictions->excluded_postal_patterns_arr[$i];
                            $xml_data->Element("country-code", $country_code);
                            if( $postal_pattern != "" ) 
                            {
                                $xml_data->Element("postal-code-pattern", $postal_pattern);
                            }

                            $xml_data->Pop("postal-area");
                        }
                        $xml_data->Pop("excluded-areas");
                    }

                    $xml_data->Pop("shipping-restrictions");
                }

                if( $ship->type == "merchant-calculated-shipping" ) 
                {
                    $address_filters = $ship->address_filters;
                    if( isset($address_filters) ) 
                    {
                        $xml_data->Push("address-filters");
                        if( $address_filters->allow_us_po_box === true ) 
                        {
                            $xml_data->Element("allow-us-po-box", "true");
                        }
                        else
                        {
                            $xml_data->Element("allow-us-po-box", "false");
                        }

                        if( $address_filters->allowed_restrictions ) 
                        {
                            $xml_data->Push("allowed-areas");
                            if( $address_filters->allowed_country_area != "" ) 
                            {
                                $xml_data->EmptyElement("us-country-area", array( "country-area" => $address_filters->allowed_country_area ));
                            }

                            foreach( $address_filters->allowed_state_areas_arr as $current ) 
                            {
                                $xml_data->Push("us-state-area");
                                $xml_data->Element("state", $current);
                                $xml_data->Pop("us-state-area");
                            }
                            foreach( $address_filters->allowed_zip_patterns_arr as $current ) 
                            {
                                $xml_data->Push("us-zip-area");
                                $xml_data->Element("zip-pattern", $current);
                                $xml_data->Pop("us-zip-area");
                            }
                            if( $address_filters->allowed_world_area === true ) 
                            {
                                $xml_data->EmptyElement("world-area");
                            }

                            for( $i = 0; $i < count($address_filters->allowed_country_codes_arr); $i++ ) 
                            {
                                $xml_data->Push("postal-area");
                                $country_code = $address_filters->allowed_country_codes_arr[$i];
                                $postal_pattern = $address_filters->allowed_postal_patterns_arr[$i];
                                $xml_data->Element("country-code", $country_code);
                                if( $postal_pattern != "" ) 
                                {
                                    $xml_data->Element("postal-code-pattern", $postal_pattern);
                                }

                                $xml_data->Pop("postal-area");
                            }
                            $xml_data->Pop("allowed-areas");
                        }

                        if( $address_filters->excluded_restrictions ) 
                        {
                            if( !$address_filters->allowed_restrictions ) 
                            {
                                $xml_data->EmptyElement("allowed-areas");
                            }

                            $xml_data->Push("excluded-areas");
                            if( $address_filters->excluded_country_area != "" ) 
                            {
                                $xml_data->EmptyElement("us-country-area", array( "country-area" => $address_filters->excluded_country_area ));
                            }

                            foreach( $address_filters->excluded_state_areas_arr as $current ) 
                            {
                                $xml_data->Push("us-state-area");
                                $xml_data->Element("state", $current);
                                $xml_data->Pop("us-state-area");
                            }
                            foreach( $address_filters->excluded_zip_patterns_arr as $current ) 
                            {
                                $xml_data->Push("us-zip-area");
                                $xml_data->Element("zip-pattern", $current);
                                $xml_data->Pop("us-zip-area");
                            }
                            for( $i = 0; $i < count($address_filters->excluded_country_codes_arr); $i++ ) 
                            {
                                $xml_data->Push("postal-area");
                                $country_code = $address_filters->excluded_country_codes_arr[$i];
                                $postal_pattern = $address_filters->excluded_postal_patterns_arr[$i];
                                $xml_data->Element("country-code", $country_code);
                                if( $postal_pattern != "" ) 
                                {
                                    $xml_data->Element("postal-code-pattern", $postal_pattern);
                                }

                                $xml_data->Pop("postal-area");
                            }
                            $xml_data->Pop("excluded-areas");
                        }

                        $xml_data->Pop("address-filters");
                    }

                }

                $xml_data->Pop($ship->type);
            }
            else
            {
                if( $ship->type == "carrier-calculated-shipping" ) 
                {
                    $xml_data->Push($ship->type);
                    $xml_data->Push("carrier-calculated-shipping-options");
                    $CCSoptions = $ship->CarrierCalculatedShippingOptions;
                    foreach( $CCSoptions as $CCSoption ) 
                    {
                        $xml_data->Push("carrier-calculated-shipping-option");
                        $xml_data->Element("price", $CCSoption->price, array( "currency" => $this->currency ));
                        $xml_data->Element("shipping-company", $CCSoption->shipping_company);
                        $xml_data->Element("shipping-type", $CCSoption->shipping_type);
                        $xml_data->Element("carrier-pickup", $CCSoption->carrier_pickup);
                        if( !empty($CCSoption->additional_fixed_charge) ) 
                        {
                            $xml_data->Element("additional-fixed-charge", $CCSoption->additional_fixed_charge, array( "currency" => $this->currency ));
                        }

                        if( !empty($CCSoption->additional_variable_charge_percent) ) 
                        {
                            $xml_data->Element("additional-variable-charge-percent", $CCSoption->additional_variable_charge_percent);
                        }

                        $xml_data->Pop("carrier-calculated-shipping-option");
                    }
                    $xml_data->Pop("carrier-calculated-shipping-options");
                    $xml_data->Push("shipping-packages");
                    $xml_data->Push("shipping-package");
                    $xml_data->Push("ship-from", array( "id" => $ship->ShippingPackage->ship_from->id ));
                    $xml_data->Element("city", $ship->ShippingPackage->ship_from->city);
                    $xml_data->Element("region", $ship->ShippingPackage->ship_from->region);
                    $xml_data->Element("postal-code", $ship->ShippingPackage->ship_from->postal_code);
                    $xml_data->Element("country-code", $ship->ShippingPackage->ship_from->country_code);
                    $xml_data->Pop("ship-from");
                    $xml_data->EmptyElement("width", array( "unit" => $ship->ShippingPackage->unit, "value" => $ship->ShippingPackage->width ));
                    $xml_data->EmptyElement("length", array( "unit" => $ship->ShippingPackage->unit, "value" => $ship->ShippingPackage->length ));
                    $xml_data->EmptyElement("height", array( "unit" => $ship->ShippingPackage->unit, "value" => $ship->ShippingPackage->height ));
                    $xml_data->Element("delivery-address-category", $ship->ShippingPackage->delivery_address_category);
                    $xml_data->Pop("shipping-package");
                    $xml_data->Pop("shipping-packages");
                    $xml_data->Pop($ship->type);
                }
                else
                {
                    if( $ship->type == "pickup" ) 
                    {
                        $xml_data->Push("pickup", array( "name" => $ship->name ));
                        $xml_data->Element("price", $ship->price, array( "currency" => $this->currency ));
                        $xml_data->Pop("pickup");
                    }

                }

            }

        }
        if( 0 < count($this->shipping_arr) ) 
        {
            $xml_data->Pop("shipping-methods");
        }

        if( $this->request_buyer_phone != "" ) 
        {
            $xml_data->Element("request-buyer-phone-number", $this->request_buyer_phone);
        }

        if( $this->merchant_calculations_url != "" ) 
        {
            $xml_data->Push("merchant-calculations");
            $xml_data->Element("merchant-calculations-url", $this->merchant_calculations_url);
            if( $this->accept_merchant_coupons != "" ) 
            {
                $xml_data->Element("accept-merchant-coupons", $this->accept_merchant_coupons);
            }

            if( $this->accept_gift_certificates != "" ) 
            {
                $xml_data->Element("accept-gift-certificates", $this->accept_gift_certificates);
            }

            $xml_data->Pop("merchant-calculations");
        }

        if( $this->thirdPartyTackingUrl ) 
        {
            $xml_data->push("parameterized-urls");
            $xml_data->push("parameterized-url", array( "url" => $this->thirdPartyTackingUrl ));
            if( is_array($this->thirdPartyTackingParams) && 0 < count($this->thirdPartyTackingParams) ) 
            {
                $xml_data->push("parameters");
                foreach( $this->thirdPartyTackingParams as $tracking_param_name => $tracking_param_type ) 
                {
                    $xml_data->emptyElement("url-parameter", array( "name" => $tracking_param_name, "type" => $tracking_param_type ));
                }
                $xml_data->pop("parameters");
            }

            $xml_data->pop("parameterized-url");
            $xml_data->pop("parameterized-urls");
        }

        if( count($this->alternate_tax_tables_arr) != 0 || count($this->default_tax_rules_arr) != 0 ) 
        {
            if( $this->merchant_calculated_tax != "" ) 
            {
                $xml_data->Push("tax-tables", array( "merchant-calculated" => $this->merchant_calculated_tax ));
            }
            else
            {
                $xml_data->Push("tax-tables");
            }

            if( count($this->default_tax_rules_arr) != 0 ) 
            {
                $xml_data->Push("default-tax-table");
                $xml_data->Push("tax-rules");
                foreach( $this->default_tax_rules_arr as $curr_rule ) 
                {
                    if( $curr_rule->country_area != "" ) 
                    {
                        $xml_data->Push("default-tax-rule");
                        $xml_data->Element("shipping-taxed", $curr_rule->shipping_taxed);
                        $xml_data->Element("rate", $curr_rule->tax_rate);
                        $xml_data->Push("tax-area");
                        $xml_data->EmptyElement("us-country-area", array( "country-area" => $curr_rule->country_area ));
                        $xml_data->Pop("tax-area");
                        $xml_data->Pop("default-tax-rule");
                    }

                    foreach( $curr_rule->state_areas_arr as $current ) 
                    {
                        $xml_data->Push("default-tax-rule");
                        $xml_data->Element("shipping-taxed", $curr_rule->shipping_taxed);
                        $xml_data->Element("rate", $curr_rule->tax_rate);
                        $xml_data->Push("tax-area");
                        $xml_data->Push("us-state-area");
                        $xml_data->Element("state", $current);
                        $xml_data->Pop("us-state-area");
                        $xml_data->Pop("tax-area");
                        $xml_data->Pop("default-tax-rule");
                    }
                    foreach( $curr_rule->zip_patterns_arr as $current ) 
                    {
                        $xml_data->Push("default-tax-rule");
                        $xml_data->Element("shipping-taxed", $curr_rule->shipping_taxed);
                        $xml_data->Element("rate", $curr_rule->tax_rate);
                        $xml_data->Push("tax-area");
                        $xml_data->Push("us-zip-area");
                        $xml_data->Element("zip-pattern", $current);
                        $xml_data->Pop("us-zip-area");
                        $xml_data->Pop("tax-area");
                        $xml_data->Pop("default-tax-rule");
                    }
                    for( $i = 0; $i < count($curr_rule->country_codes_arr); $i++ ) 
                    {
                        $xml_data->Push("default-tax-rule");
                        $xml_data->Element("shipping-taxed", $curr_rule->shipping_taxed);
                        $xml_data->Element("rate", $curr_rule->tax_rate);
                        $xml_data->Push("tax-area");
                        $xml_data->Push("postal-area");
                        $country_code = $curr_rule->country_codes_arr[$i];
                        $postal_pattern = $curr_rule->postal_patterns_arr[$i];
                        $xml_data->Element("country-code", $country_code);
                        if( $postal_pattern != "" ) 
                        {
                            $xml_data->Element("postal-code-pattern", $postal_pattern);
                        }

                        $xml_data->Pop("postal-area");
                        $xml_data->Pop("tax-area");
                        $xml_data->Pop("default-tax-rule");
                    }
                    if( $curr_rule->world_area === true ) 
                    {
                        $xml_data->Push("default-tax-rule");
                        $xml_data->Element("shipping-taxed", $curr_rule->shipping_taxed);
                        $xml_data->Element("rate", $curr_rule->tax_rate);
                        $xml_data->Push("tax-area");
                        $xml_data->EmptyElement("world-area");
                        $xml_data->Pop("tax-area");
                        $xml_data->Pop("default-tax-rule");
                    }

                }
                $xml_data->Pop("tax-rules");
                $xml_data->Pop("default-tax-table");
            }

            if( count($this->alternate_tax_tables_arr) != 0 ) 
            {
                $xml_data->Push("alternate-tax-tables");
                foreach( $this->alternate_tax_tables_arr as $curr_table ) 
                {
                    $xml_data->Push("alternate-tax-table", array( "standalone" => $curr_table->standalone, "name" => $curr_table->name ));
                    $xml_data->Push("alternate-tax-rules");
                    foreach( $curr_table->tax_rules_arr as $curr_rule ) 
                    {
                        if( $curr_rule->country_area != "" ) 
                        {
                            $xml_data->Push("alternate-tax-rule");
                            $xml_data->Element("rate", $curr_rule->tax_rate);
                            $xml_data->Push("tax-area");
                            $xml_data->EmptyElement("us-country-area", array( "country-area" => $curr_rule->country_area ));
                            $xml_data->Pop("tax-area");
                            $xml_data->Pop("alternate-tax-rule");
                        }

                        foreach( $curr_rule->state_areas_arr as $current ) 
                        {
                            $xml_data->Push("alternate-tax-rule");
                            $xml_data->Element("rate", $curr_rule->tax_rate);
                            $xml_data->Push("tax-area");
                            $xml_data->Push("us-state-area");
                            $xml_data->Element("state", $current);
                            $xml_data->Pop("us-state-area");
                            $xml_data->Pop("tax-area");
                            $xml_data->Pop("alternate-tax-rule");
                        }
                        foreach( $curr_rule->zip_patterns_arr as $current ) 
                        {
                            $xml_data->Push("alternate-tax-rule");
                            $xml_data->Element("rate", $curr_rule->tax_rate);
                            $xml_data->Push("tax-area");
                            $xml_data->Push("us-zip-area");
                            $xml_data->Element("zip-pattern", $current);
                            $xml_data->Pop("us-zip-area");
                            $xml_data->Pop("tax-area");
                            $xml_data->Pop("alternate-tax-rule");
                        }
                        for( $i = 0; $i < count($curr_rule->country_codes_arr); $i++ ) 
                        {
                            $xml_data->Push("alternate-tax-rule");
                            $xml_data->Element("rate", $curr_rule->tax_rate);
                            $xml_data->Push("tax-area");
                            $xml_data->Push("postal-area");
                            $country_code = $curr_rule->country_codes_arr[$i];
                            $postal_pattern = $curr_rule->postal_patterns_arr[$i];
                            $xml_data->Element("country-code", $country_code);
                            if( $postal_pattern != "" ) 
                            {
                                $xml_data->Element("postal-code-pattern", $postal_pattern);
                            }

                            $xml_data->Pop("postal-area");
                            $xml_data->Pop("tax-area");
                            $xml_data->Pop("alternate-tax-rule");
                        }
                        if( $curr_rule->world_area === true ) 
                        {
                            $xml_data->Push("alternate-tax-rule");
                            $xml_data->Element("rate", $curr_rule->tax_rate);
                            $xml_data->Push("tax-area");
                            $xml_data->EmptyElement("world-area");
                            $xml_data->Pop("tax-area");
                            $xml_data->Pop("alternate-tax-rule");
                        }

                    }
                    $xml_data->Pop("alternate-tax-rules");
                    $xml_data->Pop("alternate-tax-table");
                }
                $xml_data->Pop("alternate-tax-tables");
            }

            $xml_data->Pop("tax-tables");
        }

        if( $this->rounding_mode != "" && $this->rounding_rule != "" ) 
        {
            $xml_data->Push("rounding-policy");
            $xml_data->Element("mode", $this->rounding_mode);
            $xml_data->Element("rule", $this->rounding_rule);
            $xml_data->Pop("rounding-policy");
        }

        if( $this->analytics_data != "" ) 
        {
            $xml_data->Element("analytics-data", $this->analytics_data);
        }

        $xml_data->Pop("merchant-checkout-flow-support");
        $xml_data->Pop("checkout-flow-support");
        $xml_data->Pop("checkout-shopping-cart");
        return $xml_data->GetXML();
    }

    public function SetButtonVariant($variant)
    {
        switch( $variant ) 
        {
            case false:
                $this->variant = "disabled";
                break;
            case true:
            default:
                $this->variant = "text";
                break;
        }
    }

    public function CheckoutServer2Server($proxy = array(  ), $certPath = "")
    {
        ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . ".");
        require_once("includes/google_library/googlerequest.php");
        $GRequest = new GoogleRequest($this->merchant_id, $this->merchant_key, $this->server_url == "https://checkout.google.com/" ? "Production" : "sandbox", $this->currency);
        $GRequest->SetProxy($proxy);
        $GRequest->SetCertificatePath($certPath);
        return $GRequest->SendServer2ServerCart($this->GetXML());
    }

    public function CheckoutServer2ServerButton($url, $size = "large", $variant = true, $loc = "en_US", $showtext = true, $style = "trans")
    {
        switch( strtolower($size) ) 
        {
            case "medium":
                $width = "168";
                $height = "44";
                break;
            case "small":
                $width = "160";
                $height = "43";
                break;
            case "large":
            default:
                $width = "180";
                $height = "46";
                break;
        }
        if( $this->variant == false ) 
        {
            switch( $variant ) 
            {
                case false:
                    $this->variant = "disabled";
                    break;
                case true:
                default:
                    $this->variant = "text";
                    break;
            }
        }

        $data = "<div style=\"width: " . $width . "px\">";
        if( $this->variant == "text" ) 
        {
            $data .= "<div align=center><form method=\"POST\" action=\"" . $url . "\"" . ($this->googleAnalytics_id ? " onsubmit=\"setUrchinInputCode();\"" : "") . ">\n                <input type=\"image\" name=\"Checkout\" alt=\"Checkout\" \n                src=\"" . $this->server_url . "buttons/checkout.gif?merchant_id=" . $this->merchant_id . "&w=" . $width . "&h=" . $height . "&style=" . $style . "&variant=" . $this->variant . "&loc=" . $loc . "\" \n                height=\"" . $height . "\" width=\"" . $width . "\" />";
            if( $this->googleAnalytics_id ) 
            {
                $data .= "<input type=\"hidden\" name=\"analyticsdata\" value=\"\">";
            }

            $data .= "</form></div>";
            if( $this->googleAnalytics_id ) 
            {
                $data .= "<!-- Start Google analytics -->\n            <script src=\"https://ssl.google-analytics.com/urchin.js\" type=\"" . "text/javascript\">\n            </script>\n            <script type=\"text/javascript\">\n            _uacct = \"" . $this->googleAnalytics_id . "\";\n            urchinTracker();\n            </script>\n            <script src=\"https://checkout.google.com/files/digital/urchin_po" . "st.js\" type=\"text/javascript\"></script>  \n            <!-- End Google analytics -->";
            }

        }
        else
        {
            $data .= "<div><img alt=\"Checkout\" src=\"" . "" . $this->server_url . "buttons/checkout.gif?merchant_id=" . "" . $this->merchant_id . "&w=" . $width . "&h=" . $height . "&style=" . $style . "&variant=" . $this->variant . "&loc=" . $loc . "\" height=\"" . $height . "\"" . " width=\"" . $width . "\" /></div>";
        }

        $data .= "</div>";
        return $data;
    }

    public function CheckoutButtonCode($size = "large", $variant = true, $loc = "en_US", $showtext = true, $style = "trans")
    {
        switch( strtolower($size) ) 
        {
            case "smaller":
                $width = "118";
                $height = "24";
                break;
            case "medium":
                $width = "168";
                $height = "44";
                break;
            case "small":
                $width = "160";
                $height = "43";
                break;
            case "large":
            default:
                $width = "180";
                $height = "46";
                break;
        }
        if( $this->variant == false ) 
        {
            switch( $variant ) 
            {
                case false:
                    $this->variant = "disabled";
                    break;
                case true:
                default:
                    $this->variant = "text";
                    break;
            }
        }

        $data = "<div style=\"width: " . $width . "px\">";
        if( $this->variant == "text" ) 
        {
            $data .= "<div align=center><form method=\"POST\" action=\"" . $this->checkout_url . "\"" . ($this->googleAnalytics_id ? " onsubmit=\"setUrchinInputCode();\"" : "") . ">\n                <input type=\"hidden\" name=\"cart\" value=\"" . base64_encode($this->GetXML()) . "\">\n                <input type=\"hidden\" name=\"signature\" value=\"" . base64_encode($this->CalcHmacSha1($this->GetXML())) . "\"> \n                <input type=\"image\" name=\"Checkout\" alt=\"Checkout\" \n                src=\"" . $this->server_url . "buttons/checkout.gif?merchant_id=" . $this->merchant_id . "&w=" . $width . "&h=" . $height . "&style=" . $style . "&variant=" . $this->variant . "&loc=" . $loc . "\" \n                height=\"" . $height . "\" width=\"" . $width . "\" />";
            if( $this->googleAnalytics_id ) 
            {
                $data .= "<input type=\"hidden\" name=\"analyticsdata\" value=\"\">";
            }

            $data .= "</form></div>";
            if( $this->googleAnalytics_id ) 
            {
                $data .= "<!-- Start Google analytics -->\n            <script src=\"https://ssl.google-analytics.com/urchin.js\" type=\"" . "text/javascript\">\n            </script>\n            <script type=\"text/javascript\">\n            _uacct = \"" . $this->googleAnalytics_id . "\";\n            urchinTracker();\n            </script>\n            <script src=\"https://checkout.google.com/files/digital/urchin_po" . "st.js\" type=\"text/javascript\"></script>  \n            <!-- End Google analytics -->";
            }

        }
        else
        {
            if( strtolower($size) == "smaller" ) 
            {
                $buttonx1 = "checkoutMobile.gif";
            }
            else
            {
                $buttonx1 = "checkout.gif";
            }

            $data .= "<div><img alt=\"Checkout\" src=\"" . "" . $this->server_url . "buttons/" . $buttonx1 . "?merchant_id=" . "" . $this->merchant_id . "&w=" . $width . "&h=" . $height . "&style=" . $style . "&variant=" . $this->variant . "&loc=" . $loc . "\" height=\"" . $height . "\"" . " width=\"" . $width . "\" /></div>";
        }

        if( $showtext ) 
        {
            $data .= "<div align=\"center\"><a href=\"javascript:void(window.ope" . "n('http://checkout.google.com/seller/what_is_google_checkout.html'" . ",'whatischeckout','scrollbars=0,resizable=1,directories=0,height=2" . "50,width=400'));\" onmouseover=\"return window.status = 'What is G" . "oogle Checkout?'\" onmouseout=\"return window.status = ''\"><font " . "size=\"-2\">What is Google Checkout?</font></a></div>";
        }

        $data .= "</div>";
        return $data;
    }

    public function CheckoutButtonNowCode($size = "large", $variant = true, $loc = "en_US", $showtext = true, $style = "trans")
    {
        switch( strtolower($size) ) 
        {
            case "small":
                $width = "121";
                $height = "44";
                break;
            case "large":
            default:
                $width = "117";
                $height = "48";
                break;
        }
        if( $this->variant == false ) 
        {
            switch( $variant ) 
            {
                case false:
                    $this->variant = "disabled";
                    break;
                case true:
                default:
                    $this->variant = "text";
                    break;
            }
        }

        $data = "";
        if( $this->variant == "text" ) 
        {
            $data .= "<form method=\"POST\" action=\"" . $this->checkout_url . "\"" . ($this->googleAnalytics_id ? " onsubmit=\"setUrchinInputCode();\"" : "") . "><input type=\"hidden\" name=\"buyButtonCart\" value=\"" . base64_encode($this->GetXML()) . "//separator//" . base64_encode($this->CalcHmacSha1($this->GetXML())) . "\"><input type=\"image\" name=\"Checkout\" alt=\"BuyNow\" src=\"" . $this->server_url . "buttons/buy.gif?merchant_id=" . $this->merchant_id . "&w=" . $width . "&h=" . $height . "&style=" . $style . "&variant=" . $this->variant . "&loc=" . $loc . "\" height=\"" . $height . "\" width=\"" . $width . "\" />";
            if( $this->googleAnalytics_id ) 
            {
                $data .= "<input type=\"hidden\" name=\"analyticsdata\" value=\"\">";
            }

            $data .= "</form>";
            if( $this->googleAnalytics_id ) 
            {
                $data .= "<!-- Start Google analytics -->\n            <script src=\"https://ssl.google-analytics.com/urchin.js\" type=\"" . "text/javascript\">\n            </script>\n            <script type=\"text/javascript\">\n            _uacct = \"" . $this->googleAnalytics_id . "\";\n            urchinTracker();\n            </script>\n            <script src=\"https://checkout.google.com/files/digital/urchin_po" . "st.js\" type=\"text/javascript\"></script>  \n            <!-- End Google analytics -->";
            }

        }
        else
        {
            $data .= "<div><img alt=\"Checkout\" src=\"" . "" . $this->server_url . "buttons/buy.gif?merchant_id=" . "" . $this->merchant_id . "&w=" . $width . "&h=" . $height . "&style=" . $style . "&variant=" . $this->variant . "&loc=" . $loc . "\" height=\"" . $height . "\"" . " width=\"" . $width . "\" /></div>";
        }

        if( $showtext ) 
        {
            $data .= "<div align=\"center\"><a href=\"javascript:void(window.ope" . "n('http://checkout.google.com/seller/what_is_google_checkout.html'" . ",'whatischeckout','scrollbars=0,resizable=1,directories=0,height=2" . "50,width=400'));\" onmouseover=\"return window.status = 'What is G" . "oogle Checkout?'\" onmouseout=\"return window.status = ''\"><font " . "size=\"-2\">What is Google Checkout?</font></a></div>";
        }

        return $data;
    }

    public function CheckoutHTMLButtonCode($size = "large", $variant = true, $loc = "en_US", $showtext = true, $style = "trans")
    {
        switch( strtolower($size) ) 
        {
            case "medium":
                $width = "168";
                $height = "44";
                break;
            case "small":
                $width = "160";
                $height = "43";
                break;
            case "smaller":
                $width = "118";
                $height = "24";
            case "large":
            default:
                $width = "180";
                $height = "46";
                break;
        }
        if( $this->variant == false ) 
        {
            switch( $variant ) 
            {
                case false:
                    $this->variant = "disabled";
                    break;
                case true:
                default:
                    $this->variant = "text";
                    break;
            }
        }

        $data = "<div style=\"width: " . $width . "px\">";
        if( $this->variant == "text" ) 
        {
            $data .= "<div align=\"center\"><form method=\"POST\" action=\"" . $this->checkoutForm_url . "\"" . ($this->googleAnalytics_id ? " onsubmit=\"setUrchinInputCode();\"" : "") . ">";
            $request = $this->GetXML();
            require_once("xml-processing/gc_xmlparser.php");
            $xml_parser = new gc_xmlparser($request);
            $root = $xml_parser->GetRoot();
            $XMLdata = $xml_parser->GetData();
            $this->xml2html($XMLdata[$root], "", $data);
            if( $size == "smaller" ) 
            {
                $buttonx1 = "buttons/checkoutMobile.gif";
            }
            else
            {
                $buttonx1 = "buttons/checkout.gif";
            }

            $data .= "<input type=\"image\" name=\"Checkout\" alt=\"Checkout\" " . "src=\"" . $this->server_url . $buttonx1 . "?merchant_id=" . $this->merchant_id . "&w=" . $width . "&h=" . $height . "&style=" . $style . "&variant=" . $this->variant . "&loc=" . $loc . "\" \n                height=\"" . $height . "\" width=\"" . $width . "\" />";
            if( $this->googleAnalytics_id ) 
            {
                $data .= "<input type=\"hidden\" name=\"analyticsdata\" value=\"\">";
            }

            $data .= "</form></div>";
            if( $this->googleAnalytics_id ) 
            {
                $data .= "<!-- Start Google analytics -->\n            <script src=\"https://ssl.google-analytics.com/urchin.js\" type=\"" . "text/javascript\">\n            </script>\n            <script type=\"text/javascript\">\n            _uacct = \"" . $this->googleAnalytics_id . "\";\n            urchinTracker();\n            </script>\n            <script src=\"https://checkout.google.com/files/digital/urchin_po" . "st.js\" type=\"text/javascript\"></script>  \n            <!-- End Google analytics -->";
            }

        }
        else
        {
            $data .= "<div align=\"center\"><img alt=\"Checkout\" src=\"" . "" . $this->server_url . "buttons/checkout.gif?merchant_id=" . "" . $this->merchant_id . "&w=" . $width . "&h=" . $height . "&style=" . $style . "&variant=" . $this->variant . "&loc=" . $loc . "\" height=\"" . $height . "\"" . " width=\"" . $width . "\" /></div>";
        }

        if( $showtext ) 
        {
            $data .= "<div align=\"center\"><a href=\"javascript:void(window.ope" . "n('http://checkout.google.com/seller/what_is_google_checkout.html'" . ",'whatischeckout','scrollbars=0,resizable=1,directories=0,height=2" . "50,width=400'));\" onmouseover=\"return window.status = 'What is G" . "oogle Checkout?'\" onmouseout=\"return window.status = ''\"><font " . "size=\"-2\">What is Google Checkout?</font></a></div>";
        }

        $data .= "</div>";
        return $data;
    }

    public function xml2html($data, $path = "", &$rta)
    {
        foreach( $data as $tag_name => $tag ) 
        {
            if( isset($this->ignore_tags[$tag_name]) ) 
            {
                continue;
            }

            if( is_array($tag) ) 
            {
                if( !$this->is_associative_array($data) ) 
                {
                    $new_path = $path . "-" . ($tag_name + 1);
                }
                else
                {
                    if( isset($this->multiple_tags[$tag_name]) && $this->is_associative_array($tag) && !$this->isChildOf($path, $this->multiple_tags[$tag_name]) ) 
                    {
                        $tag_name .= "-1";
                    }

                    $new_path = $path . (empty($path) ? "" : ".") . $tag_name;
                }

                $this->xml2html($tag, $new_path, $rta);
            }
            else
            {
                $new_path = $path;
                if( $tag_name != "VALUE" ) 
                {
                    $new_path = $path . "." . $tag_name;
                }

                $rta .= "<input type=\"hidden\" name=\"" . $new_path . "\" value=\"" . $tag . "\"/>" . "\n";
            }

        }
    }

    public function is_associative_array($var)
    {
        return is_array($var) && !is_numeric(implode("", array_keys($var)));
    }

    public function isChildOf($path = "", $parents = array(  ))
    {
        $intersect = array_intersect(explode(".", $path), $parents);
        return !empty($intersect);
    }

    public function CheckoutAcceptanceLogo($type = 1)
    {
        switch( $type ) 
        {
            case 2:
                return "<link rel=\"stylesheet\" href=\"https://checkout.google.com/" . "seller/accept/s.css\" type=\"text/css\" media=\"screen\" /><scrip" . "t type=\"text/javascript\" src=\"https://checkout.google.com/se" . "ller/accept/j.js\"></script><script type=\"text/javascript\">sh" . "owMark(1);</script><noscript><img src=\"https://checkout.goog" . "le.com/seller/accept/images/st.gif\" width=\"92\" height=\"88\" a" . "lt=\"Google Checkout Acceptance Mark\" /></noscript>";
            case 3:
                return "<link rel=\"stylesheet\" href=\"https://checkout.google.com/" . "seller/accept/s.css\" type=\"text/css\" media=\"screen\" /><scrip" . "t type=\"text/javascript\" src=\"https://checkout.google.com/se" . "ller/accept/j.js\"></script><script type=\"text/javascript\">sh" . "owMark(2);</script><noscript><img src=\"https://checkout.goog" . "le.com/seller/accept/images/ht.gif\" width=\"182\" height=\"44\" " . "alt=\"Google Checkout Acceptance Mark\" /></noscript>";
            case 1:
            default:
                return "<link rel=\"stylesheet\" href=\"https://checkout.google.com/" . "seller/accept/s.css\" type=\"text/css\" media=\"screen\" /><scrip" . "t type=\"text/javascript\" src=\"https://checkout.google.com/se" . "ller/accept/j.js\"></script><script type=\"text/javascript\">sh" . "owMark(3);</script><noscript><img src=\"https://checkout.goog" . "le.com/seller/accept/images/sc.gif\" width=\"72\" height=\"73\" a" . "lt=\"Google Checkout Acceptance Mark\" /></noscript>";
        }
    }

    public function CalcHmacSha1($data)
    {
        $key = $this->merchant_key;
        $blocksize = 64;
        $hashfunc = "sha1";
        if( $blocksize < strlen($key) ) 
        {
            $key = pack("H*", $hashfunc($key));
        }

        $key = str_pad($key, $blocksize, chr(0));
        $ipad = str_repeat(chr(54), $blocksize);
        $opad = str_repeat(chr(92), $blocksize);
        $hmac = pack("H*", $hashfunc(($key ^ $opad) . pack("H*", $hashfunc(($key ^ $ipad) . $data))));
        return $hmac;
    }

    public function _GetBooleanValue($value, $default)
    {
        switch( strtolower($value) ) 
        {
            case "true":
                return "true";
            case "false":
                return "false";
            default:
                return $default;
        }
    }

    public function _SetBooleanValue($string, $value, $default)
    {
        $value = strtolower($value);
        if( $value == "true" || $value == "false" ) 
        {
            eval("\$this->" . $string . "=\"" . $value . "\";");
        }
        else
        {
            eval("\$this->" . $string . "=\"" . $default . "\";");
        }

    }

}


class MerchantPrivate
{
    public $data = NULL;
    public $type = "Abstract";

    public function MerchantPrivate()
    {
    }

    public function AddMerchantPrivateToXML(&$xml_data)
    {
        if( is_array($this->data) ) 
        {
            $xml_data->Push($this->type);
            $this->_recursiveAdd($xml_data, $this->data);
            $xml_data->Pop($this->type);
        }
        else
        {
            $xml_data->Element($this->type, (bool) $this->data);
        }

    }

    public function _recursiveAdd(&$xml_data, $data)
    {
        foreach( $data as $name => $value ) 
        {
            if( is_array($value) ) 
            {
                $xml_data->Push($name);
                $this->_recursiveAdd($xml_data, $name);
                $xml_data->Pop($name);
            }
            else
            {
                $xml_data->Element($name, (bool) $value);
            }

        }
    }

}


class MerchantPrivateData extends MerchantPrivate
{
    public function MerchantPrivateData($data = array(  ))
    {
        $this->data = $data;
        $this->type = "merchant-private-data";
    }

}


class MerchantPrivateItemData extends MerchantPrivate
{
    public function MerchantPrivateItemData($data = array(  ))
    {
        $this->data = $data;
        $this->type = "merchant-private-item-data";
    }

}


