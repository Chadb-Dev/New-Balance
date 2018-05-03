<?php

class Callpay_Eftsecure_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * redirect to eftsecure website
     */
    public function redirectAction()
    {
        $baseUrl = Mage::getBaseUrl();
        $last_order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($last_order_id);
        $username = Mage::getStoreConfig('payment/eftsecure/username', Mage::app()->getStore());
        $password = Mage::getStoreConfig('payment/eftsecure/password', Mage::app()->getStore());
        $password = Mage::helper('core')->decrypt($password);
        $curl = curl_init('https://eftsecure.callpay.com/api/v1/token');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response);
        if(!empty($responseData->token)){
            $curl = curl_init('https://eftsecure.callpay.com/api/v1/eft/payment-key');
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            $successUrl =$baseUrl.'checkout/onepage/success';
            $errorUrl = $baseUrl.'checkout/onepage/failure';
            $notifyUrl = $baseUrl.'eftsecure/payment/ipn';
            $params = array(
                "merchant_reference"  => $last_order_id,
                "X-Token" 			=> $responseData->token,
                "amount" 			=> number_format($order->getGrandTotal(), 2,'.',''),
                "notify_url" 		=> $notifyUrl,
                "success_url" 		=> $successUrl,
                "error_url" 		=> $errorUrl,
                "cancel_url" 		=> $errorUrl
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            $response = curl_exec($curl);
            curl_close($curl);
            $responseData = json_decode($response);
            $this->getResponse()->setRedirect($responseData->url);
        } else {
            $this->_redirect('/');
        }
    }

    /**
     * ipn action
     */
    public function ipnAction() {
        if ($this->getRequest()->getPost('merchant_reference')
            && $this->getRequest()->getPost('callpay_transaction_id')) {
            $success = $this->getRequest()->getPost("success");
            $orderId = $this->getRequest()->getPost("merchant_reference");
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            if ($success == 1) {
                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $order->setState($orderState, true, 'Payment Success.');
                try {
                    $order->sendOrderUpdateEmail();
                } catch (Exception $ex) {
                    
                }
            } else {
                $reason = $this->getRequest()->getPost('reason');
                $comment = $order->addStatusHistoryComment($reason, Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
                $order->addRelatedObject($comment);
            }
            $order->save();
        }
    }

}