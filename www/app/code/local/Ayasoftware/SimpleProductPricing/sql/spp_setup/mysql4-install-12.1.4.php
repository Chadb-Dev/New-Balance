<?php

$installer = $this;
$installer->startSetup();

$installer->installEntities();

$attributeId = $installer->getAttributeId('catalog_product', 'use_simple_product_pricing');

foreach ($installer->getAllAttributeSetIds('catalog_product') as $attributeSetId) {
    try {
        $attributeGroupId = $installer->getAttributeGroupId('catalog_product', $attributeSetId, 'General');
    } catch (Exception $e) {
        $attributeGroupId = $installer->getDefaultAttributeGroupId('catalog_product', $attributeSetId);
    }
    $installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
}

$installer->endSetup();
