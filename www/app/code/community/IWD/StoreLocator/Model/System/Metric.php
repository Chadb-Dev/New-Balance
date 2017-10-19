<?php

class IWD_StoreLocator_Model_System_Metric
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('storelocator')->__('Km')),
            array('value' => 2, 'label' => Mage::helper('storelocator')->__('Miles')),
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
            1 => Mage::helper('storelocator')->__('Km'),
            2 => Mage::helper('storelocator')->__('Miles'),
        );
    }

}