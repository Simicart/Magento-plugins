<?php

namespace Simi\Simirewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderInvoiceSaveAfter implements ObserverInterface
{

    /**
     * Store manager
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * Helper Action
     *
     * @var \Simi\Simirewardpoints\Helper\Action
     */
    protected $_action;

    /**
     * SalesOrderInvoiceSaveAfter constructor.
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Simi\Simirewardpoints\Helper\Action $action
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customer,
        \Simi\Simirewardpoints\Helper\Action $action
    ) {
        $this->_customer = $customer;
        $this->_action = $action;
    }

    /**
     * Set Final Price to product in product list
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer['invoice'];
        $order = $invoice->getOrder();
        if ($order->getCustomerIsGuest() || !$order->getCustomerId() || $invoice->getState() != \Magento\Sales\Model\Order\Invoice::STATE_PAID || !$order->getRewardpointsEarn()
        ) {
            return $this;
        }
        if (!$this->_action->getConfig(
            \Simi\Simirewardpoints\Helper\Calculation\Earning::XML_PATH_EARNING_ORDER_INVOICE,
            $order->getStoreId()
        )) {
            return $this;
        }
        $customer = $this->_customer->load($order->getCustomerId());
        if (!$customer->getId()) {
            return $this;
        }

        $this->_action->addTransaction(
            'earning_invoice',
            $customer,
            $invoice
        );

        return $this;
    }
}
