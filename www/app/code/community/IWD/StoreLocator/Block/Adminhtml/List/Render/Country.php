<?php

class IWD_StoreLocator_Block_Adminhtml_List_Render_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected static $_statuses;

    public function __construct()
    {
        parent::__construct();
    }

    public function render(Varien_Object $row)
    {
        $optionsSession = Mage::getSingleton('core/session')->getCountryOptions(false);
        if (!$optionsSession) {
            $options = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
            Mage::getSingleton('core/session')->setCountryOptions($options);
        } else {
            $options = $optionsSession;
        }


        $id = $row->getCountryId();
        if (empty($id)) {
            return '';
        }

        foreach ($options as $item) {
            if ($item['value'] == $id) {
                return $item['label'];
            }
        }

        return Mage::helper('storelocator')->__('Unknown');
    }


}