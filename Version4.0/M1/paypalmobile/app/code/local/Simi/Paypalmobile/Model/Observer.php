<?php

/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category
 * @package     Paypalmobile
 * @copyright   Copyright (c) 2012
 * @license
 */

/**
 * Paypalmobile Model
 *
 * @category
 * @package     Paypalmobile
 * @author      Developer
 */
class Simi_Paypalmobile_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Paypalmobile_Model_Observer
     */
    public function addPayment($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('paypal_mobile', 2);
        return;
    }

    public function addPaymentV2($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('paypal_mobile', 2);
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($result->isAvailable && ($method->getCode() == 'paypal_mobile')) {
            if (Mage::app()->getRequest()->getControllerModule() == 'Simi_Connector' || Mage::app()->getRequest()->getControllerModule() == 'Simi_Simiconnector') {

                $result->isAvailable = true;
            } else {
                $result->isAvailable = false;
            }
        }
    }

    public function changeApi($observer) {
        $object = $observer->getObject();
        $data = $object->getData();
        if (($data['resource'] == "paypalmobile") || ($data['resource'] == "paypalmobiles")) {
            $data['module'] = "paypalmobile";
            $object->setData($data);
        }
    }

}
