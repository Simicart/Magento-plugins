<?php

class Simi_Simidailydeal_Block_Adminhtml_Simidailydeal extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_simidailydeal';
		$this->_blockGroup = 'simidailydeal';
		$this->_headerText = Mage::helper('simidailydeal')->__('Deal Manager');
		$this->_addButtonLabel = Mage::helper('simidailydeal')->__('Add Deal');
		parent::__construct();
	}
}