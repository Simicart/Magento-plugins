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

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Simipayfort_Model_Observer
     */
    public function addPayment($observer) {
        $object = $observer->getObject();
        $object->addMethod('simipayfort', 3);
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        //$store = $quote ? $quote->getStoreId() : null;
        
        if ($result->isAvailable && ($method->getCode() == 'simipayfort')) {
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' 
                    && Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress' 
                    && Mage::app()->getRequest()->getControllerModule() != 'Simi_Simiconnector') {
                $result->isAvailable = false;
            }
        }
    }

    public function afterPlaceOrder($observer) {
        $object = $observer->getObject();
        $data = $object->getCacheData();
        if (isset($data['payment_method']) && $data['payment_method'] == "simipayfort") {
            $data['params'] = Mage::helper("simipayfort")->getIframeUrl($data['invoice_number']);
        }
        $object->setCacheData($data, "simi_connector");
    }

    public function changePayment($observer) {
        $object = $observer->getObject();
        $data = $object->getCacheData();
        $check = false;
        $i = -1;
        foreach ($data as $item) {

            $i ++;
            if (isset($item['payment_method']) && $item['payment_method'] == "SIMIPAYFORT") {
                $check = true;
                break;
            }
        }

        if ($check) {
            $data[$i]['url_action'] = "simipayfort/api/update_payment";
            $data[$i]['url_back'] = Mage::helper("simipayfort")->getUrlCallBack();
            $data[$i]['is_sandbox'] = Mage::getStoreConfig("payment/simipayfort/is_sandbox");
        }
        $object->setCacheData($data, "simi_connector");
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
        }
        $orderObject->order_placed_info = $data;
    }
}
