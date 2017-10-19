<?php

class Aitoc_Aitpreorder_Model_Catalog_Product_View extends Aitoc_Aitpreorder_Model_Abstract {

    protected function _toHtml($html)
    {
        $product = $this->getBlock()->getProduct();
        if ($this->getBlock()->getNameInLayout() == 'product.info.addtocart'
            && $product->getPreorder()
            || (!$product->isAvailable() && $product->isSalable())
        ) {
            $html = str_replace($this->__('Add to Cart'), $this->__('Pre-Order'), $html);
        }

        return $html;
    }

}