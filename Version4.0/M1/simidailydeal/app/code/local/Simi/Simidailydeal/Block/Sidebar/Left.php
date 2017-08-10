<?php

class Simi_Simidailydeal_Block_Sidebar_Left extends Simi_Simidailydeal_Block_Sidebar_Abstract
{

	public function getSidebarDeals()
	{
		return Mage::getModel('simidailydeal/dailydeal')->getLeftSidebar();
	}


	public function getTitle()
	{
		return Mage::helper('simidailydeal')->getConfig()->getLeftTitle();
	}

	public function getType()
	{
		return 'left';
	}
}