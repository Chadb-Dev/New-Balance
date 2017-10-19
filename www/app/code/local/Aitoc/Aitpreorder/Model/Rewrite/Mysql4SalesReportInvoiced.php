<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitpreorder_Model_Rewrite_Mysql4SalesReportInvoiced extends Mage_Sales_Model_Mysql4_Report_Invoiced
{
    protected function _aggregateByInvoiceCreatedAt($from, $to)
    {
        $model = Mage::helper('aitpreorder')->retrieveAppropriateVersionClass('mysql4_report_invoiced');
        return $model->_aggregateByInvoiceCreatedAt($from, $to);
    }

    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $model = Mage::helper('aitpreorder')->retrieveAppropriateVersionClass('mysql4_report_invoiced');
        return $model->_aggregateByOrderCreatedAt($from, $to);
    }
} 