<?php
class Simicart_Simihr_Block_Adminhtml_Department_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {  
        $this->_blockGroup = 'simihr';
        $this->_controller = 'adminhtml_department';
     
        parent::__construct();
     
        $this->_updateButton('save', 'label', $this->__('Save Department'));
        $this->_updateButton('delete', 'label', $this->__('Delete Department'));
    }  
     
    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {  
        if (Mage::registry('simicart_simihr')->getId()) {
            return $this->__('Edit Department');
        }  
        else {
            return $this->__('New Department');
        }  
    }  
}