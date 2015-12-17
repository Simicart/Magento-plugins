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
 * @category 	Magestore
 * @package 	Magestore_Simifblogin
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Fblogin Observer Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Simifblogin
 * @author  	Magestore Developer
 */
class Simi_Simifblogin_Model_Observer
{
	/**
	 * process controller_action_predispatch event
	 *
	 * @return Simi_Simifblogin_Model_Observer
	 */
	public function addLink($observer){
		$object = $observer->getObject();
		$data = $object->getCacheData();
		$data["product_url"] = $observer->getProduct()->getProductUrl();
		$object->setCacheData($data, "simi_connector");		
	}

	public function connectorConfigGetPluginsReturn($observer) 
    {
        if ($this->getConfig("enable") == 0) {
            $observerObject = $observer->getObject();
            $observerData = $observer->getObject()->getData();
            $contactPluginId = NULL;
            $plugins = array();
            foreach ($observerData['data'] as $key => $plugin) {
                if ($plugin['sku'] == 'simi_simifblogin') continue;
                $plugins[] = $plugin;
            }
            $observerData['data'] = $plugins;
            $observerObject->setData($observerData);
        }
    }

     public function getConfig($value) {
        return Mage::getStoreConfig("simifblogin/general/" . $value);
    }
}