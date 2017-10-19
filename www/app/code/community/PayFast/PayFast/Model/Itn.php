<?php
/**
 * Itn.php
 * 
 * @category   PayFast
 * @package    PayFast_PayFast
 * @copyright  Copyright (c) 2010 PayFast (Pty) Ltd (http://www.payfast.co.za)
 */

/**
 * Mage_Paypal_Model_Itn
 */
class Mage_Paypal_Model_Itn
{
    // {{{ getWriteLog()
    /**
     * getWriteLog
     */
	public function getWriteLog( $data )
    {
		$text = "\n";
		$text .= "RESPONSE: From PayFast[". date("Y-m-d H:i:s") ."]"."\n";
		
        foreach( $_REQUEST as $key => $val )
			$text .= $key."=>".$val."\n";

		$file = dirname( dirname( __FILE__ ) ) ."/Logs/notify.txt";
		
		$handle = fopen( $file, 'a' );
		fwrite( $handle, $text );
		fclose( $handle );
	}
    // }}}
}