<?php

namespace Simi\Simirewardpoints\Model\Total\Quote;

use Magento\Framework\DataObject;
use Magento\Framework\Event\ObserverInterface;

class ResetRewardpoints implements ObserverInterface
{

    /**
     * Set Quote information about rewardpoints
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setSimiRewardpointsSpent(0);
        $quote->setSimiRewardpointsBaseDiscount(0);
        $quote->setSimiRewardpointsDiscount(0);
        $quote->setSimiRewardpointsEarn(0);
        $quote->setSimiBaseDiscount(0);
        $quote->setSimiRewardpointsBaseAmount(0);
        $quote->setSimiRewardpointsAmount(0);
        $quote->setSimiBaseDiscountForShipping(0);
        $quote->setSimiBaseDiscount(0);
        foreach ($quote->getAllAddresses() as $address) {
            $address->setSimiRewardpointsSpent(0);
            $address->setSimiRewardpointsBaseDiscount(0);
            $address->setSimiRewardpointsDiscount(0);
            $address->setSimiRewardpointsBaseAmount(0);
            $address->setSimiRewardpointsAmount(0);
            $address->setSimiBaseDiscountForShipping(0);
            $address->setSimiBaseDiscount(0);
            $address->setSimiRewardpointsEarn(0);
            foreach ($address->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $child->setSimiRewardpointsBaseDiscount(0)
                                ->setSimiRewardpointsDiscount(0)
                                ->setSimiBaseDiscount(0)
                                ->setSimiRewardpointsEarn(0)
                                ->setSimiRewardpointsSpent(0);
                    }
                } elseif ($item->getProduct()) {
                    $item->setSimiRewardpointsBaseDiscount(0)
                            ->setSimiRewardpointsDiscount(0)
                            ->setSimiBaseDiscount(0)
                            ->setSimiRewardpointsEarn(0)
                            ->setSimiRewardpointsSpent(0);
                }
            }
        }
    }
}
