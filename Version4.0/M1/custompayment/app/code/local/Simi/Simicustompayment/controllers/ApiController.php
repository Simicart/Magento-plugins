<?php

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
	
	public function get_custom_paymentsAction()
    {
        $information = array('status' => 'SUCCESS',
            'message' => array('SUCCESS'),
        ); 
        $paymentList = array();
        $paymentList[] = array(
            'paymentmethod' => 'payfortcw_creditcard',
            'title_url_action' => 'url_action',
            'url_redirect' => Mage::getUrl(),
            'url_success' => 'checkout/onepage/success',
            'url_fail' => 'checkout/onepage/failure', 
            'url_cancel' => 'connector/api/cancel',
            'url_error' => 'checkout/onepage/failure ',
            'message_success' => 'Thank you for purchasing',
            'message_fail' => 'Sorry, payment failed',
            'message_cancel' => 'Your order has been canceled',
            'message_error' => 'Sorry, Your order has an error',
            'ischeckurl' => '0', //(bien check truoc khi chuyen sang webview. Co hoac khong) : "0" or "1"
            'url_check' => "checkout/onepage/failure"
        );

        $information['data'] = $paymentList;
        $this->_printDataJson($information);
    }
	
	/**
     * Render placement form and set New Order Status [multi-method]
     *
     * @see omnikassa/api/placement
     */
	 public function tryLoginAction()
    {
		/*
		echo base64_decode(Mage::app()->getRequest()->getParam('Email'));
		$session = Mage::getSingleton( 'customer/session' );
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		$customer = Mage::getModel("customer/customer");
		$customer->website_id = $websiteId;
		$customer->setStore($store);
		try {
			$customer->loadByEmail(base64_decode(Mage::app()->getRequest()->getParam('Email')));
			$session = Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			//zend_debug::dump($customer->getData());die;
			//$session->login($email, $password);
			zend_debug::dump($session->getCustomer()->getData());die;
		}catch(Exception $e){
			echo 'bug';
		}
		echo 'done';
		*/
	}
	public function checkLoginAction(){ 
	/*
	 if(!Mage::getSingleton('customer/session')->isLoggedIn()){
        die('not yet');//not logged in
		}else{
		  die('logged');   // logged in
		}
		zend_debug::dump($store = Mage::app()->getStore()->getData());die;
		$session = Mage::getSingleton( 'customer/session' );
		zend_debug::dump($session->getCustomer()->getData());die;
	*/
	}
    public function placementAction()
    {
		$session = Mage::getSingleton( 'customer/session' );
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		$customer = Mage::getModel("customer/customer");
		$customer->website_id = $websiteId;
		$customer->setStore($store);
		
		try {
			$customer->loadByEmail(base64_decode(Mage::app()->getRequest()->getParam('Email')));
			$session = Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			$session->login($email, $password);
		}catch(Exception $e){

		}

		//$session->login(base64_decode(Mage::app()->getRequest()->getParam('Email')), base64_decode(Mage::app()->getRequest()->getParam('Password')));
		//$session->setCustomerAsLoggedIn($session->getCustomer());
				
		Mage::getSingleton('checkout/session')->setOrderid(base64_decode(Mage::app()->getRequest()->getParam('OrderID')));
		Mage::getSingleton('checkout/session')->setMerchantid(base64_decode((Mage::app()->getRequest()->getParam('MerchantID'))));
		Mage::getSingleton('checkout/session')->setAmount(base64_decode(Mage::app()->getRequest()->getParam('Amount')));
		Mage::getSingleton('checkout/session')->setCurrencycode(base64_decode(Mage::app()->getRequest()->getParam('CurrencyCode')));
		Mage::getSingleton('checkout/session')->setTransactiontype(base64_decode(Mage::app()->getRequest()->getParam('TransactionType')));
		Mage::getSingleton('checkout/session')->setTransactiondatetime(base64_decode(Mage::app()->getRequest()->getParam('TransactionDateTime')));
		Mage::getSingleton('checkout/session')->setOrderdescription(base64_decode(Mage::app()->getRequest()->getParam('OrderDescription')));
		Mage::getSingleton('checkout/session')->setCustomername(base64_decode(Mage::app()->getRequest()->getParam('CustomerName')));
		Mage::getSingleton('checkout/session')->setAddress1(base64_decode(Mage::app()->getRequest()->getParam('Address1')));
		Mage::getSingleton('checkout/session')->setAddress2(base64_decode(Mage::app()->getRequest()->getParam('Address2')));
		Mage::getSingleton('checkout/session')->setAddress3(base64_decode(Mage::app()->getRequest()->getParam('Address3')));
		Mage::getSingleton('checkout/session')->setAddress4(base64_decode(Mage::app()->getRequest()->getParam('Address4')));
		Mage::getSingleton('checkout/session')->setCity(base64_decode(Mage::app()->getRequest()->getParam('City')));
		Mage::getSingleton('checkout/session')->setState(base64_decode(Mage::app()->getRequest()->getParam('State')));
		Mage::getSingleton('checkout/session')->setPostcode(base64_decode(Mage::app()->getRequest()->getParam('PostCode')));
		Mage::getSingleton('checkout/session')->setLastRealOrderId(base64_decode(Mage::app()->getRequest()->getParam('LastRealOrderId')));
		Mage::getSingleton('core/session')->setPayFortCwTransactionId(base64_decode(Mage::app()->getRequest()->getParam('Payforttransactionid'))); 

		$this->_redirect('PayFortCw/process/ppRedirect');
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