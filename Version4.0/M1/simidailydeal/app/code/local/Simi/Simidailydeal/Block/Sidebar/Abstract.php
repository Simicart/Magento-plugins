<?php

class Simi_Simidailydeal_Block_Sidebar_Abstract extends Mage_Core_Block_Template
{
	public function _construct()
	{
		$this->setTemplate('magegiant/simidailydeal/sidebar.phtml');

		return parent::_construct();
	}

	public function getDealByProduct($pid)
	{
		return Mage::getModel('simidailydeal/dailydeal')->getDealByProduct($pid);
	}
}