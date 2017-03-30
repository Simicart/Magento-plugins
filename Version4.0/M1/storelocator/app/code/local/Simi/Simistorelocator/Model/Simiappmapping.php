<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Loyalty
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Loyalty Model
 * 
 * @category    
 * @package     Loyalty
 * @author      Developer
 */
class Simi_Simirewardpoints_Model_Simiappmapping extends Mage_Core_Model_Abstract {

    public function getRewardInfo() {
        $list = array();
        // Collect Info - Customer Points (if logged in)
        $session = Mage::getSingleton('customer/session');
        // $customer = $session->getCustomer();
        $groupId = $session->isLoggedIn() ? $session->getCustomerGroupId() : Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
        $helper = Mage::helper('simirewardpoints/point');

        if ($session->isLoggedIn()) {
            $list['loyalty_point'] = (int) Mage::helper('simirewardpoints/customer')->getBalance();
            $list['loyalty_balance'] = Mage::helper('simirewardpoints/customer')->getBalanceFormated();
            $list['loyalty_redeem'] = $this->getMenuBalance();
            $holdingBalance = Mage::helper('simirewardpoints/customer')->getAccount()->getHoldingBalance();
            if ($holdingBalance > 0) {
                $list['loyalty_hold'] = $helper->format($holdingBalance);
            }
            $list['loyalty_image'] = $helper->getImage();
            // Notification Settings
            $list['is_notification'] = (int) Mage::helper('simirewardpoints/customer')->getAccount()->getData('is_notification');
            $list['expire_notification'] = (int) Mage::helper('simirewardpoints/customer')->getAccount()->getData('expire_notification');
        }

        // Earning Point policy
        $earningRate = Mage::getModel('simirewardpoints/rate')->getRate(Simi_Simirewardpoints_Model_Rate::MONEY_TO_POINT, $groupId);
        if ($earningRate && $earningRate->getId()) {
            $spendingMoney = Mage::app()->getStore()->convertPrice($earningRate->getMoney(), true, false);
            $earningPoints = $helper->format($earningRate->getPoints());
            $list['earning_label'] = $helper->__('How you can earn points');
            $list['earning_policy'] = $helper->__('Each %s spent for your order will earn %s.', $spendingMoney, $earningPoints);
        }

        // Spending Point policy
        $block = Mage::getBlockSingleton('simirewardpoints/account_dashboard_policy');
        $redeemablePoints = $block->getRedeemablePoints();
        $spendingRate = Mage::getModel('simirewardpoints/rate')->getRate(Simi_Simirewardpoints_Model_Rate::POINT_TO_MONEY, $groupId);
        if ($spendingRate && $spendingRate->getId()) {
            $spendingPoint = $helper->format($spendingRate->getPoints());
            $getDiscount = Mage::app()->getStore()->convertPrice($spendingRate->getMoney(), true, false);
            $list['spending_label'] = $helper->__('How you can spend points');
            $list['spending_policy'] = $helper->__('Each %s can be redeemed for %s.', $spendingPoint, $getDiscount);
            $list['spending_point'] = (int) $spendingRate->getPoints();
            $list['spending_discount'] = $getDiscount;
            $redeemablePoints = max($redeemablePoints, $spendingRate->getPoints());
            $baseAmount = $redeemablePoints * $spendingRate->getMoney() / $spendingRate->getPoints();
            $list['start_discount'] = Mage::app()->getStore()->convertPrice($baseAmount, true, false);
        }
        $list['spending_min'] = (int) $redeemablePoints;
        if ($redeemablePoints > (int) Mage::helper('simirewardpoints/customer')->getBalance()) {
            $invertPoint = $redeemablePoints - Mage::helper('simirewardpoints/customer')->getBalance();
            $list['invert_point'] = $helper->format($invertPoint);
        }

        // Other Policy Infomation
        $policies = array();
        if ($_expireDays = $block->getTransactionExpireDays()) {
            $policies[] = $helper->__('A transaction will expire after %s since its creating date.', $_expireDays . ' ' . ($_expireDays == 1 ? $helper->__('day') : $helper->__('days'))
            );
        }
        if ($_holdingDays = $block->getHoldingDays()) {
            $policies[] = $helper->__('A transaction will be withheld for %s since creation.', $_holdingDays . ' ' . ($_holdingDays == 1 ? $helper->__('day') : $helper->__('days'))
            );
        }
        if ($_maxBalance = $block->getMaxPointBalance()) {
            $policies[] = $helper->__('Maximum of your balance') . ': ' . $helper->format($_maxBalance) . '.';
        }
        if ($_redeemablePoints = $block->getRedeemablePoints()) {
            $policies[] = $helper->__('Reach %s to start using your balance for your purchase.', $helper->format($_redeemablePoints)
            );
        }
        if ($_maxPerOrder = $block->getMaxPerOrder()) {
            $policies[] = $helper->__('Maximum %s are allowed to spend for an order.', $helper->format($_maxPerOrder)
            );
        }
        $list['policies'] = $policies;
        return $list;
    }

    public function getHistory() {
        $session = Mage::getSingleton('customer/session');
        $collection = Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('customer_id', $session->getCustomerId());
        $collection->getSelect()->order('created_time DESC');
        return $collection;
    }

    public function spendPoints($data) {
        $data = (array) $data['contents'];
        $session = Mage::getSingleton('checkout/session');
        if ($data['usepoint']) {
            $session->setData('use_point', true);
            $session->setRewardSalesRules(array(
                'rule_id' => $data['ruleid'],
                'use_point' => $data['usepoint'],
            ));
        } else {
            $session->unsetData('use_point');
        }
    }

    public function saveSettings($data) {
        $data = (array) $data['contents'];
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $rewardAccount = Mage::getModel('simirewardpoints/customer')->load($customerId, 'customer_id');
        if (!$rewardAccount->getId()) {
            return;
        }
        $rewardAccount->setIsNotification((boolean) $data['is_notification'])
                ->setExpireNotification((boolean) $data['expire_notification']);
        $rewardAccount->save();
    }

    public function getMenuBalance() {
        $helper = Mage::helper('simirewardpoints/customer');
        $pointAmount = $helper->getBalance();
        if ($pointAmount > 0) {
            $rate = Mage::getModel('simirewardpoints/rate')->getRate(Simi_Simirewardpoints_Model_Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                return Mage::app()->getStore()->convertPrice($baseAmount, true, false);
            }
        }
        return $helper->getBalanceFormated();
    }

}
