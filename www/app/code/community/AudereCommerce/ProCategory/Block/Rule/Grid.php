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

class AudereCommerce_ProCategory_Block_Rule_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('auderecommerce_procategory_rule_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        
        return $this;
    }
    
    protected function _prepareCollection()
    {
        /** @var $collection AudereCommerce_ProCategory_Model_Resource_Rule_Collection */
        $collection = Mage::getModel('auderecommerce_procategory/rule')
            ->getResourceCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
        
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('auderecommerce_procategory');
        /* @var $helper AudereCommerce_ProCategory_Helper_Data */
        
        $this->addColumn('rule_id', array(
            'header'    => $helper->__('ID'),
            'align'     =>'right',
            'width'     => '5%',
            'index'     => 'rule_id',
        ));
        
        $this->addColumn('name', array(
            'header'    => $helper->__('Name'),
            'align'     =>'right',
            'width'     => '25%',
            'index'     => 'name',
        ));  
        
        $this->addColumn('description', array(
            'header'    => $helper->__('Description'),
            'align'     => 'left',
            'width'     => '50%',
            'index'     => 'description',
            'filter' => false
        )); 
        
        $this->addColumn('is_active', array(
            'header'    => $helper->__('Status'),
            'align'     => 'left',
            'width'     => '10%',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => $helper->__('Active'),
                0 => $helper->__('Inactive')
            ),
        ));   
        
        $this->addColumn('strict', array(
            'header'    => $helper->__('Strict'),
            'align'     => 'left',
            'width'     => '10%',
            'index'     => 'strict',
            'type'      => 'options',
            'options'   => array(
                1 => $helper->__('Yes'),
                0 => $helper->__('No')
            ),
        )); 

        parent::_prepareColumns();
        
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }
}