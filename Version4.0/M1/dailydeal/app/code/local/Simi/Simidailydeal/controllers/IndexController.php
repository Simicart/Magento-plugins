<?php

class Simi_Simidailydeal_IndexController extends Mage_Core_Controller_Front_Action
{
    protected function _initAction(){
		$this->loadLayout();
		$this->renderLayout();
		return $this;
	}
	public function indexAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
		if(!Mage::registry('is_random_simidailydeal'))
        Mage::helper('simidailydeal')->updateSimidailydealStatus();
		$this->loadLayout();
		$this->renderLayout();
	}
}