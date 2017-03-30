<?php

class Simi_Simitracking_Model_Mysql4_Role extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simitracking/role', 'entity_id');
    }
}
