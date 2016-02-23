<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simiaffiliatescoupon
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simiaffiliatescoupon Observer Model
 * 
 * @category    
 * @package     Simiaffiliatescoupon
 * @author      Developer
 */
class Simi_Simiaffiliatescoupon_Model_Observer extends Simi_Connector_Model_Checkout_Cart
{
    /**
     * process controller_action_predispatch_set_coupon event
     *
     * @return Simi_Simiaffiliatescoupon_Model_Observer
     */
    public function couponPostAction($observer) {
        if (!Mage::getStoreConfig('simiaffiliatescoupon/general/enable'))
            return $this;

        if (!Mage::getStoreConfig('affiliateplus/coupon/enable'))
            return $this;

        $action = $observer->getEvent()->getControllerAction();
        $value = $action->getRequest()->getParam('data');
        $action->praseJsonToData($value);
        $data = $action->getData();
        $code = $data->coupon_code;
        $return = array();
        $information = '';
        $session = Mage::getSingleton('checkout/session');
        if (!$code){
            $session->unsetData('affiliate_coupon_code');
            $session->unsetData('affiliate_coupon_data');
            $this->clearAffiliateCookie();
            return $this;
        }
            
        $account = Mage::getModel('affiliatepluscoupon/coupon')->getAccountByCoupon($code);
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        if (!$account->getId()) {
             if (!Mage::getStoreConfig('affiliateplus/coupon/parallel')
                    && $session->getData('affiliate_coupon_code')) {
                $session->unsetData('affiliate_coupon_code');
                $session->unsetData('affiliate_coupon_data');
                $this->clearAffiliateCookie();
            }
            return $this;
        } elseif ($account->getCustomerId() == $customerId) {
            $session->unsetData('affiliate_coupon_code');
            $session->unsetData('affiliate_coupon_data');
            $this->clearAffiliateCookie();
            return $this;
        }
        
        $session->setData('affiliate_coupon_code', $account->getCouponCode());
        $session->setData('affiliate_coupon_data', array(
            'account_id' => $account->getId(),
            'program_id' => $account->getCouponPid(),
        ));
        $this->clearAffiliateCookie();

        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        if (!Mage::getStoreConfig('affiliateplus/coupon/parallel'))
            $quote->setCouponCode('');
        if ($account->getCouponPid()) {
            $program = Mage::getModel('affiliateplusprogram/program')->setStoreId(Mage::app()->getStore()->getId())->load($account->getCouponPid());
            if ($program->isAvailable()) {
                $accountProgramCollection = Mage::getResourceModel('affiliateplusprogram/account_collection')
                        ->addFieldToFilter('program_id', $account->getCouponPid())
                        ->addFieldToFilter('account_id', $account->getId())
                ;
                if ($accountProgramCollection->getSize())
                    $quote->collectTotals()->save();
            }
        }
        if ($account->getCouponPid() == 0) {
            $quote->collectTotals()->save();    
        }
        $available = false;
  
        foreach ($quote->getAddressesCollection() as $address)
            if (!$address->isDeleted() && $address->getAffiliateplusDiscount()) {
                $available = true;
                break;
            }
        if ($available) {
            // $session->addSuccess(Mage::helper('simiaffiliatescoupon')->__('Coupon code "%s" was applied.', $code));
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

            $total_data = array(
                'sub_total' => $subTotal,
                'grand_total' => $grandTotal,
                'discount' => $discount,
                'affiliates_discount' => $this->getAffiliatesDiscount(),
                'tax' => $tax,
                'coupon_code' => $code,
            );
            $fee_v2 = array();
            Mage::helper('connector/checkout')->setTotal($total, $fee_v2);
            $fee_v2['coupon_code'] = $code;
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
            
            Mage::app()->getRequest()->setControllerModule('Simi_Simiaffiliatescoupon');
            // Return Data formatted
            $information = $this->statusSuccess();
            $information['message'] = array(Mage::helper('connector')->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($code)));           
            $information['data'] = array($list);
            $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            $action->_printDataJson($information);
        } else {
            $session->unsetData('affiliate_coupon_code');
            $session->unsetData('affiliate_coupon_data');
            $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            return $this;
        }    
    }

    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    public function clearAffiliateCookie() {
        $cookie = Mage::getSingleton('core/cookie');
        for ($index = intval($cookie->get('affiliateplus_map_index')); $index > 0; $index--)
            $cookie->delete("affiliateplus_account_code_$index");
        $cookie->delete('affiliateplus_map_index');
        return $this;
    }

    public function connectorConfigGetPluginsReturn($observer) 
    {
        if (Mage::helper('simiaffiliatescoupon')->getConfig("enable") == 0) {
            $observerObject = $observer->getObject();
            $observerData = $observer->getObject()->getData();
            $contactPluginId = NULL;
            $plugins = array();
            foreach ($observerData['data'] as $key => $plugin) {
                if ($plugin['sku'] == 'simi_simiaffiliatescoupon') continue;
                $plugins[] = $plugin;
            }
            $observerData['data'] = $plugins;
            $observerObject->setData($observerData);
        }
    }

    /**
     * Get Affiliates coupon discount Configuration for Customer Checkout
     */
    public function checkoutOrderConfigAffiliatesCoupon($observer)
    {
        if (!Mage::getStoreConfig('simiaffiliatescoupon/general/enable'))
            return $this;
        if (!Mage::getStoreConfig('affiliateplus/coupon/enable'))
            return $this;
        $model = $observer['object'];     
        $session = Mage::getSingleton('checkout/session');
        $affiliatesCoupon = '';
        $affiliatesDiscount = '';
        if($session->getData('affiliate_coupon_code'))
            $affiliatesCoupon = $session->getData('affiliate_coupon_code');
        $affiliatesDiscount = $this->getAffiliatesDiscount();
        if($affiliatesDiscount && $affiliatesCoupon){
            $model->setCouponCode($affiliatesCoupon);
            $model->setAffiliatesDiscount($affiliatesDiscount);
        }
    }

    /**
     * Get Affiliates coupon discount Configuration for Customer Order Details
     */
    public function checkoutOrderDetailAffiliatesCoupon($observer)
    {
        if (!Mage::getStoreConfig('simiaffiliatescoupon/general/enable'))
            return $this;
        if (!Mage::getStoreConfig('affiliateplus/coupon/enable'))
            return $this;
        $model = $observer['object']; 
        $order = Mage::getModel('sales/order')->loadByIncrementId($model['order_id']);
        if(!$order || !$order->getAffiliateplusDiscount()){
            return $this;
        }
        $affiliatesCoupon = '';
        $affiliatesDiscount = '';
        if($order->getAffiliateplusCoupon())
            $affiliatesCoupon = $order->getAffiliateplusCoupon();
        if($order->getAffiliateplusDiscount())
            $affiliatesDiscount = $order->getAffiliateplusDiscount();
        if($affiliatesDiscount && $affiliatesCoupon){
            $model->setAffiliatesDiscount(-$affiliatesDiscount);
            // $model['total_v2']['affiliates_discount'] = -$affiliatesDiscount;
            $newTotal = $model['total_v2'];
            $newTotal['affiliates_discount'] = -$affiliatesDiscount;
            $model->setTotalV2($newTotal);
            if(!$model->getOrderGiftCode()){
                $model->setOrderGiftCode($affiliatesCoupon);
            }
        }
    }

    /**
     * Get Affiliates coupon discount Configuration for Customer Order Details
     */
    public function getCartAffiliatesCoupon($observer)
    {
        if (!Mage::getStoreConfig('simiaffiliatescoupon/general/enable'))
            return $this;
        if (!Mage::getStoreConfig('affiliateplus/coupon/enable'))
            return $this;
        $model = $observer['object'];     
        $session = Mage::getSingleton('checkout/session');
        $affiliatesCoupon = '';
        if($session->getData('affiliate_coupon_code'))
            $affiliatesCoupon = $session->getData('affiliate_coupon_code');
        $affiliatesDiscount = $this->getAffiliatesDiscount();
        if($affiliatesDiscount && $affiliatesCoupon){
            $model->setCouponCode($affiliatesCoupon);
            $model->setAffiliatesDiscount($affiliatesDiscount);
        }
    }

    public function getAffiliatesDiscount()
    {
        $helper = Mage::helper('simiaffiliatescoupon');
        return $helper->getAffiliatesDiscount();
    }
    
}