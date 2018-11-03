<?php

/**
 * RewardPoints Earning Calculation Helper
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Helper\Calculation;

class Spending extends \Simi\Simirewardpoints\Helper\Calculation\AbstractCalculation
{

    const XML_PATH_MAX_POINTS_PER_ORDER = 'simirewardpoints/spending/max_points_per_order';
    const XML_PATH_SPEND_FOR_TAX = 'simirewardpoints/spending/spend_for_tax';
    const XML_PATH_FREE_SHIPPING = 'simirewardpoints/spending/free_shipping';
    const XML_PATH_SPEND_FOR_SHIPPING = 'simirewardpoints/spending/spend_for_shipping';
    const XML_PATH_SPEND_FOR_SHIPPING_TAX = 'simirewardpoints/spending/spend_for_shipping_tax';
    const XML_PATH_ORDER_REFUND_STATUS = 'simirewardpoints/spending/order_refund_state';
    const XML_PATH_MAX_POINTS_DEFAULT = 'simirewardpoints/spending/max_point_default';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Simi\Simirewardpoints\Model\Rate
     */
    protected $_rateModel;

    /**
     * Spending constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Simi\Simirewardpoints\Helper\Config $scopeConfig
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Simi\Simirewardpoints\Model\Rate $rateModel
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Simi\Simirewardpoints\Helper\Config $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Simi\Simirewardpoints\Model\Rate $rateModel
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_rateModel = $rateModel;
        parent::__construct($context, $storeManager, $customerSession, $checkoutSession, $objectManager);
    }

    /**
     * get Max point that customer can used to spend for an order
     *
     * @param mixed $store
     * @return int
     */
    public function getMaxPointsPerOrder($store = null)
    {
        $maxPerOrder = (int) $this->_scopeConfig->getConfig(self::XML_PATH_MAX_POINTS_PER_ORDER, $store);
        if ($maxPerOrder > 0) {
            return $maxPerOrder;
        }
        return 0;
    }

    /**
     * get Total Point that customer used to spent for the order
     *
     * @return int
     */
    public function getTotalPointSpent()
    {
        $container = new \Magento\Framework\DataObject([
            'total_point_spent' => 0
        ]);

        $this->_eventManager->dispatch('simirewardpoints_calculation_spending_get_total_point', [
            'container' => $container,
        ]);
        return $this->getPointItemSpent() + $this->getCheckedRulePoint() + $this->getSliderRulePoint() + $container->getTotalPointSpent();
    }

    /**
     * get discount (Base Currency) by points of each product item on the shopping cart
     * with $item is null, result is the total discount of all items
     *
     * @param Mage_Sales_Model_Quote_Item|null $item
     * @return float
     */
    public function getPointItemDiscount($item = null)
    {
        $container = new \Magento\Framework\DataObject([
            'point_item_discount' => 0
        ]);
        $this->_eventManager->dispatch('simirewardpoints_calculation_spending_point_item_discount', [
            'item' => $item,
            'container' => $container,
        ]);
        return $container->getPointItemDiscount();
    }

    /**
     * get point that customer used to spend for each product item
     * with $item is null, result is the total points used for all items
     *
     * @param Mage_Sales_Model_Quote_Item|null $item
     * @return int
     */
    public function getPointItemSpent($item = null)
    {
        $container = new \Magento\Framework\DataObject([
            'point_item_spent' => 0
        ]);
        $this->_eventManager->dispatch('simirewardpoints_calculation_spending_point_item_spent', [
            'item' => $item,
            'container' => $container,
        ]);
        return $container->getPointItemSpent();
    }

    /**
     * pre collect total for quote/address and return quote total
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param null|Mage_Sales_Model_Quote_Address $address
     * @return float
     */
    public function getQuoteBaseTotal($quote, $address = null)
    {
        $cacheKey = 'quote_base_total';
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        }

        if (is_null($address)) {
            if ($quote->isVirtual()) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
        }
        $baseTotal = 0;
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseTotal += $item->getQty() * ($child->getQty() * $this->_getItemBasePrice($child)) - $child->getBaseDiscountAmount() + $child->getSimirewardpointsBaseDiscount();
                }
            } elseif ($item->getProduct()) {
                $baseTotal += $item->getQty() * $this->_getItemBasePrice($item) - $item->getBaseDiscountAmount() + $item->getSimirewardpointsBaseDiscount();
            }
        }

        if ($this->_scopeConfig->getConfig(self::XML_PATH_SPEND_FOR_SHIPPING, $quote->getStoreId())) {
            $shippingAmount = $address->getShippingAmountForDiscount();

            if ($shippingAmount !== null) {
                $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
            } else {
                $baseShippingAmount = $address->getBaseShippingAmount();
            }

            $baseTotal += $baseShippingAmount - $address->getBaseShippingDiscountAmount() + $address->getSimirewardpointsBaseAmount();
        }
        $this->saveCache($cacheKey, $baseTotal);
        return $baseTotal;
    }

    public function _getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
    }

    /**
     * get discount (Base Currency) by points that spent with check rule type
     *
     * @return float
     */
    public function getCheckedRuleDiscount()
    {
        $container = new \Magento\Framework\DataObject([
            'checked_rule_discount' => 0
        ]);
        $this->_eventManager->dispatch('simirewardpoints_calculation_spending_checked_rule_discount', [
            'container' => $container,
        ]);
        return $container->getCheckedRuleDiscount();
    }

    /**
     * get points used to spend for checked rules
     *
     * @return int
     */
    public function getCheckedRulePoint()
    {
        $container = new \Magento\Framework\DataObject([
            'checked_rule_point' => 0
        ]);
        $this->_eventManager->dispatch('simirewardpoints_calculation_spending_checked_rule_point', [
            'container' => $container,
        ]);
        return $container->getCheckedRulePoint();
    }

    /**
     * get discount (base currency) by points that spent with slider rule type
     *
     * @return float
     */
    public function getSliderRuleDiscount()
    {
        $rewardSalesRules = $this->_checkoutSession->getSimiRewardSalesRules();
        if (is_array($rewardSalesRules) && isset($rewardSalesRules['base_discount']) && $this->_checkoutSession->getData('use_point')
        ) {
            return $rewardSalesRules['base_discount'];
        }
        return 0;
    }

    /**
     * get points used to spend by slider rule
     *
     * @return int
     */
    public function getSliderRulePoint()
    {
        $rewardSalesRules = $this->_checkoutSession->getSimiRewardSalesRules();
        if (is_array($rewardSalesRules) && isset($rewardSalesRules['use_point']) && $this->_checkoutSession->getData('use_point')
        ) {
            return $rewardSalesRules['use_point'];
        }
        return 0;
    }

    /**
     * get total point spent by rules on shopping cart
     *
     * @return int
     */
    public function getTotalRulePoint()
    {
        return $this->getCheckedRulePoint() + $this->getSliderRulePoint();
    }

    /**
     * get quote spending rule by RuleID
     *
     * @param int|'rate' $ruleId
     * @return \Magento\Framework\DataObject
     */
    public function getQuoteRule($ruleId = 'rate')
    {
        $cacheKey = "quote_rule_model:$ruleId";

        if (!$this->hasCache($cacheKey)) {
            if ($ruleId == 'rate') {
                $this->saveCache($cacheKey, $this->getSpendingRateAsRule());
                return $this->getCache($cacheKey);
            }
            $container = new \Magento\Framework\DataObject([
                'quote_rule_model' => null
            ]);
            $this->_eventManager->dispatch('simirewardpoints_calculation_spending_quote_rule_model', [
                'container' => $container,
                'rule_id' => $ruleId,
            ]);

            $this->saveCache($cacheKey, $container->getQuoteRuleModel());
        }
        return $this->getCache($cacheKey);
    }

    /**
     * get Spend Rates as a special rule (with id = 'rate')
     *
     * @return \Magento\Framework\DataObject|false
     */
    public function getSpendingRateAsRule()
    {

        $customerGroupId = $this->getCustomerGroupId();
        $websiteId = $this->getWebsiteId();
        $cacheKey = "rate_as_rule:$customerGroupId:$websiteId";
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        }
        $rate = $this->_rateModel->getRate(
            \Simi\Simirewardpoints\Model\Rate::POINT_TO_MONEY,
            $customerGroupId,
            $websiteId
        );
        if ($rate && $rate->getId()) {
            /**
             * end update
             */
            $this->saveCache($cacheKey, new \Magento\Framework\DataObject([
                'points_spended' => $rate->getPoints(),
                'base_rate' => $rate->getMoney(),
                'simple_action' => 'by_price',
                'id' => 'rate',
                'max_price_spended_type' => $rate->getMaxPriceSpendedType(), //Hai.Tran 13/11
                'max_price_spended_value' => $rate->getMaxPriceSpendedValue()//Hai.Tran 13/11
            ]));
        } else {
            $this->saveCache($cacheKey, false);
        }
        return $this->getCache($cacheKey);
    }

    /**
     * get max points can used to spend for a quote
     *
     * @param \Magento\Framework\DataObject $rule
     * @param Mage_Sales_Model_Quote $quote
     * @return int
     */
    public function getRuleMaxPointsForQuote($rule, $quote)
    {
        $cacheKey = "rule_max_points_for_quote:{$rule->getId()}";
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        }
        if ($rule->getId() == 'rate') {
            if ($rule->getBaseRate() && $rule->getPointsSpended()) {
                $quoteTotal = $this->getQuoteBaseTotal($quote);
                //Hai.Tran 13/11/2013 add limit spend theo quote total
                //Tinh max point cho max total
                $maxPrice = $rule->getMaxPriceSpendedValue() > 0 ? $rule->getMaxPriceSpendedValue() : 0;
                if ($rule->getMaxPriceSpendedType() == 'by_price') {
                    $maxPriceSpend = $maxPrice;
                } elseif ($rule->getMaxPriceSpendedType() == 'by_percent') {
                    $maxPriceSpend = $quoteTotal * $maxPrice / 100;
                } else {
                    $maxPriceSpend = 0;
                }
                if ($quoteTotal > $maxPriceSpend && $maxPriceSpend > 0) {
                    $quoteTotal = $maxPriceSpend;
                }
                //End Hai.Tran 13/11/2013 add limit spend theo quote total

                $maxPoints = ceil(($quoteTotal - $this->getCheckedRuleDiscount()) / $rule->getBaseRate()) * $rule->getPointsSpended();
                if ($maxPerOrder = $this->getMaxPointsPerOrder($quote->getStoreId())) {
                    $maxPerOrder -= $this->getPointItemSpent();
                    $maxPerOrder -= $this->getCheckedRulePoint();
                    if ($maxPerOrder > 0) {
                        $maxPoints = min($maxPoints, $maxPerOrder);
                        $maxPoints = floor($maxPoints / $rule->getPointsSpended()) * $rule->getPointsSpended();
                    } else {
                        $maxPoints = 0;
                    }
                }
                $this->saveCache($cacheKey, $maxPoints);
            }
        } else {
            $container = new \Magento\Framework\DataObject([
                'rule_max_points' => 0
            ]);
            $this->_eventManager->dispatch('simirewardpoints_calculation_spending_rule_max_points', [
                'rule' => $rule,
                'quote' => $quote,
                'container' => $container,
            ]);
            $this->saveCache($cacheKey, $container->getRuleMaxPoints());
        }
        if (!$this->hasCache($cacheKey)) {
            $this->saveCache($cacheKey, 0);
        }
        return $this->getCache($cacheKey);
    }

    /**
     * get discount for quote when a rule is applied and recalculate real point used
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param \Magento\Framework\DataObject $rule
     * @param int $points
     * @return float
     */
    public function getQuoteRuleDiscount($quote, $rule, &$points)
    {
        $cacheKey = "quote_rule_discount:{$rule->getId()}:$points";
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        }
        
        if ($rule->getId() == 'rate') {
            if ($rule->getBaseRate() && $rule->getPointsSpended()) {
                $baseTotal = $this->getQuoteBaseTotal($quote) - $this->getCheckedRuleDiscount();

                /** Brian 26/1/2015 * */
                $maxDiscountSpended = 0;
                if ($maxPriceSpended = $rule->getMaxPriceSpendedValue()) {
                    if ($rule->getMaxPriceSpendedType() == 'by_price') {
                        $maxDiscountSpended = $maxPriceSpended;
                    } elseif ($rule->getMaxPriceSpendedType() == 'by_percent') {
                        $maxDiscountSpended = $this->getQuoteBaseTotal($quote) * $maxPriceSpended / 100;
                    }
                }
                if ($maxDiscountSpended > 0) {
                    $baseTotal = min($maxDiscountSpended, $baseTotal);
                }
                /** end * */
                $maxPoints = ceil($baseTotal / $rule->getBaseRate()) * $rule->getPointsSpended();

                if ($maxPerOrder = $this->getMaxPointsPerOrder($quote->getStoreId())) {
                    $maxPerOrder -= $this->getPointItemSpent();
                    $maxPerOrder -= $this->getCheckedRulePoint();
                    if ($maxPerOrder > 0) {
                        $maxPoints = min($maxPoints, $maxPerOrder);
                    } else {
                        $maxPoints = 0;
                    }
                }

                $points = min($points, $maxPoints);      
                $points = floor($points / $rule->getPointsSpended()) * $rule->getPointsSpended();
                $this->saveCache($cacheKey, min($points * $rule->getBaseRate() / $rule->getPointsSpended(), $baseTotal));
            } else {
                $points = 0;
                $this->saveCache($cacheKey, 0);
            }
        } else {
            $container = new \Magento\Framework\DataObject([
                'quote_rule_discount' => 0,
                'points' => $points
            ]);
            $this->_eventManager->dispatch('simirewardpoints_calculation_spending_quote_rule_discount', [
                'rule' => $rule,
                'quote' => $quote,
                'container' => $container,
            ]);
            $points = $container->getPoints();
            $this->saveCache($cacheKey, $container->getQuoteRuleDiscount());
        }
        return $this->getCache($cacheKey);
    }

    public function isUseMaxPointsDefault($store = null)
    {
        return $this->_scopeConfig->getConfig(self::XML_PATH_MAX_POINTS_DEFAULT, $store);
    }

    public function isUsePoint()
    {
        return $this->_checkoutSession->getData('use_point');
    }
}
