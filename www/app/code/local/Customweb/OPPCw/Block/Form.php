<?php

/**
 *  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
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
 * @category Customweb
 * @package Customweb_OPPCw
 *
 */
class Customweb_OPPCw_Block_Form extends Mage_Payment_Block_Form {
	private $printJavascript = true;

	protected function _construct(){
		parent::_construct();
		$this->setTemplate('customweb/oppcw/form.phtml');
	}

	public function getContent(){
		
		$arguments = null;
		return Customweb_Licensing_OPPCw_License::run('ekrdscpjqef6b2cn', $this, $arguments);
	}

	final public function call_pju4lbpe1bi0ho79() {
		$arguments = func_get_args();
		$method = $arguments[0];
		$call = $arguments[1];
		$parameters = array_slice($arguments, 2);
		if ($call == 's') {
			return call_user_func_array(array(get_class($this), $method), $parameters);
		}
		else {
			return call_user_func_array(array($this, $method), $parameters);
		}
		
		
	}
	private function getPaymentForm($code, $method){
		$form = '
			<div id="payment_description_' . $code . '" class="cw_payment_description">
				' . ($method->getPaymentMethodConfigurationValue('show_image') ? '
					<img src="' . $this->getSkinUrl('images/oppcw/' . $method->getPaymentMethodName() . '.png') . '" /><br/>
				' : '') . $this->getMethodDescription() . '
			</div>
			' . $this->getAliasSelect() . '
			<input type="hidden" id="' . $code . '_authorization_method" value="' . $this->getAuthorizationMethod() . '" />
			<div id="payment_form_fields_' . $code . '">
				' . $this->getFormFields() . '
			</div>';
		if ($this->printJavascript) {
			$form .= '

			<script type="text/javascript">
				' . $this->getFormJavaScript() . '
			</script>';
		}
		return $form;
	}

	public function getFormFields(){
		Mage::getSingleton('checkout/session')->setAliasId('new');
		return $this->getMethod()->generateVisibleFormFields(array(
			'alias_id' => 'new'
		));
	}

	public function getFormJavaScript(){
		return $this->getMethod()->generateFormJavaScript(array(
			'alias_id' => 'new'
		));
	}

	public function getProcessUrl(){
		return Mage::getUrl('OPPCw/process/process', array(
			'_secure' => true
		));
	}

	public function getJavascriptUrl(){
		return Mage::getUrl('OPPCw/process/ajax', array(
			'_secure' => true
		));
	}

	public function getHiddenFieldsUrl(){
		return Mage::getUrl('OPPCw/process/getHiddenFields', array(
			'_secure' => true
		));
	}

	public function getVisibleFieldsUrl(){
		return Mage::getUrl('OPPCw/process/getVisibleFields', array(
			'_secure' => true
		));
	}

	public function getAuthorizationMethod(){
		$adapter = $this->getMethod()->getAuthorizationAdapter(false)->getAuthorizationMethodName();
		$adapter = strtolower($adapter);
		$adapter = str_replace('authorization', '', $adapter);
		return $adapter;
	}

	public function getMethodDescription(){
		return $this->getMethod()->getPaymentMethodConfigurationValue('description', Mage::app()->getLocale()->getLocaleCode());
	}

	public function getAliasSelect(){
		$payment = $this->getMethod();
		$result = "";

		if ($payment->getPaymentMethodConfigurationValue('alias_manager') == 'active') {
			$aliasList = $payment->loadAliasForCustomer();

			if (count($aliasList)) {
				$alias = array(
					'new' => Mage::helper('OPPCw')->__('New card')
				);

				foreach ($aliasList as $key => $value) {
					$alias[$key] = $value;
				}

				// The onchange even listener is added here, because there seems to be a bug with prototype's observe
				// on select fields.        	    	 	     	 
				$selectControl = new Customweb_OPPCw_Model_Select("alias_select", $alias, 'new',
						"cwpm_" . $payment->getCode() . ".loadAliasData(this)");
				$aliasElement = new Customweb_Form_Element(Mage::helper('OPPCw')->__("Saved cards:"), $selectControl,
						Mage::helper('OPPCw')->__("You may choose one of the cards you paid before on this site."));
				$aliasElement->setRequired(false);

				$renderer = new Customweb_OPPCw_Model_FormRenderer();
				$renderer->setNameSpacePrefix($payment->getCode());
				$result = $renderer->renderElements(array(
					0 => $aliasElement
				));
			}
		}

		return $result;
	}

	/**
	 * Sets printJavascript to false.
	 * The property is used to determine if javascript should be output in the getPayment
	 */
	public function disableJavascript(){
		$this->printJavascript = false;
	}
}
