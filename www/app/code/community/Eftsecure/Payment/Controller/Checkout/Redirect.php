<?php
namespace Eftsecure\Payment\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;

class Redirect extends \Magento\Framework\App\Action\Action
{
	protected $_checkoutSession;
	protected $_resultPageFactory;
	protected $_orderFactory;
	protected $_storeManager;
	protected $_scopeConfig;
	protected $_encryptor;

    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory,
								\Magento\Checkout\Model\Session $checkoutSession, \Magento\Sales\Model\OrderFactory $orderFactory,
								\Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
								\Magento\Framework\Encryption\EncryptorInterface $encryptor)
    {
		$this->_checkoutSession = $checkoutSession;
		$this->_resultPageFactory = $resultPageFactory;
		$this->_orderFactory = $orderFactory;
		$this->_storeManager = $storeManager;
		$this->_scopeConfig = $scopeConfig;
		$this->_encryptor = $encryptor;
        parent::__construct($context);
    }
	public function execute()
	{
		$last_order_id = $this->_checkoutSession->getLastRealOrder()->getIncrementId();
		if($last_order_id){
			$this->eftSecure($last_order_id);
		} else {
			$home_url = $this->_storeManager->getStore()->getBaseUrl();
			$this->_redirect($home_url);
		}
	}

	/**
	 * @param $last_order_id
	 */
	private function eftSecure($last_order_id) {
		$order = $this->_orderFactory->create()->loadByIncrementId($last_order_id);
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl();
		$username = $this->_scopeConfig->getValue('payment/eftpay/eftsecure_username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$password = $this->_encryptor->decrypt($this->_scopeConfig->getValue('payment/eftpay/eftsecure_password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
		$curl = curl_init('https://eftsecure.callpay.com/api/v1/token');
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);

		$response = curl_exec($curl);
		curl_close($curl);

		$responseData = json_decode($response);
		if(isset($responseData->token)){
			$curl = curl_init('https://eftsecure.callpay.com/api/v1/eft/payment-key');
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			$successUrl =$baseUrl.'checkout/onepage/success';
			$errorUrl = $baseUrl.'checkout/onepage/failure';
			$notifyUrl = $baseUrl.'eft/checkout/success';
			$params = array(
				"merchant_reference"  => $last_order_id,
				"X-Token" 			=> $responseData->token,
				"amount" 			=> number_format($order->getGrandTotal(), 2),
				"notify_url" 		=> $notifyUrl,
				"success_url" 		=> $successUrl,
				"error_url" 		=> $errorUrl,
				"cancel_url" 		=> $errorUrl
			);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			$response = curl_exec($curl);
			curl_close($curl);
			$responseData = json_decode($response);
			$this->_redirect($responseData->url);
		} else {
			$this->_redirect($baseUrl);
		}
	}
}
