<?php


namespace Simi\Simibarclays\Controller\Index;

use \Magento\Sales\Model\Order;

class Success extends \Magento\Framework\App\Action\Action
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
        $token = $this->getRequest()->getParam('token');
        $transaction = $simiObjectManager->create('Simi\Simibarclays\Model\Simibarclays')->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('token', $token)
            ->getFirstItem();
        if ($transaction->getId() && $transaction->getData('status') == 'pending'){
            $transaction->setData('status', 'completed');
            $transaction->save();
            $orderState = Order::STATE_PROCESSING;
            $orderModel->setState($orderState)->setStatus(Order::STATE_PROCESSING);
            $orderModel->save();
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('checkout/onepage/success?orderId='.$orderId, array(
                '_secure' => true
            )
        );
    }
}
