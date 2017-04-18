<?php

/**
 * 

 */
class Simi_Simimigrate_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'simimigrate';
        $this->_headerText = Mage::helper('simimigrate')->__('Categories');
        parent::__construct();
        $this->removeButton('add');
    }

}
