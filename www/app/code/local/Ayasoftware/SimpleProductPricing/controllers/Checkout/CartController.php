<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Shopping cart controller
 */
require_once 'Mage/Checkout/controllers/CartController.php';

class Ayasoftware_SimpleProductPricing_Checkout_CartController extends Mage_Checkout_CartController {

    /**
     * Action to reconfigure cart item
     */
    public function configureAction() {
        if( ! Mage::getStoreConfig('spp/setting/enableModule')) {
            return parent::configureAction();
        }
        // Extract item and product to configure
        $id = (int) $this->getRequest()->getParam('id');
        $quoteItem = null;
        $cart = $this->_getCart();
        if($id) {
            $quoteItem = $cart->getQuote()->getItemById($id);
        }

        if( ! $quoteItem) {
            $this->_getSession()->addError($this->__('Quote item is not found.'));
            $this->_redirect('checkout/cart');
            return;
        }

        try {
            $this->_getCart()->removeItem($id)
                    ->save();
            $oProduct = Mage::getModel('catalog/product')->load($quoteItem->getProduct()->getId());
            $this->getResponse()->setRedirect($oProduct->getProductUrl());
        } catch(Exception $e) {
            $this->_goBack();
            return;
        }
    }

}
