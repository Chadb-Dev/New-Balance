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

/**
 * @method AudereCommerce_ProCategory_Model_Rule_Condition_Combine getConditions()
 * @method bool getStrict()
 * @method AudereCommerce_ProCategory_Model_Resource_Rule getResource()
 */
class AudereCommerce_ProCategory_Model_Rule
    extends Mage_Rule_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('auderecommerce_procategory/rule');        
        return $this;
    }

    /**
     * @return AudereCommerce_ProCategory_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('auderecommerce_procategory/rule_condition_combine');
    }

    /**
     * @return AudereCommerce_ProCategory_Model_Rule_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('auderecommerce_procategory/rule_action_collection');
    }
    
    public function getCategoryIds()
    {
        if ($categoryIds = $this->getData('category_ids')) {
            return array_unique($categoryIds);
        } else {
            return $this->_getResource()->getCategoryIds($this);
        }
    }
    
    public function validateData(Varien_Object $object)
    {
        $result = parent::validateData($object);
        
        if (!$object->getData('category_ids')) {
            if ($result === true) {
                $result = array();
            }
            $result[] = Mage::helper('auderecommerce_procategory')->__('At least one category needs to be selected');
        }
        
        return $result;
    }
    
    public function loadPost(array $data)
    {
        parent::loadPost($data);        
        $this->_loadPostCategoryIds($data);    
        
        return $this;
    }    
    
    protected function _loadPostCategoryIds(array $postData)
    {
        $categoryIds = trim($postData['category_ids'], ',');
        $this->setCategoryIds(explode(',', $categoryIds));   
        
        return $this;        
    }
}