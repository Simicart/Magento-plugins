<?php

class Simi_Simitracking_Model_Mysql4_Device_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('simitracking/device');
    }
}
