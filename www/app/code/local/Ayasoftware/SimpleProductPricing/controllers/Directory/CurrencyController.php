<?php
/**
* Modify price using events.
* @category    Ayasoftware
* @package     Ayasoftware_SimpleProductPricing
* @author      EL HASSAN MATAR <support@ayasoftware.com>
*/
require_once "Mage/Directory/controllers/CurrencyController.php"; 
class Ayasoftware_SimpleProductPricing_Directory_CurrencyController extends Mage_Directory_CurrencyController{
    public function switchAction()
    {
        if ($curency = (string) $this->getRequest()->getParam('currency')) {
            Mage::app()->getStore()->setCurrentCurrencyCode($curency);
        }
        Mage::dispatchEvent('update_price_on_currency_change', array());
        $this->_redirectReferer(Mage::getBaseUrl());
    }
}