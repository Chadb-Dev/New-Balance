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
 * @package    Customscripts_Mygate
 * @author     Pierre du Plessis
 * @copyright  Copyright (c) 2015 Customscripts (http://www.customscripts.co.za)
 */
class Customscripts_Mygate_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * {@inheritdoc}
     */
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with mygate strandard order transaction information
     *
     * @return Customscripts_Mygate_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('mygate/standard');
    }

    /**
     * When a customer chooses MyGate on Checkout/Payment page, redirect to payment gateway
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setMygateStandardQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('mygate/standard_redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
     * When a payment fails or a customer cancel payment from mygate.
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    public function failAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $data = $this->getRequest()->getPost();

        $reason = (isset($data['_ERROR_MESSAGE']) && !empty($data['_ERROR_MESSAGE'])) ? $data['_ERROR_MESSAGE'] : 'An unknown error occurred';
        $session = Mage::getSingleton('checkout/session');

        if (!isset($data['_MERCHANTREFERENCE'])) {
            $data['_MERCHANTREFERENCE'] = $session->getLastRealOrderId();
        }

        $session->setErrorMessage(Mage::helper('mygate')->__($reason));
        $payment = Mage::getModel('mygate/payment');
        $payment->processFailure($data, $reason);

        return $this->_redirect('checkout/onepage/failure', array('_secure' => true));
    }

    /**
     * Process a successful result from MyGate
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    public function  successAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $data = $this->getRequest()->getPost();

        try {
            Mage::getSingleton('mygate/payment')->processPayment($data);
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('checkout/session')->setErrorMessage(Mage::helper('mygate')->__($e->getMessage()));

            return $this->_redirect('checkout/onepage/failure', array('_secure' => true));
        }

        return $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }
}
