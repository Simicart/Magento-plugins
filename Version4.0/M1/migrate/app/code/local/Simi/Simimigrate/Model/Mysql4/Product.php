<?php

class Simi_Simimigrate_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('simimigrate/product', 'entity_id');
    }

}
