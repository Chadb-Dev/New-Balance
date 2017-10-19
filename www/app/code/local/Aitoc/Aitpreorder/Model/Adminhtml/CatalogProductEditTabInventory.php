<?php

class Aitoc_Aitpreorder_Model_Adminhtml_CatalogProductEditTabInventory extends Aitoc_Aitpreorder_Model_Abstract {

    private $_restrictedTypes = array(
        Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
        Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
    );

    public function getAitCurrentProduct()
    {
        return $this->_block->getProduct();
    }

    public function getDisabled()
    {
        return $this->isPreorder() ? '' : 'disabled="disabled"';
    }

    public function getPreorderDescription()
    {
        $description = $this->getAitCurrentProduct()->getPreorderdescript();
        return strlen($description) ? $description : '';
    }

    public function isPreorder()
    {
        return (int) $this->getAitCurrentProduct()->getPreorder() == 1;
    }

    public function getDataArray()
    {
        return array(
            'is_preorder'           => $this->isPreorder(),
            'preorder_description'  => $this->getPreorderDescription(),
            'disabled'              => $this->getDisabled(),
            'backstock_preorders'   => $this->getAitCurrentProduct()->getBackstockPreorders()
        );
    }

    public function getRestrictedTypes()
    {
        return $this->_restrictedTypes;
    }

    public function canShowBlock()
    {
        return !in_array($this->getAitCurrentProduct()->getData('type_id'), $this->getRestrictedTypes());
    }
    
    public function getDescriptionAttributeScope()
    {
        return $this->getAttributeScope('preorderdescript');
    }

    protected function _toHtml($html)
    {
        if ($this->canShowBlock()) {
            $preorder = $this->_block->getLayout()
                ->createBlock('core/template', '', $this->getDataArray())
                ->setTemplate('aitpreorder/preorderinventory.phtml')
                ->setDescriptionAttributeScope($this->getDescriptionAttributeScope())
                ->setBackstockPreorderScope($this->getAttributeScope('backstock_preorders'))
                ->toHtml();

            $html = str_replace(__('Backorders') . '</label>', __('Backorders') . '\\' . __('Pre-Orders') . '</label>', $html);
            $html = str_replace('</table>', $preorder, $html);
        }

        return $html;
    }

}
