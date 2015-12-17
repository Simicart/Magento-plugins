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
 * @package 	Magestore_Simibarcode
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simibarcode Observer Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Simibarcode
 * @author  	Magestore Developer
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