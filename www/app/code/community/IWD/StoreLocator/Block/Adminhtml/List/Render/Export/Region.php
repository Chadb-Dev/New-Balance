<?php

class IWD_StoreLocator_Block_Adminhtml_List_Render_Export_Region extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{


    public function __construct()
    {
        parent::__construct();
    }

    public function render(Varien_Object $row)
    {

        $countryCode = $row->getCountryId();
        $id = $row->getRegionId();
        $region = $row->getRegion();
        if (!empty($region)) {
            return $region;
        } else {
            if (!empty($countryCode)) {
                $states = Mage::getModel('directory/region_api')->items($countryCode);
                foreach ($states as $state) {
                    if ($state['region_id'] == $id) {
                        if (empty($state['name'])) {
                            $regionModel = Mage::getModel('directory/region')->load($id);
                            return $regionModel->getCode();
                        } else {
                            return $state['code'];
                        }
                    }
                }
            }
        }

        return $row->getRegion();
    }


}