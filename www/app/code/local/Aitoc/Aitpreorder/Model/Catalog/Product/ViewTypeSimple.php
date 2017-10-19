<?php

class Aitoc_Aitpreorder_Model_Catalog_Product_ViewTypeSimple extends Aitoc_Aitpreorder_Model_Abstract {

    protected function _toHtml($html)
    {
        $product = $this->getBlock()->getProduct();
        if ($product->getPreorder()) {
            $catalogHelper = Mage::helper('catalog');

            $descript = $product->getPreorderdescript();
            if ($descript == "") {
                $descript = $this->getBlock()->__('Pre-Order');
            }
            if (stripos($html, $this->getBlock()->__('Out of stock'))) {
                if(Mage::helper('aitpreorder')->isBackstockPreorderAllowed($product)) {
                    if (!stripos($descript, 'availability out-of-stock')) {
                        $html = str_ireplace('availability out-of-stock', 'availability in-stock', $html);
                    }
                } else {
                    $descript = $this->getBlock()->__('not Available');
                }
                $html = str_ireplace($this->getBlock()->__('Out of stock'), " " . $descript, $html);
            }
            if (!stripos($descript, $this->getBlock()->__('Out of stock'))) {
                $html = str_ireplace($this->getBlock()->__('In stock'), " " . $descript, $html);
            }
            

        }

        return $html;
    }

}