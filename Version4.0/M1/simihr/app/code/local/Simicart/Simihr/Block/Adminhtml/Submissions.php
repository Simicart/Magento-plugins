<?php
class Simicart_Simihr_Block_Adminhtml_Submissions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'simihr';
        $this->_controller = 'adminhtml_submissions';
        $this->_headerText = $this->__('Submissions');
         
        parent::__construct();
    }
}