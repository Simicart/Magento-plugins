<?php

class Simi_Livechatzopim_Model_Observer
{
    public function getStoreViewReturData($observer){
		if (Mage::helper('livechatzopim')->getDataConfig("enable") == 0) return;
	    	$observerObject = $observer->getObject();
	        $observerData = $observer->getObject()->getData();     
	        $configData = $observerData['data'][0];
	        $storeConfigData = $configData['store_config'];
			$storeConfigData['zopim_config']=array(
					'enable' => Mage::getStoreConfig('livechatzopim/general/enable'),
					'account_key' => Mage::getStoreConfig('livechatzopim/general/account_key'),
					'show_profile' => Mage::getStoreConfig('livechatzopim/general/show_profile'),
					'name' => Mage::getStoreConfig('livechatzopim/general/name'),
					'email' => Mage::getStoreConfig('livechatzopim/general/email'),
					'phone' => Mage::getStoreConfig('livechatzopim/general/phone'),
			);
	        $configData['store_config'] = $storeConfigData;       
	        $observerData['data'] = array($configData);
	        $observerObject->setData($observerData);
	}
}