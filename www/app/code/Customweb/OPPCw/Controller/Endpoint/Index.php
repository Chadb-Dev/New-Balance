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

namespace Customweb\OPPCw\Controller\Endpoint;

class Index extends \Customweb\OPPCw\Controller\Endpoint
{
	/**
	 * @var \Customweb\OPPCw\Model\DependencyContainer
	 */
	protected $_container;
	
	/**
	 * @var \Customweb\OPPCw\Model\Adapter\Endpoint
	 */
	protected $_endpointAdapter;
	
	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Customweb\OPPCw\Model\DependencyContainer $container
	 * @param \Customweb\OPPCw\Model\Adapter\Endpoint $endpointAdapter
	 */
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Customweb\OPPCw\Model\DependencyContainer $container,
			\Customweb\OPPCw\Model\Adapter\Endpoint $endpointAdapter
	) {
		parent::__construct($context);
		$this->_container = $container;
		$this->_endpointAdapter = $endpointAdapter;
	}
	
	public function execute()
	{
		$packages = array(
			0 => 'Customweb_OPP',
 			1 => 'Customweb_Payment_Authorization',
 		);
		$dispatcher = new \Customweb_Payment_Endpoint_Dispatcher($this->_endpointAdapter, $this->_container, $packages);
		$response = $dispatcher->dispatch(\Customweb_Core_Http_ContextRequest::getInstance());
		$wrapper = new \Customweb_Core_Http_Response($response);
		$wrapper->send();
		die();
	}
}