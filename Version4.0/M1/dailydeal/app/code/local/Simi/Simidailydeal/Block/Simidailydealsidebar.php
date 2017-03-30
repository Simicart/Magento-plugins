<?php
class Simi_Simidailydeal_Block_Simidailydealsidebar extends Mage_Core_Block_Template
{
	public function _construct() {
        $this->setTemplate('simidailydeal/sidebar.phtml');
		return parent::_construct();
	}
    public function getSidebarProductCollection(){
		if(!Mage::registry('is_random_simidailydeal'))
		Mage::helper('simidailydeal')->updateSimidailydealStatus();
        $products=Mage::getModel('simidailydeal/simidailydeal')->getSidebarProductCollection();
        return $products;
    }
    public function getSimidailydealByProduct($productId){
        $simidailydeal=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydealByProduct($productId);
        return $simidailydeal;
    }
}