<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simivideo
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Simi Edit Form Content Tab Block
 * 
 * @category    
 * @package     Simivideo
 * @author      Developer
 */
class Simi_Simivideo_Block_Adminhtml_SimiVideo_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'simivideo';
        $this->_controller = 'adminhtml_simivideo';

        $this->_updateButton('save', 'label', Mage::helper('simivideo')->__('Save Video'));
        $this->_updateButton('delete', 'label', Mage::helper('simivideo')->__('Delete Video'));
        
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('madapter_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'madapter_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'madapter_content');
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
    public function getHeaderText() {
        if (Mage::registry('simivideo_data') && Mage::registry('simivideo_data')->getId())
            return Mage::helper('simivideo')->__("Edit Video '%s'", $this->htmlEscape(Mage::registry('simivideo_data')->getData('video_title')));
        return Mage::helper('simivideo')->__('Add Video');
    }

}