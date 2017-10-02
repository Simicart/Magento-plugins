<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simipayfort
 * @copyright   Copyright (c) 2017 
 * @license     
 */

/**
 * Simipayfort Model
 * 
 * @category    
 * @package     Simipayfort
 * @author      Developer
 */
class Simi_Simipayfort_Model_Observer {


    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        if ($result->isAvailable && ($method->getCode() == 'simipayfort')) {
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress' 
                    && Mage::app()->getRequest()->getControllerModule() != 'Simi_Simiconnector') {
                $result->isAvailable = false;
            }
        }
    }

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('simipayfort', 3);
        return;
    }

    public function connectorConfigGetPluginsReturnPayfort($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'payfortapis') {
            $observerObjectData['module'] = 'simipayfort';
        }
        $observerObject->setData($observerObjectData);
    }

    public function simiconnectorAfterPlaceOrder($observer) {
        $orderObject = $observer->getObject();
        $data = $orderObject->order_placed_info;
        if (isset($data['payment_method']) && $data['payment_method'] == "simipayfort") {
            $data['redirect_url'] = Mage::getUrl('simipayfort/index/startPayfort').'?order_id='.$data['invoice_number'];
            $data['success_url'] = Mage::getStoreConfig("payment/simipayfort/success_url");
            $data['is_sandbox'] = Mage::getStoreConfig("payment/simipayfort/is_sandbox");
        }
        $orderObject->order_placed_info = $data;
    }
}
