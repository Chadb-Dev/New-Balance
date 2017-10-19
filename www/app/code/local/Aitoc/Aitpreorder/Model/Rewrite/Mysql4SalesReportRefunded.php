<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitpreorder_Model_Rewrite_Mysql4SalesReportRefunded extends Mage_Sales_Model_Mysql4_Report_Refunded
{
    protected function _aggregateByRefundCreatedAt($from, $to)
    {
        $model = Mage::helper('aitpreorder')->retrieveAppropriateVersionClass('mysql4_report_refunded');
        return $model->_aggregateByRefundCreatedAt($from, $to);
    }

    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $model = Mage::helper('aitpreorder')->retrieveAppropriateVersionClass('mysql4_report_refunded');
        return $model->_aggregateByOrderCreatedAt($from, $to);
    }
} 