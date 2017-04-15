<?php

class Simi_Simimigrate_Model_Mysql4_Store extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('simimigrate/store', 'entity_id');
    }

}
