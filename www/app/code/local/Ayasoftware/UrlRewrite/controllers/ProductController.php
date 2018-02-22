<?php
/*
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author     EL HASSAN MATA <support@ayasoftware.com>
 */
include(Mage::getBaseDir() . "/app/code/core/Mage/Catalog/controllers/ProductController.php");

class Ayasoftware_UrlRewrite_ProductController extends Mage_Catalog_ProductController {

    /**
     * Change viewAction logic if 
     * Mage::getStoreConfig('spp/details/producturl', Mage::app()->getStore()->getId()) = 1 
     * and 
     *  Mage::getStoreConfig('catalog/seo/product_use_categories', Mage::app()->getStore()->getId()) = 0; 
     */
    
    public function viewAction() {
        $store_id = Mage::app()->getStore()->getId(); 
        if(!Mage::getStoreConfig('spp/details/producturl', $store_id) || Mage::getStoreConfig('catalog/seo/product_use_categories', $store_id)) {
            return parent::viewAction();
        }
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');
        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);
        // Get parent configurable product
        $_product = Mage::getModel('catalog/product')->load($productId);
        if($_product->getTypeId() == "simple") {
            $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());

            // If parent exists
            if(isset($parentIds[0])) {
                $productId = $parentIds[0];
            }
        }

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch(Exception $e) {
            if($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if(isset($_GET['store']) && ! $this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif( ! $this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }

}
