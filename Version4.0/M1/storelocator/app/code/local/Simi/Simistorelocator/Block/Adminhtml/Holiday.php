<?php
class Simi_Simistorelocator_Block_Adminhtml_Holiday extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {   
    $this->_controller = 'adminhtml_holiday';
    $this->_blockGroup = 'simistorelocator';
    $this->_headerText = Mage::helper('simistorelocator')->__('Holiday Manager');
    $this->_addButtonLabel = Mage::helper('simistorelocator')->__('Add Holiday');
    parent::__construct();
  }
}