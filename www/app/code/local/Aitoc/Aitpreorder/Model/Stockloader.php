<?php
/**
 * Singleton
 * Should be used like Mage::getSingleton('aitpreorder/stockloader')->applyStockToProduct($product);
 * instead of Mage::getModel('catalog/product')->load($id) to prevent insufficent loadings
 */
class Aitoc_Aitpreorder_Model_Stockloader extends Mage_Core_Model_Abstract
{ 
    /**
     * @var array product_id -> product_stock model 
     */
    private $_productItems = array();
    
    /**
     * @param object $product Mage_Catalog_Model_Product
     * @param bool $strict
     *
     * @return Aitoc_Aitpreorder_Model_Stockloader
     */
    public function applyStockToProduct($product)
    {
        $this->applyStockToProductCollection(array($product->getId() => $product));
        return $this;
    }
    
    /**
     * Validate if products in collection or array have stock item that is used to validate Pre-Order status and load it if it's not found
     * 
     * @param object|array $collection Mage_Catalog_Model_Resource_Product_Collection
     * @param bool $strict
     */
    public function applyStockToProductCollection($collection)
    {
        $ids = array();
        foreach ($collection as $_product) {
            $item = $_product->getStockItem();
            if(!$item) {
                // new product
                continue;
            }
            if(!is_null($item->getBackorders())) {
                continue;
            }
            if(isset($this->_productItems[$_product->getId()])) {
                $_product->setStockItem( $this->_productItems[$_product->getId()] );
                continue;
            }
            $ids[] = $_product->getId();
        }
        if(sizeof($ids) == 0) {
            return false;
        }
        $inventoryCollection = Mage::getModel('cataloginventory/stock_item')->getCollection();
        $inventoryCollection
            ->addStockFilter(Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID)
            ->addProductsFilter($ids);
        foreach($inventoryCollection as $item) {
            $_product = $this->_getProductFromCollection($collection, $item);
            if ($_product) {
                $_product->setStockItem($item);
                $this->_productItems[$_product->getId()] = $item;
            }            
        }
    }

    private function _getProductFromCollection($collection, $item)
    {
        if(is_object($collection)) {
            $_product = $collection->getItemById($item->getProductId());
        } else {
            if(isset($collection[$item->getProductId()])) {
                $_product = $collection[$item->getProductId()];
            } else {
                $found = false;
                foreach($collection as $_product) {
                    if($_product->getId() == $item->getProductId()) {
                        $found = true;
                        break;
                    }
                }
                if(!$found) {
                    $_product = false;
                }
            }
        }
        return $_product;
    }
    
}
