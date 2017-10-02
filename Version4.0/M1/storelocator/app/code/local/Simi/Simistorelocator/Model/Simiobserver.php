<?php

class Simi_Simistorelocator_Model_Simiobserver {

    public function simiSimiconnectorModelServerInitializeSimistorelocator($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();        
        if ($observerObjectData['resource'] == 'storelocations') {
            $observerObjectData['module'] = 'simistorelocator';
        } 
        if ($observerObjectData['resource'] == 'storelocatortags') {
            $observerObjectData['module'] = 'simistorelocator';
        }
        $observerObject->setData($observerObjectData);
    }

    public function addFieldSystemConfig($observer){
        //check SimiWebsiteId
        if(!Mage::helper('simiconnector/cloud')->getWebsiteIdSimiUser()){
            $configs = $observer->getConfig();
            $sectionsNode = $configs->getNode('sections');
            $storeLocatorConfigs = $sectionsNode->simistorelocator;
            $generalFieldsConfig = $storeLocatorConfigs->groups->general->fields;

            $xmlData = '<enable translate="label">
                            <label>Enable</label>
                            <frontend_type>select</frontend_type>
                            <sort_order>2</sort_order>
                            <source_model>adminhtml/system_config_source_yesno</source_model>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                            <comment></comment>
                        </enable>';
            $enableField = new Mage_Core_Model_Config_Element($xmlData);
            $generalFieldsConfig->appendChild($enableField);
            $storeLocatorConfigs->groups->general = $generalFieldsConfig;
            $configs = $configs->setNode('sections',$storeLocatorConfigs);
            $observer->setConfig($configs);
        }
    }
}

