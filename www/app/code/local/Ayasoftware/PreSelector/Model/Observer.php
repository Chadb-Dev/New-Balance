<?php

/**
 * @category    Ayasoftware
 * @package     Ayasoftware_PreSelector
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_PreSelector_Model_Observer {
    /**
     * Update  preselected_id value (set it to 1) in catalog_product_super_link table. 
     * @param type $observer
     */
    public function catalogProductSaveAfter($observer) {
        $product = $observer->getEvent()->getProduct();
        $autoSelected = Mage::app()->getRequest()->getParam("autoselected");
        if($actionInstance = Mage::app()->getFrontController()->getAction()) {
            $action = $actionInstance->getFullActionName();
            if($action == 'adminhtml_catalog_product_save') { //if on admin save action
                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $linkTable = Mage::getSingleton('core/resource')->getTableName("catalog/product_super_link");
                // reset preselected_id
                $write->update($linkTable, array('preselected_id' => 0), $write->quoteInto('parent_id=?', (int) $product->getId()));
                if( ! empty($autoSelected)) {
                    $where = array();
                    $where[] = $write->quoteInto('product_id = ?', (int) $autoSelected[0]);
                    $where[] = $write->quoteInto('parent_id = ?', (int) $product->getId());
                    $write->update($linkTable, array('preselected_id' => 1), $where);
                }
            }
        }
    }

}
