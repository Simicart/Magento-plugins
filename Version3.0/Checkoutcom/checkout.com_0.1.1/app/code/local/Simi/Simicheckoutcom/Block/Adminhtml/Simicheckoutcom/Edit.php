<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simicheckoutcom
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simicheckoutcom Edit Block
 * 
 * @category 	
 * @package 	Simicheckoutcom
 * @author  	Developer
 */
class Simi_Simicheckoutcom_Block_Adminhtml_Simicheckoutcom_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'simicheckoutcom';
		$this->_controller = 'adminhtml_simicheckoutcom';
		
		$this->_updateButton('save', 'label', Mage::helper('simicheckoutcom')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('simicheckoutcom')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('simicheckoutcom_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'simicheckoutcom_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'simicheckoutcom_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	
	/**
	 * get text to show in header when edit an item
	 *
	 * @return string
	 */
	public function getHeaderText(){
		if(Mage::registry('simicheckoutcom_data') && Mage::registry('simicheckoutcom_data')->getId())
			return Mage::helper('simicheckoutcom')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('simicheckoutcom_data')->getTitle()));
		return Mage::helper('simicheckoutcom')->__('Add Item');
	}
}