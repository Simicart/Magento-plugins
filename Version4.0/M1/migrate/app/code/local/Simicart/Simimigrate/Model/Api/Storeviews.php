<?php

class Simicart_Simimigrate_Model_Api_Storeviews extends Simicart_Simimigrate_Model_Api_Abstract {
    public function setBuilderQuery() {
        $data = $this->getData();
        if (!$data['resourceid']) {
            $this->builderQuery = Mage::helper('simimigrate')
                    ->addSimicartAppConfigId(Mage::getModel('simimigrate/storeview')->getCollection());
        } else {
            $this->builderQuery = Mage::getModel('simimigrate/storeview')->load($data['resourceid']);
        }
    }

    public function index() {
    	$result = parent::index();
    	$data = $this->getData();
        if (isset($data['params']['filter']['simicart_app_config_id'])){
        	$groups = Mage::helper('simimigrate')
                    ->addSimicartAppConfigId(Mage::getModel('simimigrate/store')->getCollection())
                    ->addFieldToFilter('simicart_app_config_id', $data['params']['filter']['simicart_app_config_id']);
	        $groupArray = array();
	        foreach ($groups as $key => $group) {
	        	$groupArray[$group->getGroupId()] = $group->getName();
	        }
	        foreach ($result['storeviews'] as $index => $storeview) {
        		if (isset($groupArray[$storeview['group_id']]))
	        		$result['storeviews'][$index]['group_name'] = $groupArray[$storeview['group_id']];
	        }
        }
        return $result;
    }
}
