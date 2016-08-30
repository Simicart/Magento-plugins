<?php

class Simi_Livechatzopim_Block_Adminhtml_Livechatzopim_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getLivechatzopimData()){
			$data = Mage::getSingleton('adminhtml/session')->getLivechatzopimData();
			Mage::getSingleton('adminhtml/session')->setLivechatzopimData(null);
		}elseif(Mage::registry('livechatzopim_data'))
			$data = Mage::registry('livechatzopim_data')->getData();
		
		$fieldset = $form->addFieldset('livechatzopim_form', array('legend'=>Mage::helper('livechatzopim')->__('Item information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('livechatzopim')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('livechatzopim')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('livechatzopim')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('livechatzopim/status')->getOptionHash(),
		));

		$fieldset->addField('content', 'editor', array(
			'name'		=> 'content',
			'label'		=> Mage::helper('livechatzopim')->__('Content'),
			'title'		=> Mage::helper('livechatzopim')->__('Content'),
			'style'		=> 'width:700px; height:500px;',
			'wysiwyg'	=> false,
			'required'	=> true,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}