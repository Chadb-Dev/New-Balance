<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitpreorder_Block_Rewrite_AdminhtmlSalesOrderInvoiceCreateForm extends Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Form
{
   public function hasInvoiceShipmentTypeMismatch() {
        $result = parent::hasInvoiceShipmentTypeMismatch();
        if(!$result)
        {
            $order=$this->getOrder(); 
            $havepreorder=Mage::helper('aitpreorder')->IsHavePreorder($order);
            
            if($havepreorder)
            {
                $result=true;
            }
                   
        }
        return $result;
    }
       
}
