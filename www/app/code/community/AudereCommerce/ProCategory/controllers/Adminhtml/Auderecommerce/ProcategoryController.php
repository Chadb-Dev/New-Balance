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

class AudereCommerce_ProCategory_Adminhtml_Auderecommerce_ProcategoryController
    extends Mage_Adminhtml_Controller_Action
{
    protected $_helper = null;
    
    /**
     * @return AudereCommerce_ProCategory_Helper_Data
     */
    public function getHelper()
    {
        if (!$this->_helper instanceof AudereCommerce_ProCategory_Helper_Data) {
            $this->_helper = Mage::helper('auderecommerce_procategory');
        }
        
        return $this->_helper;
    }
    
    protected function _initAction()
    {
        $helper = $this->getHelper();
        $this->loadLayout()
            ->_setActiveMenu('catalog')
            ->_addBreadcrumb(
                $helper->__('Catalog'),
                $helper->__('Catalog')
            );
        
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('ProCategory Rules'));

        $helper = $this->getHelper();
        $this->_initAction()
            ->_addBreadcrumb(
                $helper->__('Catalog'),
                $helper->__('Catalog')
            )
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_initCategory();
        $this->_title($this->__('Catalog'))->_title($this->__('ProCategory Rules'));
        $id = $this->getRequest()->getParam('id');
        $this->_registerCurrentRule();
        $rule = Mage::registry('current_auderecommerce_procategory_rule');
        /* @var $rule AudereCommerce_ProCategory_Model_Rule */
        $helper = $this->getHelper();
        $this->_title($rule->getRuleId() ? $rule->getName() : $this->__('New Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        
        if (!empty($data)) {
            $rule->addData($data);
        }
        
        $rule->getConditions()->setJsFormObject('rule_conditions_fieldset');

        $this->_initAction()->getLayout()->getBlock('auderecommerce_procategory_rule_edit')
             ->setData('action', $this->getUrl('*/auderecommerce_procategory/save'));

        $breadcrumb = $id
            ? $helper->__('Edit Rule')
            : $helper->__('New Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb)->renderLayout();

    }
    
    public function _registerCurrentRule()
    {
        $id = $this->getRequest()->getParam('id');
        $rule = Mage::getModel('auderecommerce_procategory/rule');
        /* @var $rule AudereCommerce_ProCategory_Model_Rule */
        
        if ($id) {
            $rule->load($id);
            
            if (!$rule->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('auderecommerce_procategory')->__('This rule no longer exists.')
                );
                $this->_redirect('*/*');
                return;
            }
        }
        
        Mage::register('current_auderecommerce_procategory_rule', $rule);
        
        return $this;
    }
    
    public function categoriesJsonAction() 
    {
        $this->_registerCurrentRule();
        $this->getResponse()->setBody($this->getLayout()->createBlock('auderecommerce_procategory/rule_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category')));
        
        return $this;
    }    

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $rule = Mage::getModel('auderecommerce_procategory/rule')->load($this->getRequest()->getParam('rule_id'));
                /* @var $rule AudereCommerce_ProCategory_Model_Rule */
                $data = $this->getRequest()->getPost();
                $validateResult = $rule->validateData(new Varien_Object($data)); 
                
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$rule->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
                
                $rule->loadPost($data);
                Mage::getSingleton('adminhtml/session')->setPageData($rule->getData());                     
                $rule->save();
                              
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->getHelper()->__('The rule has been saved.')
                );
                
                Mage::getSingleton('adminhtml/session')->setPageData(false);                
    
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());            
                return $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
            }
        }
        
        return $this->_redirect('*/*/');
    }
    
    /**
     * Get Rule object from POST data.
     * 
     * @return AudereCommerce_ProCategory_Model_Rule|null
     */
    protected function _getRule()
    {
        $rule = Mage::getModel('auderecommerce_procategory/rule');
        /* @var $rule AudereCommerce_ProCategory_Model_Rule */
        
        $rule->load($this->getRequest()->getPost('rule_id'));
        
        return $rule->getId() ? $rule : null;
    }

    public function deleteAction()
    {
        $rule = Mage::getModel('auderecommerce_procategory/rule');
        $rule->load($this->getRequest()->getParam('id'));
        $rule->delete();        
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('auderecommerce_procategory/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        
        $this->getResponse()->setBody($html);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/auderecommerce_procategory');
    }
    
    public function gridAction()
    {
        $this->_initCategory();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('auderecommerce_procategory/catalog_category_tab_rule')->toHtml()
        );
        
        return $this;
    } 
    
    protected function _initCategory()
    {
        $categoryId = Mage::app()->getRequest()->getParam('category_id');
        
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            Mage::register('category', $category);
        }
        
        return $this;
    }
}