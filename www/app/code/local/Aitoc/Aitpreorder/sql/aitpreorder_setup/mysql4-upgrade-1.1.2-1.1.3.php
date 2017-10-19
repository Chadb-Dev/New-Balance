<?php
/**
 * @copyright  Copyright (c) 2010 AITOC, Inc.
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

if (version_compare(Mage::getVersion(),'1.5.0','>='))
{
    $statusTable = $installer->getTable('sales/order_status');
    $data = array(
        array('status' => 'pendingpreorder', 'label' => 'Pending Pre-Order'),
        array('status' => 'processingpreorder', 'label' => 'Processing Pre-Order')
    );
    $installer->getConnection()->insertArray($statusTable, array('status', 'label'), $data);
}

$installer->endSetup();
?>