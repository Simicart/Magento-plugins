<?php

class Simi_Livechatzopim_Block_Adminhtml_Livechatzopim_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('livechatzopim_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('livechatzopim')->__('Item Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('livechatzopim')->__('Item Information'),
			'title'	 => Mage::helper('livechatzopim')->__('Item Information'),
			'content'	 => $this->getLayout()->createBlock('livechatzopim/adminhtml_livechatzopim_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}