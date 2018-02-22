<?php
/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleProductPricing
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
require_once 'Mage/Catalog/controllers/ProductController.php';

class Ayasoftware_SimpleProductPricing_AjaxController extends Mage_Catalog_ProductController {

    public function coAction() {
        $product = $this->_initProduct();
        if (!empty($product)) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    
    public function productattributesAction() {
        $product = $this->_initProduct();
        if (!empty($product)) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }
    public function priceAction() {
        $product = $this->_initProduct();
        if (!empty($product)) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }
    public function imageAction() {
        $product = $this->_initProduct();
        if (!empty($product)) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }
    
    
    public function galleryAction()
    {
       $product = $this->_initProduct();
       if (!empty($product)) {
           #$this->_initProductLayout($product);
           $this->loadLayout();
           $this->renderLayout();
       }
    }
    protected function _initProduct() {
        $productId = (int) $this->getRequest()->getParam('id');
        $parentId = (int) $this->getRequest()->getParam('pid');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
            if (!$product->getId()) {
                return false;
            }
            Mage::register('current_product', $product);
            Mage::register('product', $product);

            return $product;
        }
        $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($parentId);
        if (!$product->getId()) {
            return false;
        }
        Mage::register('current_product', $product);
        Mage::register('product', $product);
        return $product;
    }

}
