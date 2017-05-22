<?php

class Simicart_Simimigrate_Model_Mysql4_Storeview_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('simimigrate/storeview');
    }

}
