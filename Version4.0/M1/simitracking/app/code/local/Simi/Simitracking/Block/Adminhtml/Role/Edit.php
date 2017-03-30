<?php


class Simi_Simitracking_Block_Adminhtml_Role_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'simitracking';
        $this->_controller = 'adminhtml_role';
        $this->_updateButton('delete', 'label', Mage::helper('simitracking')->__('Delete'));
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        $this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('notice_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'notice_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'notice_content');
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
        if (Mage::registry('role_data') && Mage::registry('role_data')->getId())
            return Mage::helper('simitracking')->__("Edit Role '%s'", $this->htmlEscape(Mage::registry('role_data')->getRoleTitle()));
        return Mage::helper('simitracking')->__('Add Role');
    }

}
