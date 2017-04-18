<?php

/**
 * 

 */
class Simi_Simimigrate_Block_Adminhtml_App_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('app_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simimigrate')->__('App Information'));
    }

    /**
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('simimigrate')->__('App Information'),
            'title' => Mage::helper('simimigrate')->__('App Information'),
            'content' => $this->getLayout()->createBlock('simimigrate/adminhtml_app_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
