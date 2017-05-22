<?php

/**

 */
class Simicart_Simimigrate_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'id';
        $this->_blockGroup = 'simimigrate';
        $this->_controller = 'adminhtml_category';

        $this->removeButton('reset');
        $this->removeButton('save');
    }

    /**
     * get text to show in header when edit an category
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('category_data') && Mage::registry('category_data')->getId())
            return Mage::helper('simimigrate')->__("View Category '%s'", $this->htmlEscape(Mage::registry('category_data')->getId()));
    }

}
