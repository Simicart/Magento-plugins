<?php

class Simicart_Simimigrate_Controller_Action extends Mage_Core_Controller_Front_Action
{
    protected $_data;

    protected function _getServer()
    {
        return Mage::getSingleton('simimigrate/server');
    }

    protected function _printData($result)
    {
        header("Content-Type: application/json");
        $this->setData($result);
        Mage::dispatchEvent($this->getFullActionName(), array('object' => $this, 'data' => $result));
        $this->_data = $this->getData();
        ob_clean();
        echo Mage::helper('core')->jsonEncode($this->_data);
    }
    
    public function getData()
    {
        return $this->_data;
    }

    public function setData($data)
    {
        $this->_data = $data;
    }
}
