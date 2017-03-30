<?php

namespace Simi\Simirewardpoints\Model\Total\Quote;

use Magento\Framework\Event\ObserverInterface;

class Earning implements ObserverInterface
{

    /**
     * Action
     *
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Earning
     */
    protected $_helperEarning;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Spending
     */
    protected $_helperSpending;

    /**
     * @var \MSimi\Simirewardpoints\Helper\Calculator
     */
    protected $_helperCalculator;

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    public function __construct(
        \Simi\Simirewardpoints\Helper\Data $helper,
        \Simi\Simirewardpoints\Helper\Calculation\Earning $helperEarning,
        \Simi\Simirewardpoints\Helper\Calculation\Spending $helperSpending,
        \Simi\Simirewardpoints\Helper\Calculator $_helperCalculator,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->_helper = $helper;
        $this->_helperEarning = $helperEarning;
        $this->_helperSpending = $helperSpending;
        $this->_helperCalculator = $_helperCalculator;
        $this->_eventManager = $eventManager;
    }

    /**
     * Change collect total to Event to ensure earning is last runned total
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer['quote'];
        foreach ($quote->getAllAddresses() as $address) {
            if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
                continue;
            }
            if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
                continue;
            }
            $this->setEarningPoints($address, $quote);
        }
    }

    /**
     * collect reward points that customer earned (per each item and address) total
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param Mage_Sales_Model_Quote $quote
     * @return \Simi\Simirewardpoints\Model\Total\Quote\Earning
     */
    public function setEarningPoints($address, $quote)
    {
        if (!$this->_helper->isEnable($quote->getStoreId())) {
            return $this;
        }
        if ($this->_helperSpending->getTotalPointSpent() && !$this->_helper->getEarningConfig('earn_when_spend', $address->getStoreId())) {
            $address->setSimiRewardpointsEarn(0);
            return $this;
        }
//         get points that customer can earned by Rates
        $this->_eventManager->dispatch('rewardpoints_collect_earning_total_points_before', ['address' => $address]);
        if (!$address->getSimiRewardpointsEarn()) {
            $baseGrandTotal = $address->getBaseGrandTotal();
            if (!$this->_helper->getEarningConfig('by_shipping', $address->getStoreId())) {
                $baseGrandTotal -= $address->getBaseShippingAmount();
                if ($this->_helper->getEarningConfig('by_tax', $address->getStoreId())) {
                    $baseGrandTotal -= $address->getBaseShippingTaxAmount();
                }
            }
            if (!$this->_helper->getEarningConfig('by_tax', $address->getStoreId())) {
                $baseGrandTotal -= $address->getBaseTaxAmount();
            }
            $baseGrandTotal = max(0, $baseGrandTotal);
            $earningPoints = $this->_helperEarning->getRateEarningPoints(
                $baseGrandTotal,
                $address->getStoreId()
            );
            if ($earningPoints > 0) {
                $address->setSimiRewardpointsEarn($earningPoints);
                $quote->setSimiRewardpointsEarn($quote->getSimiRewardpointsEarn() + $earningPoints);
            }

            // Update earning point for each items
            $this->_updateEarningPoints($address);
        }
        $this->_eventManager->dispatch('simirewardpoints_collect_earning_total_points_after', ['address' => $address]);
        return $this;
    }

    /**
     * update earning points for address items
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return \Simi\Simirewardpoints\Model\Total\Quote\Earning
     */
    protected function _updateEarningPoints($address)
    {
        $items = $address->getAllItems();
        $earningPoints = $address->getSimiRewardpointsEarn();
        if (!count($items) || $earningPoints <= 0) {
            return $this;
        }

        // Calculate total item prices
        $baseItemsPrice = 0;
        $totalItemsQty = 0;
        $isBaseOnQty = false;
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemsPrice += $item->getQty() * ($child->getQty() * $child->getBasePriceInclTax()) - $child->getBaseDiscountAmount() - $child->getSimiBaseDiscount();
                    $totalItemsQty += $item->getQty() * $child->getQty();
                }
            } elseif ($item->getProduct()) {
                $baseItemsPrice += $item->getQty() * $item->getBasePriceInclTax() - $item->getBaseDiscountAmount() - $item->getSimiBaseDiscount();
                $totalItemsQty += $item->getQty();
            }
        }
        $earnpointsForShipping = $this->_helper->getEarningConfig('by_shipping', $address->getQuote()->getStoreId());
        if ($earnpointsForShipping) {
            $baseItemsPrice += $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getSimiBaseDiscountForShipping();
        }
        if ($baseItemsPrice < 0.0001) {
            $isBaseOnQty = true;
        }

        // Update for items
        $deltaRound = 0;
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemPrice = $item->getQty() * ($child->getQty() * $child->getBasePriceInclTax()) - $child->getBaseDiscountAmount() - $child->getSimiBaseDiscount();
                    $itemQty = $item->getQty() * $child->getQty();
                    if ($isBaseOnQty) {
                        $realItemEarning = $itemQty * $earningPoints / $totalItemsQty + $deltaRound;
                    } else {
                        $realItemEarning = $baseItemPrice * $earningPoints / $baseItemsPrice + $deltaRound;
                    }
                    $itemEarning = $this->_helperCalculator->round($realItemEarning);
                    $deltaRound = $realItemEarning - $itemEarning;
                    $child->setRewardpointsEarn($itemEarning);
                }
            } elseif ($item->getProduct()) {
                $baseItemPrice = $item->getQty() * $item->getBasePriceInclTax() - $item->getBaseDiscountAmount() - $item->getSimiBaseDiscount();
                $itemQty = $item->getQty();
                if ($isBaseOnQty) {
                    $realItemEarning = $itemQty * $earningPoints / $totalItemsQty + $deltaRound;
                } else {
                    $realItemEarning = $baseItemPrice * $earningPoints / $baseItemsPrice + $deltaRound;
                }
                $itemEarning = $this->_helperCalculator->round($realItemEarning);
                $deltaRound = $realItemEarning - $itemEarning;
                $item->setSimiRewardpointsEarn($itemEarning);
            }
        }

        return $this;
    }
}
