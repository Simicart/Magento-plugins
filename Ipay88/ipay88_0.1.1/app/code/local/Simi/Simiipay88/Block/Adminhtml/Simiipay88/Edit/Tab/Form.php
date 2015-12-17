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
 * Simiipay88 Edit Form Content Tab Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Simiipay88
 * @author  	Magestore Developer
 */
class Simi_Simiipay88_Block_Adminhtml_Simiipay88_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * prepare tab form's information
	 *
	 * @return Simi_Simiipay88_Block_Adminhtml_Simiipay88_Edit_Tab_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getSimiipay88Data()){
			$data = Mage::getSingleton('adminhtml/session')->getSimiipay88Data();
			Mage::getSingleton('adminhtml/session')->setSimiipay88Data(null);
		}elseif(Mage::registry('simiipay88_data'))
			$data = Mage::registry('simiipay88_data')->getData();
		
		$fieldset = $form->addFieldset('simiipay88_form', array('legend'=>Mage::helper('simiipay88')->__('Item information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('simiipay88')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('simiipay88')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('simiipay88')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('simiipay88/status')->getOptionHash(),
		));

		$fieldset->addField('content', 'editor', array(
			'name'		=> 'content',
			'label'		=> Mage::helper('simiipay88')->__('Content'),
			'title'		=> Mage::helper('simiipay88')->__('Content'),
			'style'		=> 'width:700px; height:500px;',
			'wysiwyg'	=> false,
			'required'	=> true,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}