<?php

class Simi_Simistorelocator_Model_Mysql4_Storevalue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('simistorelocator/storevalue');
	}
}