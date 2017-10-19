<?php
/**
* @copyright  Copyright (c) 2011 AITOC, Inc.
*/

/* AITOC static rewrite inserts start */
/* $meta=%default,Aitoc_Aitcg,Aitoc_Aitdownloadablefiles,Aitoc_Aitoptionstemplate,Aitoc_Aitpermissions% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitpermissions')){
    class Aitoc_Aitpreorder_Model_Rewrite_CatalogProduct_Aittmp extends Aitoc_Aitpermissions_Model_Rewrite_CatalogProduct {}
 }elseif(Mage::helper('core')->isModuleEnabled('Aitoc_Aitoptionstemplate')){
    class Aitoc_Aitpreorder_Model_Rewrite_CatalogProduct_Aittmp extends Aitoc_Aitoptionstemplate_Model_Rewrite_FrontCatalogProduct {}
 }elseif(Mage::helper('core')->isModuleEnabled('Aitoc_Aitdownloadablefiles')){
    class Aitoc_Aitpreorder_Model_Rewrite_CatalogProduct_Aittmp extends Aitoc_Aitdownloadablefiles_Model_Rewrite_FrontCatalogProduct {}
 }elseif(Mage::helper('core')->isModuleEnabled('Aitoc_Aitcg')){
    class Aitoc_Aitpreorder_Model_Rewrite_CatalogProduct_Aittmp extends Aitoc_Aitcg_Model_Rewrite_Catalog_Product {}
 }else{
    /* default extends start */
    class Aitoc_Aitpreorder_Model_Rewrite_CatalogProduct_Aittmp extends Mage_Catalog_Model_Product {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitpreorder_Model_Rewrite_CatalogProduct extends Aitoc_Aitpreorder_Model_Rewrite_CatalogProduct_Aittmp {

    /**
     * Added for compatibility with Multi-location Inventory.
     * Mage_Catalog_Model_Product::getPreorder() should respect setting
     * "Website Inventory: Use Default Values".
     *
     * @return integer
     */
    public function getPreorder()
    {
        $preorder = $this->_getPreorder();

        if (Mage::helper('aitpreorder')->checkIfMultilocationInventoryIsEnabled()) {
            $stockItem = Mage::getModel('aitquantitymanager/stock_item');
            $stockItem->loadByProduct($this);

            if ($stockItem->getUseDefaultWebsiteStock()) {
                $preorder =  Mage::helper('aitpreorder')->isPreOrder($stockItem, $this);
            }
        }

        if ($preorder) {
            $this->setData('is_salable', 1);
        }

        return $preorder;
    }

    protected function _beforeSave()
    {
        $item = $this->getStockData();
        if (isset($item['use_config_backorders']) && $item['use_config_backorders'] == '1') {
            $item['backorders'] = Mage::getStoreConfig('cataloginventory/item_options/backorders');
            $this->setStockData($item);
            $this->setPreorder('');
        }

        return parent::_beforeSave();
    }

    private function _getPreorder()
    {
        $item = $this->getStockItem();
        $preorder = Mage::helper('aitpreorder')->isPreOrder($item, $this);
        return $preorder;
    }
}
