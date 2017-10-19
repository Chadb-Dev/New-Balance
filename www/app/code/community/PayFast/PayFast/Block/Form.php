<?php
/**
 * Form.php
 * 
 * @category   PayFast
 * @package    PayFast_PayFast
 * @copyright  Copyright (c) 2010 PayFast (Pty) Ltd (http://www.payfast.co.za)
 */

/**
 * PayFast_PayFast_Block_Form 
 */
class PayFast_PayFast_Block_Form extends Mage_Payment_Block_Form
{
    // {{{ _construct()
    /**
     * _construct() 
     */    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate( 'payfast/form.phtml' );
    }
    // }}}
}