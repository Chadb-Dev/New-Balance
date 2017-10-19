<?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 */
class Aitoc_Aitpreorder_Model_Rewrite_StockItem extends Mage_CatalogInventory_Model_Stock_Item
{
    public function verifyStock($qty = null)
    {
        if ($qty === null) {
            $qty = $this->getQty();
        }

        if (($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_NO
                || $this->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO
                || !Mage::helper('aitpreorder')->isPreOrder($this, $this->getProduct())
            ) && $qty <= $this->getMinQty()) {
            return false;
        }
        return true;
    }
    
    public function verifyNotification($qty = null)
    {
        if ($qty === null) {
            $qty = $this->getQty();
        }
        return (float)$qty < $this->getNotifyStockQty();
    }

    /**
     * If stock item were not applied with product we will return product id
     *
     * @return object|int
     */
    private function _getAitProduct()
    {
        $product = $this->getProduct();
        if(!$product) {
            $product = $this->getProductId();
        }
        return $product;
    }

    /**
     * Override parent. Retrieve Stock Availability
     *
     * @return bool|int
     */
    public function getIsInStock()
    {
        $stock = parent::getIsInStock();
        if(!$stock && Mage::helper('aitpreorder')->isBackstockPreorderAllowed($this->_getAitProduct())) {
            $stock = true;
        }
        return $stock;
    }

    /**
     * Override parent. Check quantity
     *
     * @param   decimal $qty
     * @exception Mage_Core_Exception
     * @return  bool
     */
    public function checkQty($qty)
    {
        $qty = parent::checkQty($qty);
        if(!$qty && Mage::helper('aitpreorder')->isBackstockPreorderAllowed($this->_getAitProduct())) {
            $qty = true;
        }
        return $qty;
    }

    protected function _beforeSave()
    {
        // see if quantity is defined for this item type
        $typeId = $this->getTypeId();
        if ($productTypeId = $this->getProductTypeId()) {
            $typeId = $productTypeId;
        }

        $isQty = Mage::helper('catalogInventory')->isQty($typeId);

        if ($isQty) {
            if (!$this->verifyStock()) {
                if ($this->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO) {
                    $preorder = 1;
                }
                $this->setIsInStock(false)->setStockStatusChangedAutomaticallyFlag(true);

            } elseif ($this->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO) {
                $preorder = 0;
            }

            if (isset($preorder)) {
                $product = $this->getProduct();
                $preorderProduct = $product->getPreorder();
                if (!isset($preorderProduct) || $preorderProduct != $preorder) {
                    $product->setStoreId(0)->setPreorder($preorder);
                    $product->getResource()->saveAttribute($product, 'preorder');
                }
            }

            // if qty is below notify qty, update the low stock date to today date otherwise set null
            $this->setLowStockDate(null);
            if ($this->verifyNotification()) {
                $this->setLowStockDate(Mage::app()->getLocale()->date(null, null, null, false)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                );
            }

            $this->setStockStatusChangedAutomatically(0);
            if ($this->hasStockStatusChangedAutomaticallyFlag()) {
                $this->setStockStatusChangedAutomatically((int)$this->getStockStatusChangedAutomaticallyFlag());
            }
        } else {
            $this->setQty(0);
        }

        return parent::_beforeSave();
    }

    public function getProduct()
    {
        $product = parent::getProduct();
        if (!$product) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            $this->setProduct($product);
        }
        return $product;
    }
}
