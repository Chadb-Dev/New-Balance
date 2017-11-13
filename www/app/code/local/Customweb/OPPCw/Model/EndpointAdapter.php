<?php
//require_once 'Customweb/Payment/Endpoint/AbstractAdapter.php';
//require_once 'Customweb/OPPCw/Model/ConfigurationAdapter.php';


/**
 * @Bean
 */
class Customweb_OPPCw_Model_EndpointAdapter extends Customweb_Payment_Endpoint_AbstractAdapter
{
	protected function getBaseUrl() {
		return Mage::getUrl('OPPCw/endpoint/index', array('_store' => Customweb_OPPCw_Model_ConfigurationAdapter::getStoreId()));
	}
	
	protected function getControllerQueryKey() {
		return 'c';
	}
	
	protected function getActionQueryKey() {
		return 'a';
	}
	
	public function getFormRenderer() {
		return Mage::getModel('oppcw/formRenderer');
	}
}