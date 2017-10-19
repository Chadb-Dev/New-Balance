<?php

class IWD_Storelocator_Block_Adminhtml_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_list';
        $this->_blockGroup = 'storelocator';
        $this->_headerText = Mage::helper('storelocator')->__('Stores Locations');
        $this->_addButtonLabel = Mage::helper('storelocator')->__('Add New Store');

        $click = "setLocation('" . $this->getUrl('adminhtml/iwd_storelocator_import/removeall', array('_secure' => true)) . "');";
        $message = "if (confirm('Are you sure you want remove All stores?')){" . $click . "}";
        $this->addButton('removeall', $data = array('label' => 'Remove All Stores', 'onclick' => $message, 'class' => 'back'));

        $click = "setLocation('" . $this->getUrl('adminhtml/iwd_storelocator_import/fill', array('_secure' => true)) . "');";
        $this->addButton('fill', $data = array('label' => 'Fill Stores Geo Data', 'onclick' => $click, 'class' => 'back'));


        $click = "setLocation('" . $this->getUrl('adminhtml/iwd_storelocator_import/index', array('_secure' => true)) . "');";
        $this->addButton('import', $data = array('label' => 'Import Stores', 'onclick' => $click));


        parent::__construct();
    }

}