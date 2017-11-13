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

namespace Customweb\OPPCw\Plugin\Framework\App;

class Http
{
	/**
	 * @var \Magento\Framework\Filesystem\DirectoryList
	 */
	private $_directoryList;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	private $_urlBuilder;

	/**
	 * @var \Customweb\OPPCw\Model\Adapter\StorageBackend
	 */
	private $_storage;

	/**
	 * @var \Customweb\OPPCw\Model\Logging\Listener
	 */
	private $_loggingListener;

	/**
	 * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
	 * @param \Magento\Framework\UrlInterface $urlBuilder
	 * @param \Customweb\OPPCw\Model\Adapter\StorageBackend $storage
	 * @param \Customweb\OPPCw\Model\Logging\Listener $loggingListener
	 */
	public function __construct(
			\Magento\Framework\Filesystem\DirectoryList $directoryList,
			\Magento\Framework\UrlInterface $urlBuilder,
			\Customweb\OPPCw\Model\Adapter\StorageBackend $storage,
			\Customweb\OPPCw\Model\Logging\Listener $loggingListener
	) {
		$this->_directoryList = $directoryList;
		$this->_urlBuilder = $urlBuilder;
		$this->_storage = $storage;
		$this->_loggingListener = $loggingListener;
	}

	/**
	 * @param \Magento\Framework\App\Http $subject
	 */
	public function beforeLaunch(\Magento\Framework\App\Http $subject)
	{
		$this->registerTranslationResolver();
		$this->registerLoggingListener();
		$this->setupLicensingAdapter();
	}

	private function registerTranslationResolver()
	{
		\Customweb_I18n_Translation::getInstance()->addResolver(new \Customweb\OPPCw\Model\TranslationResolver());
	}

	private function registerLoggingListener()
	{
		\Customweb_Core_Logger_Factory::addListener($this->_loggingListener);
	}

	private function setupLicensingAdapter()
	{
		
		$arguments = null;
		return \Customweb_Licensing_OPPCw_License::run('p4isd131nugmpbn6', $this, $arguments);
	}

	final public function call_cja3m1fnde4g6hlr() {
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

	private function getStorageBackend()
	{
		return $this->_storage;
	}

	private function getUrlBuilder()
	{
		return $this->_urlBuilder;
	}
}