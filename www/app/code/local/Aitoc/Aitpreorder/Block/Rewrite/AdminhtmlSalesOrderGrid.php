<?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc. 
 */
/* AITOC static rewrite inserts start */
/* $meta=%default,AdjustWare_Deliverydate,AdjustWare_Orderproducts,Aitoc_Aitcheckoutfields,Aitoc_Aitpermissions% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitpermissions')){
    class Aitoc_Aitpreorder_Block_Rewrite_AdminhtmlSalesOrderGrid_Aittmp extends Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid {} 
 }elseif(Mage::helper('core')->isModuleEnabled('Aitoc_Aitcheckoutfields')){
    class Aitoc_Aitpreorder_Block_Rewrite_AdminhtmlSalesOrderGrid_Aittmp extends Aitoc_Aitcheckoutfields_Block_Rewrite_AdminhtmlSalesOrderGrid {} 
 }elseif(Mage::helper('core')->isModuleEnabled('AdjustWare_Orderproducts')){
    class Aitoc_Aitpreorder_Block_Rewrite_AdminhtmlSalesOrderGrid_Aittmp extends AdjustWare_Orderproducts_Block_Rewrite_AdminSalesOrderGrid {} 
 }elseif(Mage::helper('core')->isModuleEnabled('AdjustWare_Deliverydate')){
    class Aitoc_Aitpreorder_Block_Rewrite_AdminhtmlSalesOrderGrid_Aittmp extends AdjustWare_Deliverydate_Block_Rewrite_AdminhtmlSalesOrderGrid {} 
 }else{
    /* default extends start */
    class Aitoc_Aitpreorder_Block_Rewrite_AdminhtmlSalesOrderGrid_Aittmp extends Mage_Adminhtml_Block_Sales_Order_Grid {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitpreorder_Block_Rewrite_AdminhtmlSalesOrderGrid extends Aitoc_Aitpreorder_Block_Rewrite_AdminhtmlSalesOrderGrid_Aittmp
{
    protected function _prepareColumns()
    {
        $res = parent::_prepareColumns();
        $action = $this->_columns['action'];
        unset($this->_columns['action']);
        unset($this->_columns['status']);
        $this->addColumn('status_preorder', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status_preorder',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
        
        $this->_columns['action'] = $action;
        $this->_columns['action']->setId('action');
        $this->_lastColumnId = 'action';
        
        
        return $res;
    }
  
}
