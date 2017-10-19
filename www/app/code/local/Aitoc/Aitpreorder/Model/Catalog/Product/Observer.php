<?php

class Aitoc_Aitpreorder_Model_Catalog_Product_Observer {
    
    private $_regexp = null;
    
    public function onIsSalableAfter($observer)
    {
        $salable = $observer->getSalable();
        if($salable->getIsSalable()) {
            // we don't care about products that are already salable
            return;
        }
        $product = $salable->getProduct();
        if(Mage::helper('aitpreorder')->isBackstockPreorderAllowed($product)) {
            $salable->setIsSalable(true);
            return;
        }
        if($product->getTypeID() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $product->isInStock())
        {
            $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
            $simple_collection = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
            foreach($simple_collection as $simple)
            {
                if(Mage::helper('aitpreorder')->isBackstockPreorderAllowed($simple)) {
                    $salable->setIsSalable(true);
                    return;
                }
            }
        }

        if($product->getTypeID() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE && $product->isInStock())
        {
            $bundleProduct = Mage::getModel('catalog/product')->load($product->getId());
            $simple_collection = $product->getTypeInstance(true)
                ->getSelectionsCollection(
                    $product->getTypeInstance(true)
                            ->getOptionsIds($bundleProduct), $bundleProduct);
 
            foreach($simple_collection as $simple)
            {
                if(Mage::helper('aitpreorder')->isBackstockPreorderAllowed($simple)) {
                    $salable->setIsSalable(true);
                    return;
                }
            }
        }

        if($product->getTypeID() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED && $product->isInStock())
        {
            $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            foreach($associatedProducts as $simple)
            {
                if(Mage::helper('aitpreorder')->isBackstockPreorderAllowed($simple)) {
                    $salable->setIsSalable(true);
                    return;
                }
            }
        }
    }



    public function toHtmlAfter($observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Catalog_Block_Product_View) {
            Mage::getModel('aitpreorder/catalog_product_view')
                ->initBlock($block)
                ->applyHtml($observer->getTransport());
        }
        if ($block instanceof Mage_Catalog_Block_Product_Price) {
            Mage::getModel('aitpreorder/catalog_product_price')
                ->initBlock($block)
                ->applyHtml($observer->getTransport());
        }
        if ($block instanceof Mage_Catalog_Block_Product_View_Type_Configurable || $block instanceof Mage_Catalog_Block_Product_View_Type_Grouped) {
            Mage::getModel('aitpreorder/catalog_product_viewTypeConfigurable')
                ->initBlock($block)
                ->applyHtml($observer->getTransport());
        }

        if ($block instanceof Aitoc_Aitpreorder_Block_Rewrite_BundleCatalogProductViewTypeBundle) {
            Mage::getModel('aitpreorder/catalog_product_viewTypeBundle')
                ->initBlock($block)
                ->applyHtml($observer->getTransport());
        }
        
        if (
            $block instanceof Mage_Catalog_Block_Product_View_Type_Simple   ||
            $block instanceof Mage_Downloadable_Block_Catalog_Product_View_Type
           ) {
            Mage::getModel('aitpreorder/catalog_product_viewTypeSimple')
                ->initBlock($block)
                ->applyHtml($observer->getTransport());
        }

        if ($block instanceof Mage_Catalog_Block_Product_List) {
            return $this->_applyProductCollection($block->getLoadedProductCollection(), $observer->getTransport());
        }
        
    }
    
    protected function _applyProductCollection($collection, $transport)
    {
        $html = $transport->getHtml();
        Mage::getSingleton('aitpreorder/stockloader')->applyStockToProductCollection($collection);
        $catalogHelper = Mage::helper('catalog');
        foreach ($collection as $_product) {
            if ($_product->getPreorder()) {
                $match = preg_match($this->_getRegExp($_product->getId()), $html, $matches);
                if ($match) {
                    $replace = str_replace($catalogHelper->__('Add to Cart'), $catalogHelper->__('Pre-Order'), $matches[0]);
                    $html = str_replace($matches[0], $replace, $html);
                }
            }
        }
        $transport->setHtml($html);
        
    }
    
    private function _getRegExp($product_id)
    {
        if(is_null($this->_regexp)) {
            $this->_regexp = trim(Mage::getStoreConfig('cataloginventory/aitpreorder/product_list_regexp'));
            if(!$this->_regexp) {
                $this->_regexp = '!\<button([^\>]*)product\/{{product_id}}\/(.*)\<\/button\>!';
            }
        }
        return str_replace('{{product_id}}', $product_id, $this->_regexp).'Uis'; //apply flags
    }
    
}