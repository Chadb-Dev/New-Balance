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

class AudereCommerce_ProCategory_Model_Resource_Rule
    extends Mage_Rule_Model_Resource_Abstract
{
    protected $_serializableFields = array(
        'type_ids' => array()
    );
    
    protected function _construct()
    {
        $this->_init('auderecommerce_procategory/rule', 'rule_id');        
        return $this;
    }
        
    protected function _afterSave(Mage_Core_Model_Abstract $rule)
    {
        parent::_afterSave($rule);
        
        $adapter = $this->_getWriteAdapter();        
        $adapter->beginTransaction();
        
        try {
            $table = $this->getTable('auderecommerce_procategory/category_product');
            $adapter->delete($table, $adapter->quoteInto('rule_id = ?', $rule->getRuleId()));
            
            foreach ($rule->getCategoryIds() as $categoryId) {                
                $adapter->insertOnDuplicate($table, array(
                    'rule_id' => $rule->getRuleId(),
                    'category_id' => $categoryId
                ));
            }
            
            $adapter->commit();
        } catch (Exception $ex) {
            $adapter->rollBack();
            throw $ex;
        }
        
        $indexProcess = Mage::getModel('index/process');
        /* @var $indexProcess Mage_Index_Model_Process */
        $indexProcess->load('catalog_category_product', 'indexer_code');
        $indexProcess->setStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        $indexProcess->save();
        
        return $this;
    }
    
    public function getCategoryIds(AudereCommerce_ProCategory_Model_Rule $rule)
    {
        $adapter = $this->_getReadAdapter();
        
        $select = $adapter->select()
                ->distinct()
                ->from($this->getTable('auderecommerce_procategory/category_product'), 'category_id')
                ->where('rule_id = ?', $rule->getRuleId());
        
        $categoryIds = array();
        
        foreach ($adapter->fetchAll($select) as $row) {
            $categoryIds[] = $row['category_id'];
        }
        
        return $categoryIds;
    }
}