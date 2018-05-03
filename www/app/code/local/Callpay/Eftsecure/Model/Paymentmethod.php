<?php

class Callpay_Eftsecure_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract {
    protected $_code  = 'eftsecure';
    protected $_formBlockType = 'eftsecure/form_eftsecure';

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('eftsecure/payment/redirect', array('_secure' => false));
    }
}