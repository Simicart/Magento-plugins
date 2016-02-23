<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simibarcode
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simibarcode Model
 * 
 * @category    
 * @package     Simibarcode
 * @author      Developer
 */
class Simi_Simibarcode_Model_Observer
{
	/**
	 * process connector_config_get_plugins_return event
	 *
	 * @return Simi_Simibarcode_Model_Observer
	 */
    public function connectorConfigGetPluginsReturn($observer) 
    {
        if (Mage::helper('simibarcode')->getBarcodeConfig("enable") == 0) {
            $observerObject = $observer->getObject();
            $observerData = $observer->getObject()->getData();
            $contactPluginId = NULL;
            $plugins = array();
            foreach ($observerData['data'] as $key => $plugin) {
                if ($plugin['sku'] == 'simi_simibarcode') continue;
                $plugins[] = $plugin;
            }
            $observerData['data'] = $plugins;
            $observerObject->setData($observerData);
        }
    }
}