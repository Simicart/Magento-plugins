<?php

/**
 * RewardPoints Earning Calculation Helper
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Helper\Calculation;

class Earning extends \Simi\Simirewardpoints\Helper\Calculation\AbstractCalculation
{

    const XML_PATH_EARNING_EXPIRE = 'simirewardpoints/earning/expire';
    const XML_PATH_EARNING_ORDER_INVOICE = 'simirewardpoints/earning/order_invoice';
    const XML_PATH_HOLDING_DAYS = 'simirewardpoints/earning/holding_days';
    const XML_PATH_ORDER_CANCEL_STATUS = 'simirewardpoints/earning/order_cancel_state';
    const XML_PATH_EARNING_BY_SHIPPING = 'simirewardpoints/earning/by_shipping';
    const XML_PATH_EARNING_BY_TAX = 'simirewardpoints/earning/by_tax';

    /**
     * @var \Simi\Simirewardpoints\Model\RateFactory
     */
    protected $_rateFactory;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculator
     */
    protected $_helperCalculator;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculator
     */
    protected $_helperConfig;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_adminQuoteSession;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $session,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Simi\Simirewardpoints\Model\RateFactory $rateFactory,
        \Simi\Simirewardpoints\Helper\Calculator $helperCalculator,
        \Simi\Simirewardpoints\Helper\Config $helperConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Model\Session\Quote $adminQuoteSession
    ) {
        $this->_rateFactory = $rateFactory;
        $this->_appState = $appState;
        $this->_helperCalculator = $helperCalculator;
        $this->_helperConfig = $helperConfig;
        $this->_adminQuoteSession = $adminQuoteSession;
        parent::__construct($context, $storeManager, $customerSession, $session, $objectManager);
    }

    /**
     * get Total Point that customer can earn by purchase current order/ quote
     *
     * @param null|Mage_Sales_Model_Quote $quote
     * @return int
     */
    public function getTotalPointsEarning($quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->getQuote();
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        if (!$address->getSimiRewardpointsEarn()) {
            $quote->collectTotals();
        }

        return $address->getSimiRewardpointsEarn();
    }

    /**
     * get Total Point earning by discount
     *
     * @param null|Mage_Sales_Model_Quote $quote
     * @return int
     */
    public function getEarningPointByCoupon($quote = null)
    {
        $needConvert = $this->_helperConfig->getGeneralConfig('convert_point');
        if (!$needConvert) {
            return 0;
        }

        if (is_null($quote)) {
            $quote = $this->getQuote();
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        return $address->getSimiRewardpointsPointsByDiscount();
    }

    /**
     * get Total Point earning by using coupon code
     *
     * @param null|Mage_Sales_Model_Quote $quote
     * @return int
     */
    public function getCouponEarnPoints($quote = null)
    {
        $needConvert = $this->_helperConfig->getGeneralConfig('convert_point');
        if (!$needConvert) {
            return 0;
        }

        if (is_null($quote)) {
            $quote = $this->getQuote();
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        return $address->getCouponCode();
    }

    /**
     * calculate quote earning points by system rate
     *
     * @param float $baseGrandTotal
     * @param mixed $store
     * @return int
     */
    public function getRateEarningPoints($baseGrandTotal, $store = null)
    {

        $customerGroupId = $this->getCustomerGroupId();

        $websiteId = $this->getWebsiteId();

        $rate = $this->_rateFactory->create()->getRate(
            \Simi\Simirewardpoints\Model\Rate::MONEY_TO_POINT,
            $customerGroupId,
            $websiteId
        );

        if ($rate && $rate->getId()) {
            /**
             * end update
             */
            if ($baseGrandTotal < 0) {
                $baseGrandTotal = 0;
            }
            $points = $this->_helperCalculator->round(
                $baseGrandTotal * $rate->getPoints() / $rate->getMoney(),
                $store
            );
        } else {
            $points = 0;
        }

        return $points;
    }

    /**
     * get current checkout quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if ($this->_appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return $this->_adminQuoteSession->getQuote();
        }
        return $this->_checkoutSession->getQuote();
    }

    /**
     * get shipping earning point from $order
     * @return int
     */
    public function getShippingEarningPoints($order)
    {
        if (!$order instanceof \Magento\Sales\Model\Order) {
            return 0;
        }
        $shippingEarningPoints = $order->getSimiRewardpointsEarn();
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildrenItems() as $child) {
                    $shippingEarningPoints -= $child->getSimiRewardpointsEarn();
                }
            } elseif ($item->getProduct()) {
                $shippingEarningPoints -= $item->getSimiRewardpointsEarn();
            }
        }
        return $shippingEarningPoints;
    }
}
