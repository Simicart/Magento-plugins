<?php
class Simicart_Simihr_Model_Mysql4_Content_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {  
        $this->_init('simihr/content');
    }  
}