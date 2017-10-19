<?php

$installer = $this;
/** @var $installer Mage_Core_Model_Resource_Setup */
try{
    $installer->startSetup();
    
    $installer->run(
        "ALTER TABLE {$this->getTable('iwd_storelocator')} ADD COLUMN `image` VARCHAR(255) NULL AFTER `icon`;"
    );
    
    $installer->endSetup();
}catch(Exception $e){
    Mage::throwException(Mage::helper('storelocator')->__($e->getMessage()));
}