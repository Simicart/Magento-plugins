<?php

class Simi_Simimigrate_Model_Api_Customers extends Simi_Simimigrate_Model_Api_Abstract {
    public function setBuilderQuery() {
        $data = $this->getData();
        if (!$data['resourceid']) {
            $this->builderQuery = Mage::helper('simimigrate')
                    ->addSimicartAppConfigId(Mage::getModel('simimigrate/customer')->getCollection());
        } else {
            $this->builderQuery = Mage::getModel('simimigrate/customer')->load($data['resourceid']);
        }
    }
}
