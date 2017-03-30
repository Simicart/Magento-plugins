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
class Simi_Simicheckoutcom_Model_Simiobserver {
    /*
     * simiconnector 4.0 
     */

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('simicheckoutcom', 3);
        return;
    }

    public function connectorConfigGetPluginsReturnCheckoutcom($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'checkoutcomapis') {
            $observerObjectData['module'] = 'simicheckoutcom';
        }
        $observerObject->setData($observerObjectData);
    }

    public function simiconnectorAfterPlaceOrder($observer) {
        $orderObject = $observer->getObject();
        $data = $orderObject->order_placed_info;
        if (isset($data['payment_method']) && $data['payment_method'] == "simicheckoutcom") {
            $data['redirect_url'] = Mage::getUrl('simicheckoutcom/index/startCheckoutcom').'?order_id='.$data['invoice_number'];
            $data['success_url'] = Mage::getStoreConfig("payment/simicheckoutcom/success_url");
        }
        $orderObject->order_placed_info = $data;
    }

}
