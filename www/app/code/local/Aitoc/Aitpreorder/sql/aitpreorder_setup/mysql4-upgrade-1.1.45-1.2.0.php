<?php
$installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');

$installer->startSetup();

$installer->addAttribute('catalog_product', 'backstock_preorders', array(
    'label'                 => 'Backstock Pre-Orders',
    'type'                  => 'int',
    //'group'                 => 'General',
    'global'                => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'               => false,
    'required'              => false,
    'user_defined'          => false,
    'default'               => '0',
    'used_in_product_listing' => true,
    'is_configurable'       => false,
));

$installer->endSetup(); 
