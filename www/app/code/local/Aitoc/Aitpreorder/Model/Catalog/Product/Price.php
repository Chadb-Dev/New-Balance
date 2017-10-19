<?php

class Aitoc_Aitpreorder_Model_Catalog_Product_Price extends Aitoc_Aitpreorder_Model_Abstract {

    protected function _toHtml($html)
    {
        $_product = $this->getProduct();
        Mage::getSingleton('aitpreorder/stockloader')->applyStockToProduct($_product);
        $preOrderFlag = false;
        $inStock = false;
        if ($_product->getTypeId() == 'configurable') {
            // $opt = new Aitoc_Aitpreorder_Block_Rewrite_CatalogProductViewTypeConfigurable();
            // echo var_dump($opt->getJsonConfigWithPreorder());

            $associatedProducts = Mage::getSingleton('catalog/product_type')->factory($_product)->getUsedProducts($_product);
            Mage::getSingleton('aitpreorder/stockloader')->applyStockToProductCollection($associatedProducts);
            foreach ($associatedProducts as $associatedProduct) {
                if ($associatedProduct->getPreorder() == '1') {
                    $preOrderFlag = true;
                } else {
                    if ($associatedProduct->getData('is_in_stock')) {
                        $inStock = true;
                    }
                }
            }
        }

        if ($inStock) {
            $preOrderFlag = false;
        }

        $_id = $_product->getId();
        $add_preorder_before_price = "";

        if ($_product->getPreorder() == 1 or $preOrderFlag) {
            return(str_replace('price-box"', 'price-box"><span class="regular-price price pre-order">' . $this->__('Pre-Order') . '</span', $html));
        } else {
            return ($add_preorder_before_price . $html);
        }
    }

}