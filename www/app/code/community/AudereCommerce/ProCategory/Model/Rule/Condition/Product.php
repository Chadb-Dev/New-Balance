<?php
/**
 * Audere Commerce
 *
 * NOTICE OF LICENCE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customise this module for your
 * needs please contact Audere Commerce (http://www.auderecommerce.com).
 *
 * @category    AudereCommerce
 * @package     AudereCommerce_ProCategory
 * @copyright   Copyright (c) 2013, 2014 Audere Commerce Limited. (http://www.auderecommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      James Withers <james+procategory@auderecommerce.com>
 */

class AudereCommerce_ProCategory_Model_Rule_Condition_Product
    extends Mage_Rule_Model_Condition_Product_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes = Mage::getResourceSingleton('catalog/product')
                ->loadAllAttributes()
                ->getAttributesByCode();

        $attributeOptions = array();
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ($attribute->isAllowedForRuleCondition()) {
                $attributeOptions[$attribute->getAttributeCode()] = ucwords($attribute->getFrontendLabel());
            }            
        }

        $this->_addSpecialAttributes($attributeOptions);
        asort($attributeOptions);
        $this->setAttributeOption($attributeOptions);

        return $this;
    }
    
    protected function _addSpecialAttributes(array &$attributeOptions)
    {
        parent::_addSpecialAttributes($attributeOptions);
        $attributeOptions['type_id'] = Mage::helper('catalogrule')->__('Product Type');
    }
    
    protected function _compareValues($validatedValue, $value, $strict = true)
    {
        if (is_null($validatedValue) && is_numeric($value)) {
            return 0 == $value;
        } else {    
            return parent::_compareValues($validatedValue, $value, $strict);
        }        
    }
    
    public function getValueElementType()
    {
        return $this->getAttribute() == 'type_id' ? 'select' : parent::getValueElementType();
    }
    
    protected function _prepareValueOptions()
    {
        if ($this->getAttribute() == 'type_id') {
            $selectOptions = array(
                array(
                    'value' => Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
                    'label' => 'Bundle'
                ),
                array(
                    'value' => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                    'label' => 'Configurable'
                ),
                array(
                    'value' => Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
                    'label' => 'Grouped'
                ),
                array(
                    'value' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                    'label' => 'Simple'
                ),
                array(
                    'value' => Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
                    'label' => 'Virtual'
                )
            );       

            if ($selectOptions !== null) {
                if (!$selectReady) {
                    $this->setData('value_select_options', $selectOptions);
                }
                
                if (!$hashedReady) {
                    $hashedOptions = array();
                    
                    foreach ($selectOptions as $o) {
                        if (!is_array($o['value'])) {
                            $hashedOptions[$o['value']] = $o['label'];
                        }                        
                    }
                    
                    $this->setData('value_option', $hashedOptions);
                }
            }
            
        } else {
            parent::_prepareValueOptions();
        }
    }
    
    public function getInputType()
    {
        if ($this->getAttribute() == 'type_id') {
            return 'select';
        } else {
            return parent::getInputType();
        }        
    }
}