<?php

class Simi_Simistorelocator_Model_Mysql4_Storevalue extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simistorelocator/storevalue', 'value_id');
	}
        
        public function loadAttribute($storeLocatorId, $storeId, $attribute)
        {
            //get Attribute of stores with parameter $storeLocatorId, $storeId, $attribute
        }
}