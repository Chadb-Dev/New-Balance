<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitpreorder_Model_Rewrite_Mysql4SalesReportShipping extends Mage_Sales_Model_Mysql4_Report_Shipping
{
    protected function _aggregateByShippingCreatedAt($from, $to)
    {
        $model = Mage::helper('aitpreorder')->retrieveAppropriateVersionClass('mysql4_report_shipping');
        return $model->_aggregateByShippingCreatedAt($from, $to);
    }

    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $model = Mage::helper('aitpreorder')->retrieveAppropriateVersionClass('mysql4_report_shipping');
        return $model->_aggregateByOrderCreatedAt($from, $to);
    }
} 