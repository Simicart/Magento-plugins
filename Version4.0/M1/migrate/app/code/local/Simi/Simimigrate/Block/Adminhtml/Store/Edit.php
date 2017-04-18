<?php

/**

 */
class Simi_Simimigrate_Block_Adminhtml_Store_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'id';
        $this->_blockGroup = 'simimigrate';
        $this->_controller = 'adminhtml_store';

        $this->removeButton('reset');
        $this->removeButton('save');
    }

    /**
     * get text to show in header when edit an store
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('store_data') && Mage::registry('store_data')->getId())
            return Mage::helper('simimigrate')->__("View Store '%s'", $this->htmlEscape(Mage::registry('store_data')->getId()));
    }

}
