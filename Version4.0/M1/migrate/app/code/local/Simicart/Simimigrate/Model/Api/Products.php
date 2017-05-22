<?php

class Simicart_Simimigrate_Model_Api_Products extends Simicart_Simimigrate_Model_Api_Abstract {
    public function setBuilderQuery() {
        $data = $this->getData();
        if (!$data['resourceid']) {
            $this->builderQuery = Mage::helper('simimigrate')
                    ->addSimicartAppConfigId(Mage::getModel('simimigrate/product')->getCollection());
        } else {
            $this->builderQuery = Mage::getModel('simimigrate/product')->load($data['resourceid']);
        }
    }
}
