<?php

/**

 */
class Simi_Simimigrate_Block_Adminhtml_Storeview_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'simimigrate';
        $this->_controller = 'adminhtml_storeview';

        $this->removeButton('reset');
        $this->removeButton('save');
        $this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('storeview_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'storeview_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'storeview_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
    }

    /**
     * get text to show in header when edit an storeview
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('storeview_data') && Mage::registry('storeview_data')->getId())
            return Mage::helper('simimigrate')->__("View Storeview '%s'", $this->htmlEscape(Mage::registry('storeview_data')->getId()));
        return Mage::helper('simimigrate')->__('Add Storeview');
    }

}
