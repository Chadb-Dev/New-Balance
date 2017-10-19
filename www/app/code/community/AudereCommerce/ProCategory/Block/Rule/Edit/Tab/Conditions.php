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

class AudereCommerce_ProCategory_Block_Rule_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return Mage::helper('auderecommerce_procategory')->__('Conditions');
    }

    public function getTabTitle()
    {
        return Mage::helper('auderecommerce_procategory')->__('Conditions');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $rule = Mage::registry('current_auderecommerce_procategory_rule');
        /* @var $rule AudereCommerce_ProCategory_Model_Rule */

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('auderecommerce/procategory/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/auderecommerce_procategory/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('catalogrule')->__('Conditions (leave blank to assign all products)'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('auderecommerce_procategory')->__('Conditions'),
            'title' => Mage::helper('auderecommerce_procategory')->__('Conditions'),
            'required' => true,
        ))->setRule($rule)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($rule->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}