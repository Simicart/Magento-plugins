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
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Productlabel Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
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