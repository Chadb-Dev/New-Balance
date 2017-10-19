<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitpreorder_Block_Rewrite_BundleAdminhtmlSalesOrderItemsRenderer extends Mage_Bundle_Block_Adminhtml_Sales_Order_Items_Renderer
{
    public function getValueHtml($item)
    {
        $isPreorder = Mage::helper('aitpreorder')->isPreorderByOrderItem($item);
        $product = Mage::getModel('catalog/product')->setStoreId($item->getStoreId())->load($item->getData('product_id'));
		
        return parent::getValueHtml($item)
            . ((($product->getPreorder()
                || $product->getStockItem()->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS
            ) && (is_null($isPreorder) || $isPreorder)) ?
            "<input type=hidden class='bundlepreorder' />" :
            '');
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $item = $this->getItem();
        if ($this->isShipmentSeparately($item)) {
            $orderItem = $item->getOrderItem();

            $isPreorder = Mage::helper('aitpreorder')->isPreorderByOrderItem($orderItem);
            $childrenItems = $orderItem->getChildrenItems();
            $havePreorderInBundle = 0; 
            foreach ($childrenItems as $childrenItem) {
                $originalProduct = Mage::helper('aitpreorder')->initProduct($childrenItem);
                if (($originalProduct->getPreorder()
                        || $originalProduct->getStockItem()->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS
                    ) && (is_null($isPreorder) || $isPreorder)) {
                    $havePreorderInBundle = 1;
                }    
            }
            if ($havePreorderInBundle) {
                $pattern = '/<input type="text" class="input-text" name="shipment\[(.*)\]\[(.*)\]" value="(.*)" \/>/';
                $matches = array();
                if (preg_match($pattern, $html, $matches)) {
                    $txt = '<input type="hidden" class="input-text" name="shipment[items]['.$matches[2].']" value="0" /><div style="text-align:center;">'.$this->__('This product is Pre-Order and cannot be shipped').'</div>';
                    $html = str_replace($matches[0],$txt,$html);
                }
            }
        }
        return $html;
    }
}
