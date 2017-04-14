<?php

/**
 * Created by PhpStorm.
 * User: Scott
 * Date: 5/19/2016
 * Time: 4:47 PM
 */
class Simi_Simimigrate_Model_Api_Categories extends Simi_Simimigrate_Model_Api_Abstract {
    public function setBuilderQuery() {
        $data = $this->getData();
        if (!$data['resourceid']) {
            $this->builderQuery = Mage::getModel('simimigrate/category')->load($data['resourceid']);
        } else {
            $this->builderQuery = Mage::getModel('simimigrate/category')->getColletion();
        }
    }
}
