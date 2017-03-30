<?php
/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category
 * @package     Paytabsmobile
 * @copyright   Copyright (c) 2012
 * @license
 */

/**
 * Paytabsmobile Model
 *
 * @category
 * @package     Paytabsmobile
 * @author      Developer
 */
class Simi_Paytabsmobile_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Paytabsmobile_Model_Observer
     */
    public function addPayment($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('paytabs_mobile', 2);
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($result->isAvailable && ($method->getCode() == 'paytabs_mobile')) {
            if (Mage::app()->getRequest()->getControllerModule() == 'Simi_Connector'
                || Mage::app()->getRequest()->getControllerModule() == 'Simi_Simiconnector') {
                $result->isAvailable = true;
            }else{
                $result->isAvailable = false;
            }
        }
    }

    public function changeApi($observer){
        $object = $observer->getObject();
        $data = $object->getData();
        if(($data['resource'] == "paytabsmobile") || ($data['resource'] == "paytabsmobiles")){
            $data['module'] = "paytabsmobile";
            $object->setData($data);
        }
    }
    
    public function addPaymentInfo($observer){
        $object = $observer->getObject();
        if (isset($object->detail) && isset($object->detail['payment_method']) && ($object->detail['payment_method'] == 'PAYTABS_MOBILE')) {
            $object->detail['tag_merchant_email'] = Mage::getStoreConfig('payment/paytabs_mobile/tag_merchant_email');
            $object->detail['secret_key'] = Mage::getStoreConfig('payment/paytabs_mobile/secret_key');
            $object->detail['is_sandbox'] = Mage::getStoreConfig('payment/paytabs_mobile/is_sandbox');
        }
    }
}