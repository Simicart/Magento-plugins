<?php

/**
 *
 * Copyright © 2016 Simicommerce. All rights reserved.
 */

namespace Simi\Simicustompayment\Controller\Api;

class Placement extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $simiObjectManager = $this->_objectManager;
        $session = $simiObjectManager->get('Magento\Customer\Model\Session');
        $checkoutSession = $simiObjectManager->create('Magento\Checkout\Model\Session');
        $checkoutSession->setOrderid(base64_decode($this->getRequest()->getParam('OrderID')));
        $checkoutSession->setMerchantid(base64_decode(($this->getRequest()->getParam('MerchantID'))));
        $checkoutSession->setAmount(base64_decode($this->getRequest()->getParam('Amount')));
        $checkoutSession->setCurrencycode(base64_decode($this->getRequest()->getParam('CurrencyCode')));
        $checkoutSession->setTransactiontype(base64_decode($this->getRequest()->getParam('TransactionType')));
        $checkoutSession->setTransactiondatetime(base64_decode($this->getRequest()->getParam('TransactionDateTime')));
        $checkoutSession->setOrderdescription(base64_decode($this->getRequest()->getParam('OrderDescription')));
        $checkoutSession->setCity(base64_decode($this->getRequest()->getParam('City')));
        $checkoutSession->setState(base64_decode($this->getRequest()->getParam('State')));
        $checkoutSession->setPostcode(base64_decode($this->getRequest()->getParam('PostCode')));
        $checkoutSession->setLastRealOrderId(base64_decode($this->getRequest()->getParam('LastRealOrderId')));

        $this->_redirect('redirect/url/here');
    }
}
