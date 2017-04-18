<?php

/**
 * 

 */
class Simi_Simimigrate_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('customer_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simimigrate')->__('Customer Information'));
    }

    /**
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('simimigrate')->__('Customer Information'),
            'title' => Mage::helper('simimigrate')->__('Customer Information'),
            'content' => $this->getLayout()->createBlock('simimigrate/adminhtml_customer_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
