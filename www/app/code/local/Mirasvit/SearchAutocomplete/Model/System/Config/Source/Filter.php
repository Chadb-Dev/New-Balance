<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Search AutoComplete & Suggest Pro
 * @version   1.2.10
 * @build     510
 * @copyright Copyright (C) 2017 Mirasvit (http://mirasvit.com/)
 */



/**
 * @category Mirasvit
 */
class Mirasvit_SearchAutocomplete_Model_System_Config_Source_Filter
{
    public function toOptionArray()
    {
        $options = array();

        $options['category'] = array(
            'value' => Mirasvit_SearchAutocomplete_Block_Form::FILTER_TYPE_CATEGORY,
            'label' => Mage::helper('searchautocomplete')->__('Category'),
        );

        if (Mage::helper('core')->isModuleEnabled('Mirasvit_SearchIndex')) {
            $options['attribute'] = array(
                'value' => Mirasvit_SearchAutocomplete_Block_Form::FILTER_TYPE_ATTRIBUTE,
                'label' => Mage::helper('searchautocomplete')->__('Attribute'),
            );
        }

        if (Mage::helper('core')->isModuleEnabled('Mirasvit_SearchIndex')) {
            $options['searchIndex'] = array(
                'value' => Mirasvit_SearchAutocomplete_Block_Form::FILTER_TYPE_INDEX,
                'label' => Mage::helper('searchautocomplete')->__('Search index'),
            );
        }

        $options['none'] = array(
            'value' => 'none',
            'label' => Mage::helper('searchautocomplete')->__('No Display'),
        );

        return $options;
    }
}
