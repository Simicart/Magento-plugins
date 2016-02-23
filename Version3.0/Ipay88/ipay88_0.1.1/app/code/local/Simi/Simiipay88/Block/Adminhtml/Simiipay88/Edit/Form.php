<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simiipay88
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simiipay88 Edit Form Tab Block
 * 
 * @category 	
 * @package 	Simiipay88
 * @author  	Developer
 */
class Simi_Simiipay88_Block_Adminhtml_Simiipay88_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * prepare form's information for block
	 *
	 * @return Simi_Simiipay88_Block_Adminhtml_Simiipay88_Edit_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getUrl('*/*/save', array(
				'id'	=> $this->getRequest()->getParam('id'),
			)),
			'method'	=> 'post',
			'enctype'	=> 'multipart/form-data'
		));

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}