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
 */

//require_once 'Customweb/Core/DateTime.php';
//require_once 'Customweb/Core/Exception/CastException.php';
//require_once 'Customweb/Payment/Update/IAdapter.php';
//require_once 'Customweb/OPP/Authorization/OppTransaction.php';
//require_once 'Customweb/Payment/Authorization/ErrorMessage.php';
//require_once 'Customweb/I18n/Translation.php';
//require_once 'Customweb/OPP/AbstractAdapter.php';
//require_once 'Customweb/OPP/Request.php';
//require_once 'Customweb/OPP/Update/ParameterBuilder.php';


/**
 * @Bean
 */
class Customweb_OPP_Update_Adapter extends Customweb_OPP_AbstractAdapter implements Customweb_Payment_Update_IAdapter
{
	public function updateTransaction(Customweb_Payment_Authorization_ITransaction $transaction) {
		if (!($transaction instanceof Customweb_OPP_Authorization_OppTransaction)) {
			throw new Customweb_Core_Exception_CastException('Customweb_OPP_Authorization_OppTransaction');
		}
		if ($transaction->isAuthorized() && $transaction->isAuthorizationUncertain() && !$transaction->isUncertainTransactionFinallyDeclined()) {
			try {
				$request = new Customweb_OPP_Request($this->getPaymentStatusUrl($transaction->getPaymentId()));
				$request->setMethod(Customweb_OPP_Request::METHOD_GET);
				$request->setData($this->getParameterBuilder($transaction)->buildParameters());
				$response = $request->send();
				$this->finalizeAuthorization($transaction, $response);
				$transaction->setUpdateExecutionDate(null);
			} catch (Exception $e) {
				$transaction->addErrorMessage(new Customweb_Payment_Authorization_ErrorMessage(Customweb_I18n_Translation::__('Could not execute scheduled update.')));
			}
			if ($transaction->isAuthorized() && $transaction->isAuthorizationUncertain() && !$transaction->isUncertainTransactionFinallyDeclined()
				&& $transaction->getUpdateRetryCounter() < 10) {
				$transaction->increaseUpdateRetryCounter();
				$transaction->setUpdateExecutionDate(Customweb_Core_DateTime::_()->addMinutes(60*$transaction->getUpdateRetryCounter()));
			}
		}
	}

	/**
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param stdClass $response
	 * @return string
	 */
	protected function finalizeAuthorization(Customweb_Payment_Authorization_ITransaction $transaction, $response) {
		if ($transaction->isAuthorized() && $transaction->isAuthorizationUncertain()) {
			if ($response->result->code == '000.000.000'
				|| $response->result->code == '000.600.000'
				|| strpos($response->result->code, '000.100.1') === 0) {
				$transaction->approveUncertainAuthorization();
			} elseif (strpos($response->result->code, '000.400.') === 0
				|| $response->result->code == '800.400.500') {
				// Do nothing, transaction is still uncertain
			} else {
				$transaction->declineUncertainAuthorization($this->getErrorMessage($transaction, $response));
			}
		}
	}

	/**
	 * @return string
	 */
	protected function getPaymentStatusUrl($paymentId)
	{
		return $this->getConfiguration()->getBaseUrl() . '/v1/payments/' . $paymentId;
	}

	/**
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @return Customweb_OPP_Update_ParameterBuilder
	 */
	protected function getParameterBuilder(Customweb_Payment_Authorization_ITransaction $transaction)
	{
		return new Customweb_OPP_Update_ParameterBuilder($this->getContainer(), $transaction);
	}

	/**
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param stdClass $response
	 */
	protected function getErrorMessage(Customweb_Payment_Authorization_ITransaction $transaction, $response)
	{
		if (isset($response->resultDetails) && isset($response->resultDetails->faultString) && !empty($response->resultDetails->faultString)) {
			return Customweb_I18n_Translation::__($response->resultDetails->faultString);
		}
		return Customweb_I18n_Translation::__($response->result->description);
	}

}