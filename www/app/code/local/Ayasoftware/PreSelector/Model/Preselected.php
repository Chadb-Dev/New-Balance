<?php

/**
 * @category    Ayasoftware
 * @package     Ayasoftware_PreSelector
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_PreSelector_Model_Preselected extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Radio {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        Mage::log($value, null, 'preselect'); 
        return 0; 
       // return $value; 
        //return '<span style="color:red;">' . $value . '</span>';
    }

}
