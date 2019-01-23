<?php

/**
 *
 * Copyright © 2016 Simicommerce. All rights reserved.
 */

namespace Simi\Simibarclays\Controller\Index;

class Failure extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $simiObjectManager = $this->_objectManager;
        $orderId = $this->getRequest()->getParam('orderId');
        $orderModel = $simiObjectManager->get('\Magento\Sales\Model\Order')->getCollection()
            ->addAttributeToFilter('increment_id', $orderId)
            ->getFirstItem();
        if(!$orderModel->getId()) {
            echo 'No Order Found!';
            die;
        }
        $transaction = $simiObjectManager->create('Simi\Simibarclays\Model\Simibarclays')->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->getFirstItem();
        if ($transaction->getId() && $transaction->getData('status') == 'pending'){
            $transaction->setData('status', 'failure');
            $transaction->save();
            $orderState = Order::STATE_CANCELED;
            $orderModel->setState($orderState)->setStatus(Order::STATE_CANCELED);
            $orderModel->save();
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('/', array(
            '_secure' => true,
            'warning_message' => urlencode(__('Sorry, payment failure'))
        ));
    }
}
