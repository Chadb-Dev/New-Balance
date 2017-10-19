<?php

class IWD_StoreLocator_Model_Resource_Stores extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('storelocator/stores', 'entity_id');
    }

    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }

        $object->setUpdatedAt($currentTime);
        $data = parent::_prepareDataForSave($object);
        return $data;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (Mage::registry('import_storelocator') == true) {
            return parent::_afterSave($object);
        }

        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        $table = $this->getTable('storelocator/store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'locatorstore = ?' => (int)$object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'locatorstore' => (int)$object->getId(),
                    'store_id' => (int)$storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);

    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('storelocator/store'), 'store_id')
            ->where('locatorstore = :locatorstore');
        $binds = array(
            ':locatorstore' => (int)$id
        );

        return $adapter->fetchCol($select, $binds);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'locatorstore = ?' => (int)$object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('storelocator/store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Block
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }

        return parent::_afterLoad($object);
    }

}