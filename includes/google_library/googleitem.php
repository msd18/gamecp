<?php 

class GoogleItem
{
    public $item_name = NULL;
    public $item_description = NULL;
    public $unit_price = NULL;
    public $quantity = NULL;
    public $merchant_private_item_data = NULL;
    public $merchant_item_id = NULL;
    public $tax_table_selector = NULL;
    public $email_delivery = NULL;
    public $digital_content = false;
    public $digital_description = NULL;
    public $digital_key = NULL;
    public $digital_url = NULL;
    public $item_weight = NULL;
    public $numeric_weight = NULL;

    public function GoogleItem($name, $desc, $qty, $price, $item_weight = "", $numeric_weight = "")
    {
        $this->item_name = $name;
        $this->item_description = $desc;
        $this->unit_price = $price;
        $this->quantity = $qty;
        if( $item_weight != "" && $numeric_weight !== "" ) 
        {
            switch( strtoupper($item_weight) ) 
            {
                case "KG":
                    $this->item_weight = strtoupper($item_weight);
                    break;
                case "LB":
                default:
                    $this->item_weight = "LB";
            }
            $this->numeric_weight = (double) $numeric_weight;
        }

    }

    public function SetMerchantPrivateItemData($private_data)
    {
        $this->merchant_private_item_data = $private_data;
    }

    public function SetMerchantItemId($item_id)
    {
        $this->merchant_item_id = $item_id;
    }

    public function SetTaxTableSelector($tax_selector)
    {
        $this->tax_table_selector = $tax_selector;
    }

    public function SetEmailDigitalDelivery($email_delivery = "false")
    {
        $this->digital_url = "";
        $this->digital_key = "";
        $this->digital_description = "";
        $this->email_delivery = $email_delivery;
        $this->digital_content = true;
    }

    public function SetURLDigitalContent($digital_url, $digital_key, $digital_description)
    {
        $this->digital_url = $digital_url;
        $this->digital_key = $digital_key;
        $this->digital_description = $digital_description;
        $this->email_delivery = "false";
        $this->digital_content = true;
    }

}


