<?php

require_once( Mage::getBaseDir('lib') . '/payfortFort/init.php');

class Simi_Simicustompayment_ApiController extends Mage_Core_Controller_Front_Action
{	
	//error_reporting(0);
	protected $_data;
	
	public function indexAction(){
		echo 'yes';
	}
	
	public function checkInstallAction(){
		echo 'yes';
	}
	
    public function placementAction()
    {
		Mage::getSingleton('checkout/session')->setOrderid(base64_decode(Mage::app()->getRequest()->getParam('OrderID')));
		Mage::getSingleton('checkout/session')->setLastRealOrderId(base64_decode(Mage::app()->getRequest()->getParam('LastRealOrderId')));
		Mage::getSingleton('checkout/session')->setData('payfort_option', base64_decode(Mage::app()->getRequest()->getParam('payfort_option')));

		$this->_redirect('payfort/payment/redirect');
    }
	
	public function _printDataJson($data) {
        ob_start();		
        echo $this->convertToJson($data);
        header("Content-Type: application/json");
        // header("HTTP/1.0 200 OK");
        exit();
		ob_end_flush();
    }
	 
	public function convertToJson($data) {
        $this->setData($data);
        //$this->eventChangeData($this->getEventName('_return'), $data);
        //$this->_data = $this->getData();
        return Mage::helper('core')->jsonEncode($this->_data);
    }
	
	public function eventChangeData($event_name, $data) {
        Mage::dispatchEvent($event_name, array('object' => $this, 'data' => $data));
    }

    public function getEventName($last = '') {
        return $this->getFullActionName() . $last;
    }
	
	public function setData($data) {
        $this->_data = $data;
    }

	
}