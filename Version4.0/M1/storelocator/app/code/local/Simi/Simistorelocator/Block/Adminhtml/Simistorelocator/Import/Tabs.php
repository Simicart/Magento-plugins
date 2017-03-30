<?php

class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('importstore_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('simistorelocator')->__('Import Store'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('simistorelocator')->__('Import Store'),
          'title'     => Mage::helper('simistorelocator')->__('Import Store'),
          'content'   => $this->getLayout()->createBlock('simistorelocator/adminhtml_simistorelocator_import_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}