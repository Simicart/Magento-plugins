<?php

/**

 */
class Simi_Simimigrate_Block_Adminhtml_Product_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'id';
        $this->_blockGroup = 'simimigrate';
        $this->_controller = 'adminhtml_product';

        $this->removeButton('reset');
        $this->removeButton('save');
    }

    /**
     * get text to show in header when edit an product
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('product_data') && Mage::registry('product_data')->getId())
            return Mage::helper('simimigrate')->__("View Product '%s'", $this->htmlEscape(Mage::registry('product_data')->getId()));
    }

}
