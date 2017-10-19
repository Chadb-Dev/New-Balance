<?php

$installer = $this;
/** @var $installer Mage_Core_Model_Resource_Setup */
try{
    $installer->startSetup();
    
    $installer->run(
        "ALTER TABLE {$this->getTable('iwd_storelocator')}
        CHANGE `stores` `stores` VARCHAR(11) NULL, ADD COLUMN `icon` VARCHAR(255) NULL AFTER `website`;"
    );
    
    $installer->endSetup();
} catch(Exception $e) {
    Mage::throwException(Mage::helper('storelocator')->__($e->getMessage()));
}