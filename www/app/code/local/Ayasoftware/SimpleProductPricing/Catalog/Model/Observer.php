<?php

/**
 * Modify price using events.
 * @category    Ayasoftware
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @package     Ayasoftware_SimpleProductPricing
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_SimpleProductPricing_Catalog_Model_Observer extends Mage_Core_Model_Abstract {

    /**
     * This event is needed to update the price in cart when re-configuring the configurable product. 
     * @param Varien_Event_Observer $observer
     */
    public function updateCart(Varien_Event_Observer $observer) {
        $item = $observer->getItem();
        if($item->getParentItem()) {
            $item = $item->getParentItem();
            $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
            if( ! $product->isConfigurable() || ! $product->getUseSimpleProductPricing()) {
                return;
            }
            $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            $simple = Mage::getModel('catalog/product')->load($item->getProduct()->getIdBySku($productOptions['simple_sku']));
            $simplePrice = $simple->getFinalPrice();
            if($simple->getCustomerGroupId()) {
                $simplePrice = $simple->getGroupPrice();
            }
            if($simple->getTierPrice($item->getQty())) {
                $simplePrice = min($simple->getTierPrice($item->getQty()), $simplePrice);
            }
            
            if(Mage::helper('spp')->applyRulesToProduct($simple)) {
                $rulePrice = min(Mage::helper('spp')->applyRulesToProduct($simple), $simplePrice);
                if(Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $rulePrice)) {
                    $simplePrice = Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $rulePrice);
                }
            } else {
                if(Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $simplePrice)) {
                    $simplePrice = Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $simplePrice);
                }
            }
            $simplePrice = Mage::helper('core')->currency($simplePrice, false, false);
            $item->setCustomPrice($simplePrice);
            $item->setOriginalCustomPrice($simplePrice);
            $item->getProduct()->setIsSuperMode(true);
            $item->save();
        }
    }

    /**
     * Update all cart items - if price change
     * @param Varien_Event_Observer $observer
     */
    public function updateSimpleProductPricing(Varien_Event_Observer $observer) {

        foreach(Mage::getModel("checkout/cart")->getItems() as $item /* @var $item Mage_Sales_Model_Quote_Item */) {
            if($item->getParentItem()) {
                $item = $item->getParentItem();
                $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                if( ! $product->isConfigurable() || ! Mage::getStoreConfig('spp/setting/enableModule', Mage::app()->getStore()->getId()) || ( ! $product->getUseSimpleProductPricing() && ! Mage::getStoreConfig('spp/setting/useglobally', Mage::app()->getStore()->getId()))) {
                    continue;
                }
                $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                $simple = Mage::getModel('catalog/product')->load($item->getProduct()->getIdBySku($productOptions['simple_sku']));
                $simplePrice = $simple->getFinalPrice();
                if($simple->getCustomerGroupId()) {
                    $simplePrice = $simple->getGroupPrice();
                }
                if($simple->getTierPrice($item->getQty())) {
                    $simplePrice = min($simple->getTierPrice($item->getQty()), $simplePrice);
                }
                // if simple product has a special price, then use the
                // minimum of the previous price and special price
                if($simple->special_price) {
                    $simplePrice = min($simple->getFinalPrice(), $simplePrice);
                }
                if(Mage::helper('spp')->applyRulesToProduct($simple)) {
                    $rulePrice = min(Mage::helper('spp')->applyRulesToProduct($simple), $simplePrice);
                    if(Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $rulePrice)) {
                        $simplePrice = Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $rulePrice);
                    }
                } else {
                    if(Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $simplePrice)) {
                        $simplePrice = Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $simplePrice);
                    }
                }
                $simplePrice = Mage::helper('core')->currency($simplePrice, false, false);
                $item->setCustomPrice($simplePrice);
                $item->setOriginalCustomPrice($simplePrice);
                $item->getProduct()->setIsSuperMode(true);
                $item->save();
            }
        }
    }

    /**
     * Update the  price of the product in cart based on Ordered Options
     * @param Varien_Event_Observer $obs
     */
    public function useSimpleProductPricing(Varien_Event_Observer $obs) {

        $item = $obs->getQuoteItem();
        if($item->getParentItem()) {
            $item = $item->getParentItem();
            if( ! $item->getProduct()->isConfigurable()) {
                return;
            }
            if( ! $item->getProduct()->getUseSimpleProductPricing() && ! Mage::getStoreConfig('spp/setting/useglobally', Mage::app()->getStore()->getId())) {
                return;
            }
            $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            $simple = Mage::getModel('catalog/product')->load($item->getProduct()->getIdBySku($productOptions['simple_sku']));
            $simplePrice = $simple->getFinalPrice();
            if($simple->getCustomerGroupId()) {
                $simplePrice = $simple->getGroupPrice();
            }
            if($simple->getTierPrice($item->getQty())) {
                $simplePrice = min($simple->getTierPrice($item->getQty()), $simplePrice);
            }

            if(Mage::helper('spp')->applyRulesToProduct($simple)) {
                $rulePrice = min(Mage::helper('spp')->applyRulesToProduct($simple), $simplePrice);
                if(Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $rulePrice)) {
                    $simplePrice = Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $rulePrice);
                }
            } else {
                if(Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $simplePrice)) {
                    $simplePrice = Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $simplePrice);
                }
            }
            $simplePrice = Mage::helper('core')->currency($simplePrice, false, false);
            $item->setCustomPrice($simplePrice);
            $item->setOriginalCustomPrice($simplePrice);
            $item->getProduct()->setIsSuperMode(true);
        }
    }

    /**
     * added to avoid loading product in Ayasoftware_SimpleProductPricing_Catalog_Block_Product_View_Type_Configurable::getJsonConfig() in foreach
     * @param Varien_Event_Observer $event
     */
    public function addEveythingCollection(Varien_Event_Observer $event) {
        $collection = $event->getCollection();
        if($collection instanceof Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection) {
            $collection->addAttributeToSelect('*');
        }
    }

    /**
     * Change prices on the cart base on the currency change.
     * @param Varien_Event_Observer $observer
     * @return type
     */
    public function changePriceBasedOnCurrencyChange(Varien_Event_Observer $observer) {

        foreach(Mage::getModel("checkout/cart")->getItems() as $item /* @var $item Mage_Sales_Model_Quote_Item */) {
            if($item->getParentItem()) {
                $item = $item->getParentItem();
                $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                if( ! $product->isConfigurable() || ! $product->getUseSimpleProductPricing()) {
                    continue;
                }
                $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                $simple = Mage::getModel('catalog/product')->load($item->getProduct()->getIdBySku($productOptions['simple_sku']));
                $simplePrice = $simple->getFinalPrice();
                if($simple->getCustomerGroupId()) {
                    $simplePrice = $simple->getGroupPrice();
                }
                if($simple->getTierPrice($item->getQty())) {
                    $simplePrice = $simple->getTierPrice($item->getQty());
                }
                if(Mage::helper('spp')->applyRulesToProduct($simple)) {
                    $rulePrice = Mage::helper('spp')->applyRulesToProduct($simple);
                    if(Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $rulePrice)) {
                        $simplePrice = Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $rulePrice);
                        $simplePrice = Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $simplePrice);
                    }
                } else {
                    if(Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $simplePrice)) {
                        $simplePrice = Mage::helper('spp')->applyOptionsPrice($item->getProduct(), $simplePrice);
                    }
                }
                $simplePrice = Mage::helper('core')->currency($simplePrice, false, false);
                $item->setCustomPrice($simplePrice);
                $item->setOriginalCustomPrice($simplePrice);
                $item->getProduct()->setIsSuperMode(true);
                $item->save();
            }
        }
    }

    /**
     * Remove any saved cache reference for the parent of the selected 
     * simple product. 
     * @param Varien_Event_Observer $observer
     */
    public function removeCacheKey(Varien_Event_Observer $observer) {
        if(intval(Mage::getStoreConfig('spp/setting/spp_cache_lifetime')) > 0) {
            $product = $observer->getEvent()->getProduct();
            if($product->hasDataChanges()) {
                if($product->getTypeId() == "simple") {
                    $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
                    foreach($parentIds as $parentId) {
                        Mage::app()->getCacheInstance()->clean(array("jsonconfig_cache_" . $parentId));
                        Mage::app()->getCacheInstance()->clean(array("product_id_prices_cache_" . $parentId));
                    }
                }
            }
        }
    }

    /**
     * flush the cache, when you change the extension configuration area (triggered only when the extension 
     * cache life time is set to something > 0
     * @param Varien_Event_Observer $observer
     */
    public function flushCache(Varien_Event_Observer $observer) {
        if(intval(Mage::getStoreConfig('spp/setting/spp_cache_lifetime')) > 0) {
            Mage::app()->getCacheInstance()->flush();
        }
    }

    /**
     * set prices from the cheapeast simple product when the event catalog_product_load_after
     * is fired. 
     * @param Varien_Event_Observer $event
     * @return \Ayasoftware_SimpleProductPricing_Catalog_Model_Observer
     */
    public function setCheapestPrice(Varien_Event_Observer $event) {
        $product = $event->getEvent()->getProduct();
        $storeId = Mage::app()->getStore()->getId();
        if( ! Mage::getStoreConfig('spp/setting/enableModule', $storeId)) {
            return $this;
        }
        if( ! $product->getUseSimpleProductPricing() && ! Mage::getStoreConfig('spp/setting/useglobally', $storeId)) {
            return $this;
        }
        if( ! Mage::getStoreConfig('spp/details/showfromprice', $storeId)) {
            return $this;
        }
        if($product->getTypeId() == 'configurable') {
            if($prices = Mage::helper('spp')->getCheapestChildPrice($product)) {
                $product->setPrice($prices['Min']['price']);
                $product->setFinalPrice($prices['Min']['finalPrice']);
            }
        }
        return $this;
    }

}
