<?php

class Simi_Simiklarna_Model_Observer {

    public function addPayment($observer) {
        $object = $observer->getObject();
        $object->addMethod('simiklarna', 3);
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];  
        if ($result->isAvailable && ($method->getCode() == 'simiklarna')) {
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' 
                    && Mage::app()->getRequest()->getControllerModule() != 'Simi_Simiconnector' 
                    && Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress') {
                $result->isAvailable = false;
            }
        }
    }

    // public function afterPlaceOrder($observer){
    // 	$object = $observer->getObject();
    // 	$data = $object->getCacheData();		
    // 	if(isset($data['payment_method']) && $data['payment_method'] == "simiavenue"){
    // 		$data['params'] = Mage::helper("simiavenue")->getFormFields($data['invoice_number']);
    // 	}				
    // 	$object->setCacheData($data, "simi_connector");
    // }
}
