<?php

namespace Simi\Simirewardpoints\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderCreditmemoRefund implements ObserverInterface
{

    /**
     * Update the point balance to customer's account
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer['creditmemo'];
        $order = $creditmemo->getOrder();
        if ($order->getSimirewardpointsSpent() && $order->getForcedCanCreditmemo()) {
            $order->setForcedCanCreditmemo(false);
        }
    }
}
