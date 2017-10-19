<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitpreorder_Block_Rewrite_BundleAdminhtmlSalesOrderViewItemsRenderer extends Mage_Bundle_Block_Adminhtml_Sales_Order_View_Items_Renderer
{
    public function getValueHtml($item)
    {
        $product = Mage::getModel('catalog/product')->setStoreId($item->getStoreId())->load($item->getData('product_id'));

        $isPreorder = Mage::helper('aitpreorder')->isPreorderByOrderItem($item);

        return parent::getValueHtml($item)
            . ((($product->getPreorder()
                || $product->getStockItem()->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS
            ) && (is_null($isPreorder) || $isPreorder)) ?
            "<input type=hidden class='bundlepreorder' />" :
            '');
    }
}
