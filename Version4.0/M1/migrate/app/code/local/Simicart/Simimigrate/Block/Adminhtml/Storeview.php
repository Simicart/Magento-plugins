<?php

/**
 * 

 */
class Simicart_Simimigrate_Block_Adminhtml_Storeview extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_storeview';
        $this->_blockGroup = 'simimigrate';
        $this->_headerText = Mage::helper('simimigrate')->__('Storeviews');
        parent::__construct();
        $this->removeButton('add');
    }

}
