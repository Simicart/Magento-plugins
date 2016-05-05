<?php

class Magestore_Storelocator_Model_Observer
{
	/**
	 * process controller_action_predispatch event
	 *
	 * @return Magestore_Storelocator_Model_Observer
	 */
	public function controllerActionPredispatch($observer){
		$action = $observer->getEvent()->getControllerAction();
		return $this;
	}

	public function connectorConfigGetPluginsReturn($observer) 
    {
        if (!Mage::getStoreConfig('storelocator/general/enable', Mage::app()->getStore()->getId())) {
            $observerObject = $observer->getObject();
            $observerData = $observer->getObject()->getData();
            $contactPluginId = NULL;
            $plugins = array();
            foreach ($observerData['data'] as $key => $plugin) {
                if ($plugin['sku'] == 'magestore_storelocator') continue;
                $plugins[] = $plugin;
            }
            $observerData['data'] = $plugins;
            $observerObject->setData($observerData);
        }
    }
}