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
class Customscripts_Mygate_Model_Payment
{
    /**
     * Cancels an order
     *
     * @param array  $data
     * @param string $errorMessage
     */
    public function processFailure(array $data, $errorMessage)
    {
        $order = $this->getOrder($data);

        $this->addPaymentInfo($order, $data);

        /*$order
            ->registerCancellation($errorMessage, false)
            ->save();*/
    }

    /**
     * Processes a successful payment
     *
     * @param array $data
     *
     * @throws Exception
     */
    public function processPayment(array $data)
    {
        if (!isset($data['_RESULT']) || ((int) $data['_RESULT'] !== 0 && (int) $data['_RESULT'] !== 1)) {
            throw new Exception(sprintf('Payment failed: %s', isset($data['_ERROR_MESSAGE']) ? $data['_ERROR_MESSAGE'] : 'An unknown error occurred'));
        }

        $order = $this->getOrder($data);

        $this->addPaymentInfo($order, $data);

        $payment = $order->getPayment();

        $payment->setIsTransactionApproved(true);

        $payment->setTransactionId($data[Customscripts_Mygate_Model_Info::FIELD_TRANSACTION_INDEX])
            ->setCurrencyCode($data[Customscripts_Mygate_Model_Info::FIELD_CURRENCY])
            ->setPreparedMessage('')
            ->setShouldCloseParentTransaction(true)
            ->setIsTransactionClosed(0)
            ->registerCaptureNotification($data['_AMOUNT']);
        $order->save();

        // notify customer
        $invoice = $payment->getCreatedInvoice();
        if ($invoice && !$order->getEmailSent()) {
            $order->sendNewOrderEmail()->addStatusHistoryComment(
                Mage::helper('mygate')->__('Notified customer about invoice #%s.', $invoice->getIncrementId())
            )
                ->setIsCustomerNotified(true)
                ->save();
        }
    }

    /**
     * Adds additional info to the payment
     *
     * @param Mage_Sales_Model_Order $order
     * @param array                  $data
     */
    private function addPaymentInfo(Mage_Sales_Model_Order $order, array $data)
    {
        /** @var Customscripts_Mygate_Model_Info $info */
        $info = Mage::getSingleton('mygate/info');

        $payment = $order->getPayment();

        // collect basic information
        $from = array();

        $additionalInfo = array(
            Customscripts_Mygate_Model_Info::FIELD_CARD_COUNTRY,
            Customscripts_Mygate_Model_Info::FIELD_COUNTRY,
            Customscripts_Mygate_Model_Info::FIELD_CURRENCY,
            Customscripts_Mygate_Model_Info::FIELD_PAID_DATE,
            Customscripts_Mygate_Model_Info::FIELD_PAYMENT_METHOD,
            Customscripts_Mygate_Model_Info::FIELD_TRANSACTION_INDEX,
        );

        foreach ($additionalInfo as $key) {
            $value = isset($data[$key]) ? $data[$key] : null;

            if ($value) {
                $from[$key] = $value;
            }
        }

        //$from['payment_status'] = Mage_Paypal_Model_Info::PAYMENTSTATUS_COMPLETED;

        $info->importToPayment($from, $payment);
    }

    /**
     * @param array $data
     *
     * @return Mage_Sales_Model_Order
     */
    private function getOrder(array $data)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($data['_MERCHANTREFERENCE']);

        if (!$order->getId()) {
            Mage::app()->getResponse()
                ->setHeader('HTTP/1.1', '503 Service Unavailable')
                ->sendResponse();
            exit;
        }

        return $order;
    }
}
