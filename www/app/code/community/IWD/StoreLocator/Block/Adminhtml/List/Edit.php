<?php

class IWD_StoreLocator_Block_Adminhtml_List_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'storelocator';
        $this->_controller = 'adminhtml_list';
        parent::__construct();
        $this->_updateButton('delete', 'label', Mage::helper('storelocator')->__('Delete'));
        $this->_addButton(
            'delete', array(
            'label' => Mage::helper('adminhtml')->__('Delete'),
            'onclick' => 'deleteStore()',
            'class' => 'delete',),
            0
        );

        $this->_updateButton('save', 'label', Mage::helper('storelocator')->__('Save Store'));

        $this->_addButton(
            'saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',),
            -100
        );


    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('storelocator_store')->getId()) {
            return Mage::helper('storelocator')
                ->__("Edit Store '%s'", $this->htmlEscape(Mage::registry('storelocator_store')->getTitle()));
        } else {
            return Mage::helper('storelocator')->__('New Store');
        }
    }


    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'adminhtml/iwd_storelocator_list/save', array(
                '_current' => true,
                'back' => 'edit',
                'active_tab' => '{{tab_id}}'
            )
        );
    }

    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {


        $url = $this->getUrl(
            'adminhtml/iwd_storelocator_list/delete', array(
                '_current' => true,
                'back' => 'edit'
            )
        );

        $this->_formScripts[] = "
            
    
            function saveAndContinueEdit() {
              editForm.submit($('edit_form').action+'back/edit/');
            }
    			
    		function deleteStore() {
              setLocation('{$url}');;
            }
        ";
        return parent::_prepareLayout();
    }
}
