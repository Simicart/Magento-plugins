<?php

class Simi_Simiklarna_Model_Simiklarna extends Simi_Connector_Model_Abstract
{
	protected $_sharedSecret = "";
	protected $_eid = "";
    protected $_urlsandbox = "https://checkout.testdrive.klarna.com/checkout/orders";
    protected $_urllive = "https://checkout.klarna.com/checkout/orders";

	protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

	public function _construct(){
		parent::_construct();
		$this->_init('simiklarna/simiklarna');
        $this->_sharedSecret = Mage::getStoreConfig('payment/simiklarna/secret_key');
        $this->_eid = Mage::getStoreConfig('payment/simiklarna/merchant_id');
	}

	public function newKlarnaCheckout(){
               // die(Mage::getBaseDir('base').DS.'lib'.DS.'simiklarna'.DS.'src'.DS.'Klarna'.DS.'Checkout.php');
		// error_reporting(E_ALL^ E_WARNING);
		// error_reporting(E_ALL^ E_NOTICE);
		try{
            //require_once Mage::getBaseDir('base').DS.'lib'.DS.'Facebook'.DS.'facebook.php';
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'KlarnaCheckout'.DS.'Checkout.php';
		}catch(Exception $e){

           // die('1234');
        }

        // die('xxxx');
	}

	public function push($checkoutId){
		$this->newKlarnaCheckout();
      //  Zend_debug::dump($checkoutId);die();
		Klarna_Checkout_Order::$contentType
		    = "application/vnd.klarna.checkout.aggregated-order-v2+json";

		$sharedSecret = $this->_sharedSecret;

		$connector = Klarna_Checkout_Connector::create($sharedSecret);

        if(Mage::getStoreConfig('general/simiklarna/enable_auth_query') == 0){
            $checkoutId = "https://checkout.testdrive.klarna.com/checkout/orders/14EDCC79D4ADE1D93DC79A60000"
        }
        
		$order = new Klarna_Checkout_Order($connector, $checkoutId);

		$order->fetch();

		if ($order['status'] == "checkout_complete" || $order['status'] == "created") {
		    // At this point make sure the order is created in your system and send a
		    // confirmation email to the customer
		    $update['status'] = 'created';
		    $update['merchant_reference'] = array(
		        'orderid1' => uniqid()
		    );
		    $order->update($update);
    
            $information = Mage::getModel('simiklarna/simidata')->saveOrder($order);
            return $information;
		}
        $information = $this->statusError();
        return $information;
	}

	public function confirmation(){
		$this->newKlarnaCheckout();

		Klarna_Checkout_Order::$contentType
		    = "application/vnd.klarna.checkout.aggregated-order-v2+json";

		$sharedSecret = $this->_sharedSecret;
		$checkoutId = $this->_getSession()->getSimiKlarnaCheckoutId();
        Mage::log($checkoutId);
		$connector = Klarna_Checkout_Connector::create($sharedSecret);
		$order = new Klarna_Checkout_Order($connector, $checkoutId);

		$order->fetch();

		if ($order['status'] == 'checkout_incomplete') {
		   return array('status' => false, 'html'=>"");
		}
        Mage::log($order);

		$snippet = $order['gui']['snippet'];
		// DESKTOP: Width of containing block shall be at least 750px
		// MOBILE: Width of containing block shall be 100% of browser window (No
		// padding or margin)
        if ($order['status'] == "checkout_complete"){        
            $re = $this->statusSuccess();
            $re['data'] = array("klarna_order" => $checkoutId);
            return $re;
        }

	    $re = $this->statusError();
        return $re;
	}

