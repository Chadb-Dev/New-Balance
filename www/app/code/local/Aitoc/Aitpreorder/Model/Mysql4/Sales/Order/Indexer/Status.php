<?php

class Aitoc_Aitpreorder_Model_Mysql4_Sales_Order_Indexer_Status extends Mage_Index_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_setResource('aitpreorder');
    }

    public function reindexAll()
    {
        $this->_reindexEntity();
    }

    public function orderCallback($args)
    {
        $order = Mage::getModel('sales/order');
        $order->setData($args['row']);     
        if (!Mage::helper('aitpreorder')->checkSynchronization($order->getStatus(), $order->getStatusPreorder()))
        {
            $order->setStatusPreorder($order->getStatus());
            $order->selectOrderStatus();
            $this->_updateTable('sales/order', $order);
            $this->_updateTable('sales/order_grid', $order);
        }
        
    }

    protected function _reindexEntity()
    {        
        $entities = Mage::getModel('sales/order')->getCollection();
        Mage::getSingleton('core/resource_iterator')->walk($entities->getSelect(),
                                                   array(array($this, 'orderCallback')));
    }

    protected function _updateTable($tableName, $order)
    {
        $this->_getWriteAdapter()->update($this->getTable($tableName),
            array('status' => $order->getStatus(),
                'status_preorder' => $order->getStatusPreorder()),
            array('entity_id = ?' => $order->getId()));
    }
}