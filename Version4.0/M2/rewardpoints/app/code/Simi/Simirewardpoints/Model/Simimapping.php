<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Simi\Simirewardpoints\Model;

/**
 * Description of Simimapping
 *
 * @author scott
 */
class Simimapping extends \Magento\Framework\Model\AbstractModel
{

    private $customerSession;
    private $objectManager;
    public $pointHelper;
    public $customerHelper;
    private $currencyPrice;
    /*
     * Config Helper
     * Simi\Simirewardpoints\Helper\Config $helperConfig;
     */
    public $configHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Simi\Simirewardpoints\Helper\Config $configHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->customerSession = $this->objectManager
                ->get('Magento\Customer\Model\Session');
        $this->pointHelper = $this->objectManager
                ->get('Simi\Simirewardpoints\Helper\Point');
        $this->customerHelper = $this->objectManager
                ->get('Simi\Simirewardpoints\Helper\Customer');
        $this->currencyPrice = $this->objectManager
                ->create('\Magento\Framework\Pricing\PriceCurrencyInterface');
        $this->configHelper = $configHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    public function getRewardInfo()
    {
        $list = [];
        $groupId = $this->customerSession->isLoggedIn() ? $this->customerSession
                ->getCustomerGroupId()
                : Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
        
        if ($this->customerSession->isLoggedIn()) {
            $list['loyalty_point'] = (int) $this->customerHelper->getBalance();
            $list['loyalty_balance'] = $this->customerHelper->getBalanceFormated();
            $list['loyalty_redeem'] = $this->getMenuBalance();
            $holdingBalance = $this->customerHelper->getAccount()->getHoldingBalance();
            if ($holdingBalance > 0) {
                $list['loyalty_hold'] = $this->pointHelper->format($holdingBalance);
            }
            $list['loyalty_image'] = $this->pointHelper->getImage();
            // Notification Settings
            $list['is_notification'] = (int) $this->customerHelper->getAccount()
                    ->getData('is_notification');
            $list['expire_notification'] = (int) $this->customerHelper->getAccount()
                    ->getData('expire_notification');
        }
        
        $this->_getEarningPolicy($list, $groupId);
        $this->_getSpendingPolicy($list, $groupId);
        $this->_getOstherPolicy($list);
        return $list;
    }
    
    public function _getEarningPolicy(&$list, $groupId)
    {
          // Earning Point policy
        $earningRate = $this->objectManager->create('Simi\Simirewardpoints\Model\Rate')
                ->getRate(\Simi\Simirewardpoints\Model\Rate::MONEY_TO_POINT, $groupId);
        if ($earningRate && $earningRate->getId()) {
            $spendingMoney =$this->currencyPrice->convertAndFormat($earningRate->getMoney(), false);
            $earningPoints = $this->pointHelper->format($earningRate->getPoints());
            $list['earning_label'] = __('How you can earn points');
            $list['earning_policy'] = __('Each %1 spent for your order will earn %2.', $spendingMoney, $earningPoints);
        }
    }
    
    public function _getSpendingPolicy(&$list, $groupId)
    {
        // Spending Point policy
        $redeemablePoints = $this->_getRedeemablePoints();
        $spendingRate = $this->objectManager
                ->create('Simi\Simirewardpoints\Model\Rate')
                ->getRate(
                    \Simi\Simirewardpoints\Model\Rate::POINT_TO_MONEY,
                    $groupId
                );
        if ($spendingRate && $spendingRate->getId()) {
            $spendingPoint = $this->pointHelper->format($spendingRate->getPoints());
            $getDiscount = $this->currencyPrice->convertAndFormat($spendingRate->getMoney(), false);
            $list['spending_label'] = __('How you can spend points');
            $list['spending_policy'] = __('Each %1 can be redeemed for %2.', $spendingPoint, $getDiscount);
            $list['spending_point'] = (int) $spendingRate->getPoints();
            $list['spending_discount'] = $getDiscount;
            $redeemablePoints = max($redeemablePoints, $spendingRate->getPoints());
            $baseAmount = $redeemablePoints * $spendingRate->getMoney() / $spendingRate->getPoints();
            $list['start_discount'] = $this->currencyPrice->convertAndFormat($baseAmount, false);
        }
        $list['spending_min'] = (int) $redeemablePoints;
        if ($redeemablePoints > (int) $this->customerHelper->getBalance()) {
            $invertPoint = $redeemablePoints - $this->customerHelper->getBalance();
            $list['invert_point'] = $this->pointHelper->format($invertPoint);
        }
    }
    
