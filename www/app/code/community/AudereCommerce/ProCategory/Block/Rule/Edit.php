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

class AudereCommerce_ProCategory_Block_Rule_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'rule';
        $this->_blockGroup = 'auderecommerce_procategory';

        parent::__construct();
    }
    
    public function getFormActionUrl()
    {
        return $this->getUrl('*/auderecommerce_procategory/save');
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('current_auderecommerce_procategory_rule');
        /* @var $rule AudereCommerce_ProCategory_Model_Rule */
        $helper = Mage::helper('auderecommerce_procategory');
        /* @var $helper AudereCommerce_ProCategory_Helper_Data */
        
        if ($rule->getRuleId()) {
            return $helper->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        } else {
            return $helper->__('New Rule');
        }
    }
}