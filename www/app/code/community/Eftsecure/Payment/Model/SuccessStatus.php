<?php

namespace Eftsecure\Payment\Model;


/**
 * Order Statuses source model
 */
class SuccessStatus extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string
     */
    protected $_stateStatuses = [
        \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW,
        \Magento\Sales\Model\Order::STATE_PROCESSING,
        \Magento\Sales\Model\Order::STATE_COMPLETE
    ];
}