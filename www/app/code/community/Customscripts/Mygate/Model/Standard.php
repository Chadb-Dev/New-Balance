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
class Customscripts_Mygate_Model_Standard extends Mage_Payment_Model_Method_Abstract
{

    /**
     * @var string
     */
    protected $_code = 'mygate_standard';

    /**
     * @var bool
     */
    protected $_isInitializeNeeded = true;

    /**
     * @var bool
     */
    protected $_canUseInternal = false;

    /**
     * @var bool
     */
    protected $_canUseForMultishipping = false;

    /**
     * @var string
     */
    protected $_formBlockType = 'mygate/standard_form';

    /**
     * Get mygate session namespace
     *
     * @return Customscripts_Mygate_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('mygate/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Get order placed redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('mygate/standard/redirect', array('_secure' => true));
    }

    /**
     * Gets a configuration value
     *
     * @param string $keyname
     *
     * @return bool|mixed
     */
    public function getConfig($keyname)
    {
        $conf = $this->getConfigData($keyname);

        if (!$conf) {
            return false;
        } else {
            return $conf;
        }
    }

    /**
     * Return the MyGate API endpoint url
     *
     * @return string
     */
    public function getMygateUrl()
    {
        return $this->getConfigData("mygate_url");
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigPaymentAction()
    {
        return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }
}
