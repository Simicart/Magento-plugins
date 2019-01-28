<?php
class Simicart_Simihr_Block_Adminhtml_Content extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'simihr';
        $this->_controller = 'adminhtml_content';
        $this->_headerText = $this->__('Content');

        parent::__construct();
    }
}