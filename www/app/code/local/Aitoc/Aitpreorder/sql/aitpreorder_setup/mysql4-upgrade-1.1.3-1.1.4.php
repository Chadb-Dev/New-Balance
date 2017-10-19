<?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$entityTypeId = 'catalog_product';
$preorderId = $this->getAttribute($entityTypeId, 'preorder', 'attribute_id');
$preorderscriptId = $this->getAttribute($entityTypeId, 'preorderdescript', 'attribute_id');

$aitquantitymanagerTable = null;
$updatePreorder = false;

$inventoryTable = null;
$updateInventory = false;

try {
	$aitquantitymanagerTable = $installer->getTable('aitquantitymanager/stock_item');
}
catch (Exception $e) {}

if (!$aitquantitymanagerTable)
{
	 $aitquantitymanagerTable = (string) Mage::getConfig()->getTablePrefix() . 'aitoc_cataloginventory_stock_item';
}

if ($aitquantitymanagerTable && $installer->tableExists($aitquantitymanagerTable))
{
	$updatePreorder = true;
}

try {
	$inventoryTable = $installer->getTable('cataloginventory/stock_item');
	$updateInventory = $installer->tableExists($inventoryTable);
}
catch (Exception $e)
{
	$inventoryTable = null;
	$updateInventory = false;
}

$attributeIds = array();

if ($preorderId)
{
	$attributeIds[] = $preorderId;
}

if ($preorderscriptId)
{
	$attributeIds[] = $preorderscriptId;
}

if ($updatePreorder && count($attributeIds))
{
	$installer->run('
		UPDATE
			' . $installer->getTable('catalog/eav_attribute') . '
		SET
			is_global = ' . Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE . '
		WHERE
			attribute_id IN (' . implode(',', $attributeIds) . ')');
}

if ($updatePreorder && $preorderId)
{
	$installer->run('
		UPDATE
			' . $aitquantitymanagerTable .  '
		SET
			backorders = ' . Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS . '
		WHERE
			product_id IN (SELECT DISTINCT CPEI.entity_id FROM ' . $installer->getTable('catalog_product_entity_int') . ' AS CPEI WHERE CPEI.attribute_id = ' . $preorderId . ' AND CPEI.value = 1);
	');
}

if ($updateInventory && $preorderId)
{
	$installer->run('
		UPDATE
			' . $inventoryTable .  '
		SET
			backorders = ' . Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders::BACKORDERS_YES_PREORDERS . '
		WHERE
			product_id IN (SELECT DISTINCT CPEI.entity_id FROM ' . $installer->getTable('catalog_product_entity_int') . ' AS CPEI WHERE CPEI.attribute_id = ' . $preorderId . ' AND CPEI.value = 1);
	');
}

$installer->endSetup();
?>