	 /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/cart')->getQuote();
    }

	public function checkout($data_cart){
		$this->newKlarnaCheckout();

        if(Mage::getStoreConfig('general/simiklarna/enable_auth_query') == 0){
            Klarna_Checkout_Order::$baseUri
            = $this->_urlsandbox;
        }else{
            Klarna_Checkout_Order::$baseUri
            = $this->_urllive;
        }
        
        Klarna_Checkout_Order::$contentType
            = "application/vnd.klarna.checkout.aggregated-order-v2+json";

		$order = null;
		$eid = $this->_eid;
		$sharedSecret = $this->_sharedSecret;

		$connector = Klarna_Checkout_Connector::create($sharedSecret);

		if ($this->_getSession()->getSimiKlarnaCheckoutId()) {
		    // Resume session
		    $order = new Klarna_Checkout_Order(
		        $connector,
		        $this->_getSession()->getSimiKlarnaCheckoutId()
		    );
		    try {
		        $order->fetch();

		        // Reset cart
		        $update['cart']['items'] = $data_cart;

		        if ($data = $this->_getBillingAddressData()) {
		            $update['shipping_address'] = $data;
		        }

		        $order->update($update);
		    } catch (Exception $e) {
		        // Reset session
		        $order = null;
		        $this->_getSession()->unsetData('simi_klarna_checkout_id');
		    }
		}
		if($order == null){
			$create = array();
			if (version_compare(Mage::getVersion(), '1.6.2', '>=')) {
	            $create['purchase_country'] = Mage::helper('core')->getDefaultCountry();
	        } else {
	            $create['purchase_country'] = Mage::getStoreConfig('general/country/default');
	        }
		    $create['purchase_currency'] = $this->_getQuote()->getQuoteCurrencyCode();
		    $create['locale'] = str_replace('_', '-', Mage::app()->getLocale()->getLocaleCode());

            if(Mage::getStoreConfig('general/simiklarna/enable_auth_query') == 0){
                $create['purchase_country'] = 'SE';
                $create['purchase_currency'] = 'SEK';
                $create['locale'] = 'sv-se';
            }
            
		    // $create['order_amount'] = 10000;
		    // $create['order_tax_amount'] = 2000;

		    $create['merchant']['id'] = $eid;
			$create['merchant']['terms_uri'] = Mage::getUrl('privacy-policy-cookie-restriction-mode');
	        $create['merchant']['checkout_uri'] = Mage::getUrl('simiklarna/api/checkout');
	        $create['merchant']['confirmation_uri'] = Mage::getUrl('simiklarna/api/success');
		    // You can not receive push notification on non publicly available uri
		    $pushUrl = Mage::getUrl('simiklarna/api/push?klarna_order={checkout.order.id}', array('_nosid' => true));
	        if (substr($pushUrl, -1, 1) == '/') {
	            $pushUrl = substr($pushUrl, 0, strlen($pushUrl) - 1);
	        }

	        $create['merchant']['push_uri'] = $pushUrl;

	        $validateUrl = Mage::getUrl('simiklarna/api/validate?klarna_order={checkout.order.id}', array('_nosid' => true));
	        if (substr($validateUrl, -1, 1) == '/') {
	            $validateUrl = substr($validateUrl, 0, strlen($validateUrl) - 1);
	        }
	        if (substr($validateUrl, 0, 5) == 'https') {
	            $create['merchant']['validation_uri'] = $validateUrl;
	        }

	        $create['cart']['items'] = $data_cart;

		    if ($data = $this->_getBillingAddressData()) {
	            $create['shipping_address'] = $data;
	        }

           // Zend_debug::dump($create);die();
	        $order = new Klarna_Checkout_Order($connector);
	        $order->create($create);
	        $order->fetch();
		}
		
		$sessionId = $order->getLocation();
        Mage::log($sessionId);
		$this->_getSession()->setSimiKlarnaCheckoutId($sessionId);
		// Display checkout
		$snippet = $order['gui']['snippet'];
        //zend_debug::dump($snippet);die('xxxxxx');
		// DESKTOP: Width of containing block shall be at least 750px
		// MOBILE: Width of containing block shall be 100% of browser window (No
		// padding or margin)
		return array('status' => true, 'html'=>$snippet);
	}

    function escapeJsonString($value) { 
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

    public function getCart(){
        $cart = $this->_getCartItems();
        $re = $this->statusSuccess();
        $re['data'] = $cart;
        return $re;
    }
	/**
     * Get quote items and totals
     *
     * @return array
     */
    protected function _getCartItems()
    {
        $quote = $this->_getQuote();
        $items = array();

        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            if ($quoteItem->getTaxPercent() > 0) {
                $taxRate = $quoteItem->getTaxPercent();
            } else {
                $taxRate = $quoteItem->getTaxAmount() / $quoteItem->getRowTotal() * 100;
            }
            $items[] = array(
                'reference' => $quoteItem->getSku(),
                'name' => $this->escapeJsonString($quoteItem->getName()),
                'quantity' => round($quoteItem->getQty()),
                'unit_price' => round($quoteItem->getPriceInclTax() * 100),
                'tax_rate' => round($taxRate * 100),
            );
        }

        foreach ($quote->getTotals() as $key => $total) {
            switch ($key) {
                case 'shipping':
                    if ($total->getValue() != 0) {
                        $amount_incl_tax = $total->getAddress()->getShippingInclTax();
                        if (false && $amount_incl_tax) {
                            $taxAmount = $total->getAddress()->getShippingTaxAmount();
                            $amount = $amount_incl_tax - $taxAmount;
                        } else {
                            $amount = $total->getAddress()->getShippingAmount();
                            $taxAmount = $total->getAddress()->getShippingTaxAmount();
                        }
                        $hiddenTaxAmount = $total->getAddress()->getShippingHiddenTaxAmount();
                        $taxRate = ($taxAmount + $hiddenTaxAmount) / $amount * 100;
                        $amount_incl_tax = $amount + $taxAmount + $hiddenTaxAmount;
                        $items[] = array(
                            'type' => 'shipping_fee',
                            'reference' => Mage::helper('simiklarna')->__('shipping'), // $total->getCode()
                            'name' => $this->escapeJsonString($total->getTitle()),
                            'quantity' => 1,
                            'unit_price' => round(($amount_incl_tax) * 100),
                            'discount_rate' => 0,
                            'tax_rate' => round($taxRate * 100),
                        );
                    }
                    break;
                case 'discount':
                    if ($total->getValue() != 0) {
                        $taxAmount = $total->getAddress()->getHiddenTaxAmount();
                        $amount = -$total->getAddress()->getDiscountAmount() - $taxAmount;
                        $taxRate = $taxAmount / $amount * 100;
                        $items[] = array(
                            'type' => 'discount',
                            'reference' => Mage::helper('simiklarna')->__('discount'), // $total->getCode()
                            'name' => $this->escapeJsonString($total->getTitle()),
                            'quantity' => 1,
                            'unit_price' => -round(($amount + $taxAmount) * 100),
                            'discount_rate' => 0,
                            'tax_rate' => round($taxRate * 100),
                        );
                    }
                    break;
                case 'giftcardaccount':
                    if ($total->getValue() != 0) {
                        $items[] = array(
                            'type' => 'discount',
                            'reference' => Mage::helper('simiklarna')->__('gift_card'), // $total->getCode()
                            'name' => $this->escapeJsonString($total->getTitle()),
                            'quantity' => 1,
                            'unit_price' => round($total->getValue() * 100),
                            'discount_rate' => 0,
                            'tax_rate' => 0,
                        );
                    }
                    break;
                case 'ugiftcert':
                    if ($total->getValue() != 0) {
                        $items[] = array(
                            'type' => 'discount',
                            'reference' => Mage::helper('simiklarna')->__('gift_card'), // $total->getCode()
                            'name' => $this->escapeJsonString($total->getTitle()),
                            'quantity' => 1,
                            'unit_price' => -round($total->getValue() * 100),
                            'discount_rate' => 0,
                            'tax_rate' => 0,
                        );
                    }
                    break;
                case 'reward':
                    if ($total->getValue() != 0) {
                        $items[] = array(
                            'type' => 'discount',
                            'reference' => Mage::helper('simiklarna')->__('reward'), // $total->getCode()
                            'name' => $this->escapeJsonString($total->getTitle()),
                            'quantity' => 1,
                            'unit_price' => round($total->getValue() * 100),
                            'discount_rate' => 0,
                            'tax_rate' => 0,
                        );
                    }
                    break;
                case 'customerbalance':
                    if ($total->getValue() != 0) {
                        $items[] = array(
                            'type' => 'discount',
                            'reference' => Mage::helper('simiklarna')->__('customer_balance'), // $total->getCode()
                            'name' => $this->escapeJsonString($total->getTitle()),
                            'quantity' => 1,
                            'unit_price' => round($total->getValue() * 100),
                            'discount_rate' => 0,
                            'tax_rate' => 0,
                        );
                    }
                    break;
            }
        }
       
        return $items;
    }

    protected function _getBillingAddressData()
    {
       // if (!$this->_getTransport()->getConfigData('auto_prefil')) return NULL;
        
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            $address = $session->getCustomer()->getPrimaryBillingAddress();
            $result = array(
                'email' => $session->getCustomer()->getEmail(),
                'postal_code' => $address ? $address->getPostcode() : '',
            );
            return $result;
        }

        return array();
    }   
}