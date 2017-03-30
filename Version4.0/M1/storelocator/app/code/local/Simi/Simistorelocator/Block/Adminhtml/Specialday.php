<?php
class Simi_Simistorelocator_Block_Adminhtml_Specialday extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_specialday';
    $this->_blockGroup = 'simistorelocator';
    $this->_headerText = Mage::helper('simistorelocator')->__('Special Day Manager');
    $this->_addButtonLabel = Mage::helper('simistorelocator')->__('Add Special Day');
    parent::__construct();
  }
}