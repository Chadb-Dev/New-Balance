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

class AudereCommerce_ProCategory_Model_Rule_Condition_Combine
    extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('auderecommerce_procategory/rule_condition_combine');
        
        return $this;
    }

    public function getNewChildSelectOptions()
    {
        $productCondition = Mage::getModel('auderecommerce_procategory/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        
        foreach ($productAttributes as $code=>$label) {
            $attributes[] = array('value'=>'auderecommerce_procategory/rule_condition_product|'.$code, 'label'=>$label);
        }
        
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'auderecommerce_procategory/rule_condition_combine', 'label' => Mage::helper('catalogrule')->__('Conditions Combination')),
            array('label' => Mage::helper('auderecommerce_procategory')->__('Product Attribute'), 'value' => $attributes),
        ));
        
        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        
        return $this;
    }     
}