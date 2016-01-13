<?php
class Simi_PayPalExpress_Model_Observer
{
	public function addConfig($observer){
		$object = $observer->getObject();
		$data = $object->getCacheData();
		$data["is_paypal_express"] = Mage::getStoreConfig('paypalexpress/general/product_detail');
		$object->setCacheData($data, "simi_connector");		
	}

	public function addCartConfig($observer){
		$object = $observer->getObject();
		$data = $object->getData();
		$data['other']["is_paypal_express"] = Mage::getStoreConfig('paypalexpress/general/cart');
		$object->setData($data);		
	}

	public function addPayment($observer) {
        $object = $observer->getObject();		
		$object->addMethod('paypal_express', 3);	
		return;
    }
}