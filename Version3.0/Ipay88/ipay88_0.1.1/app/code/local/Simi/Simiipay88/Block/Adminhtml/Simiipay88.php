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
 * Simiipay88 Adminhtml Block
 * 
 * @category 	
 * @package 	Simiipay88
 * @author  	Developer
 */
class Simi_Simiipay88_Block_Adminhtml_Simiipay88 extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_simiipay88';
		$this->_blockGroup = 'simiipay88';
		$this->_headerText = Mage::helper('simiipay88')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('simiipay88')->__('Add Item');
		parent::__construct();
	}
}