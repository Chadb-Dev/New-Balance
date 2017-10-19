<?php
try {
    $installer = $this;
    /** @var $installer Mage_Core_Model_Resource_Setup */

    $installer->startSetup();

    $installer->run(
       "ALTER TABLE {$this->getTable('iwd_storelocator')}
        ADD COLUMN `position` INT(11) NULL DEFAULT NULL AFTER `image`;"
    );

    $installer->endSetup();
} catch (Exception $e) {
    Mage::throwException(Mage::helper('storelocator')->__($e->getMessage()));
}