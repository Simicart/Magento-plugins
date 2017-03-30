<?php

class Simi_Simitracking_Block_Adminhtml_User_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'simitracking';
        $this->_controller = 'adminhtml_user';
        $this->_updateButton('delete', 'label', Mage::helper('simitracking')->__('Delete'));
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        $this->_formScripts[] = "
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
        if (Mage::registry('user_data') && Mage::registry('user_data')->getId())
            return Mage::helper('simitracking')->__("Edit User '%s'", $this->htmlEscape(Mage::registry('user_data')->getUserTitle()));
        return Mage::helper('simitracking')->__('Add User');
    }

}
