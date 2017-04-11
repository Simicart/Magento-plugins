<?php

/**
 *
 * Copyright © 2016 Simicommerce. All rights reserved.
 */

namespace Simi\Simicheckoutcom\Controller\Index;

class StartCheckoutcom extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $simiObjectManager = $this->_objectManager;
        $simiObjectManager->get('Simi\Simicheckoutcom\Helper\Data')->getPaymentToken($orderId);
        exit();
    }
}
