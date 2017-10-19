<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitpreorder_Model_Mysql4_Report_Invoiced_1600 extends Mage_Sales_Model_Mysql4_Report_Invoiced
{

    protected function _aggregateByInvoiceCreatedAt($from, $to)
    {
        $table       = $this->getTable('sales/invoiced_aggregated');
        $sourceTable = $this->getTable('sales/invoice');
        $orderTable  = $this->getTable('sales/order');
        $helper      = Mage::getResourceHelper('core');
        $adapter     = $this->_getWriteAdapter();

        $adapter->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeRelatedSelect(
                    $sourceTable, $orderTable, array('order_id'=>'entity_id'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($table, $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr = $adapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    array('source_table' => $sourceTable),
                    'source_table.created_at', $from, $to
                )
            );
            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'                => $periodExpr,
                'store_id'              => 'order_table.store_id',
                'order_status'          => 'order_table.status_preorder',
                'orders_count'          => new Zend_Db_expr('COUNT(order_table.entity_id)'),
                'orders_invoiced'       => new Zend_Db_expr('COUNT(order_table.entity_id)'),
                'invoiced'              => new Zend_Db_expr('SUM(order_table.base_total_invoiced'
                    . ' * order_table.base_to_global_rate)'),
                'invoiced_captured'     => new Zend_Db_expr('SUM(order_table.base_total_paid'
                    . ' * order_table.base_to_global_rate)'),
                'invoiced_not_captured' => new Zend_Db_expr(
                    'SUM((order_table.base_total_invoiced - order_table.base_total_paid)'
                    . ' * order_table.base_to_global_rate)')
            );

            $select = $adapter->select();
            $select->from(array('source_table' => $sourceTable), $columns)
                ->joinInner(
                    array('order_table' => $orderTable),
                    $adapter->quoteInto(
                        'source_table.order_id = order_table.entity_id AND order_table.state <> ?',
                        Mage_Sales_Model_Order::STATE_CANCELED),
                    array()
                );

            $filterSubSelect = $adapter->select();
            $filterSubSelect->from(array('filter_source_table' => $sourceTable), 'MAX(filter_source_table.entity_id)')
                ->where('filter_source_table.order_id = source_table.order_id');

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->where('source_table.entity_id = (?)', new Zend_Db_Expr($filterSubSelect));
            unset($filterSubSelect);

            $select->group(array(
                $periodExpr,
                'order_table.store_id',
                'order_table.status_preorder'
            ));

            $select->having('orders_count > 0');
            $insertQuery = $helper->getInsertFromSelectUsingAnalytic($select, $table, array_keys($columns));
            $adapter->query($insertQuery);
            $select->reset();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr(Mage_Core_Model_App::ADMIN_STORE_ID),
                'order_status'          => 'order_status',
                'orders_count'          => new Zend_Db_expr('SUM(orders_count)'),
                'orders_invoiced'       => new Zend_Db_expr('SUM(orders_invoiced)'),
                'invoiced'              => new Zend_Db_expr('SUM(invoiced)'),
                'invoiced_captured'     => new Zend_Db_expr('SUM(invoiced_captured)'),
                'invoiced_not_captured' => new Zend_Db_expr('SUM(invoiced_not_captured)')
            );

            $select
                ->from($table, $columns)
                ->where('store_id <> ?', 0);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status'
            ));

            $insertQuery = $helper->getInsertFromSelectUsingAnalytic($select, $table, array_keys($columns));
            $adapter->query($insertQuery);
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $table       = $this->getTable('sales/invoiced_aggregated_order');
        $sourceTable = $this->getTable('sales/order');
        $adapter     = $this->_getWriteAdapter();


            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect($sourceTable, 'created_at', 'updated_at', $from, $to);
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($table, $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr = $adapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    $sourceTable, 'created_at', $from, $to
                )
            );

            $columns = array(
                'period'                => $periodExpr,
                'store_id'              => 'store_id',
                'order_status'          => 'status_preorder',
                'orders_count'          => new Zend_Db_Expr('COUNT(base_total_invoiced)'),
                'orders_invoiced'       => new Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getCheckSql('base_total_invoiced > 0', 1, 0)
                    )
                ),
                'invoiced'              => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('base_total_invoiced',0),
                        $adapter->getIfNullSql('base_to_global_rate',0)
                    )
                ),
                'invoiced_captured'     => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('base_total_paid',0),
                        $adapter->getIfNullSql('base_to_global_rate',0)
                    )
                ),
                'invoiced_not_captured' => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('base_total_invoiced',0),
                        $adapter->getIfNullSql('base_total_paid',0),
                        $adapter->getIfNullSql('base_to_global_rate',0)
                    )
                )

            );

            $select = $adapter->select();
            $select->from($sourceTable, $columns)
                ->where('state <> ?', Mage_Sales_Model_Order::STATE_CANCELED);

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                $periodExpr,
                'store_id',
                'status_preorder'
            ));

            $select->having('orders_count > 0');

            $helper      = Mage::getResourceHelper('core');
            $insertQuery = $helper->getInsertFromSelectUsingAnalytic($select, $table, array_keys($columns));
            $adapter->query($insertQuery);
            $select->reset();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr(Mage_Core_Model_App::ADMIN_STORE_ID),
                'order_status'          => 'order_status',
                'orders_count'          => new Zend_Db_expr('SUM(orders_count)'),
                'orders_invoiced'       => new Zend_Db_expr('SUM(orders_invoiced)'),
                'invoiced'              => new Zend_Db_expr('SUM(invoiced)'),
                'invoiced_captured'     => new Zend_Db_expr('SUM(invoiced_captured)'),
                'invoiced_not_captured' => new Zend_Db_expr('SUM(invoiced_not_captured)')
            );

            $select->from($table, $columns)
                ->where('store_id <> ?', 0);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status'
            ));

            $helper      = Mage::getResourceHelper('core');
            $insertQuery = $helper->getInsertFromSelectUsingAnalytic($select, $table, array_keys($columns));
            $adapter->query($insertQuery);


        return $this;
    }   
} 