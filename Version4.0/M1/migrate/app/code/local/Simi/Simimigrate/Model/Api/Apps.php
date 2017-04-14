<?php

class Simi_Simimigrate_Model_Api_Apps extends Simi_Simimigrate_Model_Api_Abstract {
    public function setBuilderQuery() {
        $data = $this->getData();
        if (!$data['resourceid']) {
            $this->builderQuery = Mage::getModel('simimigrate/app')->getColletion();
            //zend_debug::dump(get_class(Mage::getModel('simimigrate/app')->getColetion()));die;
        } else {
            $this->builderQuery = Mage::getModel('simimigrate/app')->load($data['resourceid']);
        }
    }
}
