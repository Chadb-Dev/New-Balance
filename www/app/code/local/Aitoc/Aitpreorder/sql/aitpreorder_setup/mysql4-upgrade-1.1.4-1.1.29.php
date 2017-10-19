<?php

/**
 * @copyright  Copyright (c) 2012 AITOC, Inc.
 */

$this->startSetup();

$preorderscriptId = $this->getAttribute('catalog_product', 'preorderdescript', 'attribute_id');

if ($preorderscriptId)
{
	$this->run(
        ' UPDATE ' . $this->getTable('catalog/eav_attribute') .
		' SET is_global = ' . Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE .
		' WHERE	attribute_id = ' . $preorderscriptId
    );
}

$this->endSetup();

?>