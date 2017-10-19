<?php
class IWD_StoreLocator_Model_Resource_Store_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    
    protected function _construct()
    {
        $this->_init('storelocator/store');
    }
}