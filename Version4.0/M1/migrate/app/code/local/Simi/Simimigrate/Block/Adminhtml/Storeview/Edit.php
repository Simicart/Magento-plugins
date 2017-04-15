<?php

/**

 */
class Simi_Simimigrate_Block_Adminhtml_Storeview_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'id';
        $this->_blockGroup = 'simimigrate';
        $this->_controller = 'adminhtml_storeview';

        $this->removeButton('reset');
        $this->removeButton('save');
    }

    /**
     * get text to show in header when edit an storeview
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('storeview_data') && Mage::registry('storeview_data')->getId())
            return Mage::helper('simimigrate')->__("View Store View '%s'", $this->htmlEscape(Mage::registry('storeview_data')->getId()));
    }

}
