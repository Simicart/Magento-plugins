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
 * Simicheckoutcom Adminhtml Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Simicheckoutcom
 * @author  	Magestore Developer
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