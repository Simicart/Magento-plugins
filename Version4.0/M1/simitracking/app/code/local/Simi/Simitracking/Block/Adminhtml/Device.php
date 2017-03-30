<?php
/**
 * 

 */
class Simi_Simitracking_Block_Adminhtml_Device extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_device';
		$this->_blockGroup = 'simitracking';
		$this->_headerText = Mage::helper('simiconnector')->__('Tracking Device Manager');
		parent::__construct();
		$this->removeButton('add');
	}
}