<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Simiipay88
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simiipay88 Edit Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Simiipay88
 * @author  	Magestore Developer
 */
class Simi_Simiipay88_Block_Adminhtml_Simiipay88_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'simiipay88';
		$this->_controller = 'adminhtml_simiipay88';
		
		$this->_updateButton('save', 'label', Mage::helper('simiipay88')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('simiipay88')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('simiipay88_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'simiipay88_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'simiipay88_content');
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
		if(Mage::registry('simiipay88_data') && Mage::registry('simiipay88_data')->getId())
			return Mage::helper('simiipay88')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('simiipay88_data')->getTitle()));
		return Mage::helper('simiipay88')->__('Add Item');
	}
}