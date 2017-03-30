<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simiipay88
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Simiipay88 Model
 * 
 * @category    
 * @package     Simiipay88
 * @author      Developer
 */
class Simi_Simiipay88_Model_Simiipay88 extends Mage_Core_Model_Abstract {

    public function eventChangeData($name_event, $value) {
        Mage::dispatchEvent($name_event, $value);
    }

    public function statusSuccess() {
        return array('status' => 'SUCCESS',
            'message' => array('SUCCESS'),
        );
    }

    public function statusError($error = array('NO DATA')) {
        return array(
            'status' => 'FAIL',
            'message' => $error,
        );
    }

    public function _construct() {
        parent::_construct();
        $this->_init('simiipay88/simiipay88');
    }

    public function statusPending() {
        return array(
            'status' => 'PENDING',
        );
    }

    /**
     * payment_status = 2 cancel, invoice_number, transaction_id
     * */
    public function updatePayment($dataComfrim) {

        if ($dataComfrim->status == '2') {
            $this->setOrderCancel($dataComfrim->order_id);
            return $this->statusError(array(Mage::helper('core')->__('The order has been cancelled')));
        }
        $data = array();
        $data['invoice_number'] = $dataComfrim->order_id;
        $data['transaction_id'] = $dataComfrim->transaction_id;
        $data['payment_status'] = $dataComfrim->status;
        $data['auth_code'] = $dataComfrim->auth_code;
        $data['ref_no'] = $dataComfrim->ref_no;

        try {
            if ($this->_initInvoice($data['invoice_number'], $data)) {
                $informtaion = $this->statusSuccess();
                $informtaion['message'] = array(Mage::helper('core')->__('Thank you for your purchase!'));
                return $informtaion;
            } else {
                $informtaion['message'] = array(Mage::helper('core')->__('Cannot Create Invoice'));
                return $informtaion;
            }
        } catch (Exception $e) {
            if (is_array($e->getMessage())) {
                return $this->statusError($e->getMessage());
            } else {
                return $this->statusError(array($e->getMessage()));
            }
        }
    }

    protected function _initInvoice($orderId, $data) {
        $items = array();
        $order = $this->_getOrder($orderId);
        if (!$order)
            return false;
        foreach ($order->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }

        //Zend_debug::dump(get_class_methods($order));die();
        Mage::getModel('simiipay88/simiipay88')
                ->setData('transaction_id', $data['transaction_id'])
                ->setData('auth_code', $data['auth_code'])
                ->setData('ref_no', $data['ref_no'])
                ->setData('status', $data['payment_status'])
                ->setData('order_id', $order->getId())
                ->save();
        Mage::getSingleton('core/session')->setOrderIdForEmail($order->getId());
        /* @var $invoice Mage_Sales_Model_Service_Order */
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($items);
        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
        $invoice->setEmailSent(true)->register();
        //$invoice->setTransactionId();
        Mage::register('current_invoice', $invoice);
        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
        $transactionSave->save();
        //if ($data)
        //$order->sendOrderUpdateEmail();
        $order->sendNewOrderEmail();
        Mage::getSingleton('core/session')->setOrderIdForEmail(null);
        return true;
    }

    protected $_order;

    protected function _getOrder($orderId) {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            if (!$this->_order->getId()) {
                throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));
                return;
            }
        }
        if (!$this->_order->canInvoice())
            return FALSE;
        return $this->_order;
    }

    protected function setOrderCancel($orderIncrementId) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
    }

}
