<?php

namespace Simi\Simirewardpoints\Model\Plugin\Quote;

class RewardpointsToOrderItem
{

    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return Item
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem Item */
        $orderItem = $proceed($item, $additional);
        if ($item->getSimiRewardpointsEarn()) {
            $orderItem->setSimiRewardpointsEarn($item->getSimiRewardpointsEarn());
        }
        if ($item->getSimiRewardpointsSpent()) {
            $orderItem->setSimiRewardpointsSpent($item->getSimiRewardpointsSpent());
            $orderItem->setSimiRewardpointsBaseDiscount($item->getSimiRewardpointsBaseDiscount());
            $orderItem->setSimiRewardpointsDiscount($item->getSimiRewardpointsDiscount());
        }

        return $orderItem;
    }
}
