<?php

/**
 * @category    Ayasoftware
 * @package     Ayasoftware_PreSelector
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_PreSelector_Block_Adminhtml_Catalog_Product_Edit_Tab_Super_Config_Grid_Renderer_Radio extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Radio {

    /**
     * Renders grid column value
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        $cf_product_id = $this->getColumn()->getCfProductId();
        $checked = ($row->getId() === $this->preSelectedRow($cf_product_id)) ? ' checked="checked"' : '';
        $html = '<input onclick="removeAttr('.$row->getId(). ')" type="radio" name="' . $this->getColumn()->getHtmlName() . '" ';
        $html .= 'value="' . $row->getId() . '" class="radio"' . $checked . '/>';
        return $html;
    }
    /**
     * get the id of the preselected simple product. 
     * @param int $id
     * @return int
     */
    public function preSelectedRow($id) {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $linkTable = $resource->getTableName("catalog/product_super_link");
        $query = 'SELECT product_id FROM ' . $linkTable . ' WHERE parent_id = '
                . (int) $id . '  AND preselected_id = 1' ;
        return $readConnection->fetchOne($query);
    }

}
