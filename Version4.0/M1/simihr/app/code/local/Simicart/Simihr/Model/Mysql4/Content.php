<?php
class Simicart_Simihr_Model_Mysql4_Content extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('simihr/content', 'id');
    }  
}