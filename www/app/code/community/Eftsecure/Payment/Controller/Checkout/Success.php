<?php
namespace Eftsecure\Payment\Controller\Checkout;

use Magento\Braintree\Model\PaymentMethod;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;
use Symfony\Component\Config\Definition\Exception\Exception;

class Success extends \Magento\Framework\App\Action\Action
{
	protected $_checkoutSession;
	protected $_orderFactory;
	protected $_storeManager;
	protected $_scopeConfig;
	protected $_encryptor;

	public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory,
								\Magento\Checkout\Model\Session $checkoutSession, \Magento\Sales\Model\OrderFactory $orderFactory,
								\Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
	{
		$this->_scopeConfig = $scopeConfig;
		$this->_orderFactory = $orderFactory;
		parent::__construct($context);
	}
	
	public function execute()
	{
		if ($this->getRequest()->getPost("merchant_reference") && $this->getRequest()->getPost("callpay_transaction_id")) {
			$orderId = $this->getRequest()->getPost("merchant_reference");
			$order = $this->_orderFactory->create()->loadByIncrementId($orderId);
			if(!empty($order)) {
				$reason = $this->getRequest()->getPost("reason");
				$successful = $this->getRequest()->getPost("success");
				if ($successful == 1) {
					$state = $this->_scopeConfig->getValue('payment/eftpay/success_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
					if (empty($state)) {
						$state = \Magento\Sales\Model\Order::STATE_PROCESSING;
					}
					$status = $state;
					$order->setState($state)->setStatus($status);
					try {
						$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
						$orderCommentSender = $objectManager->create('Magento\Sales\Model\Order\Email\Sender\OrderCommentSender');
						$orderCommentSender->send($order, true, 'Payment successful');
					} catch (Exception $e) {

					}
				} else {
					$order->addStatusHistoryComment($reason, false);
				}
				$order->save();
				return json_encode(['reason'=>$reason,'successful'=>$successful]);
			} else {
				$this->_redirect('401');
			}
		   
		} else {
			$this->_redirect('401');
		}
	}
}
