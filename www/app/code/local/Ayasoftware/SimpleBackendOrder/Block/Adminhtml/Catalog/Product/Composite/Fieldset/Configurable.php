<?php
/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleBackendOrder
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_SimpleBackendOrder_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Configurable extends Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Configurable
{
    public function getJsonConfig() {
        $config = Zend_Json::decode(parent::getJsonConfig());
        $storeId = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
        $productsCollection = $this->getAllowProducts();
        //Create the extra price and tier price data/html we need.
        foreach ($productsCollection as $product) {
            $productId = $product->getId();
            $product = Mage::getModel('catalog/product')
                    ->setStoreId($storeId)
                    ->setCustomerGroupId(Mage::getSingleton('adminhtml/session_quote')->getCustomer()->getGroupId())
                    ->load($productId);
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            if ($stockItem->getQty() <= 0 || !($stockItem->getIsInStock())) {
                $stockInfo[$productId] = array(
                    "stockLabel" => $this->__('Out of stock'),
                    "stockQty" => intval($stockItem->getQty()),
                    "is_in_stock" => false,
                );
            } else {
                $stockInfo[$productId] = array(
                    "stockLabel" => $this->__('In Stock'),
                    "stockQty" => intval($stockItem->getQty()),
                    "is_in_stock" => true,
                );
            }
            $finalPrice = $product->getFinalPrice();
            if ($product->getCustomerGroupId()) {
                $finalPrice = $product->getGroupPrice();
            }
            if ($product->getTierPrice()) {
                $tprices = array();
                foreach ($tierprices = $product->getTierPrice() as $tierprice) {
                    $tprices[] = $tierprice['price'];
                }
                $tierpricing = min($tprices);
            } else {
                $tierpricing = '';
            }
            $childProducts[$productId] = array(
                "price" => $this->_registerJsPrice($this->_convertPrice($product->getPrice())),
                "finalPrice" => $this->_registerJsPrice($this->_convertPrice($finalPrice)),
                "tierpricing" => $this->_registerJsPrice($this->_convertPrice($tierpricing)),
            );
        }
        $config['stockInfo'] = $stockInfo;
        $config['childProducts'] = $childProducts;
        $config['showPriceRangesInOptions'] = true;
        $config['rangeToLabel'] = $this->__('-');
        $jsonConfig = Zend_Json::encode($config);
        return $jsonConfig;

    }
}
			