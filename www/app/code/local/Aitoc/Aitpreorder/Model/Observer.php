<?php
class Aitoc_Aitpreorder_Model_Observer {

    public function adminToHtmlAfter($observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory) {
            Mage::getModel('aitpreorder/adminhtml_catalogProductEditTabInventory')
                ->initBlock($block)
                ->applyHtml($observer->getTransport());
        }elseif($block instanceof Mage_Sales_Block_Order_Email_Invoice_Items ||
            $block instanceof Mage_Sales_Block_Order_Email_Items){
            $transport          = $observer->getTransport();
            $html = $transport->getHtml();
            $order = $block->getOrder();

            $is_preorder = Mage::helper('aitpreorder')->isHavePreorder($order);

            if($is_preorder){
                if($block instanceof Mage_Sales_Block_Order_Email_Items && version_compare(Mage::getVersion(), '1.9.1.0', 'eq')){
                    $html .= '<tr><td><p style="background-color: #f5f500; text-align: center;">'.Mage::helper('aitpreorder')->__('Please note that your order contains Pre-Order products. These products will be shipped to you once they are available.').'</p></td></tr>';
                }else{
                    $html .= '<p style="background-color: #f5f500; text-align: center;">'.Mage::helper('aitpreorder')->__('Please note that your order contains Pre-Order products. These products will be shipped to you once they are available.').'</p>';
                }
            }
            $transport->setHtml($html);
        }
    }

    public function frontToHtmlAfter($observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Sales_Block_Order_Items ||
            $block instanceof Mage_Sales_Block_Order_Email_Items) {
            $transport          = $observer->getTransport();
            $html = $transport->getHtml();
            $order = $block->getOrder();

            $is_preorder = Mage::helper('aitpreorder')->isHavePreorder($order);
            if($is_preorder){
                if($block instanceof Mage_Sales_Block_Order_Email_Items && version_compare(Mage::getVersion(), '1.9.1.0', 'eq')){
                    $html .= '<tr><td><p style="background-color: #f5f500; text-align: center;">'.Mage::helper('aitpreorder')->__('Please note that your order contains Pre-Order products. These products will be shipped to you once they are available.').'</p></td></tr>';
                }else{
                    $html .= '<p style="background-color: #f5f500; text-align: center;">'.Mage::helper('aitpreorder')->__('Please note that your order contains Pre-Order products. These products will be shipped to you once they are available.').'</p>';
                }
            }
            $transport->setHtml($html);

        }
    }
    
    public function onSalesOrderSaveAfter($observer)
    {
        if(!Mage::registry("aitoc_inside_order"))
        {
            Mage::register("aitoc_inside_order",1);
            $order = $observer->getOrder();
            $order->selectOrderStatus()->save();
            Mage::unregister("aitoc_inside_order");
        }
    }    
    
    public function onSalesQuoteItemQtySetAfter($observer)
    {
        $quoteItem = $observer->getEvent()->getItem();

        $simpleProduct = $quoteItem->getProduct()->getCustomOption('simple_product');
        if (isset($simpleProduct)) {
            $_product = $simpleProduct->getProduct();
        } else {
            $_product = $quoteItem->getProduct();
        }
        Mage::getSingleton('aitpreorder/stockloader')->applyStockToProduct($_product);
        if ($_product->getPreorder() == '1') {
            $comma = ", ";
            if ($_product->getPreorderdescript() == "") {
                $comma = "";
            }
            $preordermsg = Mage::helper('aitpreorder')->__('Pre-Order') . $comma . $_product->getPreorderdescript();
        }
        if (isset($preordermsg)) {
            if ($quoteItem->getMessage() == "") {
                $quoteItem->setMessage($preordermsg);
            }
        }
    }
    
    public function onBundleProductViewConfig($observer)
    {
        $observer['selection']->getId();
        $opts = array();

        $product = Mage::getModel('catalog/product');
        $product->load($observer['selection']->getId());
        if ($product->getPreorder() == '1') {
            $opts['ispreorder'] = 1;
            $opts['preorderdescript'] = Mage::helper('aitpreorder')->__('Pre-Order');
        } else {
            $opts['ispreorder'] = 0;
        }

        $observer['response_object']->setAdditionalOptions($opts);
    }

    public function onCatalogProductSaveAfter($observer)
    {
        $event = $observer->getEvent();

        $product = $event->getProduct();

        $preorderDataOrig = $product->getOrigData('preorder');
        $preorderData = $product->getData('preorder');
        //usual - > preorder: $preorderDataOrig = 0 ; preorderData = 1
        //preorder - > usual: $preorderDataOrig = 1 ; preorderData = 0    !!!!!!!!
        $needAlert = false;
        if ($preorderDataOrig != $preorderData) {
            $coll = Mage::getResourceModel('sales/order_collection');
            /* @var $coll Mage_Sales_Model_Mysql4_Order_Collection */
            if ($preorderData != 1) {
                $needAlert = true;
                $statuses[] = 'processingpreorder';
                $statuses[] = 'pendingpreorder';
            } else {
                $statuses[] = 'processing';
                $statuses[] = 'pending';
            }


            $coll->addFieldToFilter('status_preorder', $statuses);
            /* @var $resource Mage_Sales_Model_Mysql4_Order */

            $versionInfo = Mage::getVersionInfo();
            if (version_compare(Mage::getVersion(), '1.4.1.0', '>=')) {
                $prealias = 'main_table';
            } else {
                $prealias = 'e';
            }

            $coll->getSelect()
                ->join(array('oi' => $coll->getTable('sales/order_item')), 'oi.order_id = ' . $prealias . '.entity_id', array('oi.product_id', 'oi.sku'))
                ->where('oi.product_id = ?', $product->getId())
                ->group($prealias . '.entity_id');

            foreach ($coll->getItems() as $order) {
                /* @var $order Mage_Sales_Model_Order */

                ///method for sending email
                if ($needAlert && $order->getData('customer_id') && Mage::getStoreConfig('cataloginventory/aitpreorder/send_email')) {
                    $url = Mage::getModel('core/url')->getUrl('sales/order/view', array('order_id' => $order->getId()));
                    Mage::unregister('order_view_url');
                    Mage::register('order_view_url', $url);
                    $this->sendAlert($order, $product);
                }

                $order->selectOrderStatus()->save();
            }
        }
    }

    public function sendAlert($order, $product)
    {
        $customer_id = $order->getData('customer_id');
        $configSendIfNotPreorder = true;
        if ($customer_id && $configSendIfNotPreorder) {
            $email = Mage::getModel('aitpreorder/email');
            try {
                $website = Mage::getModel('core/store')->load($order->getData('store_id'))->getWebsite();
                $email->setWebsite($website);
                $customer = Mage::getModel('customer/customer')->load($customer_id);
                $product->setCustomerGroupId($customer->getGroupId());
                $email->addStockProduct($product);
                $email->setType('stock');
                $email->setCustomer($customer);
                $email->send();
                $email->clean();
            } catch (Exception $e) {
                Mage::log($e->getMessage());
            }
        }
    }

    public function onSalesOrderLoad($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order->getId()) {
            return;
        }
        if ($order->getStatusPreorder() == '') {
            $order->setStatusPreorder($order->getStatus());
        }
        $order->setStatus($order->getStatusPreorder());
    }

    /**
     * Validate if quote have in-stock and pre-orders products and return true if products are mixed
     *
     * @param object $quote
     * @return bool
     */
    protected function _validateQuoteItems($quote)
    {
        $quoteHavePreOrdered = false;
        $quoteHaveSimple = false;

        foreach ($quote->getItemsCollection() as $item_id => $item) {
            if ($item->getProduct()->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            {
                if ($item->getProduct()->getPreorder()) {
                    $quoteHavePreOrdered = true;
                } else {
                    $quoteHaveSimple = true;
                }
            }
        }
        return $quoteHavePreOrdered && $quoteHaveSimple;
    }

    /**
     * Checks quote for pre-ordered and default products
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkQuoteProduct(Varien_Event_Observer $observer)
    {
        if (Mage::helper('aitpreorder')->isMixedCartAllowed()) {
            return;
        }

        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();

        if ($quoteItem->getId()) {
            return $this;
        }

        $name = $quoteItem->getName();
        if ($quoteItem->getParentItem()) {
            $parentItem = $quoteItem->getParentItem();
            $name = $parentItem->getName();
        }
        $quote = $quoteItem->getQuote();
        $addedItem = null;

        foreach ($quote->getItemsCollection() as $item_id => $item) {
            if ($item === $quoteItem) {
                $addedItem = $item_id;
                break;
            }
        }
        if ($this->_validateQuoteItems($quote)) {
            $quote->removeItem($addedItem);
            $options = $quoteItem->getOptions();
            foreach ($options as $option) {
                $quoteItem->removeOption($option->getCode());
            }
            if (Mage::app()->getRequest()->getActionName() == 'reorder') {
                Mage::getSingleton('checkout/session')->addError(Mage::helper('aitpreorder')->__('Can\'t add "%s" product to cart. It is not allowed to place order with pre-order and in-stock products.', $name));
            } else {
                Mage::throwException(Mage::helper('aitpreorder')->__('Sorry, you can’t add pre-order and in-stock items to the same cart.'));
            }
        }
    }

    public function salesOrderPlaceBefore($observer)
    {
        if (Mage::helper('aitpreorder')->isMixedCartAllowed()) {
            return;
        }

        $quote = $observer->getOrder()->getQuote();
        if ($this->_validateQuoteItems($quote)) {
            Mage::throwException(Mage::helper('aitpreorder')->__('Order cannot be placed. It is not allowed to place order with pre-order and in-stock products.'));
        }
    }

    public function convertQuoteItemToOrderItem(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        $isPreorder = $quoteItem->getProduct()->getPreorder();
        $orderItem = $observer->getEvent()->getOrderItem();
        $productOptions = $orderItem->getProductOptions();
        $productOptions['info_buyRequest']['is_preorder'] = intval($isPreorder);

        $orderItem->setProductOptions($productOptions);
    }
}
