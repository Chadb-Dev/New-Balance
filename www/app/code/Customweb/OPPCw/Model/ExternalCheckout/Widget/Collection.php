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

namespace Customweb\OPPCw\Model\ExternalCheckout\Widget;

class Collection
{
	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 * @var \Customweb\OPPCw\Model\ExternalCheckout\ContextFactory
	 */
	protected $_contextFactory;

	/**
	 * @var \Customweb\OPPCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Customweb\OPPCw\Model\ExternalCheckout\ContextFactory $contextFactory
	 * @param \Customweb\OPPCw\Model\DependencyContainer $container
	 */
	public function __construct(
			\Magento\Checkout\Model\Session $checkoutSession,
			\Customweb\OPPCw\Model\ExternalCheckout\ContextFactory $contextFactory,
			\Customweb\OPPCw\Model\DependencyContainer $container
	) {
		$this->_checkoutSession = $checkoutSession;
		$this->_contextFactory = $contextFactory;
		$this->_container = $container;
	}

	/**
	 * @return \Customweb\Base\Model\ExternalCheckout\IWidget[]
	 */
	public function getWidgets()
	{
		$lastException = null;
		for ($i = 0; $i < 10; $i++) {
			try {
				$widgets = [];

				

				return $widgets;
			} catch (\Customweb\OPPCw\Model\Exception\OptimisticLockingException $e) {
				// Try again.
				$lastException = $e;
			}
		}
		throw $lastException;
	}
}