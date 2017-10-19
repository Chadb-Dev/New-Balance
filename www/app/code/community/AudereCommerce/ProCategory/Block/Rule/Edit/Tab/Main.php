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
 * @copyright   Copyright (c) 2013. 2014 Audere Commerce Limited. (http://www.auderecommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      James Withers <james+procategory@auderecommerce.com>
 */

class AudereCommerce_ProCategory_Block_Rule_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return Mage::helper('auderecommerce_procategory')->__('Rule Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('auderecommerce_procategory')->__('Rule Information');
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
        $helper = Mage::helper('auderecommerce_procategory');
        /* @var $helper AudereCommerce_ProCategory_Helper_Data */
                
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend '=> $helper->__('General Information'))
        );

        if ($rule->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => $helper->__('Rule Name'),
            'title' => $helper->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => $helper->__('Description'),
            'title' => $helper->__('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => $helper->__('Status'),
            'title'     => $helper->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => $helper->__('Active'),
                '0' => $helper->__('Inactive'),
            ),
            'note' => $helper->__('Setting a rule to inactive does not remove category/product associations.')
        ));
        
        $fieldset->addField('strict', 'select', array(
            'label'     => $helper->__('Strict'),
            'title'     => $helper->__('Strict'),
            'name'      => 'strict',
            'required' => true,
            'options'    => array(
                '1' => $helper->__('Yes'),
                '0' => $helper->__('No'),
            ),
            'note' => $helper->__('Strict rules remove products from the selected categories that do not match this rule.')
        ));    

        $form->setValues($rule->getData());

        if ($rule->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}