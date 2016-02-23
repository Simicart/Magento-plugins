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
		$object->addMethod('paypal_mobile', 2);		
		return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($method->getCode() == 'paypal_mobile') {
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector') {
                $result->isAvailable = false;
            }
        }
    }

}