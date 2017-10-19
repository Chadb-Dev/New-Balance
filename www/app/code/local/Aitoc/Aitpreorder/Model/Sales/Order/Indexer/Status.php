<?php

class Aitoc_Aitpreorder_Model_Sales_Order_Indexer_Status extends Mage_Index_Model_Indexer_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpreorder/sales_order_indexer_status');
    }

    public function getName()
    {
        return Mage::helper('aitpreorder')->__('Pre-order Statuses');
    }

    public function getDescription()
    {
        return Mage::helper('aitpreorder')->__('Refresh Pre-order Statuses');
    }

    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addData('aitpreorder', true);
    }

    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getData('aitpreorder'))
        {
            $this->getResourceModel('aitpreorder')->preorderStatusesRefresh($event);
        }
    }

    public function disableKeys()
    {
        return $this;
    }

    public function enableKeys()
    {
        return $this;
    }
}