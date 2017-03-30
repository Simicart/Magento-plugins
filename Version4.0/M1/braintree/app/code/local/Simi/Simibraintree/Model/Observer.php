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
 * Simibraintree Model
 * 
 * @category    
 * @package     Simibraintree
 * @author      Developer
 */
class Simi_Simibraintree_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Simibraintree_Model_Observer
     */
    public function addPayment($observer) {
        $object = $observer->getObject();
        $object->addMethod('simibraintree', 2);
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        $paymentList = $this->getBraintreePayments();
        if ($result->isAvailable && (in_array($method->getCode(), $paymentList))) {
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' 
                    && Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress'
                    && Mage::app()->getRequest()->getControllerModule() != 'Simi_Simiconnector') {
                $result->isAvailable = false;
            }
        }
    }

    public function changePayment($observer) {
        $object = $observer->getObject();
        $data = $object->getCacheData();
        $helper = Mage::helper('simibraintree');
        $check = false;
        $i = -1;
        foreach ($data as $item) {
            $i ++;
            if (isset($item['payment_method']) && $item['payment_method'] == "SIMIBRAINTREE") {
                $check = true;
                break;
            }
        }
        if ($check) {
            $data[$i]['show_type'] = 2;
            $data[$i]['merchant_id'] = $helper->getMerchantId();
            $data[$i]['public_key'] = $helper->getPublicKey();
            $data[$i]['private_key'] = $helper->getPrivateKey();
            $data[$i]['token'] = $helper->getBraintreeToken();
            $data[$i]['is_sandbox'] = $helper->getEnviroment();
            $data[$i]['type'] = $helper->getPaymentType();
            $data[$i]['payment_list'] = $helper->getPaymentList();
            $data[$i]['apple_merchant'] = $helper->getAppleMerchant();
            $data[$i]['google_merchant'] = $helper->getGoogleMerchant();
        }
        $object->setCacheData($data, "simi_connector");
    }

    /**
     * get braintree payment list from configuration
     *
     * @return array
     */
    public function getBraintreePayments() {
        $helper = Mage::helper('simibraintree');
        $paymentList = $helper->getConfigBraintree('payment_list');
        $paymentList = explode(',', $paymentList);
        $paymentList[] = 'simibraintree';
        return $paymentList;
    }

}
