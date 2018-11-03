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
        // $websiteId = $storeManager->getStore()->getWebsiteId();
        // $customer  = $simiObjectManager->get('Magento\Customer\Model\Customer')
        //     ->setWebsiteId($websiteId);

        // try {
        //     $customer->loadByEmail(base64_decode($this->getRequest()->getParam('Email')));
        //     $loginSession = $session->setCustomerAsLoggedIn($customer);
        // }catch(Exception $e){

        // }
        
        // $checkoutSession->setLastRealOrderId(base64_decode($this->getRequest()->getParam('LastRealOrderId')));
        $orderId = $this->getRequest()->getParam('LastRealOrderId');
        $this->_redirect('simibraintree/index/redirect/',['_secure'=>1,'OrderId'=>$orderId]);
    }
}
