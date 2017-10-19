<?php
$status = false;
try {
    $installer = $this;
    /** @var $installer Mage_Core_Model_Resource_Setup */

    $installer->startSetup();
    $installer->run(
        "DROP TABLE IF EXISTS {$this->getTable('iwd_storelocator_store')};
              CREATE TABLE  {$this->getTable('iwd_storelocator_store')} (
                                                      `entity_id` int(11) NOT NULL AUTO_INCREMENT,
                                                      `store_id` int(11) NOT NULL,
                                                      `locatorstore` int(11) NOT NULL,
                                                      PRIMARY KEY (`entity_id`)
                                                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;"
    );

    $installer->endSetup();
} catch (Exception $e) {
    $status = false;
}


if ($status == true) {
    try {
        $collection = Mage::getModel('storelocator/stores')->getCollection();

        foreach ($collection as $item) {
            $stores = $item->getStores();
            $storesList = explode(',', $stores);
            foreach ($storesList as $id) {
                $id = trim($id);
                $model = Mage::getModel('storelocator/store');
                if (!empty($id)) {
                    $model->setData('store_id', $id);
                    $model->setData('locatorstore', $item->getId());

                    try {
                        $model->save();
                    } catch (Exception $e) {
                        Mage::throwException(Mage::helper('storelocator')->__($e->getMessage()));
                    }
                }
            }
        }

        $installer->startSetup();

        $installer->run(
            "ALTER TABLE {$this->getTable('iwd_storelocator')} DROP COLUMN `stores`;"
        );
        $installer->endSetup();
    } catch (Exception $e) {
        $status = false;
    }
}