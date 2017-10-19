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

class AudereCommerce_ProCategory_Model_Resource_Indexer
    extends Mage_Catalog_Model_Resource_Category_Indexer_Product
{
    protected $categoryProductAssociations = array();
    
    public function reindexAll()
    {
        $productIds = Mage::getModel('catalog/product')
                ->getCollection()
                ->getAllIds();
        
        $product = Mage::getSingleton('catalog/product');
        /* @var $product Mage_Catalog_Model_Product */
        
        foreach ($productIds as $productId) {
            $product->reset();
            $product->load($productId);
            
            foreach ($this->getActiveRuleCollection() as $activeRule) {
                /* @var $activeRule AudereCommerce_ProCategory_Model_Rule */
                if ($activeRule->getConditions()->validate($product)) {
                    $this->addCategoryProductAssociation($activeRule->getCategoryIds(), $productId);
                }
            }
        }
        
        $this->applyCategoryProductAssociations();        
        parent::reindexAll();
    }
    
    /**
     * @return AudereCommerce_ProCategory_Model_Resource_Rule_Collection
     */
    public function getActiveRuleCollection()
    {
        return Mage::getModel('auderecommerce_procategory/rule')
                ->getCollection()
                ->addFieldToFilter('is_active', true);
    }
    
    public function applyCategoryProductAssociations()
    {
        $this->_getWriteAdapter()
                ->beginTransaction();
        
        try {           
            foreach ($this->getActiveRuleCollection() as $activeRule) {
                /* @var $activeRule AudereCommerce_ProCategory_Model_Rule */
                if ($activeRule->getStrict()) {
                    $this->_getWriteAdapter()->delete($this->getTable('catalog/category_product'),
                            array('category_id IN (?)' => $activeRule->getCategoryIds()));
                }
            }
            
            foreach ($this->categoryProductAssociations as $categoryProduct) {
                $this->_getWriteAdapter()->insertOnDuplicate($this->getTable('catalog/category_product'), $categoryProduct);
            }
            
            $this->_getWriteAdapter()->commit();
        } catch (Exception $ex) {
            $this->_getWriteAdapter()->rollBack();
            throw $ex;
        }
    }
    
    public function addCategoryProductAssociation($categoryIds, $productId)
    {
        foreach ($categoryIds as $categoryId) {
            $this->categoryProductAssociations[] = array(
                'product_id' => $productId,
                'category_id' => $categoryId
            );
        }
    }
}