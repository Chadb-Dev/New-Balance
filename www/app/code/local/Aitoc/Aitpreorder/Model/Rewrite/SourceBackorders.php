<?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 */
class Aitoc_Aitpreorder_Model_Rewrite_SourceBackorders extends Mage_CatalogInventory_Model_Source_Backorders
{
	const BACKORDERS_YES_PREORDERS = 30;
	const BACKORDERS_YES_PREORDERS_ZERO = 35;

	public function toOptionArray()
    {
        $options = parent::toOptionArray();

		$options[] = array(
			'value' => self::BACKORDERS_YES_PREORDERS,
			'label'=>Mage::helper('aitpreorder')->__('Pre-Orders')
		);
		$options[] = array(
			'value' => self::BACKORDERS_YES_PREORDERS_ZERO,
			'label'=>Mage::helper('aitpreorder')->__('Pre-Order for Out-Of-Stock')
		);

		return $options;
    }
}
