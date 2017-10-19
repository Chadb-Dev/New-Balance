<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitpreorder_Model_Mysql4_Report_Refunded_1400 extends Mage_Sales_Model_Mysql4_Report_Refunded
{
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        try {
            $tableName = $this->getTable('sales/refunded_aggregated_order');
            $writeAdapter = $this->_getWriteAdapter();

            $writeAdapter->beginTransaction();

            if (is_null($from) && is_null($to)) {
                $writeAdapter->query("TRUNCATE TABLE {$tableName}");
            } else {
                $where = (!is_null($from)) ? "so.updated_at >= '{$from}'" : '';
                if (!is_null($to)) {
                    $where .= (!empty($where)) ? " AND so.updated_at <= '{$to}'" : "so.updated_at <= '{$to}'";
                }

                $subQuery = $writeAdapter->select();
                $subQuery->from(array('so' => $this->getTable('sales/order')), array('DISTINCT DATE(so.created_at)'))
                    ->where($where);

                $deleteCondition = 'DATE(period) IN (' . new Zend_Db_Expr($subQuery) . ')';
                $writeAdapter->delete($tableName, $deleteCondition);
            }

            $columns = array(
                'period'            => "DATE(created_at)",
                'store_id'          => 'store_id',
                'order_status'      => 'status_preorder',
                'orders_count'      => 'COUNT(`total_refunded`)',
                'refunded'          => 'SUM(`base_total_refunded` * `base_to_global_rate`)',
                'online_refunded'   => 'SUM(`base_total_online_refunded` * `base_to_global_rate`)',
                'offline_refunded'  => 'SUM(`base_total_offline_refunded` * `base_to_global_rate`)'
            );

            $select = $writeAdapter->select()
                ->from($this->getTable('sales/order'), $columns)
                ->where('state <> ?', Mage_Sales_Model_Order::STATE_CANCELED)
                ->where('base_total_refunded > 0');

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(created_at) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    "DATE(created_at)",
                    'store_id',
                    'order_status'
                ));

                $select->having('orders_count > 0');

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

            $select = $writeAdapter->select();

            $columns = array(
                'period'            => 'period',
                'store_id'          => new Zend_Db_Expr('0'),
                'order_status'      => 'order_status',
                'orders_count'      => 'SUM(orders_count)',
                'refunded'          => 'SUM(refunded)',
                'online_refunded'   => 'SUM(online_refunded)',
                'offline_refunded'  => 'SUM(offline_refunded)'
            );

            $select
                ->from($tableName, $columns)
                ->where("store_id <> 0");

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(period) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    'period',
                    'order_status'
                ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $writeAdapter->commit();
        return $this;
    }

    protected function _aggregateByRefundCreatedAt($from, $to)
    {
        try {
            $tableName = $this->getTable('sales/refunded_aggregated');
            $writeAdapter = $this->_getWriteAdapter();

            $writeAdapter->beginTransaction();

            if (is_null($from) && is_null($to)) {
                $writeAdapter->query("TRUNCATE TABLE {$tableName}");
            } else {
                $where = (!is_null($from)) ? "so.updated_at >= '{$from}'" : '';
                if (!is_null($to)) {
                    $where .= (!empty($where)) ? " AND so.updated_at <= '{$to}'" : "so.updated_at <= '{$to}'";
                }

                $subQuery = $writeAdapter->select();
                $subQuery->from(array('so' => $this->getTable('sales/order')), array('DISTINCT DATE(so.created_at)'))
                    ->where($where);

                $deleteCondition = 'DATE(period) IN (' . new Zend_Db_Expr($subQuery) . ')';
                $writeAdapter->delete($tableName, $deleteCondition);
            }

            $creditmemo = Mage::getResourceSingleton('sales/order_creditmemo');
            $creditmemoAttr = $creditmemo->getAttribute('order_id');

            $columns = array(
                'period'            => "DATE(soe.created_at)",
                'store_id'          => 'so.store_id',
                'order_status'      => 'so.status_preorder',
                'orders_count'      => 'COUNT(so.`total_refunded`)',
                'refunded'          => 'SUM(so.`base_total_refunded` * so.`base_to_global_rate`)',
                'online_refunded'   => 'SUM(so.`base_total_online_refunded` * so.`base_to_global_rate`)',
                'offline_refunded'  => 'SUM(so.`base_total_offline_refunded` * so.`base_to_global_rate`)'
            );

            $select = $writeAdapter->select()
                ->from(array('soe' => $this->getTable('sales/order_entity')), $columns)
                ->where('state <> ?', 'canceled')
                ->where('base_total_refunded > 0');

            $select->joinInner(array('soei' => $this->getTable($creditmemoAttr->getBackend()->getTable())),
                "`soei`.`entity_id` = `soe`.`entity_id`
                AND `soei`.`attribute_id` = {$creditmemoAttr->getAttributeId()}
                AND `soei`.`entity_type_id` = `soe`.`entity_type_id`",
                array()
            );

            $select->joinInner(array(
                'so' => $this->getTable('sales/order')),
                '`soei`.`value` = `so`.`entity_id`',
                array()
            );

            if (!is_null($from) || !is_null($to)) {
                $select->where("DATE(soe.created_at) IN(?)", new Zend_Db_Expr($subQuery));
            }

            $select->group(array(
                "DATE(soe.created_at)",
                'store_id',
                'order_status'
            ));

            $select->having('orders_count > 0');

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

            $select = $writeAdapter->select();

            $columns = array(
                'period'            => 'period',
                'store_id'          => new Zend_Db_Expr('0'),
                'order_status'      => 'order_status',
                'orders_count'      => 'SUM(orders_count)',
                'refunded'          => 'SUM(refunded)',
                'online_refunded'   => 'SUM(online_refunded)',
                'offline_refunded'  => 'SUM(offline_refunded)'
            );

            $select
                ->from($tableName, $columns)
                ->where("store_id <> 0");

                if (!is_null($from) || !is_null($to)) {
                    $select->where("DATE(period) IN(?)", new Zend_Db_Expr($subQuery));
                }

                $select->group(array(
                    'period',
                    'order_status'
                ));

            $writeAdapter->query("
                INSERT INTO `{$tableName}` (" . implode(',', array_keys($columns)) . ") {$select}
            ");

        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }
        $writeAdapter->commit();
        return $this;
    } 
} 