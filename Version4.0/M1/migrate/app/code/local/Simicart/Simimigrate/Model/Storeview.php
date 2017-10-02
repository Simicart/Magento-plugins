<?php

class Simicart_Simimigrate_Model_Storeview extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('simimigrate/storeview');
    }

}
