<?php

/**
 * RewardPoints Action Library Helper
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Helper\Block;

class Spend extends \Simi\Simirewardpoints\Helper\Calculation\AbstractCalculation
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Spending
     */
    protected $_helperSpending;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Simi\Simirewardpoints\Helper\Customer
     */
    protected $_rewardHelperCustomer;

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_rewardHelperData;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_quoteSessionBackend;

    /**
     * Spend constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Simi\Simirewardpoints\Helper\Calculation\Spending $helperSpending
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Backend\Model\Session\Quote $quoteSessionBackend
     * @param \Simi\Simirewardpoints\Helper\Customer $rewardHelperCustomer
     * @param \Simi\Simirewardpoints\Helper\Data $rewardHelperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Simi\Simirewardpoints\Helper\Calculation\Spending $helperSpending,
        \Magento\Framework\App\State $appState,
        \Magento\Backend\Model\Session\Quote $quoteSessionBackend,
        \Simi\Simirewardpoints\Helper\Customer $rewardHelperCustomer,
        \Simi\Simirewardpoints\Helper\Data $rewardHelperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_helperSpending = $helperSpending;
        $this->_appState = $appState;
        $this->_rewardHelperCustomer = $rewardHelperCustomer;
        $this->_rewardHelperData = $rewardHelperData;
        $this->_quoteSessionBackend = $quoteSessionBackend;
        parent::__construct($context, $storeManager, $customerSession, $checkoutSession, $objectManager);
    }

    /**
     * get spending calculation
     *
     * @return \Simi\Simirewardpoints\Helper\Calculation\Spending
     */
    public function getCalculation()
    {
        return $this->_helperSpending;
    }

    /**
     * get current working with quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if ($this->_appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return $this->_quoteSessionBackend->getQuote();
        }
        return $this->_checkoutSession->getQuote();
    }

    /**
     * check reward points is enable to use or not
     *
     * @return boolean
     */
    public function enableReward()
    {
        if (!$this->_rewardHelperData->isEnable($this->_rewardHelperCustomer->getStoreId())) {
            return false;
        }

        if ($this->getQuote()->getBaseGrandTotal() < 0.0001 && !$this->getCalculation()->getTotalRulePoint()
        ) {
            return false;
        }
        if (!$this->_rewardHelperCustomer->isAllowSpend($this->_storeManager->getStore()->getStoreId())) {
            return false;
        }
        return true;
    }

    /**
     * get all spending rules available for current shopping cart
     *
     * @return array
     */
    public function getSpendingRules()
    {
        $cacheKey = 'spending_rules_array';
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        }
        $container = new \Magento\Framework\DataObject([
            'spending_rules' => []
        ]);
        $this->_eventManager->dispatch('simirewardpoints_block_spend_get_rules', [
            'container' => $container,
        ]);
        $this->saveCache($cacheKey, $container->getSpendingRules());
        return $this->getCache($cacheKey);
    }

    /**
     * get all spending rule with type is slider
     *
     * @return array
     */
    public function getSliderRules()
    {
        $rules = [];
        $rule = $this->getCalculation()->getSpendingRateAsRule();
        if ($rule && $rule->getId()) {
            $rules[] = $rule;
        }
        foreach ($this->getSpendingRules() as $rule) {
            if ($rule->getSimpleAction() == 'by_price') {
                $rules[] = $rule;
            }
        }
        return $rules;
    }

    /**
     * get all spending rule with type is checkbox
     *
     * @return array
     */
    public function getCheckboxRules()
    {
        $rules = [];
        $customerPoints = $this->getCustomerTotalPoints() - $this->getCalculation()->getPointItemSpent();
        foreach ($this->getSpendingRules() as $rule) {
            if (in_array($rule->getId(), $this->getCheckedData()) ||
                    ($rule->getSimpleAction() == 'fixed' && $rule->getPointsSpended() <= $customerPoints
                    )) {
                $rules[] = $rule;
            }
        }
        return $rules;
    }

    /**
     * get JSON string used for JS
     *
     * @param array $rules
     * @return string
     */
    public function getRulesJson($rules = null)
    {
        if (is_null($rules)) {
            $rules = $this->getSliderRules();
        }
        $result = [];
        foreach ($rules as $rule) {
            $ruleOptions = [];
            if ($this->getCustomerPoint() < $rule->getPointsSpended()) {
                $ruleOptions['optionType'] = 'needPoint';
                $ruleOptions['needPoint'] = $rule->getPointsSpended() - $this->getCustomerPoint();
            } else {
                $quote = $this->getQuote();
                $sliderOption = [];

                $sliderOption['minPoints'] = 0;
                $sliderOption['pointStep'] = (int) $rule->getPointsSpended();

                $maxPoints = $this->getCustomerPoint();

                if ($rule->getMaxPointsSpended() && $maxPoints > $rule->getMaxPointsSpended()) {
                    $maxPoints = $rule->getMaxPointsSpended();
                }

                if ($maxPoints > $this->getCalculation()->getRuleMaxPointsForQuote($rule, $quote)) {
                    $maxPoints = $this->getCalculation()->getRuleMaxPointsForQuote($rule, $quote);
                }

                // Refine max points
                if ($sliderOption['pointStep']) {
                    $maxPoints = floor($maxPoints / $sliderOption['pointStep']) * $sliderOption['pointStep'];
                }

                $sliderOption['maxPoints'] = max(0, $maxPoints);

                $ruleOptions['sliderOption'] = $sliderOption;
                $ruleOptions['optionType'] = 'slider';
            }
            $result[$rule->getId()] = $ruleOptions;
        }
        return json_encode($result);
    }

    /**
     * get JSON string used for JS
     *
     * @param array $rules
     * @return string
     */
    public function getRulesArray($rules = null)
    {
        if (is_null($rules)) {
            $rules = $this->getSliderRules();
        }
        $result = [];
        foreach ($rules as $rule) {
            $ruleOptions = [];
            if ($this->getCustomerPoint() < $rule->getPointsSpended()) {
                $ruleOptions['optionType'] = 'needPoint';
                $ruleOptions['needPoint'] = $rule->getPointsSpended() - $this->getCustomerPoint();
            } else {
                $quote = $this->getQuote();
                $sliderOption = [];

                $sliderOption['minPoints'] = 0;
                $sliderOption['pointStep'] = (int) $rule->getPointsSpended();

                $maxPoints = $this->getCustomerPoint();

                if ($rule->getMaxPointsSpended() && $maxPoints > $rule->getMaxPointsSpended()) {
                    $maxPoints = $rule->getMaxPointsSpended();
                }
                if ($maxPoints > $this->getCalculation()->getRuleMaxPointsForQuote($rule, $quote)) {
                    $maxPoints = $this->getCalculation()->getRuleMaxPointsForQuote($rule, $quote);
                }
                // Refine max points
                if ($sliderOption['pointStep']) {
                    $maxPoints = floor($maxPoints / $sliderOption['pointStep']) * $sliderOption['pointStep'];
                }
                $sliderOption['maxPoints'] = max(0, $maxPoints);

                $ruleOptions['sliderOption'] = $sliderOption;
                $ruleOptions['optionType'] = 'slider';
            }
            $result[$rule->getId()] = $ruleOptions;
        }
        return $result;
    }

    /**
     * get customer total points on his balance
     *
     * @return int
     */
    public function getCustomerTotalPoints()
    {
        return $this->_rewardHelperCustomer->getBalance();
    }

    /**
     * get customer point after he use to spend for order (estimate)
     *
     * @return int
     */
    public function getCustomerPoint()
    {
        if (!$this->hasCache('customer_point')) {
            $points = $this->getCustomerTotalPoints();
            $points -= $this->getCalculation()->getPointItemSpent();
            $points -= $this->getCalculation()->getCheckedRulePoint();
            if ($points < 0) {
                $points = 0;
            }
            $this->saveCache('customer_point', $points);
        }
        return $this->getCache('customer_point');
    }

    /**
     * get current customer model
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_rewardHelperCustomer->getCustomer();
    }

    /**
     * @param $rule
     * @return string
     */
    public function formatDiscount($rule)
    {
        if ($rule->getId() == 'rate') {
            $price = $rule->getBaseRate();
        } else {
            if ($rule->getDiscountStyle() == 'cart_fixed') {
                $price = $rule->getDiscountAmount();
            } else {
                return round($rule->getDiscountAmount(), 2) . '%';
            }
        }
        return $this->_rewardHelperData->convertAndFormat($price, true);
    }

    /**
     * get slider rules date that applied
     *
     * @return Varien_Object
     */
    public function getSliderData()
    {
        if ($this->_checkoutSession->getSimiRewardSalesRules()) {
            return new \Magento\Framework\DataObject($this->_checkoutSession->getSimiRewardSalesRules());
        }
        return new \Magento\Framework\DataObject([]);
    }

    /**
     * get checked rule data that applied
     *
     * @return array
     */
    public function getCheckedData()
    {
        if (!$this->hasCache('checked_data')) {
            $rewardCheckedRules = $this->_checkoutSession->getSimiRewardCheckedRules();
            if (!is_array($rewardCheckedRules)) {
                $this->saveCache('checked_data', []);
            } else {
                $this->saveCache('checked_data', array_keys($rewardCheckedRules));
            }
        }
        return $this->getCache('checked_data');
    }

    /**
     * check current checkout session is using point or not
     *
     * @return boolean
     */
    public function isUsePoint()
    {
        return $this->_checkoutSession->getData('use_point');
    }
}
