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
 * Hideaddress Edit Block
 * 
 * @category    
 * @package     Hideaddress
 * @author      Developer
 */
class Simi_Hideaddress_Block_Adminhtml_Hideaddress_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'hideaddress';
        $this->_controller = 'adminhtml_hideaddress';
        
        $this->_updateButton('save', 'label', Mage::helper('hideaddress')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('hideaddress')->__('Delete Item'));
        
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('hideaddress_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'hideaddress_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'hideaddress_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('hideaddress_data')
            && Mage::registry('hideaddress_data')->getId()
        ) {
            return Mage::helper('hideaddress')->__("Edit Item '%s'",
                                                $this->htmlEscape(Mage::registry('hideaddress_data')->getTitle())
            );
        }
        return Mage::helper('hideaddress')->__('Add Item');
    }
}