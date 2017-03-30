<?php

class Simi_Simidailydeal_Block_Adminhtml_Simidailydeal_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('simidailydeal_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('simidailydeal')->__('Deal Information'));
	}

	protected function _beforeToHtml(){
		$simidailydeal = $this->getSimidailydeal();
		if( ! $simidailydeal || ($simidailydeal && ($simidailydeal->getStatus()=='' )))
		{  
	  	$this->addTab('form_listproduct', array(
          'label'     => Mage::helper('simidailydeal')->__('Select Product'),
          'title'     => Mage::helper('simidailydeal')->__('Select Product'),
		  'class'     => 'ajax',
		  'url'   => $this->getUrl('*/*/listproduct',array('_current'=>true,'id'=>$this->getRequest()->getParam('id'))),
	  	));	
		}
        $this->addTab('form_section', array(
          'label'     => Mage::helper('simidailydeal')->__('General Information'),
          'title'     => Mage::helper('simidailydeal')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('simidailydeal/adminhtml_simidailydeal_edit_tab_form')->toHtml(),
	  	));
		if( ! $simidailydeal || ($simidailydeal && ($simidailydeal->getStatus() !='')))
		{
		$this->addTab('form_listorder', array(
          'label'     => Mage::helper('simidailydeal')->__('Sold Items'),
          'title'     => Mage::helper('simidailydeal')->__('Sold Items'),
          'content'   => $this->getLayout()->createBlock('simidailydeal/adminhtml_simidailydeal_edit_tab_listorder')->toHtml(),
	  	));
    	}
        return parent::_beforeToHtml();
    }
    public function getSimidailydeal()     
    { 
        if (!$this->hasData('simidailydeal_data')) {
                $this->setData('simidailydeal_data', Mage::registry('simidailydeal_data'));
        }
        return $this->getData('simidailydeal_data');   
    }
}