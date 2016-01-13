<?php

class Simi_Simiklarna_Model_Mysql4_Simiklarna extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simiklarna/simiklarna', 'simiklarna_id');
	}
}