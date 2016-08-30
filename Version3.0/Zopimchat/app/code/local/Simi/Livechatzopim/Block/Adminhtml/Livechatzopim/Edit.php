<?php

class Simi_Livechatzopim_Block_Adminhtml_Livechatzopim_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'livechatzopim';
		$this->_controller = 'adminhtml_livechatzopim';
		
		$this->_updateButton('save', 'label', Mage::helper('livechatzopim')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('livechatzopim')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('livechatzopim_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'livechatzopim_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'livechatzopim_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText(){
		if(Mage::registry('livechatzopim_data') && Mage::registry('livechatzopim_data')->getId())
			return Mage::helper('livechatzopim')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('livechatzopim_data')->getTitle()));
		return Mage::helper('livechatzopim')->__('Add Item');
	}
}