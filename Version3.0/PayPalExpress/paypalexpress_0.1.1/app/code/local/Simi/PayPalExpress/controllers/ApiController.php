<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	PayPalExpress
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * PayPalExpress Api Controller
 * 
 * @category 	
 * @package 	PayPalExpress
 * @author  	Developer
 */
class Simi_PayPalExpress_ApiController extends Simi_Connector_Controller_Action {
	/**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'paypal/config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'paypal/express_checkout';
	
	protected $_paramMobile = "&useraction=commit";
	
	/**
     * @var Mage_Paypal_Model_Express_Checkout
     */
    protected $_checkout = null;

    /**
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = false;

    /**
     * Instantiate config
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_config = Mage::getModel($this->_configType, array($this->_configMethod));
    }

    /**
     * Start Express Checkout by requesting initial token and dispatching customer to PayPal
     */
    public function startAction()
    {
		if((int)Mage::getStoreConfig('paypalexpress/general/enable_app') == 0){
			$modelAbstarct = Mage::getModel("connector/abstract");
			$info = $modelAbstarct->statusError(array(Mage::helper("core")->__("PayPal Express was disabled in App")));			
			$this->_printDataJson($info);	
			return;
		}
        try {
            $this->_initCheckout();

            if ($this->_getQuote()->getIsMultiShipping()) {
                $this->_getQuote()->setIsMultiShipping(false);
                $this->_getQuote()->removeAllAddresses();
            }

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer && $customer->getId()) {
                $this->_checkout->setCustomerWithAddressChange(
                    $customer, $this->_getQuote()->getBillingAddress(), $this->_getQuote()->getShippingAddress()
                );
            }

            // billing agreement
            $isBARequested = (bool)$this->getRequest()
                ->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
            if ($customer && $customer->getId()) {
                $this->_checkout->setIsBillingAgreementRequested($isBARequested);
            }

            // giropay
            $this->_checkout->prepareGiropayUrls(
                Mage::getUrl('checkout/onepage/success'),
                Mage::getUrl('paypal/express/cancel'),
                Mage::getUrl('checkout/onepage/success')
            );

            $token = $this->_checkout->start(Mage::getUrl('*/*/return'), Mage::getUrl('*/*/cancel'));
			$review_address = Mage::getStoreConfig('paypalexpress/general/enable');
			
            if ($token && $url = $this->_checkout->getRedirectUrl()) {				
                $this->_initToken($token);				
                $url .= $this->_paramMobile;
				$modelAbstarct = Mage::getModel("connector/abstract");
				$info = $modelAbstarct->statusSuccess();
				$info["data"] = array(
							array("url" => $url,
									"review_address" => $review_address,
								));
				$this->_printDataJson($info);				
				return;
                
            }
        } catch (Mage_Core_Exception $e) {            
			$modelAbstarct = Mage::getModel("connector/abstract");
			$info = $modelAbstarct->statusError(array($e->getMessage()));			
			$this->_printDataJson($info);	
        } catch (Exception $e) {            
			$modelAbstarct = Mage::getModel("connector/abstract");
			$info = $modelAbstarct->statusError(array($this->__('Unable to start Express Checkout.')));			
			$this->_printDataJson($info);	
            Mage::logException($e);
        }
        return;
    }

    /**
     * Return shipping options items for shipping address from request
     */
    public function shippingOptionsCallbackAction()
    {
        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $this->_quote = Mage::getModel('sales/quote')->load($quoteId);
            $this->_initCheckout();
            $response = $this->_checkout->getShippingOptionsCallbackResponse($this->getRequest()->getParams());
            $this->getResponse()->setBody($response);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Cancel Express Checkout
     */
    public function cancelAction()
    {
        try {
            $this->_initToken(false);
            // TODO verify if this logic of order cancelation is deprecated
            // if there is an order - cancel it
            $orderId = $this->_getCheckoutSession()->getLastOrderId();
            $order = ($orderId) ? Mage::getModel('sales/order')->load($orderId) : false;
            if ($order && $order->getId() && $order->getQuoteId() == $this->_getCheckoutSession()->getQuoteId()) {
                $order->cancel()->save();
                $this->_getCheckoutSession()
                    ->unsLastQuoteId()
                    ->unsLastSuccessQuoteId()
                    ->unsLastOrderId()
                    ->unsLastRealOrderId()
                    ->addSuccess($this->__('Express Checkout and Order have been canceled.'))
                ;
            } else {
                $this->_getCheckoutSession()->addSuccess($this->__('Express Checkout has been canceled.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getCheckoutSession()->addError($this->__('Unable to cancel Express Checkout.'));
            Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Return from PayPal and dispatch customer to order review page
     */
	 
	public function addressAction(){
		try {
            $this->_initCheckout();			
            $this->_checkout->returnFromPaypal($this->_initToken());    						
            return;
        }
        catch (Mage_Core_Exception $e) {          
			$modelAbstarct = Mage::getModel("connector/abstract");		
			$info = $modelAbstarct->statusError(array($e->getMessage()));			
			$this->_printDataJson($info);	
        }
        catch (Exception $e) {            
			$modelAbstarct = Mage::getModel("connector/abstract");
			$info = $modelAbstarct->statusError(array($this->__('Unable to process Express Checkout approval.')));			
			$this->_printDataJson($info);	            
        }
	}
	
    public function returnAction()
    {
		
        try {
            $this->_initCheckout();			
            $this->_checkout->returnFromPaypal($this->_initToken());    						
            return;
        }
        catch (Mage_Core_Exception $e) {          
			$modelAbstarct = Mage::getModel("connector/abstract");		
			$info = $modelAbstarct->statusError(array($e->getMessage()));			
			$this->_printDataJson($info);	
        }
        catch (Exception $e) {            
			$modelAbstarct = Mage::getModel("connector/abstract");
			$info = $modelAbstarct->statusError(array($this->__('Unable to process Express Checkout approval.')));			
			$this->_printDataJson($info);	            
        }
     
    }       
  
    /**
     * Instantiate quote and checkout
     * @throws Mage_Core_Exception
     */
    private function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {		
            $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            Mage::throwException(Mage::helper('paypal')->__('Unable to initialize Express Checkout.'));
        }
        $this->_checkout = Mage::getSingleton($this->_checkoutType, array(
            'config' => $this->_config,
            'quote'  => $quote,
        ));
    }

    /**
     * Search for proper checkout token in request or session or (un)set specified one
     * Combined getter/setter
     *
     * @param string $setToken
     * @return Mage_Paypal_ExpressController|string
     */
    protected function _initToken($setToken = null)
    {
        if (null !== $setToken) {
            if (false === $setToken) {
                // security measure for avoid unsetting token twice
                if (!$this->_getSession()->getExpressCheckoutToken()) {
                    Mage::throwException($this->__('PayPal Express Checkout Token does not exist.'));
                }
                $this->_getSession()->unsExpressCheckoutToken();
            } else {
                $this->_getSession()->setExpressCheckoutToken($setToken);
            }
            return $this;
        }
        if ($setToken = $this->getRequest()->getParam('token')) {
            if ($setToken !== $this->_getSession()->getExpressCheckoutToken()) {
                Mage::throwException($this->__('Wrong PayPal Express Checkout Token specified.'));
            }
        } else {
            $setToken = $this->_getSession()->getExpressCheckoutToken();
        }
        return $setToken;
    }

    /**
     * PayPal session instance getter
     *
     * @return Mage_PayPal_Model_Session
     */
    private function _getSession()
    {
        return Mage::getSingleton('paypal/session');
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    private function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    private function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Redirect to login page
     *
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        $this->getResponse()->setRedirect(
            Mage::helper('core/url')->addRequestParam(
                Mage::helper('customer')->getLoginUrl(),
                array('context' => 'checkout')
            )
        );
    }

	public function review_addressAction(){
		$this->_initCheckout();		
		$this->_checkout->returnFromPaypal($this->_initToken());
		$this->_checkout->prepareOrderReview($this->_initToken());
		$expressModel = Mage::getModel("paypalexpress/express");
		$expressModel->setQuote($this->_getQuote());
		$info = $expressModel->getBillingShippingAddress();			
		$this->_printDataJson($info);	
	}
	
	public function update_addressAction(){
		$data = $this->getData();
		// Zend_debug::dump($data);die();
		$expressModel = Mage::getModel("paypalexpress/express");
		$expressModel->setQuote($this->_getQuote());
		$info = $expressModel->updateAddress($data);	
		$this->_printDataJson($info);			
	}
	
	
	public function get_shipping_methodsAction(){	
		// Zend_debug::dump(get_class_methods($this->_getQuote()));die();
		$this->_initCheckout();		
		$this->_checkout->returnFromPaypal($this->_initToken());    			
		$this->_checkout->prepareOrderReview($this->_initToken());
		$expressModel = Mage::getModel("paypalexpress/express");
		$expressModel->setQuote($this->_getQuote());
		$info = $expressModel->getInfomation();			
		$this->_printDataJson($info);		
	}		
	
	public function placeAction(){
		$data = $this->getData();
		try{
			$modelAbstarct = Mage::getModel("connector/abstract");
			$info = $modelAbstarct->statusSuccess();			
			$this->_initCheckout();		
			$this->_checkout->place($this->_initToken(), $data->shipping_method);
			$session = $this->_getCheckoutSession();
            $session->clearHelperData();
            $message_success = Mage::helper('checkout')->__("Thank you for your purchase!");
			$info['message'] = array($message_success);
			$this->_printDataJson($info);	           
		}
		catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
			$modelAbstarct = Mage::getModel("connector/abstract");
			$info = $modelAbstarct->statusError(array($e->getMessage()));						
			$this->_printDataJson($info);	           
        }catch (Exception $e) {            
			$modelAbstarct = Mage::getModel("connector/abstract");
			$info = $modelAbstarct->statusError(array($this->__('Unable to place the order.')));			
			$this->_printDataJson($info);	           
            Mage::logException($e);
        }		
	}

    public function cleanSession() {
        $session = $this->_getOnepage()->getCheckout();
        $lastOrderId = $session->getLastOrderId();
       // $this->_oldQuote = $session->getData('old_quote');
        $session->clear();
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
    }

    public function _getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }
}