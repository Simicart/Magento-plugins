<?php

namespace Simi\Simirewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveBefore implements ObserverInterface
{

    /**
     * Set Final Price to product in product list
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            $order->setSimiRewardpointsEarn(0);
            foreach ($order->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildrenItems() as $child) {
                        $child->setSimiRewardpointsEarn(0);
                    }
                } elseif ($item->getProduct()) {
                    $item->setSimiRewardpointsEarn(0);
                }
            }
            return $this;
        }

        return $this;
    }
}
