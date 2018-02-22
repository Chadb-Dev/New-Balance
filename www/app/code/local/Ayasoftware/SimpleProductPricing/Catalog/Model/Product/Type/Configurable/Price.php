<?php
/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleProductPricing
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_SimpleProductPricing_Catalog_Model_Product_Type_Configurable_Price extends Mage_Catalog_Model_Product_Type_Configurable_Price {
    //Force tier pricing to be empty for configurable products that use simple product pricing. 
    public function getTierPrice($qty = null, $product) {
        if(!$product->getUseSimpleProductPricing() && ! Mage::getStoreConfig('spp/setting/useglobally', Mage::app()->getStore()->getId())) {
               return parent::getTierPrice($qty,$product);
        }
        return array();
    }
}