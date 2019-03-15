<?php

class Simi_Simibraintree_Model_Simiobserver {
    /*
     * simiconnector 4.0 
     */

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('simibraintree', 3);
        return;
    }

    public function connectorConfigGetPluginsReturnSimibraintree($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'braintreeapis') {
            $observerObjectData['module'] = 'simibraintree';
        }
        $observerObject->setData($observerObjectData);
    }

    public function changePayment($observer) {
        $payment = $observer->getObject();
        $data = $payment->detail;
        if (isset($data['payment_method']) && $data['payment_method'] == "SIMIBRAINTREE") {
            $helper = Mage::helper('simibraintree');
            $data['show_type'] = 3;
            $data['merchant_id'] = $helper->getMerchantId();
            $data['public_key'] = $helper->getPublicKey();
            $data['private_key'] = $helper->getPrivateKey();
            $data['token'] = $helper->getBraintreeToken();
            $data['is_sandbox'] = $helper->getEnviroment();
            $data['type'] = $helper->getPaymentType();
            $data['payment_list'] = $helper->getPaymentList();
            $data['apple_merchant'] = $helper->getAppleMerchant();
            $data['google_merchant'] = $helper->getGoogleMerchant();
        }
        $payment->detail = $data;
    }

}
