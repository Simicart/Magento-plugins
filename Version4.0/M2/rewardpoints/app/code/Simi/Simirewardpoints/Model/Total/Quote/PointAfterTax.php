<?php

namespace Simi\Simirewardpoints\Model\Total\Quote;

class PointAfterTax extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkOutSession;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Simi\Simirewardpoints\Helper\Config
     */
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Simi\Simirewardpoints\Helper\Block\Spend
     */
    protected $_blockSpend;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Spending
     */
    protected $_calculationSpending;

    /**
     * @var \Simi\Simirewardpoints\Helper\Customer
     */
    protected $_helperCustomer;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelperData;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_taxModelCalculation;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxModelConfig;

    /**
     * Point constructor.
     * @param \Simi\Simirewardpoints\Helper\Config $globalConfig
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Simi\Simirewardpoints\Helper\Data $helperData
     * @param \Simi\Simirewardpoints\Helper\Block\Spend $blockSpend
     * @param \Simi\Simirewardpoints\Helper\Calculation\Spending $calculationSpending
     * @param \Simi\Simirewardpoints\Helper\Customer $helperCustomer
     * @param \Magento\Tax\Helper\Data $taxHelperData
     * @param \Magento\Tax\Model\Calculation $taxModelCalculation
     * @param \Magento\Tax\Model\Config $taxModelConfig
     */
    public function __construct(
        \Simi\Simirewardpoints\Helper\Config $globalConfig,
        \Magento\Checkout\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Simi\Simirewardpoints\Helper\Data $helperData,
        \Simi\Simirewardpoints\Helper\Block\Spend $blockSpend,
        \Simi\Simirewardpoints\Helper\Calculation\Spending $calculationSpending,
        \Simi\Simirewardpoints\Helper\Customer $helperCustomer,
        \Magento\Tax\Helper\Data $taxHelperData,
        \Magento\Tax\Model\Calculation $taxModelCalculation,
        \Magento\Tax\Model\Config $taxModelConfig
    ) {
        $this->setCode('simirewardpoint');
        $this->_helper = $globalConfig;
        $this->_checkOutSession = $session;
        $this->_storeManager = $storeManager;
        $this->_priceCurrency = $priceCurrency;
        $this->_helperData = $helperData;
        $this->_blockSpend = $blockSpend;
        $this->_calculationSpending = $calculationSpending;
        $this->_helperCustomer = $helperCustomer;
        $this->_taxHelperData = $taxHelperData;
        $this->_taxModelCalculation = $taxModelCalculation;
        $this->_taxModelConfig = $taxModelConfig;
    }

    /**
     * @param $quote
     * @param $address
     * @param $session
     * @return $this|bool
     */
    public function checkOutput($quote, $address, $session)
    {
        $applyTaxAfterDiscount = (bool) $this->_helper->getConfig(
            \Magento\Tax\Model\Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT,
            $quote->getStoreId()
        );
        if ($applyTaxAfterDiscount) {
            $this->_processHiddenTaxes($address);
            return true;
        }
        if (!$this->_helperData->isEnable($quote->getStoreId())) {
            return true;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return true;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return true;
        }
        if (!$session->getData('use_point')) {
            return $this;
        }
        return false;
    }

    /**
     * collect reward points total
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return \Simi\Simirewardpoints\Model\Total\Quote\Point
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $address = $shippingAssignment->getShipping()->getAddress();
        $session = $this->_checkOutSession;
        if ($this->checkOutput($quote, $address, $session)) {
            return $this;
        }
        $rewardSalesRules = $session->getSimiRewardSalesRules();
        $rewardCheckedRules = $session->getSimiRewardCheckedRules();
        if (!$rewardSalesRules && !$rewardCheckedRules) {
            return $this;
        }

        $helper = $this->_calculationSpending;
        $baseTotal = $helper->getQuoteBaseTotal($quote, $address);
        $maxPoints = $this->_helperCustomer->getBalance();
        if ($maxPointsPerOrder = $helper->getMaxPointsPerOrder($quote->getStoreId())) {
            $maxPoints = min($maxPointsPerOrder, $maxPoints);
        }
        $maxPoints -= $helper->getPointItemSpent();
        if ($maxPoints <= 0) {
            return $this;
        }
        $baseDiscount = 0;
        $pointUsed = 0;
        // Checked Rules Discount First
        if (is_array($rewardCheckedRules)) {
            $newRewardCheckedRules = [];
            foreach ($rewardCheckedRules as $ruleData) {
                if ($baseTotal < 0.0001) {
                    break;
                }
                $rule = $helper->getQuoteRule($ruleData['rule_id']);
                if (!$rule || !$rule->getId()|| $rule->getSimpleAction() != 'fixed') {
                    continue;
                }
                if ($maxPoints < $rule->getPointsSpended()) {
                    $session->addNotice(__('You cannot spend more than %s points per order', $helper->getMaxPointsPerOrder($quote->getStoreId())));
                    continue;
                }
                $points = $rule->getPointsSpended();
                $ruleDiscount = $helper->getQuoteRuleDiscount($quote, $rule, $points);
                if ($ruleDiscount < 0.0001) {
                    continue;
                }
                $baseTotal -= $ruleDiscount;
                $maxPoints -= $points;
                $baseDiscount += $ruleDiscount;
                $pointUsed += $points;
                $newRewardCheckedRules[$rule->getId()] = [
                    'rule_id' => $rule->getId(),
                    'use_point' => $points,
                    'base_discount' => $ruleDiscount,
                ];
                $this->_prepareDiscountForTaxAmount($shippingAssignment, $ruleDiscount, $points, $rule);
                if ($rule->getStopRulesProcessing()) {
                    break;
                }
            }
            $session->setSimiRewardCheckedRules($newRewardCheckedRules);
        }
        // Sales Rule (slider) discount Last
        if (is_array($rewardSalesRules)) {
            $newRewardSalesRules = [];
            if ($baseTotal > 0.0 && isset($rewardSalesRules['rule_id'])) {
                $rule = $helper->getQuoteRule($rewardSalesRules['rule_id']);
                if ($rule && $rule->getId() && $rule->getSimpleAction() == 'by_price') {
                    $points = min($rewardSalesRules['use_point'], $maxPoints);
                    $ruleDiscount = $helper->getQuoteRuleDiscount($quote, $rule, $points);
                    if ($ruleDiscount > 0.0) {
                        $baseTotal -= $ruleDiscount;
                        $maxPoints -= $points;
                        $baseDiscount += $ruleDiscount;
                        $pointUsed += $points;
                        $newRewardSalesRules = [
                            'rule_id' => $rule->getId(),
                            'use_point' => $points,
                            'base_discount' => $ruleDiscount,
                        ];
                        if ($rule->getId() == 'rate') {
                            $this->_prepareDiscountForTaxAmount($shippingAssignment, $ruleDiscount, $points);
                        } else {
                            $this->_prepareDiscountForTaxAmount($shippingAssignment, $ruleDiscount, $points, $rule);
                        }
                    }
                }
            }
            $session->setSimiRewardSalesRules($newRewardSalesRules);
        }
        // verify quote total data
        if ($baseTotal < 0.0001) {
            $baseTotal = 0.0;
            $baseDiscount = $helper->getQuoteBaseTotal($quote, $address);
        }
        if ($baseDiscount) {
            $this->setDiscount($baseDiscount, $total, $address, $pointUsed, $quote);
        }
        return $this;
    }

    /**
     * @param $baseDiscount
     * @param $total
     * @param $address
     * @param $pointUsed
     * @param $quote
     */
    public function setDiscount($baseDiscount, $total, $address, $pointUsed, $quote)
    {
        $discount = $this->_priceCurrency->convert($baseDiscount);
        $total->addTotalAmount('simirewardpoints', -$discount);
        $total->addBaseTotalAmount('simirewardpoints', -$baseDiscount);
        $total->setBaseGrandTotal($address->getBaseGrandTotal() - $baseDiscount);
        $total->setGrandTotal($address->getGrandTotal() - $discount);
        $total->setSimirewardpointsSpent($address->getSimirewardpointsSpent() + $pointUsed);
        $total->setSimirewardpointsBaseDiscount($address->getSimirewardpointsBaseDiscount() + $baseDiscount);
        $total->setSimirewardpointsDiscount($address->getSimirewardpointsDiscount() + $discount);
        $quote->setSimirewardpointsSpent($total->getSimirewardpointsSpent());
        $quote->setSimirewardpointsBaseDiscount($total->getSimirewardpointsBaseDiscount());
        $quote->setSimirewardpointsDiscount($total->getSimirewardpointsDiscount());
        $address->setSimiBaseDiscount($address->getSimiBaseDiscount() + $baseDiscount);
        $quote->setSimiBaseDiscount($quote->getSimirewardpointsBaseDiscount() + $baseDiscount);
    }

    /**
     * Prepare Discount Amount used for Tax
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param type $baseDiscount
     * @return \Simi\Simirewardpoints\Model\Total\Quote\Point
     */
    public function _prepareDiscountForTaxAmount(\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment, $baseDiscount, $points, $rule = null)
    {
        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
        // Calculate total item prices
        $baseItemsPrice = 0;
        $spendHelper = $this->_calculationSpending;
        $baseParentItemsPrice = [];
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $baseParentItemsPrice[$item->getId()] = 0;
                foreach ($item->getChildren() as $child) {
                    if ($rule !== null && !$rule->getActions()->validate($child)) {
                        continue;
                    }
                    $baseParentItemsPrice[$item->getId()] = $item->getQty() * ($child->getQty() * $spendHelper->_getItemBasePrice($child)) - $child->getBaseDiscountAmount() - $child->getSimiBaseDiscount();
                }
                $baseItemsPrice += $baseParentItemsPrice[$item->getId()];
            } elseif ($item->getProduct()) {
                if ($rule !== null && !$rule->getActions()->validate($item)) {
                    continue;
                }
                $baseItemsPrice += $item->getQty() * $spendHelper->_getItemBasePrice($item) - $item->getBaseDiscountAmount() - $item->getSimiBaseDiscount();
            }
        }
        if ($baseItemsPrice < 0.0001) {
            return $this;
        }
        $discountForShipping = $this->_helper->getConfig(
            \Simi\Simirewardpoints\Helper\Calculation\Spending::XML_PATH_SPEND_FOR_SHIPPING,
            $address->getQuote()->getStoreId()
        );
        if ($baseItemsPrice < $baseDiscount && $discountForShipping) {
            $baseDiscountForShipping = $baseDiscount - $baseItemsPrice;
            $baseDiscount = $baseItemsPrice;
        } else {
            $baseDiscountForShipping = 0;
        }
        // Update discount for each item
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $parentItemBaseDiscount = $baseDiscount * $baseParentItemsPrice[$item->getId()] / $baseItemsPrice;
                foreach ($item->getChildren() as $child) {
                    if ($parentItemBaseDiscount <= 0) {
                        break;
                    }
                    if ($rule !== null && !$rule->getActions()->validate($child)) {
                        continue;
                    }
                    $baseItemPrice = $item->getQty() * ($child->getQty() * $spendHelper->_getItemBasePrice($child)) - $child->getBaseDiscountAmount() - $child->getSimiBaseDiscount();
                    $itemBaseDiscount = min($baseItemPrice, $parentItemBaseDiscount);
                    $parentItemBaseDiscount -= $itemBaseDiscount;
                    $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
                    $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                    $child->setSimirewardpointsBaseDiscount($child->getSimirewardpointsBaseDiscount() + $itemBaseDiscount)
                            ->setSimirewardpointsDiscount($child->getSimirewardpointsDiscount() + $itemDiscount)
                            ->setSimiBaseDiscount($child->getSimiBaseDiscount() + $itemBaseDiscount)
                            ->setSimirewardpointsSpent($child->getSimirewardpointsSpent() + $pointSpent);
                }
            } elseif ($item->getProduct()) {
                if ($rule !== null && !$rule->getActions()->validate($item)) {
                    continue;
                }
                $baseItemPrice = $item->getQty() * $spendHelper->_getItemBasePrice($item) - $item->getBaseDiscountAmount() - $item->getSimiBaseDiscount();
                $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
                $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
                $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                $item->setSimirewardpointsBaseDiscount($item->getSimirewardpointsBaseDiscount() + $itemBaseDiscount)
                        ->setSimirewardpointsDiscount($item->getSimirewardpointsDiscount() + $itemDiscount)
                        ->setSimiDiscount($item->getSimiBaseDiscount() + $itemBaseDiscount)
                        ->setSimirewardpointsSpent($item->getSimirewardpointsSpent() + $pointSpent);
            }
        }
        if ($baseDiscountForShipping) {
            $this->baseDiscountForShipping($address, $baseDiscountForShipping);
        }
        return $this;
    }

    /**
     * @param $address
     * @param $baseDiscountForShipping
     */
    public function baseDiscountForShipping($address, $baseDiscountForShipping)
    {
        $shippingAmount = $address->getShippingAmountForDiscount();
        if ($shippingAmount !== null) {
            $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
        } else {
            $baseShippingAmount = $address->getBaseShippingAmount();
        }
        $baseShipping = $baseShippingAmount - $address->getBaseShippingDiscountAmount() - $address->getSimiBaseDiscountForShipping();
        $itemBaseDiscount = ($baseDiscountForShipping <= $baseShipping) ? $baseDiscountForShipping : $baseShipping; //$baseDiscount * $address->getBaseShippingAmount() / $baseItemsPrice;
        $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
        $address->setSimirewardpointsBaseAmount($address->getSimirewardpointsBaseAmount() + $itemBaseDiscount)
                ->setSimirewardpointsAmount($address->getSimirewardpointsAmount() + $itemDiscount)
                ->setSimiBaseDiscountForShipping($address->getSimiBaseDiscountForShipping() + $itemBaseDiscount);
    }

    protected function _processHiddenTaxes($address)
    {
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setHiddenTaxAmount($child->getHiddenTaxAmount() + $child->getSimirewardpointsHiddenTaxAmount());
                    $child->setBaseHiddenTaxAmount($child->getBaseHiddenTaxAmount() + $child->getSimirewardpointsBaseHiddenTaxAmount());

                    $address->addTotalAmount('hidden_tax', $child->getSimirewardpointsHiddenTaxAmount());
                    $address->addBaseTotalAmount('hidden_tax', $child->getSimirewardpointsBaseHiddenTaxAmount());
                }
            } elseif ($item->getProduct()) {
                $item->setHiddenTaxAmount($item->getHiddenTaxAmount() + $item->getSimirewardpointsHiddenTaxAmount());
                $item->setBaseHiddenTaxAmount($item->getBaseHiddenTaxAmount() + $item->getSimirewardpointsBaseHiddenTaxAmount());

                $address->addTotalAmount('hidden_tax', $item->getSimirewardpointsHiddenTaxAmount());
                $address->addBaseTotalAmount('hidden_tax', $item->getSimirewardpointsBaseHiddenTaxAmount());
            }
        }
        if ($address->getSimirewardpointsShippingHiddenTaxAmount()) {
            $address->addTotalAmount('shipping_hidden_tax', $address->getSimirewardpointsShippingHiddenTaxAmount());
            $address->addBaseTotalAmount('shipping_hidden_tax', $address->getSimirewardpointsBaseShippingHiddenTaxAmount());
        }
    }
}
