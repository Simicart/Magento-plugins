<?php

class Simi_Simitracking_Block_Adminhtml_Role_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('notice_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simitracking')->__('Role Information'));
    }

    
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('simitracking')->__('Role Information'),
            'title' => Mage::helper('simitracking')->__('Role Information'),
            'content' => $this->getLayout()->createBlock('simitracking/adminhtml_role_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