    public function _getOstherPolicy(&$list)
    {
        $policies = [];
        $block = $this->objectManager->create('Simi\Simirewardpoints\Block\Account\Dashboard\Policy');
        if ($_expireDays = $block->getTransactionExpireDays()) {
            $policies[] = __(
                'A transaction will expire after %1 since its creating date.',
                $_expireDays . ' ' . ($_expireDays == 1 ? __('day') : __('days'))
            );
        }
        if ($_holdingDays = $block->getHoldingDays()) {
            $policies[] = __(
                'A transaction will be withheld for %1 since creation.',
                $_holdingDays . ' ' . ($_holdingDays == 1 ? __('day') : __('days'))
            );
        }
        if ($_maxBalance = $block->getMaxPointBalance()) {
            $policies[] = __('Maximum of your balance') . ': ' . $this->pointHelper->format($_maxBalance) . '.';
        }
        if ($_redeemablePoints = $block->getRedeemablePoints()) {
            $policies[] = __('Reach %1 to start using your balance for your purchase.',
                    $this->pointHelper->format($_redeemablePoints));
        }
        if ($_maxPerOrder = $block->getMaxPerOrder()) {
            $policies[] = __('Maximum %1 are allowed to spend for an order.',
                    $this->pointHelper->format($_maxPerOrder));
        }
        $list['policies'] = $policies;
    }
    
    public function getMenuBalance()
    {
        $pointAmount = $this->customerHelper->getBalance();
        if ($pointAmount > 0) {
            $rate = $this->objectManager->create('Simi\Simirewardpoints\Model\Rate')
                    ->getRate(\Simi\Simirewardpoints\Model\Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                return $this->currencyPrice->convertAndFormat($baseAmount, false);
            }
        }
        return $this->customerHelper->getBalanceFormated();
    }
    
    public function _getRedeemablePoints()
    {
        $points = (int) $this->configHelper
                ->getConfig(\Simi\Simirewardpoints\Helper\Customer::XML_PATH_REDEEMABLE_POINTS);
        return max(0, $points);
    }
    
    /*
     * Customer transaction
     */
    
    public function getHistory()
    {
        $customerSession = $this->objectManager->get('Magento\Customer\Model\Session');
        $collection = $this->objectManager->create('Simi\Simirewardpoints\Model\Transaction')
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerSession->getCustomer()->getId());
        $collection->getSelect()->order('created_time DESC');
        return $collection;
    }
    
    /*
     * Change customer reward settings
     */
    
    public function saveSettings($param)
    {
        $data = (array) $param['contents'];
        $customerSession = $this->objectManager->get('Magento\Customer\Model\Session');
        $customerId = $customerSession->getId();
        
        $rewardAccount = $this->objectManager->create('Simi\Simirewardpoints\Model\Customer')
                ->load($customerId, 'customer_id');
        if (!$rewardAccount->getId()) {
            return;
        }
        $rewardAccount->setIsNotification((boolean) $data['is_notification'])
            ->setExpireNotification((boolean) $data['expire_notification']);
        $rewardAccount->save();
    }
    
    /*
     * Customer Use Point
     */
    
    public function spendPoints($param){
        $data =  $param['contents_array'];
        //$checkoutForm = $this->objectManager->get('\Simi\Simirewardpoints\Block\Checkout\Form');
        $checkoutCart = $this->objectManager->get('\Magento\Checkout\Model\Cart');
        $checkoutSession = $this->objectManager->get('Magento\Checkout\Model\Session');
       
        if ($data['usepoint']) {
            $checkoutSession->setData('use_point', true);
            $checkoutSession->setSimiRewardSalesRules(array(
                'rule_id' => $data['ruleid'],
                'use_point' => $data['usepoint'],
            ));
        } else {
            $checkoutSession->unsetData('use_point');
        }
        
        if($checkoutCart->getQuote()->getItemsCount()){
            $checkoutCart->save();
        }
        $checkoutSession->getQuote()->collectTotals()->save();
    }
}
