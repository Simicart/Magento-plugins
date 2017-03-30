<?php

class Simi_Simistorelocator_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {            
        $this->_init('simistorelocator/tag', 'tag_id');
    }
}