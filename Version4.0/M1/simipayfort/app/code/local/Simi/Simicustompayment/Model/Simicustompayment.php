<?php

class Simi_Simicustompayment_Model_Simicustompayment extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('simicustompayment/simicustompayment');
	}
}