<?php

class Simi_Simiklarna_Block_Simiklarna extends Mage_Payment_Block_Info_Cc
{
	protected $_tranS;
    

    protected function _prepareSpecificInformation($transport = null) {
		// die("xxxxxxxx");
        $orderId = Mage::app()->getRequest()->getParam('order_id');
        $invoiceId = Mage::app()->getRequest()->getParam('invoice_id');
		
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            $orderId = $invoice->getOrderId();
        } elseif (Mage::getSingleton('core/session')->getOrderIdForEmail()) {
            $orderId = Mage::getSingleton('core/session')->getOrderIdForEmail();
        }
        $order = Mage::getModel('sales/order')->load($orderId);
        
        $train = Mage::getModel('simiklarna/simiklarna')->getCollection()
                ->addFieldToFilter('order_id', $order->getRealOrderId())
                ->getLastItem();
        // $this->_tranS = $train;
      
        $info = null;
        $transport = parent::_prepareSpecificInformation($transport);
        if (count($train->getData())) {
            $info = array('Reference' => $train->getReference(),
					"Reservation" => $train->getReservation(),
                    );
        } else {
            $info = array('Notice' => 'Pending');
        }

        return $transport->addData($info);
    }

    public function getCcTypeName() {
        return '';
    }
	
}