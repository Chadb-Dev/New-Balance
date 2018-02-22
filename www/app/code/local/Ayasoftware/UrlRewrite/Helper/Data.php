<?php
/**
 * @package    Mage_Catalog
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author     EL HASSAN MATA <support@ayasoftware.com>
 */
class Ayasoftware_UrlRewrite_Helper_Data extends Mage_Core_Helper_Abstract
{
    // get simple products url key
    public function getRealUrlKey($product_id) {
        $storeId =  Mage::app()->getStore()->getId(); 
        $coreUrlRewritesTable = Mage::getSingleton('core/resource')->getTableName("core_url_rewrite");
        $configurableSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select()
                ->from(array('c' => $coreUrlRewritesTable), 'c.request_path')
                ->where("c.product_id = ?", $product_id)
                ->where("c.store_id = ?", $storeId);
        $prodIds = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($configurableSelect);
        if( ! empty($prodIds)) {
            return $prodIds[0];
        } else {
            return false;
        }
    }
}
	 