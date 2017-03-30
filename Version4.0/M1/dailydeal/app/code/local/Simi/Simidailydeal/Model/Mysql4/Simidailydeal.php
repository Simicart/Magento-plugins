<?php

class Simi_Simidailydeal_Model_Mysql4_Simidailydeal extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simidailydeal/simidailydeal', 'id');
	}
}