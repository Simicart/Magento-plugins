<?php

class Simi_Simistorelocator_Model_Mysql4_Simistorelocator_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected $_store_id = null;
    protected $_addedTable = array();

    public function _construct() {
        parent::_construct();
        $this->_init('simistorelocator/simistorelocator');
    }

}
