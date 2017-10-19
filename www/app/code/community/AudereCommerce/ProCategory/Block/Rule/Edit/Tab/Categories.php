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
 * @copyright   Copyright (c) 2013 Audere Commerce Limited. (http://www.auderecommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      James Withers <james+procategory@auderecommerce.com>
 */

class AudereCommerce_ProCategory_Block_Rule_Edit_Tab_Categories
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('auderecommerce/procategory/categories.phtml');
    }    
    
    public function getTabLabel()
    {
        return Mage::helper('auderecommerce_procategory')->__('Categories');
    }

    public function getTabTitle()
    {
        return Mage::helper('auderecommerce_procategory')->__('Categories');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
    
    public function isReadonly()
    {
        return false;
    }
    
    public function getCategoryIds()
    {
        $category = Mage::registry('category');
        
        if ($category instanceof Mage_Catalog_Model_Category) {
            return array($category->getId());
        }
        
        $rule = Mage::registry('current_auderecommerce_procategory_rule');
        
        if ($rule instanceof AudereCommerce_ProCategory_Model_Rule) {
            return $rule->getCategoryIds();
        } else {
            return array();
        }
    }
    
    public function getIdsString()
    {
        $categoryIds = $this->getCategoryIds(); 
        
        if (empty($categoryIds)) {
            return '';
        } elseif (count($categoryIds) <= 1) {
            return $categoryIds[key($categoryIds)];
        } else {
            return implode(',', $categoryIds);
        }
    }
}