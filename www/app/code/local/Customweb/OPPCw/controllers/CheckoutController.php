<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_OPPCw
 * 
 */

class Customweb_OPPCw_CheckoutController extends Customweb_OPPCw_Controller_Action
{
	public function breakoutAction()
	{
		$transaction = $this->getTransaction();
		$redirectionUrl = '';
		if ($transaction->getTransactionObject()->isAuthorizationFailed()) {
			$redirectionUrl = Customweb_Util_Url::appendParameters($transaction->getTransactionObject()->getTransactionContext()->getFailedUrl(), $transaction->getTransactionObject()->getTransactionContext()->getCustomParameters());
		} else {

			$redirectionUrl = Mage::getUrl('OPPCw/process/success', array('transaction_id' => $transaction->getTransactionId(), '_secure' => true));
		}

		$html =
		'<html>
			<body>
				<script type="text/javascript">
					top.location.href = "' . $redirectionUrl . '";
				</script>
				' . Mage::helper('OPPCw')->__("You will be redirect to the order confirmation page.") . '
			</body>
		<html>';

		echo $html;
		die();
	}

	public function payAction()
	{
		$transaction = $this->getTransactionFromRequest();
		if($transaction->getId()){
			$payment = $transaction->getTransactionObject()->getPaymentMethod();
			if(!$transaction->getTransactionObject()->isAuthorizationFailed()){
				if(!$transaction->getTransactionObject()->isAuthorized()) {
					$this->loadLayout();
					$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
					if ($transaction->getAuthorizationType() == Customweb_Payment_Authorization_Iframe_IAdapter::AUTHORIZATION_METHOD_NAME) {
						$this->getLayout()->getBlock('content')->append(
							$this->getLayout()->createBlock('oppcw/iframe')
						);
					} elseif ($transaction->getAuthorizationType() == Customweb_Payment_Authorization_Widget_IAdapter::AUTHORIZATION_METHOD_NAME) {
						$this->getLayout()->getBlock('content')->append(
							$this->getLayout()->createBlock('oppcw/widget')
						);
					}
					if($transaction->getTransactionObject()->isAuthorizationFailed()){
						$this->_redirect('OPPCw/process/fail',array('transaction_id' => $transaction->getTransactionId(), '_secure' => true));
					}
					else {
						$this->renderLayout();
					}
				}
				else{
					$this->_redirect('checkout/cart',array('_secure' => true));
				}
			}
			else{
				$this->_redirect('OPPCw/process/fail',array('transaction_id' => $transaction->getTransactionId(), '_secure' => true));
			}
		}
		else{
			$this->_redirect('checkout/cart',array('_secure' => true));
		}
	}


	/**        	    	 	     	 
	 * Renders the template for the ____ModuleCamel___ payment
	 * interface.
	 */
	public function templateAction()
	{
		$this->loadLayout();

		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');

		$this->getLayout()->getBlock('content')->append(
				$this->getLayout()->createBlock('oppcw/template')
		);

		@ob_end_clean();

		$this->renderLayout();
	}
}