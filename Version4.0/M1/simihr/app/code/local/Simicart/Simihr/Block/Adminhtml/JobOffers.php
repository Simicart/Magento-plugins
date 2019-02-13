<?php
class Simicart_Simihr_Block_Adminhtml_JobOffers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'simihr';
        $this->_controller = 'adminhtml_jobOffers';
        $this->_headerText = $this->__('Job Offers');
         
        parent::__construct();
    }
}