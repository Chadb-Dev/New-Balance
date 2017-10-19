<?php
/*
 * Copyright (C) 2015 CustomScripts - All Rights Reserved
 *
 * Unauthorized copying, modifying or distribution of this file,
 * via any medium is strictly prohibited.
 *
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from CustomScripts.
 *
 * Written by Pierre du Plessis <info@customscripts.co.za>, January 2015
 */

/**
 * CustomScripts MyGate Extension for Magento
 *
 * @category   Payment
 * @package    Customscripts_Mygate_Model
 * @author     Pierre du Plessis
 * @copyright  Copyright (c) 2015 Customscripts (http://www.customscripts.co.za)
 */
class Customscripts_Mygate_Model_Info
{
    const FIELD_PAYMENT_METHOD = '_PAYMETHOD';
    const FIELD_PAID_DATE = 'TXTACQUIRERDATETIME';
    const FIELD_CURRENCY = '_CURRENCYCODE';
    const FIELD_COUNTRY = '_COUNTRYCODE';
    const FIELD_CARD_COUNTRY = '_CARDCOUNTRY';
    const FIELD_TRANSACTION_INDEX = '_TRANSACTIONINDEX';

    protected $_paymentMap = array(
        self::FIELD_PAYMENT_METHOD => 'payment_method',
        self::FIELD_PAID_DATE => 'paid_date',
        self::FIELD_CURRENCY => 'currency',
        self::FIELD_COUNTRY => 'country',
        self::FIELD_CARD_COUNTRY => 'card_country',
    );

    /**
     * Grab data from source and map it into payment
     *
     * @param array|Varien_Object|callback $from
     * @param Mage_Payment_Model_Info      $payment
     */
    public function importToPayment($from, Mage_Payment_Model_Info $payment)
    {
        if (is_object($from)) {
            $from = array($from, 'getDataUsingMethod');
        }

        Varien_Object_Mapper::accumulateByMap($from, array($payment, 'setAdditionalInformation'), $this->_paymentMap);
    }
}
