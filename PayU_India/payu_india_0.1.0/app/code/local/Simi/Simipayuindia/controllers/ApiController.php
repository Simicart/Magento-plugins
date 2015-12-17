<?php

class Simi_Simipayuindia_ApiController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
	}

	public function  successAction()
  {
    $response = $this->getRequest()->getPost();
	  Mage::helper("simipayuindia")->getResponseOperation($response);
    echo "1";
   	exit();
  }
		
	public function failureAction()
    {
       
	   $arrParams = $this->getRequest()->getPost();
	   Mage::helper("simipayuindia")->getResponseOperation($arrParams);
     //  $this->getCheckout()->clear();
     echo "1";
     exit();
    }

    public function canceledAction()
    {
	    $arrParams = $this->getRequest()->getParams();
  		Mage::helper("simipayuindia")->getResponseOperation($arrParams);
  		//$this->getCheckout()->clear();
  		echo "1";
     	exit();
    }

    public function checkoutAction(){
     // die($this->getRequest()->getParam('invoice_number'));
      if($this->getRequest()->getParam('invoice_number')){
        $order_id = $this->getRequest()->getParam('invoice_number');
        $this->getResponse()->setBody(
                $this->getLayout()
                    ->createBlock('simipayuindia/redirect')
                    ->setOrderId($order_id)
                    ->toHtml()
            );  
      }else{
          $this->_redirect('checkout/cart');
          return;
      }         
    }

}