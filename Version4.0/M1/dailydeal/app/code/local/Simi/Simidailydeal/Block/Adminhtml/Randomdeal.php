<?php

class Simi_Simidailydeal_Block_Adminhtml_Randomdeal extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_randomdeal';
		$this->_blockGroup = 'simidailydeal';
		$this->_headerText = Mage::helper('simidailydeal')->__('Generator Manager');
		$this->_addButtonLabel = Mage::helper('simidailydeal')->__('Add Generator');
		parent::__construct();
	}
}