<?php

class Simi_Simistorelocator_Model_Mysql4_Specialday extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {           
        $this->_init('simistorelocator/specialday', 'simistorelocator_specialday_id');
    }
}