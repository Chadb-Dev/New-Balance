<?php

/**
 * @category    Ayasoftware
 * @package     Ayasoftware_PreSelector
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_PreSelector_Block_Adminhtml_Catalog_Product_Edit_Tab_Super_Config_Grid extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid {

    protected function _prepareColumns() {
        $product = $this->_getProduct();
        $cf_product_id =  $product->getId();
        $this->addColumnAfter('preselected_id', array(
            'header' => Mage::helper('adminhtml')->__('Pre-Select'),
            'header_css_class' => 'a-center',
            'align' => 'center',
            'type' => 'radio',
            'html_name' => 'autoselected[]',
            'cf_product_id' => $cf_product_id,
            'align' => 'center',
             'renderer' => 'preselector/adminhtml_catalog_product_edit_tab_super_config_grid_renderer_radio'
                ), 'entity_id');

        return parent::_prepareColumns();
    }

}
