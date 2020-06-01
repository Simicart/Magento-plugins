<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simipaypalexpress\Model\Api;

class Ppexpressapis extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public $configType = 'Magento\Paypal\Model\Config';
    public $configMethod = \Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS;
    public $checkoutType = 'Magento\Paypal\Model\Express\Checkout';
    public $paramMobile = "&useraction=commit";
    public $checkout = null;
    public $config = null;
    public $quote = false;

    public function setBuilderQuery()
    {
        $this->config = $this->simiObjectManager->create($this->configType, array($this->configMethod));
        $this->config->setMethod($this->configMethod);
        $data = $this->getData();
        if ($data['resourceid']) {
        } else {
        }
    }

    /*
     * Get Paypal API information
     */

    public function show()
    {
        $data = $this->getData();
        $result = array();
        if (isset($data['resourceid'])) {
            if ($data['resourceid'] == 'start') {
                $result['ppexpressapi'] = $this->startPayment();
            } elseif ($data['resourceid'] == 'return') {
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                $result['ppexpressapi'] = array();
//                for pwa
//                $controller = $data['controller'];
//                $controller->getResponse()->setRedirect('/checkout/onepage?placeOrder=paypal');
            } elseif ($data['resourceid'] == 'checkout_address') {
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                $this->checkout->prepareOrderReview($this->_initToken());
                $expressModel = $this->simiObjectManager->create("Simi\Simipaypalexpress\Model\Simiconnectorexpress");
                $expressModel->setQuote($this->_getQuote());
                $addressSelected = $expressModel->getBillingShippingAddress();
                $result['ppexpressapi'] = $addressSelected['data'][0];
            } elseif ($data['resourceid'] == 'shipping_methods') {
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                $this->checkout->prepareOrderReview($this->_initToken());
                $info['methods'] = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Checkout\Shipping')
                    ->getMethods();
                $result['ppexpressapi'] = $info;
            } elseif ($data['resourceid'] == 'placeOrder') {
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                // $controller = $data['controller'];
                // $controller->getResponse()->setRedirect('/paypal/express/review/');
                $result = array();
                $session = $this->_getCheckoutSession();

                $this->checkout->place($this->_initToken());
                $order = $this->checkout->getOrder();
                $session->clearHelperData();
                if ($order && $order->getIncrementId()) {
                    $orderId = $order->getIncrementId();
                    $result['order'] = ['invoice_number' => $orderId];
                    $this->cleanCheckoutSession();
                }
            } elseif ($data['resourceid'] == 'cancel') {
                # code...
                $controller = $data['controller'];
                $controller->getResponse()->setRedirect('/checkout/cart');
            } elseif (strpos($data['resourceid'], 'return') !== false) { //for pwa-studio
                $controller = $data['controller'];
                $quoteId = explode('_', $data['resourceid']);
                $quoteId = $quoteId[1];
                $quoteModel = $this->simiObjectManager->get('Magento\Quote\Model\Quote')->load($quoteId);
                if ($quoteModel->getId() && $quoteModel->getData('is_active')) {
                    $this->quote = $quoteModel;
                    $this->_initCheckout();
                    $this->checkout->returnFromPaypal($this->_initToken());
                    if ($pwa_url = $this->getStoreConfig('simiconnector/general/pwa_studio_url')) {
                        $pwa_url = $this->endsWith($pwa_url, '/')?$pwa_url:$pwa_url.'/';
                        $controller->getResponse()->setRedirect($pwa_url . 'paypal_express.html?placeOrder=true&token=' . $controller->getRequest()->getParam('token'));
                    }
                }
            } elseif (strpos($data['resourceid'], 'cancel')  !== false) { //for pwa-studio
                $controller = $data['controller'];
                if ($pwa_url = $this->getStoreConfig('simiconnector/general/pwa_studio_url')) {
                    $pwa_url = $this->endsWith($pwa_url, '/')?$pwa_url:$pwa_url.'/';
                    $controller->getResponse()->setRedirect($pwa_url . 'paypal_express.html?paymentFaled=true');
                }
            }
        }
        return $result;
    }


    public function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }

    /*
     * Update paypal checkout information
     */

    public function update()
    {
        $data = $this->getData();
        $result = array();
        $parameters = (array) $data['contents'];
        if (isset($data['resourceid'])) {
            if ($data['resourceid'] == 'checkout_address') {
                $expressModel = $this->simiObjectManager->create("Simi\Simipaypalexpress\Model\Simiconnectorexpress");
                $expressModel->setQuote($this->_getQuote());
                $info = $expressModel->updateAddress($parameters);
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                $this->checkout->prepareOrderReview($this->_initToken());
                $addressSelected = $expressModel->getBillingShippingAddress();
                $result['ppexpressapi'] = $addressSelected['data'];
            }
        }
        return $result;
    }

    /*
     * Place Order
     */

    public function store()
    {
        $data = $this->getData();
        $result = array();
        $orderInfo = array();
        $parameters = (array) $data['contents'];
        if (!isset($parameters['s_method'])) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Please select Shipping Method'), 6);
        }
        if (isset($data['resourceid'])) {
            if ($data['resourceid'] == 'place') {
                $this->_initCheckout();

                $quote = $this->_getQuote();
                if (!$this->simiObjectManager->create('Magento\Customer\Model\Session')->isLoggedIn() && $quote->getPasswordHash()) {
                    $billingAddress   = $quote->getBillingAddress();
                    $customer         = $this->simiObjectManager->create('Magento\Customer\Model\Data\Customer')
                        ->setFirstname($billingAddress->getFirstname())
                        ->setLastname($billingAddress->getLastname())
                        ->setEmail($billingAddress->getEmail());
                    // $this->simiObjectManager->get('Simi\Simiconnector\Helper\Customer')->applyDataToCustomer($customer, $data);

                    $password = null;
                    if ($billingAddress->getData('customer_password')) {
                        $password = $billingAddress->getData('customer_password');
                    }
                    $customer = $this->simiObjectManager->get('Magento\Customer\Api\AccountManagementInterface')->createAccount($customer, $password, '');
                    $addressDataArray = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Address')
                    ->getAddressDetail($billingAddress, $customer);
                    foreach ($addressDataArray as $index => $dataAddressItem) {
                        $customer->setData($index, $dataAddressItem);
                    }
                    $quote->setCustomer($customer);
                }

                $this->checkout->place($this->_initToken(), $parameters['s_method']->method);
                $session = $this->_getCheckoutSession();
                $session->clearHelperData();
                $orderInfo['message'] = __("Thank you for your purchase!");
                $order = $this->checkout->getOrder();
                if ($order && $order->getIncrementId()) {
                    $orderId = $order->getIncrementId();
                    $orderInfo['invoice_number'] = $orderId;
                }
            }
        }
        $result['order'] = $orderInfo;
        $this->cleanCheckoutSession();
        return $result;
    }

    public function cleanCheckoutSession()
    {
        try {
            $quote = $this->_getQuote();
            $quote->setIsActive(false);
            $quote->delete();
        } catch (\Exception $e) {
            $this->_getCheckoutSession()->clearQuote()->clearStorage();
        }
        $checkoutSession = $this->_getCheckoutSession();
        $checkoutSession->clearQuote();
        $checkoutSession->clearStorage();
        $checkoutSession->clearHelperData();
        $checkoutSession->resetCheckout();
        $checkoutSession->restoreQuote();
    }

    
    /*
     * get Paypal start payment information
     */

    public function startPayment()
    {
        $data = $this->getData();
        $controller = $data['controller'];

        $quote = $this->_getQuote();

        try { //when quote total dismatch the ordertotal, paypal would throw error, need to update quote address again
            if ($quote->isMultipleShippingAddresses()) {
                foreach ($quote->getAllShippingAddresses() as $address) {
                    $quote->removeAddress($address->getId());
                }

                $shippingAddress = $quote->getShippingAddress();
                $defaultShipping = $quote->getCustomer()->getDefaultShipping();
                if ($defaultShipping) {
                    $defaultCustomerAddress = $this->simiObjectManager->create('\Magento\Customer\Api\AddressRepositoryInterface')->getById(
                        $defaultShipping
                    );
                    $shippingAddress->importCustomerAddressData($defaultCustomerAddress);
                }
                $this->simiObjectManager->create('\Magento\Quote\Api\CartRepositoryInterface')->save($quote);
            }
        } catch (\Exception $e) {
        }

        if ((int) $this->getStoreConfig('simipaypalexpress/general/enable_app') == 0) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('PayPal Express was disabled in App'), 6);
        }
        $this->_initCheckout();

        if ($this->_getQuote()->getIsMultiShipping()) {
            $this->_getQuote()->setIsMultiShipping(false);
            $this->_getQuote()->removeAllAddresses();
        }

        $customer = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getCustomer();
        if ($customer && $customer->getId()) {
            $customerData = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getCustomerDataObject();
            $billingAddress = $this->_getQuote()->getBillingAddress();
            $shippingAddress = $this->_getQuote()->getShippingAddress();
            $customerAddresses = $this->getCustomerAddresses();
            if (!$billingAddress || ($billingAddress && (!$billingAddress->getFirstname() || $billingAddress->getCustomerId() != $customer->getId()))) {
                $billingAddressId = null;
                if ($customer->getDefaultBilling()) {
                    $billingAddressId = $customer->getDefaultBilling();
                } elseif ($customerAddresses->getSize() > 0) {
                    $billingAddressId = $customerAddresses->getFirstItem()->getId();
                }
                if ($billingAddressId) {
                    $this->simiObjectManager->create('Simi\Simiconnector\Helper\Address')->saveBillingAddress((object) ['entity_id' => $billingAddressId]);
                }
            }
            if (!$shippingAddress || ($shippingAddress && (!$shippingAddress->getFirstname() || $shippingAddress->getCustomerId() != $customer->getId()))) {
                $shippingAddressId = null;
                if ($customer->getDefaultShipping()) {
                    $shippingAddressId = $customer->getDefaultBilling();
                } elseif ($customerAddresses->getSize() > 0) {
                    $shippingAddressId = $customerAddresses->getFirstItem()->getId();
                }
                if ($shippingAddressId) {
                    $this->simiObjectManager->create('Simi\Simiconnector\Helper\Address')->saveShippingAddress((object) ['entity_id' => $shippingAddressId]);
                }
            }
            $this->checkout->setCustomerWithAddressChange(
                $customerData,
                $this->_getQuote()->getBillingAddress(),
                $this->_getQuote()->getShippingAddress()
            );
            // if (!$this->_getQuote()->getShippingAddress()->getFirstname() && $customer->getDefaultShipping()) {
            //     $expressModel = $this->simiObjectManager->create("Simi\Simipaypalexpress\Model\Simiconnectorexpress");
            //     $expressModel->setQuote($this->_getQuote());
            //     $expressModel->updateAddress([
            //         's_address' => [
            //             'entity_id' => $customer->getDefaultShipping()
            //         ]
            //     ]);
            // }
        }

        // billing agreement
        $isBARequested = (bool) $controller->getRequest()
                        ->getParam(\Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
        if ($customer && $customer->getId()) {
            $this->checkout->setIsBillingAgreementRequested($isBARequested);
        }
        
        // giropay
        $this->checkout->prepareGiropayUrls(
            $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('checkout/onepage/success'),
            $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('paypal/express/cancel'),
            $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('checkout/onepage/success')
        );

        $returnresourceid = 'return';
        if ($controller->getRequest()->getParam('quote_id')) { //pwa-studio
            $returnresourceid .= ('_'.$this->_getQuote()->getId());
        }
        $cancelresourceid = 'cancel';
        if ($controller->getRequest()->getParam('quote_id')) { //pwa-studio
            $cancelresourceid .= ('_'.$this->_getQuote()->getId());
        }
        $token = $this->checkout->start(
            $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('simiconnector/rest/v2/ppexpressapis/'.$returnresourceid),
            $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('simiconnector/rest/v2/ppexpressapis/'.$cancelresourceid)
        );

        $review_address = $this->getStoreConfig('simipaypalexpress/general/enable');
        $this->_initToken($token);
        $url = $this->checkout->getRedirectUrl();
        $url .= $this->paramMobile;

        return array("url" => $url,
                "review_address" => $review_address,
        );
    }

    protected function _getQuote()
    {
        if (!$this->quote) {
            $this->quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->simiObjectManager->get('\Magento\Quote\Api\CartRepositoryInterface')->getActive($this->quote->getId());
    }

    private function _getCheckoutSession()
    {
        return $this->simiObjectManager->get('\Magento\Checkout\Model\Session');
    }

    protected function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Unable to initialize Express Checkout.'), 4);
        }
        $this->checkout =  $this->simiObjectManager->create(
            $this->checkoutType,
            [
                'params' => [
                    'config' => $this->config,
                    'quote' => $quote,
                ]
            ]
        );
    }

    protected function _initToken($setToken = null)
    {
        if (null !== $setToken) {
            if (false === $setToken) {
                // security measure for avoid unsetting token twice
                if (!$this->_getSession()->getExpressCheckoutToken()) {
                    throw new \Simi\Simiconnector\Helper\SimiException(__('PayPal Express Checkout Token does not exist.'), 4);
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
            if ($this->_getSession()->getExpressCheckoutToken() && $setToken !== $this->_getSession()->getExpressCheckoutToken()) {
                throw new \Simi\Simiconnector\Helper\SimiException(__('Wrong PayPal Express Checkout Token specified.'), 4);
            }
        } else {
            $setToken = $this->_getSession()->getExpressCheckoutToken();
        }
        return $setToken;
    }

    private function _getSession()
    {
        return $this->simiObjectManager->get('\Magento\Framework\Session\Generic');
    }

    private function getCustomerAddresses()
    {
        $customer     = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getCustomer();
        $addressArray = [];
        $billing      = $customer->getPrimaryBillingAddress();
        if ($billing) {
            $addressArray[] = $billing->getId();
        }
        $shipping = $customer->getPrimaryShippingAddress();
        if ($shipping) {
            $addressArray[] = $shipping->getId();
        }
        foreach ($customer->getAddresses() as $index => $address) {
            $addressArray[] = $index;
        }
        return $this->simiObjectManager
                        ->create('Magento\Customer\Model\Address')->getCollection()
                        ->addFieldToFilter('entity_id', ['in' => $addressArray]);
    }
}
