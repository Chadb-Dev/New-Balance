<?php

class IWD_StoreLocator_Block_Adminhtml_List_Render_Export_Stores extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{


    public function __construct()
    {
        parent::__construct();
    }

    public function render(Varien_Object $row)
    {
        $item = Mage::getModel('storelocator/stores')->load($row->getId());
        $ids = $item->getStoreId();
        return implode(',', $ids);
    }
}
