<?php
/**
 *
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simibraintree
 * @copyright   Copyright (c) 2012 
 * @license    
 */

/**
 * Simibraintree Block
 * 
 * @category    
 * @package     Simibraintree
 * @author      Developer
 */
class Simi_Simibraintree_Block_Form extends Mage_Payment_Block_Info_Cc {

    public $supportedMethods =[];

    public function getStoreConfig($path){
        return Mage::getStoreConfig($path, Mage::app()->getStore()->getId());
    }

    public function getMethodsAvailable(){
        return explode(',', $this->getStoreConfig('payment/simibraintree/payment_list'));
    }


    public function getPaymentConfigJson($order){
        $this->supportedMethods = $this->getMethodsAvailable();
        $config =[];
        $config['authorization'] = Mage::helper('simibraintree')->getBraintreeToken();
        $config['container'] ='#dropin-container';
        $this->_getPaypalConfig($config,$order);
        $this->_getPaypalCreditConfig($config,$order);
        $this->_get3DSecureConfig($config,$order);
        $this->_getVenmoConfig($config,$order);
        $this->_getGoogleConfig($config,$order);
        $this->_getAppleConfig($config,$order);

        return $config;
    }

    public function isUse3DSecure(){
        return $this->getStoreConfig('payment/simibraintree/use_3d_secure')?true:false;
    }

    private function _getPaypalConfig(&$config,$order){
        if(in_array('braintree_paypal', $this->supportedMethods)){
            $config['paypal'] = [
                'flow'=> 'vault'
            ];
        }
    }

    private function _getPaypalCreditConfig(&$config,$order){
        if(in_array('braintree_creditcard', $this->supportedMethods)){
            $config['paypalCredit'] = [
                'flow'=> 'vault'
            ];
        }
    }

    private function _get3DSecureConfig(&$config,$order){
        $amount = $order->getData('grand_total_incl_tax') ? $order->getData('grand_total_incl_tax') : $order->getData('grand_total');
        if($this->isUse3DSecure()){
            $config['threeDSecure'] = [
                'amount'=> $amount
            ];
        }
    }

    private function _getVenmoConfig(&$config,$order){
        if(in_array('braintree_venmo', $this->supportedMethods)){
            $config['venmo'] = [
                'allowNewBrowserTab'=> false
            ];
        }
    }

    private function _getGoogleConfig(&$config,$order){
        $amount = $order->getData('grand_total_incl_tax') ? $order->getData('grand_total_incl_tax') : $order->getData('grand_total');
        $currencyCode   = $order->getData('order_currency_code');
        if(in_array('braintree_googlepay', $this->supportedMethods)){
            $config['googlePay'] = [
                'merchantId'=> $this->getStoreConfig('payment/simibraintree/google_account'),
                'transactionInfo'=>[
                    'totalPriceStatus'=>'FINAL',
                    'totalPrice'=> $amount,
                    'currencyCode'=> $currencyCode
                ],
                'cardRequirements'=>[
                    'billingAddressRequired' => true
                ]
            ];
        }
    }

    private function _getAppleConfig(&$config,$order){
        $amount = $order->getData('grand_total_incl_tax') ? $order->getData('grand_total_incl_tax') : $order->getData('grand_total');
        if(in_array('braintree_applepay', $this->supportedMethods)){
            $config['applePay'] = [
                'displayName'=>  Mage::app()->getStore()->getName(),
                'paymentRequest' => [
                    'total'=>[
                        'label' => Mage::app()->getStore()->getName(),
                        'amount' => $amount
                    ],
                    'requiredBillingContactFields' => ["postalAddress"]
                ]
            ];
        }
    }

}