<?php

class Simi_Simidailydeal_Block_Sidebar_Right extends Simi_Simidailydeal_Block_Sidebar_Abstract
{
	public function getSidebarDeals()
	{
		return Mage::getModel('simidailydeal/dailydeal')->getRightSidebar();
	}

	public function getTitle()
	{
		return Mage::helper('simidailydeal')->getConfig()->getRightTitle();
	}

	public function getType()
	{
		return 'right';
	}
}