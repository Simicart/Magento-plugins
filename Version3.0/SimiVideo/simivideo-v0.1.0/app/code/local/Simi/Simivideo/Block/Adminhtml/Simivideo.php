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
 * Simi Block
 * 
 * @category 	
 * @package 	Simivideo
 * @author  	Developer
 */
class Simi_Simivideo_Block_Adminhtml_Simivideo extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_simivideo';
        $this->_blockGroup = 'simivideo';
        $this->_headerText = Mage::helper('simivideo')->__('Videos');                
		parent::__construct();
    }
}