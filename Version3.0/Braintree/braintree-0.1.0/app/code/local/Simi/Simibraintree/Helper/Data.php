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
 * Simibraintree Helper
 * 
 * @category    
 * @package     Simibraintree
 * @author      Developer
 */
class Simi_Simibraintree_Helper_Data extends Mage_Core_Helper_Abstract {
    
    /**
     * Generates md5 hash to be used as customer id
     * 
     * @param string $customerId
     * @param string $email
     * @return string
     */
    public function generateCustomerId($customerId, $email){
        return md5($customerId . '-' . $email);
    }

    /**
     * Braintree Configuration
     * 
     * @param string $customerId
     * @param string $email
     * @return string
     */
    public function braintreeConfiguration(){
        require_once("lib/Simibraintree/Braintree.php");
        if ($this->getEnviroment()) {            
            Braintree_Configuration::environment('sandbox');
        } else {
            Braintree_Configuration::environment('production');
        }
        Braintree_Configuration::merchantId($this->getMerchantId());
        Braintree_Configuration::publicKey($this->getPublicKey());
        Braintree_Configuration::privateKey($this->getPrivateKey());
    }
    
    /**
     * Generates token for further use
     * 
     * @return string | boolean
     */
    public function getBraintreeToken(){        
        $this->braintreeConfiguration();
        $customerExists = false;
        $customerSession = Mage::getSingleton('customer/session');
        $magentoCustomerId = false;
        $magentoCustomerEmail = false;
        $storeId = null;
        if ($customerSession->isLoggedIn()) {
            $magentoCustomerId = $customerSession->getCustomerId();
            $magentoCustomerEmail = $customerSession->getCustomer()->getEmail();
        }
        if ($magentoCustomerId && $magentoCustomerEmail) {
            $customerId = $this->generateCustomerId($magentoCustomerId, $magentoCustomerEmail);
            try {
                $customerExists = Braintree_Customer::find($customerId);
            } catch (Exception $e) {
                $customerExists = false;
            }
        }        
        $params = array("merchantAccountId" => $this->getMerchantId());
        if ($customerExists) {
            $params['customerId'] = $customerId;
        }
        try {
            $token = Braintree_ClientToken::generate($params);
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
        return $token;
    }

    /**
     * create transaction Authorized
     *
     * @return
     */
    public function createTransaction($data)
    {
        $this->braintreeConfiguration();
        $orderId = $data->order_id;
        $amount = $data->amount;
        $nonce = $data->nonce;
        $status = $this->getPaymentType() == 'sale' ? true : false;         
        $result = Braintree_Transaction::sale(array(
            'amount' => $amount,
            'orderId' => $orderId,
            'paymentMethodNonce' => $nonce,
            'options' => array(
                'submitForSettlement' => $status
            ),
        ));     
        return $result;             
    }

    /**
     * get payment methods were enabled
     * 
     * @return array
     */
    public function getPaymentList(){
        $paymentList = $this->getConfigBraintree('payment_list');
        $paymentList = explode(',', $paymentList);
        return $paymentList;
    }


    /**
     * get payment environment
     * 
     * @return boolean
     */
    public function getEnviroment(){
        return $this->getConfigBraintree('is_sandbox');
    }

    /**
     * get merchant Id
     * 
     * @return boolean
     */
    public function getMerchantId(){
        return $this->getConfigBraintree('merchant_id');
    }

    /**
     * get public key
     * 
     * @return boolean
     */
    public function getPublicKey(){
        return $this->getConfigBraintree('public_key');
    }

    /**
     * get private key
     * 
     * @return boolean
     */
    public function getPrivateKey(){
        return $this->getConfigBraintree('private_key');
    }

    /**
     * get payment type
     * 
     * @return boolean
     */
    public function getPaymentType(){
        return $this->getConfigBraintree('type');
    }

    /**
     * get apple merchant
     * 
     * @return boolean
     */
    public function getAppleMerchant(){
        return $this->getConfigBraintree('apple_merchant');
    }

    /**
     * get gootle merchant
     * 
     * @return boolean
     */
    public function getGoogleMerchant(){
        return $this->getConfigBraintree('google_merchant');
    }

    
    /**
     * get braintree configuration
     * 
     * @return boolean
     */
    public function getConfigBraintree($value) {
        return (string) Mage::getStoreConfig('payment/simibraintree/' . $value, Mage::app()->getStore()->getId());
    }

}