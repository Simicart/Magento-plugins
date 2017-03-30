<?php

class Simi_Simitracking_Model_User extends Mage_Core_Model_Abstract {

    protected $_eventPrefix = 'simitracking_user';
    protected $_eventObject = 'simitracking_user';

    public function _construct() {
        parent::_construct();
        $this->_init('simitracking/user');
    }

}
