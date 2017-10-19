<?php
/**
 * Info.php
 * 
 * @category   PayFast
 * @package    PayFast_PayFast
 * @copyright  Copyright (c) 2010 PayFast (Pty) Ltd (http://www.payfast.co.za)
 */

/**
 * PayFast_PayFast_Block_Payment_Info 
 */
class PayFast_PayFast_Block_Payment_Info extends Mage_Payment_Block_Info
{
    // {{{ _prepareSpecificInformation()
    /**
     * _prepareSpecificInformation 
     */
    protected function _prepareSpecificInformation( $transport = null )
    {
        $transport = parent::_prepareSpecificInformation( $transport );
        $payment = $this->getInfo();
        $pfInfo = Mage::getModel( 'payfast/info' );
        
        if( !$this->getIsSecureMode() )
            $info = $pfInfo->getPaymentInfo( $payment, true );
        else
            $info = $pfInfo->getPublicPaymentInfo( $payment, true );

        return( $transport->addData( $info ) );
    }
    // }}}
}