<?php
/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category
 * @package     Paytabsmobile
 * @copyright   Copyright (c) 2012
 * @license
 */

/**
 * Paytabsmobile Model
 *
 * @category
 * @package     Paytabsmobile
 * @author      Developer
 */
class Simi_Paytabsmobile_Model_Paytabsmobile extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('paytabsmobile/paytabsmobile');
    }

    public function updatePaytabsPaymentv2($data)
    {
        if ($order = $this->_initInvoicev2($data['invoice_number'], $data)) {
            return array('order' => $order, 'message' => Mage::helper('core')->__('Thank you for your purchase!'));
        } else {
            return array('message' => 'The order has been pending');
        }
    }

    protected function _initInvoicev2($orderId, $data)
    {
        $items = array();
        $order = $this->_getOrder($orderId);
        if (!$order) {
            throw new Exception(Mage::helper('core')->__('The order is not existed'), 4);
            return;
        }
        foreach ($order->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }
        try {
            Mage::getModel('paytabsmobile/paytabsmobile')
                ->setData('transaction_id', $data['transaction_id'])
                ->setData('transaction_name', $data['fund_source_type'])
                    /*
                ->setData('transaction_dis', $data['last_four_digits'])
                ->setData('transaction_email', $data['transaction_email'])
                ->setData('amount', $data['amount'])
                ->setData('currency_code', $data['currency_code'])
                     * 
                     */
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
        } catch (Exception $e) {
            throw new Exception(Mage::helper('core')->__('Unable to save the order'), 4);
        }

        return $order;
    }

    protected $_order;

    protected function _getOrder($orderId)
    {
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

    protected function setOrderCancel($orderIncrementId)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        // $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
        if ($order->getId()) {
            $order->cancel()->save();
        }

        return $order;
    }
}