<?php

/**
 * 

 */
class Simi_Simimigrate_Block_Adminhtml_Storeview_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('storeview_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simimigrate')->__('Storeview Information'));
    }

    /**
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('simimigrate')->__('Storeview Information'),
            'title' => Mage::helper('simimigrate')->__('Storeview Information'),
            'content' => $this->getLayout()->createBlock('simimigrate/adminhtml_storeview_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
