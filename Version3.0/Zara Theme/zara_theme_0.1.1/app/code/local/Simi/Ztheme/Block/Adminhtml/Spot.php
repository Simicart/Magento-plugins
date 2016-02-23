<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Ztheme
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Ztheme Block
 * 
 * @category    
 * @package     Ztheme
 * @author      Developer
 */
class Simi_Ztheme_Block_Adminhtml_Spot extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_spot'; //Path folder of Grid
        $this->_blockGroup = 'ztheme';
        $this->_headerText = Mage::helper('ztheme')->__('Spot Product Manager');
        $this->_addButtonLabel = Mage::helper('ztheme')->__('Add Item');
        
       
        parent::__construct();
        $this->_removeButton("add");
    }
}