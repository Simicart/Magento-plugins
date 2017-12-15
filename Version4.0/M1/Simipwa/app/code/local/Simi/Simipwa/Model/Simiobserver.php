<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 11/20/17
 * Time: 5:12 PM
 */
class Simi_Simipwa_Model_Simiobserver
{
    public function simiSimiconnectorModelServerInitialize($observer)
    {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'simipwas' || $observerObjectData['resource'] == 'sitemaps') {
            $observerObjectData['module'] = 'simipwa';
        }
        $observerObject->setData($observerObjectData);
    }

    public function simiPwaChangeStoreView($observer){
    	$observerObject = $observer->getObject();
    	$data = $observerObject->getData();
    	if(isset($data['params']) && isset($data['params']['pwa'])){
    		$obj = $observer['object'];
    		$info = $obj->storeviewInfo;
    		$siteMap = Mage::helper('simipwa')->getSiteMaps();
            if($siteMap && isset($siteMap['sitemaps']))
    		  $info['urls'] = $siteMap['sitemaps'];
    		$obj->storeviewInfo = $info;    		
    	}    	
    }
}