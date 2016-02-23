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
class Simi_Twout_Model_Observer 
{
	/**
	 * process controller_action_predispatch event
	 *
	 * @return Simi_Twout_Model_Observer
	 */
	public function addPayment($observer) 
	{		
        $object = $observer->getObject();	
        $object->addMethod('twout', 3);
		//$object->addMethod('paypal_mobile', 2);		
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];           
        if ($method->getCode() == 'twout' || $method->getCode() == 'paypal_mobile' ) {			
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' 
				&& Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress') {
                $result->isAvailable = false;
            }
        } 
    }
	
	public function afterPlaceOrder($observer){
		$object = $observer->getObject();
		$data = $object->getCacheData();		
		if(isset($data['payment_method']) && $data['payment_method'] == "twout"){
			$data['params'] = Mage::helper("twout")->getFormFields($data['invoice_number']);
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
			if(isset($item['payment_method']) && $item['payment_method'] == "TWOUT"){				
				$check = true;
				break;					
			}			
		}
		if($check){			
			$data[$i]['url_action'] = "twout/api/update_payment";		
			$data[$i]['url_back'] = Mage::getStoreConfig("payment/twout/url_back") == null ? "" : Mage::getStoreConfig("payment/twout/url_back");
			$data[$i]['is_sandbox'] = Mage::getStoreConfig("payment/twout/is_sandbox");
		}		
		$object->setCacheData($data, "simi_connector");
	}
}