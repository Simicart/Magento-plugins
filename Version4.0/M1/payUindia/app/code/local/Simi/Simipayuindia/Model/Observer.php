<?php
class Simi_Simipayuindia_Model_Observer
{
    public function addPayment($observer) 
    {               
        $object = $observer->getObject();   
        $object->addMethod('simipayuindia', 3);    
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];    
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($result->isAvailable && ($method->getCode() == 'simipayuindia')) {    
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' 
                && Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress' 
                && Mage::app()->getRequest()->getControllerModule() != 'Simi_Simiconnector' ) {
                $result->isAvailable = false;
            }
        } 
    }
    
    public function afterPlaceOrder($observer){
        $object = $observer->getObject();
        $data = $object->getCacheData();        
        if(isset($data['payment_method']) && $data['payment_method'] == "simipayuindia"){
          //  $data['invoice_number'] = $data['invoice_number'];
            $data['url_action'] = Mage::helper("simipayuindia")->getUrlCheckout($data['invoice_number']);
        }               
        $object->setCacheData($data, "simi_connector");
    }
}