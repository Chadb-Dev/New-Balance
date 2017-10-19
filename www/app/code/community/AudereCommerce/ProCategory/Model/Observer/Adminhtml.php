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

class AudereCommerce_ProCategory_Model_Observer_Adminhtml
    extends Mage_Core_Model_Abstract
{
    public function adminhtmlCatalogCategoryTabs(Varien_Event_Observer $observer)
    {       
        $tabs = $observer->getEvent()->getTabs();
        $tabs->addTab('auderecommerce_procategory_rule', array(
            'label' => Mage::helper('auderecommerce_procategory')->__('ProCategory Rule'),
            'content'   => $tabs->getLayout()->createBlock(
                'auderecommerce_procategory/catalog_category_tab_rule',
                'auderecommerce_catalogcategory_category_rule'
            )->toHtml()
        ));
        
        return $this;
    }
}