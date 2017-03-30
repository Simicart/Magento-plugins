<?php

class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_simistorelocator';
		$this->_blockGroup = 'simistorelocator';
		$this->_headerText = Mage::helper('simistorelocator')->__('Store Manager');
		$this->_addButton('import_store', array(
		'label'     => Mage::helper('simistorelocator')->__('Import Store'),
		'onclick'   => 'location.href=\''. $this->getUrl('*/simistorelocator_import/importstore',array()) .'\'',
                'class'     => 'add',
		
	));
                $this->_addButtonLabel = Mage::helper('simistorelocator')->__('Add Store');
        parent::__construct();        
	}
}