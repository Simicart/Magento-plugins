<?php

class Simi_Simiklarna_ApiController extends Mage_Core_Controller_Front_Action
{

    protected $_data;

    public function preDispatch() {
        parent::preDispatch();
        $value = $this->getRequest()->getParam('data');
        $this->praseJsonToData($value);
    }

    public function praseJsonToData($json) {  
        $string = preg_replace("/[\r\n]+/", " ", $json);
        $json = utf8_encode($string);
          
        $data = json_decode($json);    
        if(!$data){

          $json = urldecode($json); 
          $data = json_decode($json);    
        }   
        $data = json_decode($json);    
        $this->setData($data);
        $this->_data = $this->getData();
      }

    public function getData() {
        return $this->_data;
    }

    public function setData($data) {
        $this->_data = $data;
    }

    public function _printDataJson($data) {
        ob_start();   
        echo $this->convertToJson($data);
        header("Content-Type: application/json");
        exit();
      ob_end_flush();
    }

    public function convertToJson($data) {
        
      $this->setData($data);
      $this->_data = $this->getData();
      return Mage::helper('core')->jsonEncode($this->_data);
    }

    public function get_paramsAction(){
        $simiklarna = Mage::getModel('simiklarna/simiklarna');
        $information = $simiklarna->getCart();
        $this->_printDataJson($information);
    }

    public function indexAction(){
        echo "<div></div>";
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    } 

  	public function checkoutAction(){
          $value = $this->getRequest()->getParam('data');
          // Zend_debug::dump($value);die();
          if (!$value) {
              $this->_redirect('checkout/cart');
              return;
          }
          //Zend_debug::dump($value);
          $data = json_decode($value, true);
           // Zend_debug::dump($data);die();
          $simiklarna = Mage::getModel('simiklarna/simiklarna');
          $checkout = $simiklarna->checkout($data);
        //  die('xxxxxxx');
          if($checkout['status'] == true){
              echo '<div>'.$checkout["html"].'</div>';
          	//echo $html;
          }else{
          	$this->_redirect('checkout/cart');
          }
         
          exit();
  	}

  	public function pushAction(){
      $checkoutModel = Mage::getSingleton('connector/checkout');
      $message = $checkoutModel->indexPlace();
      
      if ($message) {
          $information = $checkoutModel->statusError(array($message));
          $this->_printDataJson($information);
          return;
      }

    	$data = $this->getData();
     // $checkoutId = $data->klarna_order;
      //Zend_debug::dump($data);die();
    	$simiklarna = Mage::getModel('simiklarna/simiklarna');
      $information = $simiklarna->push($checkoutId);
      $this->_printDataJson($information);
  	}

  	public function successAction(){
      // die('VU MINH NHAT');
      $simiklarna = Mage::getModel('simiklarna/simiklarna');
      $information = $simiklarna->confirmation();
      $this->_printDataJson($information);
    }
}