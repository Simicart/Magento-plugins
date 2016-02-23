<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Productlabel
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Productlabel Block
 * 
 * @category    
 * @package     Productlabel
 * @author      Developer
 */
class Magestore_Productlabel_Block_Adminhtml_Productlabel extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_productlabel';
        $this->_blockGroup = 'productlabel';
        $this->_headerText = Mage::helper('productlabel')->__('Manage Product Labels');
        $this->_addButton('apply_rules', array(
            'label'     => Mage::helper('catalogrule')->__('Apply Labels'),
            'onclick'   => "location.href='".$this->getUrl('*/*/applyRules')."'",
            'class'     => '',
        ));

        $this->_addButtonLabel = Mage::helper('productlabel')->__('Add Product Label');
        parent::__construct();
    }
}