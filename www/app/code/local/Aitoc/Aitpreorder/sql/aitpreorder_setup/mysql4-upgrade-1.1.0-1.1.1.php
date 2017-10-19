<?php
/**
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 */
 
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->addAttribute('order', 'status_preorder', array(
    'label'        => 'Pre-Order Status',
    'required'     => 0,
    'type'         => 'static',
    'input'        => 'text',
));

$orderTable = $installer->getTable('sales/order');

$installer->run("
	ALTER TABLE `{$orderTable}` ADD `status_preorder` VARCHAR (50) NOT NULL AFTER `status`;
	UPDATE `{$orderTable}` SET `status_preorder`=`status`;
	UPDATE `{$orderTable}` SET `status`='pending' WHERE `status`='pendingpreorder';
	UPDATE `{$orderTable}` SET `status`='processing' WHERE `status`='processingpreorder';
");

if(version_compare(Mage::getVersion(),'1.4.1','>='))
{
	$orderGridTable = $installer->getTable('sales/order_grid');
	$installer->run("
		ALTER TABLE `{$orderGridTable}` ADD `status_preorder` VARCHAR (50) NOT NULL AFTER `status`;
		UPDATE `{$orderGridTable}` SET `status_preorder`=`status`;
		UPDATE `{$orderGridTable}` SET `status`='pending' WHERE `status`='pendingpreorder';
		UPDATE `{$orderGridTable}` SET `status`='processing' WHERE `status`='processingpreorder';
	");
}

$installer->endSetup();
?>