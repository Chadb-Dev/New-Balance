<?php
/**
 * Server.php
 * 
 * @category   PayFast
 * @package    PayFast_PayFast
 * @copyright  Copyright (c) 2010 PayFast (Pty) Ltd (http://www.payfast.co.za)
 */

/**
 * PayFast_PayFast_Model_Source_Server
 */
class PayFast_PayFast_Model_Source_Server
{
    // {{{ toOptionArray()
    /**
     * toOptionArray
     */ 
    public function toOptionArray()
    {
        return array(
            array( 'value' => 'test', 'label' => Mage::helper( 'payfast' )->__( 'Test' ) ),
            array( 'value' => 'live', 'label' => Mage::helper( 'payfast' )->__( 'Live' ) ),
        );
    }
    // }}}
}