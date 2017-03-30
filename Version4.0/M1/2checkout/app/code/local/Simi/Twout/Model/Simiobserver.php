<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Twout
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

/**
 * Twout Model
 * 
 * @category 	
 * @package 	Twout
 * @author  	Developer
 */
class Simi_Twout_Model_Simiobserver {
    /*
     * simiconnector 4.0 
     */

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('twout', 3);
        return;
    }

    public function connectorConfigGetPluginsReturnTwout($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'twoutapis') {
            $observerObjectData['module'] = 'twout';
        }
        $observerObject->setData($observerObjectData);
    }

    public function simiconnectorAfterPlaceOrder($observer) {
        $orderObject = $observer->getObject();
        $data = $orderObject->order_placed_info;
        if (isset($data['payment_method']) && $data['payment_method'] == "twout") {
            $data['params'] = Mage::helper("twout")->getFormFields($data['invoice_number']);
        }
        $orderObject->order_placed_info = $data;
    }

    public function changePayment($observer) {
        $payment = $observer->getObject();
        $data = $payment->detail;
        if (isset($data['payment_method']) && $data['payment_method'] == "TWOUT") {
            $data['url_action'] = "simiconnector/rest/v2/twoutapis/update_order";
            $data['url_back'] = Mage::getStoreConfig("payment/twout/url_back") == null ? "" : Mage::getStoreConfig("payment/twout/url_back");
            $data['is_sandbox'] = Mage::getStoreConfig("payment/twout/is_sandbox");
        }
        $payment->detail = $data;
    }

}
