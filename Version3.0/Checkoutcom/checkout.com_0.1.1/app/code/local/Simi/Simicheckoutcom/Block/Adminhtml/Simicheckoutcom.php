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
 * Simicheckoutcom Block
 * 
 * @category 	
 * @package 	Simicheckoutcom
 * @author  	Developer
 */
class Simi_Simicheckoutcom_Block_Adminhtml_Simicheckoutcom extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_simicheckoutcom';
		$this->_blockGroup = 'simicheckoutcom';
		$this->_headerText = Mage::helper('simicheckoutcom')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('simicheckoutcom')->__('Add Item');
		parent::__construct();
	}
}