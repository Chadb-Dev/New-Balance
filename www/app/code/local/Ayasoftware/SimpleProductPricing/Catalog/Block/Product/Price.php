<?php

/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleProductPricing
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_SimpleProductPricing_Catalog_Block_Product_Price extends Mage_Catalog_Block_Product_Price {

    protected $store_id;
    public function __construct() {
        $this->store_id = Mage::app()->getStore()->getId();
        return parent::__construct();
    }
    public function getDisplayMinimalPrice() {
        if( ! Mage::getStoreConfig('spp/setting/enableModule', $this->store_id)) {
            return parent::getDisplayMinimalPrice();
        }
        $product = $this->getProduct();
        if(is_object($product) && $product->isConfigurable()) {
            return false;
        }
        return parent::getDisplayMinimalPrice();
    }

    public function _toHtml() {
        if( ! Mage::getStoreConfig('spp/setting/enableModule', $this->store_id)) {
            return parent::_toHtml();
        }
        $product = $this->getProduct();
        if( ! $product->getUseSimpleProductPricing() && ! Mage::getStoreConfig('spp/setting/useglobally', $this->store_id)) {
            if($product->special_price) {
                $product->setFinalPrice($product->special_price);
            }
            return parent::_toHtml();
        }
        if( ! Mage::getStoreConfig('spp/details/showfromprice', $this->store_id)) {
            return parent::_toHtml();
        }
        if(Mage::registry('current_product')){
            return parent::_toHtml();
        }
        $prices = array();
        $htmlToInsertAfter = '<div class="price-box">';

        if($this->getTemplate() == 'catalog/product/price.phtml') {
            if(is_object($product) && $product->isConfigurable()) {
                $extraHtml = '<span class="label" id="configurable-price-from-'
                        . $product->getId()
                        . $this->getIdSuffix()
                        . '"><span class="configurable-price-from-label">';
               if( ! Mage::registry('current_product') && Mage::app()->getRequest()->getControllerName() != 'result') {
                    if($product->getMaxPrice() != $product->getIndexedPrice()) {
                        $extraHtml .= $this->__(Mage::getStoreConfig('spp/setting/spp_from', $this->store_id));
                    }
                    $product->setPrice($product->getIndexedPrice());
                    $extraHtml .= '</span></span>';
                    $priceHtml = parent::_toHtml();
                    #manually insert extra html needed by the extension into the normal price html
                    return substr_replace($priceHtml, $extraHtml, strpos($priceHtml, $htmlToInsertAfter) + strlen($htmlToInsertAfter), 0);
                } else {
                    if($prices = Mage::helper('spp')->getCheapestChildPrice($product)) {
                        if($prices['Min']['finalPrice'] != $prices['Max']['finalPrice']) {
                            $extraHtml = '<span class="label" id="configurable-price-from-'
                                    . $product->getId()
                                    . $this->getIdSuffix()
                                    . '"><span class="configurable-price-from-label">';
                            $extraHtml .= $this->__(Mage::getStoreConfig('spp/setting/spp_from'));
                            $extraHtml .= '</span></span>';
                        }
                        $product->setPrice($prices['Min']['price']);
                        $product->setFinalPrice($prices['Min']['finalPrice']);
                        $extraHtml .= '</span></span>';
                        $priceHtml = parent::_toHtml();
                        #manually insert extra html needed by the extension into the normal price html
                        return substr_replace($priceHtml, $extraHtml, strpos($priceHtml, $htmlToInsertAfter) + strlen($htmlToInsertAfter), 0);
                    }
                }
                return parent::_toHtml();
            }
        }
        return parent::_toHtml();
    }

}
