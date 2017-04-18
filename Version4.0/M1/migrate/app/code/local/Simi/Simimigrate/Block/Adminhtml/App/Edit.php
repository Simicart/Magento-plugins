<?php

/**

 */
class Simi_Simimigrate_Block_Adminhtml_App_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'id';
        $this->_blockGroup = 'simimigrate';
        $this->_controller = 'adminhtml_app';

        $this->removeButton('reset');
        $this->removeButton('save');
    }

    /**
     * get text to show in header when edit an app
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('app_data') && Mage::registry('app_data')->getId())
            return Mage::helper('simimigrate')->__("View App '%s'", $this->htmlEscape(Mage::registry('app_data')->getId()));
    }

}
