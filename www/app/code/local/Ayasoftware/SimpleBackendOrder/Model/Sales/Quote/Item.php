<?php

/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleBackendOrder
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_SimpleBackendOrder_Model_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item {

    public function checkData() {
        if( ! Mage::app()->getStore()->isAdmin()) {
            return parent::checkData();
        }
        $parent = parent::checkData();
        if( ! ($item = $this->getParentItem())) {
            $item = $this;
        }
        if($item->getProductType() == 'configurable') {
            $storeId = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
            $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            $simple = Mage::getModel('catalog/product')
                    ->setStoreId($storeId)
                    ->setCustomerGroupId(Mage::getSingleton('adminhtml/session_quote')->getCustomer()->getGroupId())
                    ->load($item->getProduct()->getIdBySku($productOptions['simple_sku']));
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
        }
        return $parent;
    }

}
