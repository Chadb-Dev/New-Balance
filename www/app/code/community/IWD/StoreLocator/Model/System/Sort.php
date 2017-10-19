<?php

class IWD_StoreLocator_Model_System_Sort
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('storelocator')->__('Position')),
            array('value' => 2, 'label' => Mage::helper('storelocator')->__('Title')),
            array('value' => 3, 'label' => Mage::helper('storelocator')->__('Distance'))
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            1 => Mage::helper('storelocator')->__('Position'),
            2 => Mage::helper('storelocator')->__('Title'),
            3 => Mage::helper('storelocator')->__('Distance'),
        );
    }

}