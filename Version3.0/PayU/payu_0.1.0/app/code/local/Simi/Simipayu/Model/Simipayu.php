<?php

class Simi_Simipayu_Model_Simipayu extends Simi_Connector_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('simipayu/simipayu');
	}

	public function thankPur(){
		$thank = Mage::helper('core')->__("Thank for your purchase");
		$information = $this->statusSuccess();
		$information['message'] = array($thank);
		return $information;
	}

	public function errorPur(){
		$error = Mage::helper('core')->__("Has some errors");
		$information = $this->statusError(array($error));
		return $information;
	}
}