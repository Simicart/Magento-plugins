<?php
class Simicart_Simihr_Model_Mysql4_Department extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('simihr/department', 'id');
    }  
}