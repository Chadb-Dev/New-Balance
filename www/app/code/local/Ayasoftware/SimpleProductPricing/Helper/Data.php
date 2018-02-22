<?php

/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleProductPricing
 * @copyright   2016 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_SimpleProductPricing_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Get Cheapest Child Price
     * @param type $product
     * @return $prices or false (if not a configurable product)
     */
    public function getCheapestChildPrice($product) {
         if($product->getTypeId() != 'configurable') {
            return false;
        }
        $saveCache = false;
        $cache =  Mage::app()->getCacheInstance();
        $storeId = Mage::app()->getStore()->getId();
        $cacheKey = $product->getId() . "_" . $storeId;
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $cacheKey = $product->getId() . "_" . $storeId . "_" . Mage::getModel('customer/session')->getCustomer()->getId();
        }
        if(intval(Mage::getStoreConfig('spp/setting/spp_cache_lifetime'),$storeId) > 0) {
            if($cache->load("product_id_prices_" . $cacheKey)) {
                $product_id_prices_cache = unserialize($cache->load("product_id_prices_" . $cacheKey));
                return $product_id_prices_cache;
            } else {
                $saveCache = true;
            }
        }
        $productIds = array();
        $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
        $childProducts = $conf->getUsedProductCollection()->addAttributeToSelect(array('msrp', 'price', 'special_price', 'status', 'special_from_date', 'special_to_date'));
        foreach($childProducts as $childProduct) {
            if( ! $childProduct->isSalable()) {
                if( ! Mage::getStoreConfig('spp/details/show',$storeId)) {
                    continue;
                }
            }
            $productIds[$childProduct->getId()] = array("finalPrice" => $childProduct->getFinalPrice(), "price" => $childProduct->getPrice());
        }
        if(empty($productIds)) {
            return false;
        }
        $productCheapestId = array_search(min($productIds), $productIds);
        $productExpensiveId = array_search(max($productIds), $productIds);
        $prices = array();
        $prices["Min"] = array("finalPrice" => $productIds[$productCheapestId]['finalPrice'], 'price' => $productIds[$productCheapestId]['price']);
        $prices["Max"] = array("finalPrice" => $productIds[$productExpensiveId]['finalPrice'], 'price' => $productIds[$productExpensiveId]['price']);
        if(intval(Mage::getStoreConfig('spp/setting/spp_cache_lifetime',$storeId)) > 0 && $saveCache) {
            $cache->save(serialize($prices), "product_id_prices_" . $cacheKey, array("product_id_prices_cache_".$product->getId()), intval(Mage::getStoreConfig('spp/setting/spp_cache_lifetime',$storeId)));
        }
        return $prices;
    }

    public function applyRulesToProduct($product) {
        $rule = Mage::getModel("catalogrule/rule");
        return $rule->calcProductPriceRule($product, $product->getPrice());
    }

    public function canApplyTierPrice($product, $qty) {
        $tierPrice = $product->getTierPrice($qty);
        if(empty($tierPrice)) {
            return false;
        }
        $price = $product->getPrice();
        if($tierPrice != $price) {
            return true;
        } else {
            return false;
        }
    }

    public function applyOptionsPrice($product, $finalPrice) {
        if($optionIds = $product->getCustomOption('option_ids')) {
            $basePrice = $finalPrice;
            foreach(explode(',', $optionIds->getValue()) as $optionId) {
                if($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                            ->setOption($option)
                            ->setConfigurationItemOption($confItemOption);
                    $finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                }
            }
        }
        return $finalPrice;
    }
    /**
     * Find Cheapest Simple Product Id
     * @param type $childProducts
     * @return int
     */
    public function findCheapestSimpleProdyctId($childProducts) {
        $childs = array();
        foreach($childProducts as $k => $childProduct) {
            $childs[$k] = $childProduct['finalPrice'];
        }
        return array_search(min($childs), $childs);
    }

    /**
     * True if the version of Magento currently being used is Enterprise Edition
     */
    public function isMageEnterprise() {
        return Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && Mage::getConfig()->getModuleConfig('Enterprise_AdminGws') && Mage::getConfig()->getModuleConfig('Enterprise_Checkout') && Mage::getConfig()->getModuleConfig('Enterprise_Customer');
    }

    /**
     * True if the version of Magento currently being used is Enterprise Edition
     */
    public function isMageCommunity() {
        return ! $this->isMageEnterprise();
    }
    /**
     * Get the id of the pre-selected associated product (in configurable associated products tab)
     * @param type $cf_product_id
     * @return boolean
     */
    public function getPreslectedId($cf_product_id) {
        $configurableProductsTable = Mage::getSingleton('core/resource')->getTableName("catalog/product_super_link");
        $configurableSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select()
                ->from(array('c' => $configurableProductsTable), 'c.product_id')
                ->where("c.parent_id = ?", $cf_product_id)
                ->where("c.preselected_id = ?", 1);
        $configurableIds = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($configurableSelect);
        if( ! empty($configurableIds)) {
            return $configurableIds[0];
        } else {
            return false;
        }
    }

}
