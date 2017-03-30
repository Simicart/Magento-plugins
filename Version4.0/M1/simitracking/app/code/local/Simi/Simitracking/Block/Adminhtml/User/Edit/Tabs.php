<?php

class Simi_Simitracking_Block_Adminhtml_User_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('notice_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simitracking')->__('User Information'));
    }

    
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('simitracking')->__('User Information'),
            'title' => Mage::helper('simitracking')->__('User Information'),
            'content' => $this->getLayout()->createBlock('simitracking/adminhtml_user_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
