<?php

class IWD_StoreLocator_Block_Adminhtml_List_Render_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected static $_statuses;

    public function __construct()
    {
        self::$_statuses = array(0 => Mage::helper('storelocator')
            ->__('Disabled'), 1 => Mage::helper('storelocator')->__('Enabled'));
        parent::__construct();
    }

    public function render(Varien_Object $row)
    {
        return Mage::helper('storelocator')->__($this->getStatus($row->getIsActive()));
    }

    public static function getStatus($status)
    {
        if (isset(self::$_statuses [$status])) {
            return self::$_statuses [$status];
        }

        return Mage::helper('storelocator')->__('Unknown');
    }
}