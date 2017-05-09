<?php

/**
 *
 * Copyright © 2016 Simicommerce. All rights reserved.
 */

namespace Simi\Simicustompayment\Controller\Index;

class Placement extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $simiObjectManager = $this->_objectManager;
        $session = $simiObjectManager->get('Magento\Customer\Model\Session');
        $storeManager  = $simiObjectManager->create('\Magento\Store\Model\StoreManagerInterface');
        $websiteId = $storeManager->getStore()->getWebsiteId();
        $customer  = $simiObjectManager->get('Magento\Customer\Model\Customer')
            ->setWebsiteId($websiteId);

        try {
            $customer->loadByEmail(base64_decode($this->getRequest()->getParam('Email')));
            $loginSession = $session->setCustomerAsLoggedIn($customer);
        }catch(Exception $e){

        }
        $checkoutSession = $simiObjectManager->create('Magento\Checkout\Model\Session');
        $checkoutSession->setOrderid(base64_decode($this->getRequest()->getParam('OrderID')));
        $checkoutSession->setMerchantid(base64_decode(($this->getRequest()->getParam('MerchantID'))));
        $checkoutSession->setAmount(base64_decode($this->getRequest()->getParam('Amount')));
        $checkoutSession->setCurrencycode(base64_decode($this->getRequest()->getParam('CurrencyCode')));
        $checkoutSession->setTransactiontype(base64_decode($this->getRequest()->getParam('TransactionType')));
        $checkoutSession->setTransactiondatetime(base64_decode($this->getRequest()->getParam('TransactionDateTime')));
        $checkoutSession->setOrderdescription(base64_decode($this->getRequest()->getParam('OrderDescription')));
        $checkoutSession->setCustomername(base64_decode($this->getRequest()->getParam('CustomerName')));
        $checkoutSession->setAddress1(base64_decode($this->getRequest()->getParam('Address1')));
        $checkoutSession->setAddress2(base64_decode($this->getRequest()->getParam('Address2')));
        $checkoutSession->setAddress3(base64_decode($this->getRequest()->getParam('Address3')));
        $checkoutSession->setAddress4(base64_decode($this->getRequest()->getParam('Address4')));
        $checkoutSession->setCity(base64_decode($this->getRequest()->getParam('City')));
        $checkoutSession->setState(base64_decode($this->getRequest()->getParam('State')));
        $checkoutSession->setPostcode(base64_decode($this->getRequest()->getParam('PostCode')));
        $checkoutSession->setLastRealOrderId(base64_decode($this->getRequest()->getParam('LastRealOrderId')));
        $checkoutSession->setPayFortCwTransactionId(base64_decode($this->getRequest()->getParam('Payforttransactionid')));

        $this->_redirect('redirect/url/here');
    }
}
