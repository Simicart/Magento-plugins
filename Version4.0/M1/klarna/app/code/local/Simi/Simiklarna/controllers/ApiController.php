<?php

class Simi_Simiklarna_ApiController extends Mage_Core_Controller_Front_Action {

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
        if (!$data) {

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

    public function get_paramsAction() {
        $simiklarna = Mage::getModel('simiklarna/simiklarna');
        $information = $simiklarna->getCart();
        $this->_printDataJson($information);
    }

    public function indexAction() {
        echo "<div></div>";
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote() {
        return $this->_getCart()->getQuote();
    }

    public function checkoutAction() {
		$quote = Mage::getModel('checkout/session')->getQuote();
		$quoteData= $quote->getData();
		$create = array();
		$create['purchase_country'] = Mage::getStoreConfig('general/country/default');
		//$create['order_amount'] = (float)$quoteData['grand_total'];
		//$create['order_tax_amount'] = $quoteData['grand_total'] - $quoteData['subtotal_with_discount'];
		$create['purchase_currency'] = $this->_getQuote()->getQuoteCurrencyCode();
		$create['locale'] = str_replace('_', '-', Mage::app()->getLocale()->getLocaleCode());
		if(Mage::getStoreConfig('general/simiklarna/enable_auth_query') == 0){
			$create['purchase_country'] = 'SE';
			$create['purchase_currency'] = 'SEK';
			$create['locale'] = 'sv-se';
        }
			
		$create['merchant']['id'] = Mage::getStoreConfig('payment/simiklarna/merchant_id');
		$create['merchant']['terms_uri'] = Mage::getUrl('privacy-policy-cookie-restriction-mode');
	        $create['merchant']['checkout_uri'] = Mage::getUrl('simiklarna/api/checkout');
	        $create['merchant']['confirmation_uri'] = Mage::getUrl('simiconnector/rest/v2/simiklarnaapis/success');
		    // You can not receive push notification on non publicly available uri
		    $pushUrl = Mage::getUrl('simiconnector/rest/v2/simiklarnaapis/push?klarna_order={checkout.order.id}', array('_nosid' => true));
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
		$create['cart'] = array();
		Mage::register('simicart_data', $this->getData());
		Mage::register('klarna_checkout_data', $create);
		Mage::register('klarna_secret', Mage::getStoreConfig('payment/simiklarna/secret_key'));
		Mage::register('klarna_is_test', Mage::getStoreConfig('payment/simiklarna/enable_auth_query'));
		
        $value = $this->getRequest()->getParam('data');
        if (!$value) {
			die('1');
            $this->_redirect('checkout/cart');
            return;
        }
        $data = json_decode($value, true);
        $simiklarna = Mage::getModel('simiklarna/simiklarna');
        $checkout = $simiklarna->checkout($data);
        if ($checkout['status'] == true) {
            echo '<div>' . $checkout["html"] . '</div>';
        } else {
			die('2');
            $this->_redirect('checkout/cart');
        }

        exit();
    }

    public function pushAction() {
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

    public function successAction() {
        $simiklarna = Mage::getModel('simiklarna/simiklarna');
        $information = $simiklarna->confirmation();
        $this->_printDataJson($information);
    }

}
