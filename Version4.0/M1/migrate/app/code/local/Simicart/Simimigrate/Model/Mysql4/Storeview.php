<?php

class Simicart_Simimigrate_Model_Mysql4_Storeview extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('simimigrate/storeview', 'entity_id');
    }

}
