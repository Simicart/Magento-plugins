<?php

/**
 * 

 */
class Simi_Simimigrate_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'simimigrate';
        $this->_headerText = Mage::helper('simimigrate')->__('Products');
        parent::__construct();
        $this->removeButton('add');
    }

}
