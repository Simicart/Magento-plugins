<?php

namespace Simi\Simirewardpoints\Block\Checkout;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Form extends \Magento\Payment\Block\Form
{

    /**
     * @var \Simi\Simirewardpoints\Helper\Point
     */
    protected $_helperPoint;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Earning
     */
    protected $_helperEarning;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Spending
     */
    protected $_helperSpending;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Simi\Simirewardpoints\Helper\Block\Spend
     */
    protected $_blockSpend;

    /**
     * @var \Simi\Simirewardpoints\Helper\Customer
     */
    protected $_rewardpointCustomer;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_sessionManager;

    /**
     * Form constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Simi\Simirewardpoints\Helper\Point $helperPoint
     * @param \Simi\Simirewardpoints\Helper\Data $helperData
     * @param \Simi\Simirewardpoints\Helper\Customer $helperCustomer
     * @param \Simi\Simirewardpoints\Helper\Calculation\Earning $helperEarning
     * @param \Simi\Simirewardpoints\Helper\Calculation\Spending $helperSpending
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Customer\Model\Session $customerSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Simi\Simirewardpoints\Helper\Block\Spend $blockSpend
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Simi\Simirewardpoints\Helper\Data $helperData,
        \Simi\Simirewardpoints\Helper\Customer $helperCustomer,
        \Simi\Simirewardpoints\Helper\Calculation\Earning $helperEarning,
        \Simi\Simirewardpoints\Helper\Calculation\Spending $helperSpending,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Customer\Model\Session $customerSession,
        PriceCurrencyInterface $priceCurrency,
        \Simi\Simirewardpoints\Helper\Block\Spend $blockSpend,
        \Magento\Framework\Session\SessionManager $sessionManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helperPoint = $helperPoint;
        $this->_helperEarning = $helperEarning;
        $this->_helperSpending = $helperSpending;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_currency = $currency;
        $this->_priceCurrency = $priceCurrency;
        $this->_helperData = $helperData;
        $this->_rewardpointCustomer = $helperCustomer;
        $this->_blockSpend = $blockSpend;
        $this->_sessionManager = $sessionManager;
    }

    /**
     * @param null $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore($storeId = null)
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return \Magento\Framework\App\ActionFlag|
     * @return \Simi\Simirewardpoints\Helper\Calculation\Earning
     */
    public function getHelperEarning()
    {
        return $this->_helperEarning;
    }

    /**
     * @return array
     */
    public function getRewardpointsData()
    {
        $earningLabel = __('You will earn');
        $spendingLabel = __('You will spend');

        if ($this->_request->getModuleName() == 'webpos') {
            $earningLabel = __('Customer will earn');
            $spendingLabel = __('Customer will spend');
        } elseif (!$this->_customerSession->isLoggedIn()) {
            if ($this->_helperData->isModuleOutputEnabled('Magestore_Onestepcheckout') && $this->_request->getModuleName() == 'onestepcheckout') {
                $earningLabel = "<a href='javascript:void(0);' onclick='login_popup.show();'>" . __('Login') . "</a> " . __('to earn');
            } else {
                $this->_sessionManager->setData('redirect', $this->_urlInterface->getCurrentUrl());
                $earningLabel = "<a href='" . $this->getUrl('simirewardpoints/index/redirectLogin') . "'>" . __('Login') . "</a> " . __('to earn');
            }
        }
        $this->_checkoutSession->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
        $rewardPointDiscount = strip_tags($this->_helperData->convertAndFormat(-$this->_checkoutSession->getQuote()->getRewardpointsBaseDiscount()));

        $result = [];
        $result['enableReward'] = $this->_blockSpend->enableReward();
        $result['displayEarning'] = $this->getEarningPoint() > 0;
        $result['rewardpointsEarning'] = $this->_helperPoint->format($this->getEarningPoint());
        $result['displaySpending'] = $this->getSpendingPoint() > 0;
        $result['rewardpointsSpending'] = $this->_helperPoint->format($this->getSpendingPoint());
        $result['displayUsePoint'] = $this->_checkoutSession->getQuote()->getRewardpointsBaseDiscount() ? $this->_checkoutSession->getQuote()->getRewardpointsBaseDiscount() : 0;
        $result['rewardpointsUsePoint'] = $rewardPointDiscount;
        $result['earningLabel'] = $earningLabel;
        $result['spendingLabel'] = $spendingLabel;
        return $result;
    }

    /**
     * @return int
     */
    public function getEarningPoint()
    {
        if ($this->_helperSpending->getTotalPointSpent() && !$this->_helperData->getEarningConfig('earn_when_spend', $this->_storeManager->getStore()->getId())) {
            return 0;
        }
        return $this->_helperEarning->getTotalPointsEarning();
    }

    /**
     * @return int
     */
    public function getSpendingPoint()
    {

        return $this->_helperSpending->getTotalPointSpent();
    }
}
