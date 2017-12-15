<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Paypalexpress_Model_Api_Ppexpressapis extends Simi_Simiconnector_Model_Api_Abstract {

    protected $_configType = 'paypal/config';
    protected $_configMethod = Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS;
    protected $_checkoutType = 'paypal/express_checkout';
    protected $_paramMobile = "&useraction=commit";
    protected $_checkout = null;
    protected $_config = null;
    protected $_quote = false;

    public function setBuilderQuery() {
        $this->_config = Mage::getModel($this->_configType, array($this->_configMethod));
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            
        }
    }

    /*
     * Get Paypal API information
     */

    public function show() {
        $data = $this->getData();
        $result = array();
        if (isset($data['resourceid'])) {
            if ($data['resourceid'] == 'start') {
                $result['ppexpressapi'] = $this->startPayment();
            } else if ($data['resourceid'] == 'return') {
                $this->_initCheckout();
                $this->_checkout->returnFromPaypal($this->_initToken());
                $result['ppexpressapi'] = array();
            } else if ($data['resourceid'] == 'checkout_address') {
                $this->_initCheckout();
                $this->_checkout->returnFromPaypal($this->_initToken());
                $this->_checkout->prepareOrderReview($this->_initToken());
                $expressModel = Mage::getModel("paypalexpress/simiconnectorexpress");
                $expressModel->setQuote($this->_getQuote());
                $addressSelected = $expressModel->getBillingShippingAddress();
                $result['ppexpressapi'] = $addressSelected['data'][0];
            } else if ($data['resourceid'] == 'shipping_methods') {
                $this->_initCheckout();
                $this->_checkout->returnFromPaypal($this->_initToken());
                $this->_checkout->prepareOrderReview($this->_initToken());
                $info['methods'] = Mage::helper('simiconnector/checkout_shipping')->getMethods();
                $result['ppexpressapi'] = $info;
            }
        }
        return $result;
    }

    /*
     * Update paypal checkout information
     */

    public function update() {
        $data = $this->getData();
        $result = array();
        $parameters = (array) $data['contents'];
        if (isset($data['resourceid'])) {
            if ($data['resourceid'] == 'checkout_address') {
                $expressModel = Mage::getModel("paypalexpress/simiconnectorexpress");
                $expressModel->setQuote($this->_getQuote());
                $info = $expressModel->updateAddress($parameters);
                $this->_initCheckout();
                $this->_checkout->returnFromPaypal($this->_initToken());
                $this->_checkout->prepareOrderReview($this->_initToken());
                $addressSelected = $expressModel->getBillingShippingAddress();
                $result['ppexpressapi'] = $addressSelected['data'];
            }
        }
        return $result;
    }

    /*
     * Place Order
     */

    public function store() {
        $data = $this->getData();
        $result = array();
        $order = array();
        $parameters = (array) $data['contents'];
        if (!isset($parameters['s_method']) || !$parameters['s_method']) {
            if ($data['resourceid'] == 'place') {
                $this->_initCheckout();
                $this->_checkout->place($this->_initToken());
                $incrementId = $this->_getCheckoutSession()->getLastRealOrderId();
                $orderId = Mage::getModel('sales/order')->loadByIncrementId($incrementId)->getId();
                Mage::helper('simiconnector/checkout')->processOrderAfter($orderId, $order);
                $session = $this->_getCheckoutSession();
                $session->clearHelperData();
                $order['message'] = Mage::helper('checkout')->__("Thank you for your purchase!");
            }
        } else if (isset($data['resourceid'])) {
            if ($data['resourceid'] == 'place') {
                $this->_initCheckout();
                $this->_checkout->place($this->_initToken(), $parameters['s_method']->method);
                $orderDetail = $this->_checkout->getOrder();
                Mage::helper('simiconnector/checkout')->processOrderAfter($orderDetail->getId(), $order);
                $session = $this->_getCheckoutSession();
                $session->clearHelperData();
                $order['message'] = Mage::helper('checkout')->__("Thank you for your purchase!");
            }
        }
        $result['order'] = $order;
        $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
        $session->clear();
        return $result;
    }

    /*
     * get Paypal start payment information
     */

    public function startPayment() {
        $data = $this->getData();
        $controller = $data['controller'];

        if ((int) Mage::getStoreConfig('paypalexpress/general/enable_app') == 0) {
            throw new Exception(Mage::helper("core")->__("PayPal Express was disabled in App"), 6);
        }
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
        $isBARequested = (bool) $controller->getRequest()
                        ->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
        if ($customer && $customer->getId()) {
            $this->_checkout->setIsBillingAgreementRequested($isBARequested);
        }

        // giropay
        $this->_checkout->prepareGiropayUrls(
                Mage::getUrl('checkout/onepage/success'), Mage::getUrl('paypal/express/cancel'), Mage::getUrl('checkout/onepage/success')
        );
        $token = $this->_checkout->start(Mage::getUrl('*/*/v2/ppexpressapis/return'), Mage::getUrl('*/*/v2/ppexpressapis/cancel'));
        $review_address = Mage::getStoreConfig('paypalexpress/general/enable');
        $this->_initToken($token);
        $url = $this->_checkout->getRedirectUrl();
        $url .= $this->_paramMobile;

        return array("url" => $url,
                "review_address" => $review_address,
        );
    }

    protected function _getQuote() {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    private function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    protected function _initCheckout() {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            Mage::throwException(Mage::helper('paypal')->__('Unable to initialize Express Checkout.'));
        }
        $this->_checkout = Mage::getSingleton($this->_checkoutType, array(
                    'config' => $this->_config,
                    'quote' => $quote,
        ));
    }

    protected function _initToken($setToken = null) {
        if (null !== $setToken) {
            if (false === $setToken) {
                // security measure for avoid unsetting token twice
                if (!$this->_getSession()->getExpressCheckoutToken()) {
                    Mage::throwException(Mage::helper('paypal')->__('PayPal Express Checkout Token does not exist.'));
                }
                $this->_getSession()->unsExpressCheckoutToken();
            } else {
                $this->_getSession()->setExpressCheckoutToken($setToken);
            }
            return $this;
        }
        $data = $this->getData();
        $controller = $data['controller'];
        if ($setToken = $controller->getRequest()->getParam('token')) {
            if ($setToken !== $this->_getSession()->getExpressCheckoutToken()) {
                Mage::throwException(Mage::helper('paypal')->__('Wrong PayPal Express Checkout Token specified.'));
            }
        } else {
            $setToken = $this->_getSession()->getExpressCheckoutToken();
        }
        return $setToken;
    }

    private function _getSession() {
        return Mage::getSingleton('paypal/session');
    }

}
