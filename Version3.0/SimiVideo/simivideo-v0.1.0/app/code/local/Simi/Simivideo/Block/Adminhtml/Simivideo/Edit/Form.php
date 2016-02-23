<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simivideo
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

/**
 * Simi Edit Form Content Block
 * 
 * @category 	
 * @package 	Simivideo
 * @author  	Developer
 */
class Simi_Simivideo_Block_Adminhtml_Simivideo_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm(){
		$form = new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getUrl('*/*/save', array(
				'video_id'	=> $this->getRequest()->getParam('video_id'),                             
			)),
			'method'	=> 'post',
			'enctype'	=> 'multipart/form-data'
		));

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}