<?php

/**
 *  * You are allowed to use this API in your web application.
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
 */




/**
 * @Bean
 */
class Customweb_OPP_Authorization_Widget_Adapter extends Customweb_OPP_Authorization_AbstractAdapter implements
		Customweb_Payment_Authorization_Widget_IAdapter {

	public function getAdapterPriority(){
		return 50;
	}

	public function getAuthorizationMethodName(){
		return self::AUTHORIZATION_METHOD_NAME;
	}

	public function createTransaction(Customweb_Payment_Authorization_Widget_ITransactionContext $transactionContext, $failedTransaction){
		return $this->createTransactionInternal($transactionContext, $failedTransaction);
	}

	public function getWidgetHTML(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		try{
			if ($transaction->getTransactionContext()->getAlias() instanceof Customweb_Payment_Authorization_ITransaction) {
				$processAliasUrl = $this->getEndpointAdapter()->getUrl('process', 'alias',
						array(
							'cw_transaction_id' => $transaction->getExternalTransactionId()
						));
				return '<div>' . Customweb_I18n_Translation::__('Please wait...') . '</div><script type="text/javascript">window.location = "' .
						$processAliasUrl . '"</script>';
			}
			$checkoutId = $transaction->getCheckoutId();
			if(empty($checkoutId)){
				$checkoutId = $this->generateCheckout($transaction, $formData);
				$transaction->setCheckoutId($checkoutId);
			}

			$responseUrl = $this->getEndpointAdapter()->getUrl('process', 'index',
					array(
						'cw_transaction_id' => $transaction->getExternalTransactionId()
					));
			$html = '<script>var wpwlOptions = {';
			$html .= 'locale: "' . $transaction->getTransactionContext()->getOrderContext()->getLanguage()->getIso2LetterCode() . '",';
			$html .= 'style: "' . $this->getPaymentMethod($transaction->getPaymentMethod())->getWidgetStyle() . '",';
			$html .= $this->getPaymentMethod($transaction->getPaymentMethod())->getAdditionalWidgetOptionString($transaction);
			$html .= '}</script>';
			$html .= '<script src="' . $this->getJavascriptUrl($checkoutId) . '"></script>';
			$html .= '<form action="' . $responseUrl . '" class="paymentWidgets">';
			$html .= $this->getPaymentMethod($transaction->getPaymentMethod())->getPaymentMethodBrand();
			$html .= '</form>';
			return $html;
		}
		catch(Exception $e){
			$transaction->setAuthorizationFailed($e->getMessage());
			return $e->getMessage();
		}
	}

	public function processAuthorization(Customweb_Payment_Authorization_ITransaction $transaction, array $parameters){
		$response = null;
		try {
			$request = new Customweb_OPP_Request($this->getCheckoutStatusUrl($transaction->getCheckoutId()));
			$request->setMethod(Customweb_OPP_Request::METHOD_GET);
			$request->setData($this->getParameterBuilder($transaction)->buildStatusParameters());
			$response = $request->send();
		}
		catch (Exception $e) {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__($e->getMessage()));
		}
		return $this->finalizeAuthorization($transaction, $response);
	}

	/**
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param array $parameters
	 * @throws Exception
	 */
	protected function generateCheckout(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		$request = new Customweb_OPP_Request($this->getCheckoutUrl());
		$parameters = $this->getParameterBuilder($transaction)->buildAuthorizationParameters($formData);
		$transaction->setAuthorizationChannel($parameters['authentication.entityId']);
		$request->setData($parameters);
		$response = $request->send();
		if ($response->result->code != '000.200.100' || !isset($response->id)) {
			throw new Exception('The checkout could not have been created.');
		}
		return $response->id;
	}

	/**
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @return Customweb_OPP_Authorization_Widget_ParameterBuilder
	 */
	protected function getParameterBuilder(Customweb_Payment_Authorization_ITransaction $transaction){
		return new Customweb_OPP_Authorization_Widget_ParameterBuilder($this->getContainer(), $transaction);
	}
}