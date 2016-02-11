<?php 

class GoogleTaxRule
{
    public $tax_rate = NULL;
    public $world_area = false;
    public $country_codes_arr = NULL;
    public $postal_patterns_arr = NULL;
    public $state_areas_arr = NULL;
    public $zip_patterns_arr = NULL;
    public $country_area = NULL;

    public function GoogleTaxRule()
    {
    }

    public function SetWorldArea($world_area = true)
    {
        $this->world_area = $world_area;
    }

    public function AddPostalArea($country_code, $postal_pattern = "")
    {
        $this->country_codes_arr[] = $country_code;
        $this->postal_patterns_arr[] = $postal_pattern;
    }

    public function SetStateAreas($areas)
    {
        if( is_array($areas) ) 
        {
            $this->state_areas_arr = $areas;
        }
        else
        {
            $this->state_areas_arr = array( $areas );
        }

    }

    public function SetZipPatterns($zips)
    {
        if( is_array($zips) ) 
        {
            $this->zip_patterns_arr = $zips;
        }
        else
        {
            $this->zip_patterns_arr = array( $zips );
        }

    }

    public function SetCountryArea($country_area)
    {
        switch( $country_area ) 
        {
            case "CONTINENTAL_48":
            case "FULL_50_STATES":
            case "ALL":
                $this->country_area = $country_area;
                break;
            default:
                $this->country_area = "";
                break;
        }
    }

}


class GoogleDefaultTaxRule extends GoogleTaxRule
{
    public $shipping_taxed = false;

    public function GoogleDefaultTaxRule($tax_rate, $shipping_taxed = "false")
    {
        $this->tax_rate = $tax_rate;
        $this->shipping_taxed = $shipping_taxed;
        $this->country_codes_arr = array(  );
        $this->postal_patterns_arr = array(  );
        $this->state_areas_arr = array(  );
        $this->zip_patterns_arr = array(  );
    }

}


class GoogleAlternateTaxRule extends GoogleTaxRule
{
    public function GoogleAlternateTaxRule($tax_rate)
    {
        $this->tax_rate = $tax_rate;
        $this->country_codes_arr = array(  );
        $this->postal_patterns_arr = array(  );
        $this->state_areas_arr = array(  );
        $this->zip_patterns_arr = array(  );
    }

}


class GoogleAlternateTaxTable
{
    public $name = NULL;
    public $tax_rules_arr = NULL;
    public $standalone = NULL;

    public function GoogleAlternateTaxTable($name = "", $standalone = "false")
    {
        if( $name != "" ) 
        {
            $this->name = $name;
            $this->tax_rules_arr = array(  );
            $this->standalone = $standalone;
        }

    }

    public function AddAlternateTaxRules($rules)
    {
        $this->tax_rules_arr[] = $rules;
    }

}


