<?php
/**
 * Data.php
 * 
 * @category   PayFast
 * @package    PayFast_PayFast
 * @copyright  Copyright (c) 2010 PayFast (Pty) Ltd (http://www.payfast.co.za)
 */

/**
 * PayFast_PayFast_Helper_Data
 */
class PayFast_PayFast_Helper_Data extends Mage_Payment_Helper_Data
{
    // {{{ getPendingPaymentStatus()
    /**
     * getPendingPaymentStatus
     */
    public function getPendingPaymentStatus()
    {
        if( version_compare( Mage::getVersion(), '1.4.0', '<' ) )
            return( Mage_Sales_Model_Order::STATE_HOLDED );
        else
            return( Mage_Sales_Model_Order::STATE_PENDING_PAYMENT );
    }
    // }}}
}
