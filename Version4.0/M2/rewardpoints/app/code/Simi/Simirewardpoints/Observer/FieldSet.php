<?php

namespace Simi\Simirewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class FieldSet implements ObserverInterface
{

    public $_helperPoint;
    public $_helperCustomer;
    public $_helperSpending;

    /**
     * FieldSet constructor.
     * @param \Simi\Simirewardpoints\Helper\Point $helperPoint
     * @param \Simi\Simirewardpoints\Helper\Customer $helperCustomer
     * @param \Simi\Simirewardpoints\Helper\Calculation\Spending $helperSpending
     */
    public function __construct(
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Simi\Simirewardpoints\Helper\Customer $helperCustomer,
        \Simi\Simirewardpoints\Helper\Calculation\Spending $helperSpending
    ) {
        $this->_helperPoint = $helperPoint;
        $this->_helperCustomer = $helperCustomer;
        $this->_helperSpending = $helperSpending;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if ($order->getCustomerIsGuest()) {
            return $this;
        }

        $order->setSimiRewardpointsEarn($quote->getSimiRewardpointsEarn())
                ->setSimiRewardpointsSpent($quote->getSimiRewardpointsSpent())
                ->setSimiRewardpointsBaseDiscount($quote->getSimiRewardpointsBaseDiscount())
                ->setSimiRewardpointsDiscount($quote->getSimiRewardpointsDiscount())
                ->setSimiRewardpointsBaseAmount($quote->getSimiRewardpointsBaseAmount())
                ->setSimiRewardpointsAmount($quote->getSimiRewardpointsAmount());

        // Validate point amount before place order
        $totalPointSpent = $this->_helperSpending->getTotalPointSpent();
        if (!$totalPointSpent) {
            return $this;
        }

        $balance = $this->_helperCustomer->getBalance();
        if ($balance < $totalPointSpent) {
            throw new \Exception(__(
                'Your points balance is not enough to spend for this order'
            ));
        }

        $minPoint = (int) $this->_helperPoint->getConfig(
            \Simi\Simirewardpoints\Helper\Customer::XML_PATH_REDEEMABLE_POINTS,
            $quote->getStoreId()
        );
        if ($minPoint > $balance) {
            throw new \Exception(__(
                'Minimum points balance allows to redeem is %s',
                $this->_helperPoint->format($minPoint, $quote->getStoreId())
            ));
        }

        $applyTaxAfterDiscount = (bool) $this->_helperPoint->getConfig(\Magento\Tax\Model\Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $quote->getStoreId());

        if ($applyTaxAfterDiscount) {
            foreach ($quote->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $child->setDiscountAmount($child->getDiscountAmount() - $child->getSimiRewardpointsDiscount());
                        $child->setBaseDiscountAmount($child->getBaseDiscountAmount() - $child->getSimiRewardpointsBaseDiscount());
                    }
                } elseif ($item->getProduct()) {
                    $item->setDiscountAmount($item->getDiscountAmount() - $item->getSimiRewardpointsDiscount());
                    $item->setBaseDiscountAmount($item->getBaseDiscountAmount() - $item->getSimiRewardpointsBaseDiscount());
                }
            }
            $order->setBaseShippingDiscountAmount($order->getBaseShippingDiscountAmount() - $quote->getSimiRewardpointsBaseAmount());
            $order->setShippingDiscountAmount($order->getShippingDiscountAmount() - $quote->getSimiRewardpointsAmount());
            foreach ($order->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildrenItems() as $child) {
                        $child->setDiscountAmount($child->getDiscountAmount() - $child->getSimiRewardpointsDiscount());
                        $child->setBaseDiscountAmount($child->getBaseDiscountAmount() - $child->getSimiRewardpointsBaseDiscount());
                    }
                } elseif ($item->getProduct() && !$item->getParentItem()) {
                    $item->setDiscountAmount($item->getDiscountAmount() - $item->getSimiRewardpointsDiscount());
                    $item->setBaseDiscountAmount($item->getBaseDiscountAmount() - $item->getSimiRewardpointsBaseDiscount());
                }
            }
        }
    }
}
