<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/
class Aitoc_Aitpreorder_Block_Rewrite_AiteditablecartBundleCatalogProductViewTypeBundleOptionCheckbox extends Aitoc_Aiteditablecart_Block_BundleCatalogProductViewTypeBundleOptionCheckbox                                          
{
    public function getSelectionQtyTitlePrice($_selection, $includeContainer = true)
    {
        $addinf='';
        $_product = Mage::getModel('catalog/product')->load($_selection->getId());
        if($_product->getPreorder()==1)
        {
            $addinf=__('Pre-Order');
        }
        return parent::getSelectionQtyTitlePrice($_selection, $includeContainer = true).'  <span class="price-notice">'.$addinf.'</span>';  
    } 
  
}            