<?php

class Simi_Simistorelocator_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Simistorelocator_Model_Observer
     */
    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    public function connectorConfigGetPluginsReturn($observer) {
        if (!Mage::getStoreConfig('simistorelocator/general/enable', Mage::app()->getStore()->getId())) {
            $observerObject = $observer->getObject();
            $observerData = $observer->getObject()->getData();
            $contactPluginId = NULL;
            $plugins = array();
            foreach ($observerData['data'] as $key => $plugin) {
                if ($plugin['sku'] == 'simi_simistorelocator')
                    continue;
                $plugins[] = $plugin;
            }
            $observerData['data'] = $plugins;
            $observerObject->setData($observerData);
        }
    }

}
