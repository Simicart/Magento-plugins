<?php

class Simi_Simistorelocator_Model_Mysql4_Simistorelocator extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simistorelocator/simistorelocator', 'simistorelocator_id');
	}
}