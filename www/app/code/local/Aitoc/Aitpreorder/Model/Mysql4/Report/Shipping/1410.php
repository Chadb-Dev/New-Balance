<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitpreorder_Model_Mysql4_Report_Shipping_1410 extends Mage_Sales_Model_Mysql4_Report_Shipping
{
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $table = $this->getTable('sales/shipping_aggregated_order');
        $sourceTable = $this->getTable('sales/order');
        $this->_getWriteAdapter()->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect($sourceTable, 'created_at', 'updated_at', $from, $to);
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($table, $from, $to, $subSelect);

            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'                => "DATE(CONVERT_TZ(created_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))",
                'store_id'              => 'store_id',
                'order_status'          => 'status_preorder',
                'shipping_description'  => 'shipping_description',
                'orders_count'          => 'COUNT(entity_id)',
                'total_shipping'        => 'SUM((`base_shipping_amount` - IFNULL(`base_shipping_canceled`, 0)) * `base_to_global_rate`)',
                'total_shipping_actual' => 'SUM((`base_shipping_invoiced` - IFNULL(`base_shipping_refunded`, 0)) * `base_to_global_rate`)',
            );

            $select = $this->_getWriteAdapter()->select();
            $select->from($sourceTable, $columns)
                 ->where('state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                ))
                ->where('is_virtual = 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'created_at'));
            }

            $select->group(array(
                'period',
                'store_id',
                'order_status',
                'shipping_description'
            ));

            $select->having('orders_count > 0');

            $this->_getWriteAdapter()->query($select->insertFromSelect($table, array_keys($columns)));

            $select->reset();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr('0'),
                'order_status'          => 'order_status',
                'shipping_description'  => 'shipping_description',
                'orders_count'          => 'SUM(orders_count)',
                'total_shipping'        => 'SUM(total_shipping)',
                'total_shipping_actual' => 'SUM(total_shipping_actual)',
            );

            $select
                ->from($table, $columns)
                ->where("store_id <> 0");

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status',
                'shipping_description'
            ));

            $this->_getWriteAdapter()->query($select->insertFromSelect($table, array_keys($columns)));
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }

        $this->_getWriteAdapter()->commit();
        return $this;
    }

    /**
     * Aggregate shipping report by shipment create_at as period
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Report_Shipping
     */
    protected function _aggregateByShippingCreatedAt($from, $to)
    {
        $table = $this->getTable('sales/shipping_aggregated');
        $sourceTable = $this->getTable('sales/invoice');
        $orderTable = $this->getTable('sales/order');
        $this->_getWriteAdapter()->beginTransaction();

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

            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'                => "DATE(CONVERT_TZ(source_table.created_at, '+00:00', '" . $this->_getStoreTimezoneUtcOffset() . "'))",
                'store_id'              => 'order_table.store_id',
                'order_status'          => 'order_table.status_preorder',
                'shipping_description'  => 'order_table.shipping_description',
                'orders_count'          => 'COUNT(order_table.entity_id)',
                'total_shipping'        => 'SUM((order_table.`base_shipping_amount` - IFNULL(order_table.`base_shipping_canceled`, 0)) * order_table.`base_to_global_rate`)',
                'total_shipping_actual' => 'SUM((order_table.`base_shipping_invoiced` - IFNULL(order_table.`base_shipping_refunded`, 0)) * order_table.`base_to_global_rate`)',
            );

            $select = $this->_getWriteAdapter()->select();
            $select->from(array('source_table' => $sourceTable), $columns)
                ->joinInner(
                    array('order_table' => $orderTable),
                    $this->_getWriteAdapter()->quoteInto(
                        'source_table.order_id = order_table.entity_id AND order_table.state <> ?',
                        Mage_Sales_Model_Order::STATE_CANCELED),
                    array()
                )
                ->useStraightJoin();

            $filterSubSelect = $this->_getWriteAdapter()->select();
            $filterSubSelect->from(array('filter_source_table' => $sourceTable), 'MIN(filter_source_table.entity_id)')
                ->where('filter_source_table.order_id = source_table.order_id');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'source_table.created_at'));
            }

            $select->where('source_table.entity_id = (?)', new Zend_Db_Expr($filterSubSelect));
            unset($filterSubSelect);

            $select->group(array(
                'period',
                'store_id',
                'order_status',
                'shipping_description'
            ));

            $this->_getWriteAdapter()->query($select->insertFromSelect($table, array_keys($columns)));

            $select->reset();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr('0'),
                'order_status'          => 'order_status',
                'shipping_description'  => 'shipping_description',
                'orders_count'          => 'SUM(orders_count)',
                'total_shipping'        => 'SUM(total_shipping)',
                'total_shipping_actual' => 'SUM(total_shipping_actual)',
            );

            $select
                ->from($table, $columns)
                ->where('store_id <> 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'order_status',
                'shipping_description'
            ));

            $this->_getWriteAdapter()->query($select->insertFromSelect($table, array_keys($columns)));
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }

        $this->_getWriteAdapter()->commit();
        return $this;

    }
} 