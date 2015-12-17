<?php

class Simi_Simipayuindia_Model_Mysql4_Simipayuindia extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simipayuindia/simipayuindia', 'simipayuindia_id');
	}
}