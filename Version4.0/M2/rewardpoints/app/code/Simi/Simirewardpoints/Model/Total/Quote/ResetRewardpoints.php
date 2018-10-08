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
        $quote->setSimirewardpointsSpent(0);
        $quote->setSimirewardpointsBaseDiscount(0);
        $quote->setSimirewardpointsDiscount(0);
        $quote->setSimirewardpointsEarn(0);
        $quote->setSimiBaseDiscount(0);
        $quote->setSimirewardpointsBaseAmount(0);
        $quote->setSimirewardpointsAmount(0);
        $quote->setSimiBaseDiscountForShipping(0);
        $quote->setSimiBaseDiscount(0);
        foreach ($quote->getAllAddresses() as $address) {
            $address->setSimirewardpointsSpent(0);
            $address->setSimirewardpointsBaseDiscount(0);
            $address->setSimirewardpointsDiscount(0);
            $address->setSimirewardpointsBaseAmount(0);
            $address->setSimirewardpointsAmount(0);
            $address->setSimiBaseDiscountForShipping(0);
            $address->setSimiBaseDiscount(0);
            $address->setSimirewardpointsEarn(0);
            foreach ($address->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $child->setSimirewardpointsBaseDiscount(0)
                                ->setSimirewardpointsDiscount(0)
                                ->setSimiBaseDiscount(0)
                                ->setSimirewardpointsEarn(0)
                                ->setSimirewardpointsSpent(0);
                    }
                } elseif ($item->getProduct()) {
                    $item->setSimirewardpointsBaseDiscount(0)
                            ->setSimirewardpointsDiscount(0)
                            ->setSimiBaseDiscount(0)
                            ->setSimirewardpointsEarn(0)
                            ->setSimirewardpointsSpent(0);
                }
            }
        }
    }
}
