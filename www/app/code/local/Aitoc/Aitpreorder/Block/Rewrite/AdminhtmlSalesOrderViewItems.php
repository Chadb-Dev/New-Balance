<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc.
*/

class Aitoc_Aitpreorder_Block_Rewrite_AdminhtmlSalesOrderViewItems extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
    public function getItemHtml(Varien_Object $item)
    {
        $prnthtml = parent::getItemHtml($item);
        $itemData=unserialize($item->getData('product_options'));//$prnthtml=$prnthtml."<pre>".print_r($item->getData(''),1)."</pre>";
        if (($item->getData('product_type')=='bundle')
                && ($itemData['product_calculations']
                && $itemData['shipment_type'])
        ) {
            $bundle_preorder=strpos($prnthtml, "class='bundlepreorder'");
            if ($bundle_preorder > 0) {
                $replace_start=strpos($prnthtml,'<td>' . __('Ordered'));
                if ($replace_start > 0) {
                   $prnthtml = substr_replace(
                       $prnthtml,'<td>' . __('Pre-Ordered'),
                       $replace_start,
                       strlen('<td>'.__('Ordered'))
                   );
                }
            }
        }

        $isPreorder = Mage::helper('aitpreorder')->isPreorderByOrderItem($item);

        if (isset($itemData['simple_sku'])) {
            $originalProduct = Mage::getModel('catalog/product');
            $originalProductId = $originalProduct->getIdBySku($itemData['simple_sku']);
            $originalProduct->setStoreId($item->getOrder()->getStoreId());

            //FIX FOR WRONG STORE ID IN Aitoc_Aitquantitymanager_Model_Rewrite_FrontCatalogInventoryStockItem::loadByProduct
            if (!Mage::registry('aitoc_order_refund_store_id')) {
                Mage::register('aitoc_order_refund_store_id', $item->getOrder()->getStoreId());
            }
            //END FIX

            $originalProduct->load($originalProductId);
            $originalProductId = $originalProduct->getId();
            if ($originalProductId
                && ($originalProduct->getPreorder()
                    || $originalProduct->getStockItem()->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS
                ) && (is_null($isPreorder) || $isPreorder)) {
                $prnthtml = str_replace('<td>'.__('Ordered'),'<td>'.__('Pre-Ordered'), $prnthtml);
            }

        } else {
            $originalProduct = Mage::getModel('catalog/product');
            $originalProduct->setStoreId($item->getOrder()->getStoreId());

            //FIX FOR WRONG STORE ID IN Aitoc_Aitquantitymanager_Model_Rewrite_FrontCatalogInventoryStockItem::loadByProduct
            if (!Mage::registry('aitoc_order_refund_store_id')) {
                Mage::register('aitoc_order_refund_store_id', $item->getOrder()->getStoreId());
            }
            //END FIX

            $originalProduct->load($item->getData('product_id'));
            if (($originalProduct->getPreorder()
                    || $originalProduct->getStockItem()->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS
                )
                && (is_null($isPreorder) || $isPreorder)) {
                $prnthtml = str_replace('<td>' . __('Ordered'),'<td>' . __('Pre-Ordered'), $prnthtml);
            }
            
            $prnthtml = $this->_bundleProductReplace($originalProduct, $prnthtml);
		}

        return $prnthtml;

    }
    
    protected function _bundleProductReplace($originalProduct, $prnthtml)
    {
        if (!$originalProduct->getData('price_type')) //for bundle products with dynamic price
        {
            $strSearch = "class='bundlepreorder'";
        }
        elseif (strpos($prnthtml, "class='bundlepreorder'")) //for bundle products with fixed price
        {
            $strSearch = 'class="qty-table"';
        }
        else //for not preorder products
        {
            return $prnthtml;
        }
        
        $bundle_preorder_pos=strpos($prnthtml, $strSearch);
        while($bundle_preorder_pos>0)
        {
            $replace_start=strpos($prnthtml,'<td>'.__('Ordered'),$bundle_preorder_pos);
            if($replace_start>0)
            {
                $prnthtml=substr_replace($prnthtml,'<td>'.__('Pre-Ordered'),$replace_start,strlen('<td>'.__('Ordered')));
            }
            $bundle_preorder_pos=strpos($prnthtml, $strSearch, $replace_start);
        }

        return $prnthtml;
    }
}
