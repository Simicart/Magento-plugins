<?php

/**

 */
class Simi_Simimigrate_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'id';
        $this->_blockGroup = 'simimigrate';
        $this->_controller = 'adminhtml_customer';

        $this->removeButton('reset');
        $this->removeButton('save');
    }

    /**
     * get text to show in header when edit an customer
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('customer_data') && Mage::registry('customer_data')->getId())
            return Mage::helper('simimigrate')->__("View Customer '%s'", $this->htmlEscape(Mage::registry('customer_data')->getId()));
    }

}
