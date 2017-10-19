<?php

$installer = $this;
/** @var $installer Mage_Core_Model_Resource_Setup */
try {
    $installer->startSetup();

    $installer->run(
        "CREATE TABLE IF NOT EXISTS {$this->getTable('iwd_storelocator')} (
              `entity_id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `is_active` int(11) NOT NULL,
              `phone` varchar(50) DEFAULT NULL,
              `country_id` varchar(3) NOT NULL,
              `region_id` varchar(255) DEFAULT NULL,
              `region` varchar(255) DEFAULT NULL,
              `street` varchar(255) NOT NULL,
              `city` varchar(255) NOT NULL,
              `postal_code` varchar(15) NOT NULL,
              `stores` int(11) DEFAULT NULL,
              `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `desc` longtext,
              `latitude` varchar(10) NOT NULL,
              `longitude` varchar(10) NOT NULL,
              `website` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`entity_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8"
    );

    $installer->endSetup();
} catch (Exception $e) {
    Mage::throwException(Mage::helper('storelocator')->__($e->getMessage()));
}