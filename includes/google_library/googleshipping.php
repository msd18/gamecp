<?php 

class GoogleFlatRateShipping
{
    public $price = NULL;
    public $name = NULL;
    public $type = "flat-rate-shipping";
    public $shipping_restrictions = NULL;

    public function GoogleFlatRateShipping($name, $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function AddShippingRestrictions($restrictions)
    {
        $this->shipping_restrictions = $restrictions;
    }

}


class GoogleMerchantCalculatedShipping
{
    public $price = NULL;
    public $name = NULL;
    public $type = "merchant-calculated-shipping";
    public $shipping_restrictions = NULL;
    public $address_filters = NULL;

    public function GoogleMerchantCalculatedShipping($name, $price)
    {
        $this->price = $price;
        $this->name = $name;
    }

    public function AddShippingRestrictions($restrictions)
    {
        $this->shipping_restrictions = $restrictions;
    }

    public function AddAddressFilters($filters)
    {
        $this->address_filters = $filters;
    }

}


class GoogleCarrierCalculatedShipping
{
    public $name = NULL;
    public $type = "carrier-calculated-shipping";
    public $CarrierCalculatedShippingOptions = array(  );
    public $ShippingPackage = NULL;

    public function GoogleCarrierCalculatedShipping($name)
    {
        $this->name = $name;
    }

    public function addCarrierCalculatedShippingOptions($option)
    {
        $this->CarrierCalculatedShippingOptions[] = $option;
    }

    public function addShippingPackage($package)
    {
        $this->ShippingPackage = $package;
    }

}


class GoogleCarrierCalculatedShippingOption
{
    public $price = NULL;
    public $shipping_company = NULL;
    public $shipping_type = NULL;
    public $carrier_pickup = NULL;
    public $additional_fixed_charge = NULL;
    public $additional_variable_charge_percent = NULL;

    public function GoogleCarrierCalculatedShippingOption($price, $shipping_company, $shipping_type, $additional_fixed_charge = 0, $additional_variable_charge_percent = 0, $carrier_pickup = "DROP_OFF")
    {
        $this->price = (double) $price;
        $this->shipping_company = $shipping_company;
        $this->shipping_type = trim($shipping_type);
        switch( strtoupper($carrier_pickup) ) 
        {
            case "DROP_OFF":
            case "REGULAR_PICKUP":
            case "SPECIAL_PICKUP":
                $this->carrier_pickup = $carrier_pickup;
                break;
            default:
                $this->carrier_pickup = "DROP_OFF";
        }
        if( $additional_fixed_charge ) 
        {
            $this->additional_fixed_charge = (double) $additional_fixed_charge;
        }

        if( $additional_variable_charge_percent ) 
        {
            $this->additional_variable_charge_percent = (double) $additional_variable_charge_percent;
        }

    }

}


class GoogleShippingPackage
{
    public $width = NULL;
    public $length = NULL;
    public $height = NULL;
    public $unit = NULL;
    public $ship_from = NULL;
    public $delivery_address_category = NULL;

    public function GoogleShippingPackage($ship_from, $width, $length, $height, $unit, $delivery_address_category = "RESIDENTIAL")
    {
        $this->width = (double) $width;
        $this->length = (double) $length;
        $this->height = (double) $height;
        switch( strtoupper($unit) ) 
        {
            case "CM":
                $this->unit = strtoupper($unit);
                break;
            case "IN":
            default:
                $this->unit = "IN";
        }
        $this->ship_from = $ship_from;
        switch( strtoupper($delivery_address_category) ) 
        {
            case "COMMERCIAL":
                $this->delivery_address_category = strtoupper($delivery_address_category);
                break;
            case "RESIDENTIAL":
            default:
                $this->delivery_address_category = "RESIDENTIAL";
        }
    }

}


class GoogleShipFrom
{
    public $id = NULL;
    public $city = NULL;
    public $country_code = NULL;
    public $postal_code = NULL;
    public $region = NULL;

