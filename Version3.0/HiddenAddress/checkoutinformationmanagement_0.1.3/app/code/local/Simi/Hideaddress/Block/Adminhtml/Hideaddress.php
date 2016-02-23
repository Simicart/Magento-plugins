<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Hideaddress
 * @copyright   Copyright (c) 2012 
 * @license   
 */

/**
 * Hideaddress Adminhtml Block
 * 
 * @category    
 * @package     Hideaddress
 * @author      Developer
 */
class Simi_Hideaddress_Block_Adminhtml_Hideaddress extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_hideaddress';
        $this->_blockGroup = 'hideaddress';
        $this->_headerText = Mage::helper('hideaddress')->__('Item Manager');
        $this->_addButtonLabel = Mage::helper('hideaddress')->__('Add Item');
        parent::__construct();
    }
}