<?php

class Simi_Livechatzopim_Block_Adminhtml_Livechatzopim extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_livechatzopim';
		$this->_blockGroup = 'livechatzopim';
		$this->_headerText = Mage::helper('livechatzopim')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('livechatzopim')->__('Add Item');
		parent::__construct();
	}
}