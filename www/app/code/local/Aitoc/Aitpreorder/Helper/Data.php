<?php
class Aitoc_Aitpreorder_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_productBackstockCache = array();
    private $_validController = null;

    /**
     * Gets the order for particular item (if order exists)
     *
     * @param object $item
     * @return object
     */
    public function getOrder($item)
    {
        try
        {
            return $item->getOrder();
        }
        catch (Exception $e) {}

        return null;
    }

    /**
     * Inits product in a "right" way. Tries to add store id to product when loadinig it.
     *
     * @param object $item
     * @param string $sku
     * @return Mage_Catalog_Model_Product
     */
    public function initProduct($item, $sku = null)
    {
        $product = Mage::getModel('catalog/product');
        $order = $this->getOrder($item);

        if ($order)
        {
            $product->setStoreId($order->getStoreId());

            //FIX FOR WRONG STORE ID IN Aitoc_Aitquantitymanager_Model_Rewrite_FrontCatalogInventoryStockItem::loadByProduct
            if (!Mage::registry('aitoc_order_refund_store_id'))
            {
                Mage::register('aitoc_order_refund_store_id', $order->getStoreId());
            }
        }

        $itemData = $item->getData();
        $productId = $sku ? $product->getIdBySku($sku) : $itemData['product_id'];
        $product->load($productId);
        return $product;
    }

    public function bundleHaveReg($_item)
    {
        $haveregular=0;
        $havePreorderInBundle=0;
        $bundleItems=$_item->getChildrenItems();
        foreach ($bundleItems as $bundleItem)
        {
            $original_product = $this->initProduct($bundleItem);
            if($original_product->getPreorder()==1)
            {
                $havePreorderInBundle=1;
            }

        }
        if($havePreorderInBundle==0)
        {
            $haveregular=1;
        }
        return $haveregular;
    }

    public function isHaveReg($_items, $ispending=0)
    {
        $haveregular=0;
        $havepreorder = 0;
        $noparent_item=0;
        $alldownloadable=1;
        $preorderdownloadable=0;
        foreach($_items as $_item)
        {
            $itemInOrderData=$_item->getData();
            $noparent_item=0;
            $isshiped=0;
            // if we here from frontend
            if(isset($itemInOrderData['qty_shipped']) && isset($itemInOrderData['qty_ordered']))
            {
                if(((int)($itemInOrderData['qty_shipped']))==((int)($itemInOrderData['qty_ordered'])))
                {
                    $isshiped=1;
                }
                if(!isset($itemInOrderData['parent_item_id']))
                {
                    $noparent_item=1;
                }
            }
            elseif(!isset($itemInOrderData['parent_item_id']))
            {
                $noparent_item=1;
            }

            if($isshiped==0)
            {
                if($itemInOrderData['product_type']=='grouped')
                {
                    $alldownloadable=0;
                    $_product = $this->initProduct($_item);
                    $preorder=$_product->getPreorder();
                    if($preorder!='1')
                    {
                        $haveregular=1;
                    } else {
                        $havepreorder = 1;
                    }
                }
                elseif($itemInOrderData['product_type']=='configurable')
                {
                    $alldownloadable=0;
                    $item_data=unserialize($_item->getData('product_options'));
                    $original_product = $this->initProduct($_item, $item_data['simple_sku']);
                    if ($original_product->getPreorder()!=1)
                    {
                        $haveregular=1;
                    } else {
                        $havepreorder = 1;
                    }
                }
                elseif($itemInOrderData['product_type']=='bundle')
                {
                    $alldownloadable=0;

                    if(Mage::helper('aitpreorder')->bundleHaveReg($_item)=='1')
                    {
                        $haveregular=1;
                    }

                }
                elseif(($itemInOrderData['product_type']=='virtual')&&($ispending==1)&&($noparent_item==1))
                {
                    $alldownloadable=0;
                    $haveregular=1;
                }
                elseif(($itemInOrderData['product_type']=='downloadable')&&($noparent_item==1))//&&($ispending==1))
                {
                    $_product = $this->initProduct($_item);
                    $preorder=$_product->getPreorder();
                    if($preorder!='1')
                    {
                        if($ispending==1)
                        {
                            $haveregular=1;
                        }
                    }
                    else
                    {
                        $havepreorder = 1;
                        $preorderdownloadable=1;
                    }
                }
                elseif(($itemInOrderData['product_type']=='simple')&&($noparent_item==1))
                {
                    $alldownloadable=0;
                    $_product = $this->initProduct($_item);
                    $preorder=$_product->getPreorder();
                    if($preorder!='1')
                    {
                        $haveregular=1;
                    } else {
                        $havepreorder = 1;
                    }
                }
            }
        }
        if ($havepreorder && Mage::getStoreConfig('cataloginventory/aitpreorder/status_change') == 1) {
            $haveregular = 0;
        }
        if ($ispending==0) {
            if (($alldownloadable==1)&&($preorderdownloadable==1)) {
                $haveregular=-1;
            } elseif(($alldownloadable==1)&&($preorderdownloadable==0)) {
                $haveregular=-2;
            }
        }
        return $haveregular;
    }

    public function isHavePreorder($order)
    {
        if ($order) {
            $orderItems = $order->getItemsCollection();
        } else {
            Mage::log('There is no order coming in Aitoc_Aitpreorder_Helper_Data::isHavePreorder() method');
            return 0;
        }

        $haveregular = 0;
        $noparent_item = 0;
        $alldownloadable = 1;
        $preorderdownloadable = 0;
        $havepreorder = 0;

        foreach($orderItems as $item) {
            $isPreorder = $this->isPreorderByOrderItem($item);

            $itemInOrderData = $item->getData();
            $noparent_item=0;
            if (!isset($itemInOrderData['parent_item_id'])) {
                $noparent_item = 1;
            }

            if ($itemInOrderData['product_type']=='grouped') {
                $alldownloadable = 0;
                $product = $this->initProduct($item);
                $preorder = ($product->getPreorder()
                        || $product->getStockItem()->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS
                    ) && (is_null($isPreorder) || $isPreorder);
                if (!$preorder) {
                    $haveregular = 1;
                } else {
                    $havepreorder = 1;
                }
            } elseif ($itemInOrderData['product_type']=='configurable') {
                $alldownloadable = 0;
                $item_data=unserialize($item->getData('product_options'));
                $product = $this->initProduct($item, $item_data['simple_sku']);
                if (($product->getPreorder()
                        || $product->getStockItem()->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS
                    ) && (is_null($isPreorder) || $isPreorder)) {
                    $havepreorder = 1;
                } else {
                    $haveregular = 1;
                }
            } elseif ($itemInOrderData['product_type']=='bundle') {
                $alldownloadable = 0;
                if ($this->bundleHaveReg($item)=='1') {
                    $haveregular = 1;
                } else {
                    $havepreorder = 1;
                }

            } elseif ($itemInOrderData['product_type']=='virtual' && $noparent_item==1) {
                $alldownloadable = 0;
                $haveregular = 1;
            } elseif ($itemInOrderData['product_type']=='downloadable' && $noparent_item==1) {
                $product = $this->initProduct($item);
                $preorder = ($product->getPreorder()
                        || $product->getStockItem()->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS
                    ) && (is_null($isPreorder) || $isPreorder);
                if (!$preorder) {
                    $haveregular = 1;
                } else {
                    $preorderdownloadable = 1;
                    $havepreorder = 1;
                }
            } elseif ($itemInOrderData['product_type']=='simple' && $noparent_item==1) {
                $alldownloadable = 0;
                $product = $this->initProduct($item);
                $preorder = ($product->getPreorder()
                        || $product->getStockItem()->getBackorders() == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS
                    ) && (is_null($isPreorder) || $isPreorder);
                if (!$preorder) {
                    $haveregular = 1;
                } else {
                    $havepreorder = 1;
                }
            }
        }

        if (!$alldownloadable && $havepreorder && $haveregular
            && Mage::getStoreConfig('cataloginventory/aitpreorder/status_change') == 0
        ) {
            $havepreorder = 0;
        }

        return $havepreorder;
    }

    public function checkSynchronization($status,$statusPreorder)
    {
    	if(!$statusPreorder)
    	{
            return false;
    	}
    	if($status!=$statusPreorder)
    	{
            if(!(($statusPreorder=='pendingpreorder' && $status=='pending')
            || ($statusPreorder=='processingpreorder' && $status=='processing')))
            {
                return false;
            }
    	}
    	return true;
    }

    /**
     * Checks if Aitoc Multi-Location Inventory module is enabled
     *
     * @return boolean
     */
    public function checkIfMultilocationInventoryIsEnabled()
    {
        $aitocModulesList = Mage::getModel('aitsys/aitsys')->getAitocModuleList();

        if ($aitocModulesList)
        {
            foreach ($aitocModulesList as $aitocModule)
            {
                if ('Aitoc_Aitquantitymanager' == $aitocModule->getKey())
                {
                    return Mage::helper('core')->isModuleEnabled('Aitoc_Aitquantitymanager') && $aitocModule->getValue();
                }
            }
        }

        return false;
    }

    public function retrieveAppropriateVersionClass($modelName)
    {
        switch($modelName)
        {
            case 'mysql4_report_order':
                return $this->_retrieveAppropriateVersionClassSchema1($modelName);
                break;
            case 'mysql4_report_refunded':
            case 'mysql4_report_shipping':
            case 'mysql4_report_invoiced':
                return $this->_retrieveAppropriateVersionClassSchema2($modelName);
                break;
        }
    }

    protected function _retrieveAppropriateVersionClassSchema1($modelName)
    {
        if(version_compare(Mage::getVersion(), '1.5.0.0', '<'))
        {
            $modelName = 'aitpreorder/' . $modelName . '_1410';
            $model = Mage::getModel($modelName);
            return $model;
        }
        elseif(version_compare(Mage::getVersion(), '1.6.0.0', '<'))
        {
            $modelName = 'aitpreorder/' . $modelName . '_1500';
            $model = Mage::getModel($modelName);
            return $model;
        }
        elseif(version_compare(Mage::getVersion(), '1.6.1.0', '<'))
        {
            $modelName = 'aitpreorder/' . $modelName . '_1600';
            $model = Mage::getModel($modelName);
            return $model;
        }

        return false;
    }

    protected function _retrieveAppropriateVersionClassSchema2($modelName)
    {
        if(version_compare(Mage::getVersion(), '1.4.1.0', '<'))
        {
            $modelName = 'aitpreorder/' . $modelName . '_1400';
            $model = Mage::getModel($modelName);
            return $model;
        }
        elseif(version_compare(Mage::getVersion(), '1.6.0.0', '<'))
        {
            $modelName = 'aitpreorder/' . $modelName . '_1410';
            $model = Mage::getModel($modelName);
            return $model;
        }
        $modelName = 'aitpreorder/' . $modelName . '_1600';
        $model = Mage::getModel($modelName);
        return $model;
    }

    public function isAvailableForDownload($item)
    {
        $sku = $item->getData('sku');

        $product = Mage::getModel('catalog/product');
        $product_id = $product->getIdBySku($sku);
        $product->load($product_id);

        if (($product->getPreorder() == 1) &&
            ($product->getData('type_id') == 'downloadable'))
        {
            return false;
        }
        return true;
    }

    public function getPatternInputSubmitShipping()
    {
        if (version_compare(Mage::getVersion(), '1.8.1.0', '<'))
        {
            return 'type="text" class="input-text" ';
        }
        else
        {
            return 'type="text" class="input-text qty-item" ';
        }
    }

    public function isMixedCartAllowed()
    {
        return !(bool)Mage::getStoreConfig('cataloginventory/aitpreorder/deny_mixing_products');
    }

    public function isBackstockPreorderAllowed($product)
    {
        $backstock = false;

        if (!is_object($product)) {
            if (isset($this->_productBackstockCache[$product])) {
                $product = $this->_productBackstockCache[$product];
            } else {
                $product = Mage::getModel('catalog/product')->load($product);
            }
        }

        if (is_null($product->getBackstockPreorders())) {
            $isPreorder = $product
                ->getResource()
                ->getAttributeRawValue(
                    $product->getId(),
                    'backstock_preorders',
                    Mage::app()->getStore()
                );

            $product->setData('backstock_preorders', (bool) $isPreorder);
        }

        $stockItem = $product->getStockItem();
        if ($stockItem->getUseConfigBackorders() == '1') {
            $backorders = Mage::getStoreConfig('cataloginventory/item_options/backorders');
        } else {
            $backorders = $stockItem->getBackorders();
        }

        if ($backorders == Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO) {
            $backstock = true;
        } elseif ($product->getBackstockPreorders() == 0) {
            $backstock = (bool)Mage::getStoreConfig('cataloginventory/aitpreorder/backstock_preorders');
        } elseif ($product->getBackstockPreorders() == 1) {
            $backstock = true;
        }
        if (!isset($this->_productBackstockCache[$product->getId()])) {
            $this->_productBackstockCache[$product->getId()] = $product;
        }
        if (!$backstock) {
            return false;
        }
        //backstock allowed for this product, validate if product is pre-order and out-of-stock
        $item = $product->getStockItem();
        $outOfStock = !$item->getData('is_in_stock');

        Mage::getSingleton('aitpreorder/stockloader')->applyStockToProduct($product);
        if ($product->getPreorder() && $outOfStock && $this->_allowToReplaceStock()) {
            return true;
        }
        return false;
    }

    /**
     * Validate if current page is backend and if it load product for edit - then our validation should fail
     *
     * @return bool
     */
    private function _allowToReplaceStock()
    {
        if(is_null($this->_validController)) {
            $this->_validController = true;

            $request = Mage::app()->getRequest();
            $isAdmin = (Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml');
            $controller = $request->getControllerName();
            $notAllowedControllers = array('catalog_product');

            if($isAdmin && in_array($controller, $notAllowedControllers)) {
                $this->_validController = false;
            }
        }
        return $this->_validController;
    }

    public function isPreOrder($item, $product)
    {
        if (!isset($item)) {
            $confValue = Mage::getStoreConfig('cataloginventory/item_options/backorders');
            $qty = 0;
            $minQty = 0;
            return false;
        } else {
            $confValue = $item->getBackorders();
            $qty = $item->getQty();
            $minQty =  $item->getMinQty();
        }

        return (Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS == $confValue
            && ($qty > $minQty ||
                ($qty <= $minQty &&
                    ($product->getBackstockPreorders() == 1 ||
                       ($product->getBackstockPreorders() == 0 &&
                           Mage::getStoreConfigFlag('cataloginventory/aitpreorder/backstock_preorders')
                       )
                    )
                )
            )
        ) || (Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO == $confValue
                && $qty <= $minQty
                && $item->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
                && $item->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                && $item->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_GROUPED);
    }


    public function isPreorderByOrderItem($orderItem)
    {
        $productOptions = $orderItem->getProductOptions();
        if (isset($productOptions['info_buyRequest']['is_preorder'])) {
            $isPreorder = $productOptions['info_buyRequest']['is_preorder'];
        } else {
            $isPreorder = null;
        }

        if (!$isPreorder && $childrenItems = $orderItem->getChildrenItems()) {
            foreach ($childrenItems as $childrenItem) {
                $productOptions = $childrenItem->getProductOptions();
                if (isset($productOptions['info_buyRequest']['is_preorder'])
                    && $productOptions['info_buyRequest']['is_preorder']
                ) {
                    $isPreorder = 1;
                }
            }
        }
        return $isPreorder;
    }
}
