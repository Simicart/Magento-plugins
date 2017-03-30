<?php
class Simi_Simidailydeal_Block_Sidebar extends Mage_Core_Block_Template
{
	public function _construct() {
		return parent::_construct();
	}
    public function displayOnLeftSidebarBlock() {
		$block = $this->getParentBlock();
		
		if($block) {
            $temp = !($this->getlink()=='indexindexsimidailydealsimidailydeal');
			if($temp && Mage::helper('simidailydeal')->isDisplayOnSidebar() && Mage::helper('simidailydeal')->displayOnLeftRightSideBar() == 1)
            {
					
				$sidebarBlock = $this->getLayout()
							->createBlock('simidailydeal/simidailydealsidebar');
				$block->insert($sidebarBlock, '', false, 'simidailydeal-sidebar');
			}
		}
	}
    public function displayOnRightSidebarBlock() {
		$block = $this->getParentBlock();
		if($block) {
            $temp = !($this->getlink()=='indexindexsimidailydealsimidailydeal');
			if($temp && Mage::helper('simidailydeal')->isDisplayOnSidebar() && Mage::helper('simidailydeal')->displayOnLeftRightSideBar() == 2)
            {
				$sidebarBlock = $this->getLayout()
							->createBlock('simidailydeal/simidailydealsidebar');
				$block->insert($sidebarBlock, '', false, 'simidailydeal-sidebar');
			}
		}
	}
    public function getlink(){
        $link=Mage::app()->getRequest()->getControllerName().
        Mage::app()->getRequest()->getActionName().
        Mage::app()->getRequest()->getRouteName().
        Mage::app()->getRequest()->getModuleName();
        return $link;
    }
}