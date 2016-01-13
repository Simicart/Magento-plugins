<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_SimiAvenue
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiAvenue Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiAvenue
 * @author      Magestore Developer
 */
class Simi_SimiAvenue_Model_Observer
{
    public function addPayment($observer) 
	{				
        $object = $observer->getObject();	
        $object->addMethod('simiavenue', 3);	
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($method->getCode() == 'simiavenue') {			
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' 
				&& Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress') {
                $result->isAvailable = false;
            }
        } 
    }
	
	public function afterPlaceOrder($observer){
		$object = $observer->getObject();
		$data = $object->getCacheData();		
		if(isset($data['payment_method']) && $data['payment_method'] == "simiavenue"){
			$data['params'] = Mage::helper("simiavenue")->getFormFields($data['invoice_number']);
		}				
		$object->setCacheData($data, "simi_connector");
	}
}