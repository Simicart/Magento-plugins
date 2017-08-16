<?php

class Simi_Simidailydeal_Block_Adminhtml_Dailydeal_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();

		$this->_objectId   = 'id';
		$this->_blockGroup = 'simidailydeal';
		$this->_controller = 'adminhtml_dailydeal';

		$this->_updateButton('save', 'label', Mage::helper('simidailydeal')->__('Save Deal'));
		$this->_updateButton('delete', 'label', Mage::helper('simidailydeal')->__('Delete Deal'));

		$this->_addButton('saveandcontinue', array(
			'label'   => Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick' => 'saveAndContinueEdit()',
			'class'   => 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('dailydeal_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'dailydeal_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'dailydeal_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText()
	{
		if (Mage::registry('simidailydeal_data') AND Mage::registry('simidailydeal_data')->getId()) {
			return Mage::helper('simidailydeal')->__("Edit '%s'", Mage::registry('simidailydeal_data')->getProductName());

		} else {
			return Mage::helper('simidailydeal')->__('Add Deal');
		}
	}

	public function getDailydeal()
	{
		if (!$this->hasData('simidailydeal_data')) {
			$this->setData('simidailydeal_data', Mage::registry('simidailydeal_data'));
		}

		return $this->getData('simidailydeal_data');
	}
}