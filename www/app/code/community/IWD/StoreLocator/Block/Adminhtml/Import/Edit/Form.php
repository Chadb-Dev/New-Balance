<?php

class IWD_Storelocator_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add fieldset
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('importexport');
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/validate'),
                'method' => 'post',
                'enctype' => 'multipart/form-data')
        );

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('Import Settings')));


        $fieldset->addField(
            Mage_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE,
            'file',
            array(
                'name' => Mage_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE,
                'label' => $helper->__('Select File to Import'),
                'title' => $helper->__('Select File to Import'),
                'required' => true)
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
