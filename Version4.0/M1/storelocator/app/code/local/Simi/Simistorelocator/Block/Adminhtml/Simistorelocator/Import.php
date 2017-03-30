<?php

class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'simistorelocator';
		$this->_controller = 'adminhtml_simistorelocator';
                $this->_mode       = 'import';
		$this->_updateButton('save', 'label', Mage::helper('simistorelocator')->__('Import'));
//                $this->_removeButton('delete');
//                $this->_removeButton('saveandcontinue');
//                $this->_removeButton('reset');
                //$editBlock->_updateButton('back', 'onclick', 'backEdit()');

	}
	
	/**
	 * get text to show in header when edit an item
	 *
	 * @return string
	 */
	public function getHeaderText(){
		return Mage::helper('simistorelocator')->__('Import Store');
	}
}
?>
