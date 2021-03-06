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
 * Simicheckoutcom Edit Tabs Block
 * 
 * @category 	
 * @package 	Simicheckoutcom
 * @author  	Developer
 */
class Simi_Simicheckoutcom_Block_Adminhtml_Simicheckoutcom_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('simicheckoutcom_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('simicheckoutcom')->__('Item Information'));
	}
	
	/**
	 * prepare before render block to html
	 *
	 * @return Simi_Simicheckoutcom_Block_Adminhtml_Simicheckoutcom_Edit_Tabs
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('simicheckoutcom')->__('Item Information'),
			'title'	 => Mage::helper('simicheckoutcom')->__('Item Information'),
			'content'	 => $this->getLayout()->createBlock('simicheckoutcom/adminhtml_simicheckoutcom_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}