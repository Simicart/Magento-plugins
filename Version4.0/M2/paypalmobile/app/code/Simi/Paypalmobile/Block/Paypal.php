<?php

namespace Simi\Paypalmobile\Block;

class Paypal extends \Magento\Payment\Block\Info\Cc
{
    protected $_tranS;
    protected $_objectManager;
    
    protected function _prepareSpecificInformation($transport = null) {
        $orderId = $this->getRequest()->getParam('order_id');
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if ($invoiceId) {
            $invoice = $this->_objectManager->get('Magento\Sales\Model\Order\Invoice')->load($invoiceId);
            $orderId = $invoice->getOrderId();
        } elseif ($this->_objectManager->get('\Magento\Checkout\Model\Session')->getOrderIdForEmail()) {
            $orderId = $this->_objectManager->get('\Magento\Checkout\Model\Session')->getOrderIdForEmail();
        }
        $train = $this->_objectManager->get('Simi\Paypalmobile\Model\Paypalmobile')->getCollection()
                ->addFieldToFilter('order_id', $orderId)
                ->getLastItem();
        $this->_tranS = $train;
        $info = null;
        $transport = parent::_prepareSpecificInformation($transport);
        if (count($train->getData())) {
            $info = array('Transaction ID' => $train->getTransactionId(),
                'Fund Source Type' => $train->getTransactionName()
                    );
        } else {
            $info = array('Notice' => 'Pending');
        }

        return $transport->addData($info);
    }
    
}
