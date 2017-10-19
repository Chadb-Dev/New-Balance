<?php

class IWD_Storelocator_Block_System_Config_Form_Fieldset_Path extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $path = Mage::getBaseUrl();
        if (preg_match('/demo/i', $path)) {
            $element->setDisabled('disabled');
        }

        return parent::_getElementHtml($element);
    }
}