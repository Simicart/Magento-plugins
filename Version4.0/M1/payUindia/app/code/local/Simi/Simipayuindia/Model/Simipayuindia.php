<?php

class Simi_Simipayuindia_Model_Simipayuindia extends Mage_Core_Model_Abstract
{
	protected $_order; 
	public function _construct(){
		parent::_construct();
		$this->_init('simipayuindia/simipayuindia');
	}

	public function setOrderHistory($order_id){
		$order = Mage::getModel('sales/order');
        $order->loadByIncrementId($order_id);
        $order->addStatusToHistory($order->getStatus(), Mage::helper('payucheckout')->__('Customer was redirected to payu.'));
        $order->save();	
        $this->_order = $order;
	}

	public function getFieldsFrom($order_id){
		$this->setOrderHistory($order_id);
		Mage::helper('simipayuindia')->getFormFields($this->_order);
	}
}