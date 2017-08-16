<?php

class Simi_Simidailydeal_Block_Adminhtml_Dailydeal_Edit_Tab_Generalform extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
	{

	}

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);

		if (Mage::getSingleton('adminhtml/session')->getDailydealData()) {
			$data = Mage::getSingleton('adminhtml/session')->getDailydealData();
			Mage::getSingleton('adminhtml/session')->setDailydealData(null);
		} elseif (Mage::registry('simidailydeal_data'))
			$data = Mage::registry('simidailydeal_data')->getData();

		$fieldset = $form->addFieldset('dailydeal_form', array('legend' => Mage::helper('simidailydeal')->__('Deal information')));

		$fieldset->addField('title', 'text', array(
			'label'    => Mage::helper('simidailydeal')->__('Title'),
			'class'    => 'required-entry',
			'required' => true,
			'name'     => 'title',
		));

		$form->setValues($data);

		return parent::_prepareForm();
	}
}