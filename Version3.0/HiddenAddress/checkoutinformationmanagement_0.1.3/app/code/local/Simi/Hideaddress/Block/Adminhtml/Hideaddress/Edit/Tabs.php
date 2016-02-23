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
 * Hideaddress Edit Tabs Block
 * 
 * @category    
 * @package     Hideaddress
 * @author      Developer
 */
class Simi_Hideaddress_Block_Adminhtml_Hideaddress_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('hideaddress_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('hideaddress')->__('Item Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Simi_Hideaddress_Block_Adminhtml_Hideaddress_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('hideaddress')->__('Item Information'),
            'title'     => Mage::helper('hideaddress')->__('Item Information'),
            'content'   => $this->getLayout()
                                ->createBlock('hideaddress/adminhtml_hideaddress_edit_tab_form')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}