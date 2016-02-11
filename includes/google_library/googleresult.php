<?php 

class GoogleResult
{
    public $shipping_name = NULL;
    public $address_id = NULL;
    public $shippable = NULL;
    public $ship_price = NULL;
    public $tax_amount = NULL;
    public $coupon_arr = array(  );
    public $giftcert_arr = array(  );

    public function GoogleResult($address_id)
    {
        $this->address_id = $address_id;
    }

    public function SetShippingDetails($name, $price, $shippable = "true")
    {
        $this->shipping_name = $name;
        $this->ship_price = $price;
        $this->shippable = $shippable;
    }

    public function SetTaxDetails($amount)
    {
        $this->tax_amount = $amount;
    }

    public function AddCoupons($coupon)
    {
        $this->coupon_arr[] = $coupon;
    }

    public function AddGiftCertificates($gift)
    {
        $this->giftcert_arr[] = $gift;
    }

}


class GoogleCoupons
{
    public $coupon_valid = NULL;
    public $coupon_code = NULL;
    public $coupon_amount = NULL;
    public $coupon_message = NULL;

    public function googlecoupons($valid, $code, $amount, $message)
    {
        $this->coupon_valid = $valid;
        $this->coupon_code = $code;
        $this->coupon_amount = $amount;
        $this->coupon_message = $message;
    }

}


class GoogleGiftcerts
{
    public $gift_valid = NULL;
    public $gift_code = NULL;
    public $gift_amount = NULL;
    public $gift_message = NULL;

    public function googlegiftcerts($valid, $code, $amount, $message)
    {
        $this->gift_valid = $valid;
        $this->gift_code = $code;
        $this->gift_amount = $amount;
        $this->gift_message = $message;
    }

}


