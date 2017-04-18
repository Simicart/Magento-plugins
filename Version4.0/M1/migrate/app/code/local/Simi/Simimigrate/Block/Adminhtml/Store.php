<?php

/**
 * 

 */
class Simi_Simimigrate_Block_Adminhtml_Store extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_store';
        $this->_blockGroup = 'simimigrate';
        $this->_headerText = Mage::helper('simimigrate')->__('Stores');
        parent::__construct();
        $this->removeButton('add');
    }

}
