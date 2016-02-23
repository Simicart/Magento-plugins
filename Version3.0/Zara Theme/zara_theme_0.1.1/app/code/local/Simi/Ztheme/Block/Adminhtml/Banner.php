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
class Simi_Ztheme_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'ztheme';
        $this->_headerText = Mage::helper('ztheme')->__('Banner Manager');
        $this->_addButtonLabel = Mage::helper('ztheme')->__('Add Banner');
        parent::__construct();
    }
}