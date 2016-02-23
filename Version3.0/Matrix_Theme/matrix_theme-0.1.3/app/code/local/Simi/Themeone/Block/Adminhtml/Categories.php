<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Themeone
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Themeone Block
 * 
 * @category    
 * @package     Themeone
 * @author      Developer
 */
class Simi_Themeone_Block_Adminhtml_Categories extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_categories'; //Path folder of Grid
        $this->_blockGroup = 'themeone';
        $this->_headerText = Mage::helper('themeone')->__('Categories Manager');
        $this->_addButtonLabel = Mage::helper('themeone')->__('Add Item');
        
       
        parent::__construct();
        $this->_removeButton("add");
    }
}