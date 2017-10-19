<?php

class IWD_StoreLocator_Block_Search_Result extends Mage_Core_Block_Template
{


    private $_countryOptoins = null;
    private $_states = array();
    private $_prepareCountry = array();

    public function prepareState($countryCode, $region, $regionId)
    {
        if (!empty($region)) {
            return $region;
        } else {
            $this->_states = Mage::registry('dealer_state');
            if (!isset($this->_states[$countryCode])) {
                $this->_states[$countryCode] = Mage::getModel('directory/region_api')->items($countryCode);
            }

            Mage::unregister('dealer_state');
            Mage::register('dealer_state', $this->_states);
            $result = array();
            foreach ($this->_states[$countryCode] as $state) {
                if ($state['region_id'] == $regionId) {
                    if (empty($state['name'])) {
                        $regionModel = Mage::getModel('directory/region')->load($regionId);
                        return $regionModel->getName();
                    } else {
                        return $state['name'];
                    }
                }
            }
        }

        return '';
    }


    public function prepareCountry($country)
    {

        $this->_prepareCountry = Mage::registry('dealer_country');

        if ($this->_prepareCountry == null) {
            $this->_prepareCountry = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
        }

        Mage::unregister('dealer_country');
        Mage::register('dealer_country', $this->_prepareCountry);

        foreach ($this->_prepareCountry as $option) {
            if ($option['value'] == $country) {
                return $option['label'];
            }
        }

        return '';
    }


    public function prepareImage($store)
    {
        $image = $store->getImage();

        $image = $this->helper('storelocator/image')->init('image', $image)->resize(247, 137);
        return $image;
    }

}