<?php

class Simicart_Simimigrate_Model_Api_Apps extends Simicart_Simimigrate_Model_Api_Abstract {
    
    protected $_DEFAULT_ORDER = 'app_id';
    
    public function setBuilderQuery() {
        $data = $this->getData();
        if (!$data['resourceid']) {
            $this->builderQuery = Mage::getModel('simimigrate/app')->getCollection();
        } else {
            $this->builderQuery = Mage::getModel('simimigrate/app')->load($data['resourceid']);
        }
    }
}
