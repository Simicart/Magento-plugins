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
 * @package 	Magestore_Simicheckoutcom
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simicheckoutcom Edit Form Content Tab Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Simicheckoutcom
 * @author  	Magestore Developer
 */
class Simi_Simicheckoutcom_Block_Adminhtml_Simicheckoutcom_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * prepare tab form's information
	 *
	 * @return Simi_Simicheckoutcom_Block_Adminhtml_Simicheckoutcom_Edit_Tab_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getSimicheckoutcomData()){
			$data = Mage::getSingleton('adminhtml/session')->getSimicheckoutcomData();
			Mage::getSingleton('adminhtml/session')->setSimicheckoutcomData(null);
		}elseif(Mage::registry('simicheckoutcom_data'))
			$data = Mage::registry('simicheckoutcom_data')->getData();
		
		$fieldset = $form->addFieldset('simicheckoutcom_form', array('legend'=>Mage::helper('simicheckoutcom')->__('Item information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('simicheckoutcom')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('simicheckoutcom')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('simicheckoutcom')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('simicheckoutcom/status')->getOptionHash(),
		));

		$fieldset->addField('content', 'editor', array(
			'name'		=> 'content',
			'label'		=> Mage::helper('simicheckoutcom')->__('Content'),
			'title'		=> Mage::helper('simicheckoutcom')->__('Content'),
			'style'		=> 'width:700px; height:500px;',
			'wysiwyg'	=> false,
			'required'	=> true,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}