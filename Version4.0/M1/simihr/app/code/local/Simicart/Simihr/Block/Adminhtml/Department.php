<?php
class Simicart_Simihr_Block_Adminhtml_Department extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'simihr';
        $this->_controller = 'adminhtml_department';
        $this->_headerText = $this->__('Department');
         
        parent::__construct();
    }
}