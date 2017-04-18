<?php

/**
 * 

 */
class Simi_Simimigrate_Block_Adminhtml_App extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_app';
        $this->_blockGroup = 'simimigrate';
        $this->_headerText = Mage::helper('simimigrate')->__('Apps');
        parent::__construct();
        $this->removeButton('add');
    }

}