    public function GoogleShipFrom($id, $city, $country_code, $postal_code, $region)
    {
        $this->id = $id;
        $this->city = $city;
        $this->country_code = $country_code;
        $this->postal_code = $postal_code;
        $this->region = $region;
    }

}


class GoogleShippingFilters
{
    public $allow_us_po_box = true;
    public $allowed_restrictions = false;
    public $excluded_restrictions = false;
    public $allowed_world_area = false;
    public $allowed_country_codes_arr = NULL;
    public $allowed_postal_patterns_arr = NULL;
    public $allowed_country_area = NULL;
    public $allowed_state_areas_arr = NULL;
    public $allowed_zip_patterns_arr = NULL;
    public $excluded_country_codes_arr = NULL;
    public $excluded_postal_patterns_arr = NULL;
    public $excluded_country_area = NULL;
    public $excluded_state_areas_arr = NULL;
    public $excluded_zip_patterns_arr = NULL;

    public function GoogleShippingFilters()
    {
        $this->allowed_country_codes_arr = array(  );
        $this->allowed_postal_patterns_arr = array(  );
        $this->allowed_state_areas_arr = array(  );
        $this->allowed_zip_patterns_arr = array(  );
        $this->excluded_country_codes_arr = array(  );
        $this->excluded_postal_patterns_arr = array(  );
        $this->excluded_state_areas_arr = array(  );
        $this->excluded_zip_patterns_arr = array(  );
    }

    public function SetAllowUsPoBox($allow_us_po_box = true)
    {
        $this->allow_us_po_box = $allow_us_po_box;
    }

    public function SetAllowedWorldArea($world_area = true)
    {
        $this->allowed_restrictions = true;
        $this->allowed_world_area = $world_area;
    }

    public function AddAllowedPostalArea($country_code, $postal_pattern = "")
    {
        $this->allowed_restrictions = true;
        $this->allowed_country_codes_arr[] = $country_code;
        $this->allowed_postal_patterns_arr[] = $postal_pattern;
    }

    public function SetAllowedCountryArea($country_area)
    {
        switch( $country_area ) 
        {
            case "CONTINENTAL_48":
            case "FULL_50_STATES":
            case "ALL":
                $this->allowed_country_area = $country_area;
                $this->allowed_restrictions = true;
                break;
            default:
                $this->allowed_country_area = "";
                break;
        }
    }

    public function SetAllowedStateAreas($areas)
    {
        $this->allowed_restrictions = true;
        $this->allowed_state_areas_arr = $areas;
    }

    public function AddAllowedStateArea($area)
    {
        $this->allowed_restrictions = true;
        $this->allowed_state_areas_arr[] = $area;
    }

    public function SetAllowedZipPatterns($zips)
    {
        $this->allowed_restrictions = true;
        $this->allowed_zip_patterns_arr = $zips;
    }

    public function AddAllowedZipPattern($zip)
    {
        $this->allowed_restrictions = true;
        $this->allowed_zip_patterns_arr[] = $zip;
    }

    public function AddExcludedPostalArea($country_code, $postal_pattern = "")
    {
        $this->excluded_restrictions = true;
        $this->excluded_country_codes_arr[] = $country_code;
        $this->excluded_postal_patterns_arr[] = $postal_pattern;
    }

    public function SetExcludedStateAreas($areas)
    {
        $this->excluded_restrictions = true;
        $this->excluded_state_areas_arr = $areas;
    }

    public function AddExcludedStateArea($area)
    {
        $this->excluded_restrictions = true;
        $this->excluded_state_areas_arr[] = $area;
    }

    public function SetExcludedZipPatternsStateAreas($zips)
    {
        $this->excluded_restrictions = true;
        $this->excluded_zip_patterns_arr = $zips;
    }

    public function SetAllowedZipPatternsStateArea($zip)
    {
        $this->excluded_restrictions = true;
        $this->excluded_zip_patterns_arr[] = $zip;
    }

    public function SetExcludedCountryArea($country_area)
    {
        switch( $country_area ) 
        {
            case "CONTINENTAL_48":
            case "FULL_50_STATES":
            case "ALL":
                $this->excluded_country_area = $country_area;
                $this->excluded_restrictions = true;
                break;
            default:
                $this->excluded_country_area = "";
                break;
        }
    }

}


class GooglePickUp
{
    public $price = NULL;
    public $name = NULL;
    public $type = "pickup";

    public function GooglePickUp($name, $price)
    {
        $this->price = $price;
        $this->name = $name;
    }

}


