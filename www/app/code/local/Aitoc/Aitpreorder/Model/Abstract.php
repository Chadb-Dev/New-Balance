<?php

abstract class Aitoc_Aitpreorder_Model_Abstract extends Mage_Core_Model_Abstract {

    protected $_block;

    abstract protected function _toHtml($html);

    public function initBlock($block)
    {
        $this->_block = $block;
        return $this;
    }
    
    public function getBlock()
    {
        return $this->_block;
    }
    
    public function __($string)
    {
        return $this->getBlock()->__($string);
    }
    
    public function getProduct()
    {
        return $this->getBlock()->getProduct();        
    }

    public function getAttributeScope($attribute_code)
    {
        $html = '';
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute_code);

        if ($attribute->isScopeGlobal()) {
            $html.= '[GLOBAL]';
        } elseif ($attribute->isScopeWebsite()) {
            $html.= '[WEBSITE]';
        } elseif ($attribute->isScopeStore()) {
            $html.= '[STORE VIEW]';
        }

        return $html;        
    }

    public function applyHtml($transport)
    {
        $html = $transport->getHtml();
        $html = $this->_toHtml($html);
        $transport->setHtml($html);
    }

}
