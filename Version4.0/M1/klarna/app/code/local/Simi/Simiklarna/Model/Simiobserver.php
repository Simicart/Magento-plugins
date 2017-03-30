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
class Simi_Simiklarna_Model_Simiobserver {
    /*
     * simiconnector 4.0 
     */

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('simiklarna', 3);
        return;
    }

    public function connectorConfigGetPluginsReturnSimiklarna($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'simiklarnaapis') {
            $observerObjectData['module'] = 'simiklarna';
        }
        $observerObject->setData($observerObjectData);
    }

}
