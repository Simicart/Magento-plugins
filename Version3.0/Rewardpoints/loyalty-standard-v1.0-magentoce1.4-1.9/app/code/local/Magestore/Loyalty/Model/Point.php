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
class Magestore_Loyalty_Model_Point extends Simi_Connector_Model_Checkout
{
	
	public function getRewardInfo($data)
	{
		$list = array();
		// Collect Info - Customer Points (if logged in)
		$session  = Mage::getSingleton('customer/session');
		// $customer = $session->getCustomer();
		$groupId  = $session->isLoggedIn() ? $session->getCustomerGroupId() : Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
		$helper   = Mage::helper('rewardpoints/point');
		
		if ($session->isLoggedIn()) {
			$list['loyalty_point'] = (int)Mage::helper('rewardpoints/customer')->getBalance();
			$list['loyalty_balance'] = Mage::helper('rewardpoints/customer')->getBalanceFormated();
			$list['loyalty_redeem'] = Mage::helper('loyalty')->getMenuBalance();
			$holdingBalance = Mage::helper('rewardpoints/customer')->getAccount()->getHoldingBalance();
			if ($holdingBalance > 0) {
				$list['loyalty_hold'] = $helper->format($holdingBalance);
			}
			$list['loyalty_image'] = $helper->getImage();
			// Notification Settings
			$list['is_notification'] = (int)Mage::helper('rewardpoints/customer')->getAccount()->getData('is_notification');
			$list['expire_notification'] = (int)Mage::helper('rewardpoints/customer')->getAccount()->getData('expire_notification');
		}
		
		if (Mage::helper('loyalty')->cardConfig('enable')) {
			$list['loyalty_card'] = 1;
			$media = Mage::getBaseUrl('media') . 'loyalty/';
			$passbookLogo = Mage::helper('loyalty')->cardConfig('logo');
			if (!$passbookLogo) {
				$passbookLogo = 'default/logo@2x.png';
			}
			$list['passbook_logo'] = $media . $passbookLogo;
			$list['passbook_text'] = Mage::helper('loyalty')->cardConfig('logo_text');
			$list['passbook_background'] = Mage::helper('loyalty')->cardConfig('background');
			$list['passbook_foreground'] = Mage::helper('loyalty')->cardConfig('foreground');
			$niceID = str_pad((string)$session->getCustomerId(), 12, '0', STR_PAD_LEFT);
			$list['passbook_barcode'] = $niceID;
			$niceID  = substr($niceID, 0, 4) . ' ' . substr($niceID, 4, 4) . ' ' . substr($niceID, 8);
			$list['passbook_alt'] = $niceID;
		}
		
		// Earning Point policy
		$earningRate = Mage::getModel('rewardpoints/rate')->getRate(Magestore_RewardPoints_Model_Rate::MONEY_TO_POINT, $groupId);
		if ($earningRate && $earningRate->getId()) {
			$spendingMoney = Mage::app()->getStore()->convertPrice($earningRate->getMoney(), true, false);
			$earningPoints = $helper->format($earningRate->getPoints());
			$list['earning_label']  = $helper->__('How you can earn points');
			$list['earning_policy'] = $helper->__('Each %s spent for your order will earn %s.', $spendingMoney, $earningPoints);
		}
		
		// Spending Point policy
        $block = Mage::getBlockSingleton('rewardpoints/account_dashboard_policy');
		$redeemablePoints = $block->getRedeemablePoints();
		$spendingRate = Mage::getModel('rewardpoints/rate')->getRate(Magestore_RewardPoints_Model_Rate::POINT_TO_MONEY, $groupId);
		if ($spendingRate && $spendingRate->getId()) {
			$spendingPoint = $helper->format($spendingRate->getPoints());
			$getDiscount   = Mage::app()->getStore()->convertPrice($spendingRate->getMoney(), true, false);
			$list['spending_label']  = $helper->__('How you can spend points');
			$list['spending_policy'] = $helper->__('Each %s can be redeemed for %s.', $spendingPoint, $getDiscount);
			$list['spending_point'] = (int)$spendingRate->getPoints();
			$list['spending_discount'] = $getDiscount;
			$redeemablePoints = max($redeemablePoints, $spendingRate->getPoints());
			$baseAmount = $redeemablePoints * $spendingRate->getMoney() / $spendingRate->getPoints();
			$list['start_discount'] = Mage::app()->getStore()->convertPrice($baseAmount, true, false);
		}
		$list['spending_min'] = (int)$redeemablePoints;
		if ($redeemablePoints > (int)Mage::helper('rewardpoints/customer')->getBalance()) {
            $invertPoint = $redeemablePoints - Mage::helper('rewardpoints/customer')->getBalance();
            $list['invert_point'] = $helper->format($invertPoint);
		}
		
		// Other Policy Infomation
		$policies = array();
		if ($_expireDays = $block->getTransactionExpireDays()) {
			$policies[] = $helper->__('A transaction will expire after %s since its creating date.',
			    $_expireDays . ' ' . ($_expireDays == 1 ? $helper->__('day') : $helper->__('days'))
			);
		}
		if ($_holdingDays = $block->getHoldingDays()) {
			$policies[] = $helper->__('A transaction will be withheld for %s since creation.',
			    $_holdingDays . ' ' . ($_holdingDays == 1 ? $helper->__('day') : $helper->__('days'))
			);
		}
		if ($_maxBalance = $block->getMaxPointBalance()) {
			$policies[] = $helper->__('Maximum of your balance') . ': ' . $helper->format($_maxBalance) . '.';
		}
	    if ($_redeemablePoints = $block->getRedeemablePoints()) {
            $policies[] = $helper->__('Reach %s to start using your balance for your purchase.',
                $helper->format($_redeemablePoints)
            );
        }
		if ($_maxPerOrder = $block->getMaxPerOrder()) {
			$policies[] = $helper->__('Maximum %s are allowed to spend for an order.',
			    $helper->format($_maxPerOrder)
			);
		}
		$list['policies'] = $policies;
		
		// Return Data formatted
		$information = $this->statusSuccess();
        $information['data'] = $list;
        return $information;
	}
	
