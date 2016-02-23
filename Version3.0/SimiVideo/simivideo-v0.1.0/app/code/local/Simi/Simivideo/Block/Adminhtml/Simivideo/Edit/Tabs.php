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
 * Simi Edit Tabs Block
 * 
 * @category 	
 * @package 	Simivideo
 * @author  	Developer
 */
class Simi_Simivideo_Block_Adminhtml_Simivideo_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('simivideo_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('simivideo')->__('Video Information'));
	}
	
	/**
	 * prepare before render block to html
	 *
	 * @return Magestore_Madapter_Block_Adminhtml_Madapter_Edit_Tabs
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('simivideo')->__('Video Information'),
			'title'	 => Mage::helper('simivideo')->__('Video Information'),
			'content'	 => $this->getLayout()->createBlock('simivideo/adminhtml_simivideo_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}