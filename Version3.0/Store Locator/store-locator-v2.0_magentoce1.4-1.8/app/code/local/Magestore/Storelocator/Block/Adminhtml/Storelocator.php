<?php

class Magestore_Storelocator_Block_Adminhtml_Storelocator extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_storelocator';
		$this->_blockGroup = 'storelocator';
		$this->_headerText = Mage::helper('storelocator')->__('Store Manager');
		$this->_addButton('import_store', array(
		'label'     => Mage::helper('storelocator')->__('Import Store'),
		'onclick'   => 'location.href=\''. $this->getUrl('*/adminhtml_import/importstore',array()) .'\'',
                'class'     => 'add',
		
	));
                $this->_addButtonLabel = Mage::helper('storelocator')->__('Add Store');
        parent::__construct();        
	}
}