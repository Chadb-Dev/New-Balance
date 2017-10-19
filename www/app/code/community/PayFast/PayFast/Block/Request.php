<?php
/**
 * Request.php
 * 
 * @category   PayFast
 * @package    PayFast_PayFast
 * @copyright  Copyright (c) 2010 PayFast (Pty) Ltd (http://www.payfast.co.za)
 */

/**
 * PayFast_Block_Request 
 */
class PayFast_PayFast_Block_Request extends Mage_Core_Block_Abstract
{
    // {{{ _toHtml()
    /**
     * _toHtml 
     */
    protected function _toHtml()
    {
        $standard = Mage::getModel( 'payfast/standard' );
        $form = new Varien_Data_Form();
        $form->setAction( $standard->getPayFastUrl() )
            ->setId( 'payfast_checkout' )
            ->setName( 'payfast_checkout' )
            ->setMethod( 'POST' )
            ->setUseContainer( true );
        
        foreach( $standard->getStandardCheckoutFormFields() as $field=>$value )
            $form->addField( $field, 'hidden', array( 'name' => $field, 'value' => $value, 'size' => 200 ) );
        
        $html = '<html><body>';
        $html.= $this->__( 'You will be redirected to PayFast in a few seconds.' );
        $html.= $form->toHtml();
		#echo $html;exit;
        $html.= '<script type="text/javascript">document.getElementById( "payfast_checkout" ).submit();</script>';
        $html.= '</body></html>';
        return $html;
    }
    // }}}
}