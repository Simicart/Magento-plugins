<?php

class Simi_Simitracking_Model_Permission extends Mage_Core_Model_Abstract {

    protected $_eventPrefix = 'simitracking_permission';
    protected $_eventObject = 'simitracking_permission';

    public function _construct() {
        parent::_construct();
        $this->_init('simitracking/permission');
    }

}
