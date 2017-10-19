<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

/* AITOC static rewrite inserts start */
/* $meta=%default,Aitoc_Aiteditablecart% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aiteditablecart')){
    class Aitoc_Aitpreorder_Block_Rewrite_BundleCheckoutCartItemRenderer_Aittmp extends Aitoc_Aiteditablecart_Block_Rewrite_FrontBundleCheckoutCartItemRenderer {} 
 }else{
    /* default extends start */
    class Aitoc_Aitpreorder_Block_Rewrite_BundleCheckoutCartItemRenderer_Aittmp extends Mage_Bundle_Block_Checkout_Cart_Item_Renderer {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitpreorder_Block_Rewrite_BundleCheckoutCartItemRenderer extends Aitoc_Aitpreorder_Block_Rewrite_BundleCheckoutCartItemRenderer_Aittmp
{
 
    public function getOptionList($useCache = true)
    {
        return $this->_getBundleOptions($useCache);
    }

    protected function _getBundleOptions($useCache = true)
    {
        $options = array();

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $this->getProduct()->getTypeInstance(true);

        // get bundle options
        $optionsQuoteItemOption =  $this->getItem()->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = unserialize($optionsQuoteItemOption->getValue());
        if ($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $this->getProduct());

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $this->getItem()->getOptionByCode('bundle_selection_ids');

            $selectionsCollection = $typeInstance->getSelectionsByIds(
                unserialize($selectionsQuoteItemOption->getValue()),
                $this->getProduct()
            );

            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            foreach ($bundleOptions as $bundleOption) {
                if ($bundleOption->getSelections()) {
                    $option = array('label' => $bundleOption->getTitle(), "value" => array());
                    $bundleSelections = $bundleOption->getSelections();

                    foreach ($bundleSelections as $bundleSelection) {
                        $addinf='';
                        $selectionQty = $this->getProduct()->getCustomOption('selection_qty_' . $bundleSelection->getSelectionId());
                        $_product = Mage::getModel('catalog/product')->load($selectionQty->getProduct()->getId());
                        if($_product->getPreorder()=='1')
                        {
                            $preorderdescript='';
                            if($_product->getPreorderdescript()!="")
                            {
                                $preorderdescript=', '.$_product->getPreorderdescript();
                            }
                            $addinf='<p class="item-msg notice">* '.__('Pre-Order').$preorderdescript.'</p>';
                        }
                 
                        $option['value'][] = $this->_getSelectionQty($bundleSelection->getSelectionId()).' x '. $this->htmlEscape($bundleSelection->getName()). ' ' .Mage::helper('core')->currency($this->_getSelectionFinalPrice($bundleSelection)).$addinf;
                    }

                    $options[] = $option;
                }
            }
        }
        return $options;
    }

}