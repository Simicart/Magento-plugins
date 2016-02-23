<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simicheckoutcom
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simicheckoutcom Model
 * 
 * @category    
 * @package     Simicheckoutcom
 * @author      Developer
 */
class Simi_Simicheckoutcom_Model_Observer
{
	/**
	 * process controller_action_predispatch event
	 *
	 * @return Simi_Simicheckoutcom_Model_Observer
	 */
	public function addPayment($observer) 
	{		
        $object = $observer->getObject();	
        $object->addMethod('simicheckoutcom', 3);	
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($method->getCode() == 'simicheckoutcom') {			
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' 
				&& Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress') {
                $result->isAvailable = false;
            }
        } 
    }
	
	public function afterPlaceOrder($observer){
		$object = $observer->getObject();
		$data = $object->getCacheData();		
		if(isset($data['payment_method']) && $data['payment_method'] == "simicheckoutcom"){
			$data['params'] = Mage::helper("simicheckoutcom")->getIframeUrl($data['invoice_number']);
		}				
		$object->setCacheData($data, "simi_connector");
	}
	
	public function changePayment($observer){
		// echo "hai";
		$object = $observer->getObject();
		$data = $object->getCacheData();
		$check = false;
		$i = -1;
		foreach ($data as $item){
			
			$i ++;
			if(isset($item['payment_method']) && $item['payment_method'] == "SIMICHECKOUTCOM"){				
				$check = true;
				break;					
			}			
		}
		
		if($check){			
			$data[$i]['url_action'] = "simicheckoutcom/api/update_payment";		
			$data[$i]['url_back'] = Mage::helper("simicheckoutcom")->getUrlCallBack();					
			$data[$i]['is_sandbox'] = Mage::getStoreConfig("payment/simicheckoutcom/is_sandbox");
		}		
		$object->setCacheData($data, "simi_connector");
	}
}