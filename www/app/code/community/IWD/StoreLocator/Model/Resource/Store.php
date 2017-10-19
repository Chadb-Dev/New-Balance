<?php
class IWD_StoreLocator_Model_Resource_Store extends Mage_Core_Model_Resource_Db_Abstract {
    
    protected function _construct()
    {
        $this->_init('storelocator/store', 'entity_id');
    }
}