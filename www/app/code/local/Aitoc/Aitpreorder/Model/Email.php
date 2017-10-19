<?php
/**
 * @copyright  Copyright (c) 2012 AITOC, Inc. 
 */
class Aitoc_Aitpreorder_Model_Email extends Mage_ProductAlert_Model_Email
{ 
    protected function _getStockBlock()
    {
        if (is_null($this->_stockBlock)) {
            $this->_stockBlock = Mage::helper('productalert')
                ->createBlock('aitpreorder/email_stock');
        }
		return $this->_stockBlock;
    }
}
