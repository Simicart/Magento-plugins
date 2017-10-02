<?php

/**
 * 

 */
class Simicart_Simimigrate_Block_Adminhtml_Store_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('store_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simimigrate')->__('Store Information'));
    }

    /**
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('simimigrate')->__('Store Information'),
            'title' => Mage::helper('simimigrate')->__('Store Information'),
            'content' => $this->getLayout()->createBlock('simimigrate/adminhtml_store_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
