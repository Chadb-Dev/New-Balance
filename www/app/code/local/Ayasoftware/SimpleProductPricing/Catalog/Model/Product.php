<?php

/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleProductPricing
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_SimpleProductPricing_Catalog_Model_Product extends Mage_Catalog_Model_Product {

    /**
     * Check is product available for sale
     *
     * @return bool
     */
    public function isSalable() {
        if( ! $this->isConfigurable()) {
            return parent::isSalable();
        }
        if(Mage::getStoreConfig('spp/setting/enableModule', Mage::app()->getStore()->getId())) {
            if( ! Mage::getStoreConfig('spp/details/show', Mage::app()->getStore()->getId())) {
                return parent::isSalable();
            }
            if($this->getUseSimpleProductPricing() || Mage::getStoreConfig('spp/setting/useglobally', Mage::app()->getStore()->getId())) {
                return true;
            } else {
                return parent::isSalable();
            }
        }
        return parent::isSalable();
    }

}
