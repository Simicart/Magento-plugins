<?php

namespace Simi\Simirewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesModelServiceQuoteSubmitSuccess implements ObserverInterface
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkOutSession;

    /**
     * Helper Action
     *
     * @var \Simi\Simirewardpoints\Helper\Action
     */
    protected $_action;

    /**
     * SalesModelServiceQuoteSubmitAfter constructor.
     * @param \Magento\Checkout\Model\Session $session
     * @param \Simi\Simirewardpoints\Helper\Action $action
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Simi\Simirewardpoints\Helper\Action $action
    ) {
        $this->_checkOutSession = $session;
        $this->_action = $action;
    }

    /**
     * Set Final Price to product in product list
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $order = $observer['order'];
        $quote = $observer['quote'];
        if ($order->getCustomerIsGuest()) {
            return $this;
        }

        // Process spending points for order
        if ($order->getSimirewardpointsSpent() > 0) {
            $this->_action->addTransaction('spending_order', $quote->getCustomer(), $order);
        }

        // Clear reward points checkout session
        $session = $this->_checkOutSession;
        $session->setCatalogRules([]);
        $session->setData('use_point', 0);
        $session->setSimiRewardSalesRules([]);
        $session->setSimiRewardCheckedRules([]);

        return $this;
    }
}
