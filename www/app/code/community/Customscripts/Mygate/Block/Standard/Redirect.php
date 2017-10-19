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
 * @package    Customscripts_Mygate_Block_Standard
 * @author     Pierre du Plessis
 * @copyright  Copyright (c) 2015 Customscripts (http://www.customscripts.co.za)
 */
class Customscripts_Mygate_Block_Standard_Redirect extends Mage_Core_Block_Abstract
{
    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $standard = Mage::getModel('mygate/standard');

        $form = new Varien_Data_Form();
        $form->setAction($standard->getMygateUrl())
            ->setId('mygate_standard_checkout')
            ->setName('mygate_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);

        $lastRealOrderId = $standard->getCheckout()->getLastRealOrderId();
        $form->addField("txtMerchantID", 'hidden', array('name' => "txtMerchantID", 'value' => $standard->getConfig('mygate_merchant_id')));
        $form->addField("txtApplicationID", 'hidden', array('name' => "txtApplicationID", 'value' => $standard->getConfig('mygate_application_id')));
        $form->addField("txtMerchantReference", 'hidden', array('name' => "txtMerchantReference", 'value' => $lastRealOrderId));
        $form->addField("txtMode", 'hidden', array('name' => "Mode", 'value' => (int) !$standard->getConfig('mode')));

        $order = Mage::getModel('sales/order')->loadByIncrementId($lastRealOrderId);

        if ($order->getIsVirtual()) {
            $address = $order->getBillingAddress();
        } else {
            $address = $order->getShippingAddress();
        }

        $amount = number_format($order->getBaseGrandTotal(), 2, '.', '');
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();

        $amount = number_format(Mage::helper('directory')->currencyConvert($amount, $currentCurrencyCode, $baseCurrencyCode), 2, ".", "");

        $form->addField("txtPrice", 'hidden', array('name' => "txtPrice", 'value' => $amount));

        $form->addField("txtCurrencyCode", 'hidden', array('name' => "txtCurrencyCode", 'value' => Mage::app()->getStore()->getCurrentCurrencyCode()));
        $form->addField("txtRedirectSuccessfulURL", 'hidden', array('name' => "txtRedirectSuccessfulURL", 'value' => Mage::getUrl("mygate/standard/success")));
        $form->addField("txtRedirectFailedURL", 'hidden', array('name' => "txtRedirectFailedURL", 'value' => Mage::getUrl("mygate/standard/fail")));
        $form->addField("ShippingCountryCode", 'hidden', array('name' => "ShippingCountryCode", 'value' => $address->getCountry()));
        $items = $order->getAllItems();

        if ($items) {
            $i = 0;
            $itemArray = array();
            foreach ($items as $item) {

                if ($item->getParentItem()) {
                    continue;
                }

                $itemArray[$i] = array(
                    'item_name' => $item->getName(),
                    'item_number' => $item->getSku(),
                    'quantity' => (int) $item->getQtyOrdered(),
                    'amount' => sprintf('%.2f', $item->getBaseRowTotal()),
                );

                $i++;
            }

            for ($k = 0; $k < count($itemArray); $k++) {
                $form->addField("txtQty".$k, 'hidden', array('name' => "txtQty".$k, 'value' => $itemArray[$k]['quantity']));
                $form->addField("txtItemRef".$k, 'hidden', array('name' => "txtItemRef".$k, 'value' => $itemArray[$k]['item_number']));
                $form->addField("txtItemDescr".$k, 'hidden', array('name' => "txtItemDescr".$k, 'value' => $itemArray[$k]['item_name']));
                $form->addField("txtItemAmount".$k, 'hidden', array('name' => "txtItemAmount".$k, 'value' => $itemArray[$k]['amount']));
            }
        }

        $shipping = sprintf('%.2f', $order->getBaseShippingAmount());
        $form->addField("txtDiscount", 'hidden', array('name' => "txtDiscount", 'value' => sprintf('%.2f', $order->getBaseDiscountAmount())));
        $form->addField("txtShippingCost", 'hidden', array('name' => "txtShippingCost", 'value' => $shipping));
        $form->addField("txtRecipient", 'hidden', array('name' => "txtRecipient", 'value' => $address->getFirstname()." ".$address->getLastname()));
        $form->addField("txtShippingAddress1", 'hidden', array('name' => "txtShippingAddress1", 'value' => $address->getStreet(1)));
        $form->addField("txtShippingAddress2", 'hidden', array('name' => "txtShippingAddress2", 'value' => $address->getStreet(2)));
        $form->addField("txtShippingAddress3", 'hidden', array('name' => "txtShippingAddress3", 'value' => $address->getCity()));
        $form->addField("txtShippingAddress4", 'hidden', array('name' => "txtShippingAddress4", 'value' => $address->getPostcode()));
        $form->addField("txtShippingAddress5", 'hidden', array('name' => "txtShippingAddress5", 'value' => $address->getCountry()));

        $idSuffix = Mage::helper('core')->uniqHash();
        $submitButton = new Varien_Data_Form_Element_Submit(array(
            'value' => $this->__('Click here if you are not redirected within 10 seconds...'),
        ));
        $id = "submit_to_paypal_button_{$idSuffix}";
        $submitButton->setId($id);
        $form->addElement($submitButton);

        $html = '<html><body>';
        $html .= $this->__('You will be redirected to MyGate in a few seconds.');
        $html .= $form->toHtml();
        $html .= '<script type="text/javascript">document.forms["mygate_standard_checkout"].submit();</script>';
        $html .= '</body></html>';

        return $html;
    }
}
