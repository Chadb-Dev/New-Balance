<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitpreorder_Model_Rewrite_Mysql4SalesReportOrder extends Mage_Sales_Model_Mysql4_Report_Order
{
    public function aggregate($from = null, $to = null)
    {
        $model = Mage::helper('aitpreorder')->retrieveAppropriateVersionClass('mysql4_report_order');
        if($model)
        {
            return $model->aggregate($from, $to);        
        }        

        return parent::aggregate($from, $to);
    }
} 