	public function getHistory($data)
	{
		$list = array();
        // Collect Info - Customer Points (if logged in)
        $session  = Mage::getSingleton('customer/session');
        
		$collection = Mage::getResourceModel('rewardpoints/transaction_collection')
		    ->addFieldToFilter('customer_id', $session->getCustomerId());
		$collection->getSelect()->order('created_time DESC');
		
		$limit = $data->limit ? $data->limit : null;
        $offset = $data->offset ? $data->offset : null;
        $collection->getSelect()->limit($limit, $offset);
        
        $actions  = Mage::helper('rewardpoints/action')->getActionsHash();
        $statuses = array(
            Magestore_RewardPoints_Model_Transaction::STATUS_PENDING    => 'pending',
            Magestore_RewardPoints_Model_Transaction::STATUS_ON_HOLD    => 'onhold',
            Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED  => 'completed',
            Magestore_RewardPoints_Model_Transaction::STATUS_CANCELED   => 'canceled',
            Magestore_RewardPoints_Model_Transaction::STATUS_EXPIRED    => 'expired'
        );
        $helper   = Mage::helper('rewardpoints/point');
        foreach ($collection as $transaction) {
        	$title = $transaction->getTitle();
        	if ($title == '') {
        		if (isset($actions[$transaction->getData('action')])) {
        			$title = $actions[$transaction->getData('action')];
        		} else {
        			$title = $transaction->getData('action');
        		}
        	}
        	$list[] = array(
        	    'title'        => $title,
        	    'point_amount' => (int)$transaction->getData('point_amount'),
        	    'point_label'  => $helper->format($transaction->getData('point_amount')),
        	    'created_time' => $transaction->getData('created_time'),
        	    'expiration_date'  => $transaction->getData('expiration_date') ? $transaction->getData('expiration_date') : '',
        	    'status'       => $statuses[$transaction->getData('status')]
        	);
        }
        
        // Return Data formatted
        $information = $this->statusSuccess();
        $information['message'] = array($collection->getSize());
        $information['data'] = $list;
        return $information;
	}
	
	public function spendPoints($data)
	{
		$list = array();
		Mage::app()->getRequest()->setControllerModule('Simi_Connector');
		// Checkout session: spend points
		$session = Mage::getSingleton('checkout/session');
		if ($data->usepoint) {
			$session->setData('use_point', true);
	        $session->setRewardSalesRules(array(
	            'rule_id'   => $data->ruleid,
	            'use_point' => $data->usepoint,
	        ));
		} else {
			$session->unsetData('use_point');
		}
        // Return Total Information
        $quote = $session->getQuote();
        $quote->collectTotals()->save();
        
        // Total checkout
        $total = $quote->getTotals();
        $grandTotal = $total['grand_total']->getValue();
        $subTotal = $total['subtotal']->getValue();
        $discount = 0;
        if (isset($total['discount']) && $total['discount']) {
            $discount = abs($total['discount']->getValue());
        }
        if (isset($total['tax']) && $total['tax']->getValue()) {
            $tax = $total['tax']->getValue();
        } else {
            $tax = 0;
        }
        if ($quote->getCouponCode()) {
        	$coupon = $quote->getCouponCode();
        } else {
        	$coupon = '';
        }
        $total_data = array(
            'sub_total' => $subTotal,
            'grand_total' => $grandTotal,
            'discount' => $discount,
            'tax' => $tax,
            'coupon_code' => $coupon,
        );
        $fee_v2 = array();
        Mage::helper('connector/checkout')->setTotal($total, $fee_v2);
        $total_data['v2'] = $fee_v2;
        $list['fee'] = $this->changeData($total_data, 'connector_checkout_get_order_config_total', array('object' => $this));
        
        // Payment
        $totalPay = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
        $payment = Mage::getModel('connector/checkout_payment');
        Mage::dispatchEvent('simi_add_payment_method', array('object' => $payment));
        $paymentMethods = $payment->getMethods($quote, $totalPay);
        $list_payment = array();
        foreach ($paymentMethods as $method) {
            $list_payment[] = $payment->getDetailsPayment($method);
        }
		$list['payment_method_list'] = $this->changeData($list_payment, 'simicart_change_payment_detail', array('object' => $this));
        
		Mage::app()->getRequest()->setControllerModule('Magestore_Loyalty');
		// Return Data formatted
        $information = $this->statusSuccess();
        $information['data'] = array($list);
        return $information;
	}
	
	public function saveSettings($data)
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerId     = Mage::getSingleton('customer/session')->getCustomerId();
		    $rewardAccount  = Mage::getModel('rewardpoints/customer')->load($customerId, 'customer_id');
            if (!$rewardAccount->getId()) {
                $rewardAccount->setCustomerId($customerId)
                    ->setData('point_balance', 0)
                    ->setData('holding_balance', 0)
                    ->setData('spent_balance', 0);
            }
            $rewardAccount->setIsNotification((boolean)$data->is_notification)
                ->setExpireNotification((boolean)$data->expire_notification);
            try {
            	$rewardAccount->save();
            } catch (Exception $e) {
            	return $this->statusError(array($e->getMessage()));
            }
		} else {
			return $this->statusError(array(Mage::helper('loyalty')->__('Your session has been expired. Please relogin and try again.')));
		}
		// Return Data formatted
		$information = $this->statusSuccess();
        $information['data'] = array('success' => 1);
        return $information;
	}
}
