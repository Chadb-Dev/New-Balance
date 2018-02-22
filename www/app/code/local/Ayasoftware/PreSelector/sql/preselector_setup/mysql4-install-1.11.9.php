<?php
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
if($installer->getConnection()->tableColumnExists($installer->getTable('catalog/product_super_link'), 'preselected_id')):
    Mage::log('Column preselect already exists!');
else:
    $installer->getConnection()->addColumn($installer->getTable('catalog/product_super_link'), 'preselected_id', 'tinyint(1) NOT NULL');
endif;
$installer->endSetup();
