<?php

class Simi_Simipayu_Model_Observer
{
    public function addPayment($observer) 
    {               
        $object = $observer->getObject();   
        $object->addMethod('simipayu', 3);    
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];    
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($method->getCode() == 'simipayu') {    
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' 
                && Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress') {
                $result->isAvailable = false;
            }
        } 
    }
    
    public function afterPlaceOrder($observer){
        $object = $observer->getObject();
        $data = $object->getCacheData();        
        if(isset($data['payment_method']) && $data['payment_method'] == "simipayu"){
            $data['params'] = Mage::helper("simipayu")->getFormFields($data['invoice_number']);
        }               
        // Zend_debug::dump($data);die();
        $object->setCacheData($data, "simi_connector");
    }
}