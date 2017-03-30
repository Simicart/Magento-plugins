<?php

class Simi_Simitracking_Model_Role extends Mage_Core_Model_Abstract {

    protected $_eventPrefix = 'simitracking_role';
    protected $_eventObject = 'simitracking_role';

    public function _construct() {
        parent::_construct();
        $this->_init('simitracking/role');
    }

}
