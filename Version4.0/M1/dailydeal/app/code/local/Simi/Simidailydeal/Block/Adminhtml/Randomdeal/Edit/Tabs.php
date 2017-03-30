<?php

class Simi_Simidailydeal_Block_Adminhtml_Randomdeal_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('randomdeal_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('simidailydeal')->__('Patten Information'));
	}

	protected function _beforeToHtml(){
	$randomdeal = $this->getRandomdeal();

	if((($randomdeal->getStatus())!=4 )&&(($randomdeal->getStatus())!=3 ))
	{  
	  $this->addTab('form_listproduct', array(
          'label'     => Mage::helper('simidailydeal')->__('Select Products'),
          'title'     => Mage::helper('simidailydeal')->__('Select Products'),
         'content'   => $this->getLayout()->createBlock('simidailydeal/adminhtml_randomdeal_edit_tab_listproduct')->toHtml(),
    ));	
	}
        $this->addTab('form_section', array(
          'label'     => Mage::helper('simidailydeal')->__('General Information'),
          'title'     => Mage::helper('simidailydeal')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('simidailydeal/adminhtml_randomdeal_edit_tab_form')->toHtml(),
	  ));
    return parent::_beforeToHtml();
    }
  public function getRandomdeal()     
  { 
    if (!$this->hasData('randomdeal_data')) {
        $this->setData('randomdeal_data', Mage::registry('randomdeal_data'));
    }
    return $this->getData('randomdeal_data');   
  }
}