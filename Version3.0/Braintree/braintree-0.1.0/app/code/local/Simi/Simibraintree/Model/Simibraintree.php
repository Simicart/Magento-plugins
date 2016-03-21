<?php
/**
 *
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simibraintree
 * @copyright   Copyright (c) 2012 
 * @license    
 */

/**
 * Simibraintree Model
 * 
 * @category    
 * @package     Simibraintree
 * @author      Developer
 */
class Simi_Simibraintree_Model_Simibraintree extends Simi_Connector_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('simibraintree/simibraintree');
    }

    public function statusPending() {
        return array(
            'status' => 'PENDING',
        );
    }

    public function updateBraintreePayment($data) {                
        $result = Mage::helper('simibraintree')->createTransaction($data);    
        if (isset($result->success) && $result->success == 1) {
            $transaction = $result->transaction;
            $transaction = array(
                "transaction_id" => $transaction->id,
                "status" => $transaction->status,
                "order_id" => $data->order_id,
                "type" => $transaction->type,
                "is_closed" => "0",
                "currency_code" => $transaction->currencyIsoCode,
                "amount" => $transaction->amount,
                "merchant_id" => $transaction->merchantAccountId,
            );                                        
            try {
                if ($this->_initInvoice($data->order_id, $transaction)){
                    $informtaion = $this->statusSuccess();                                    
                    $informtaion['message'] = array(Mage::helper('core')->__('Thank you for your purchase!'));
                    return $informtaion;
                }            
                    else{
                        return $this->statusPending();
                }                               
            } catch (Exception $e) {
                if (is_array($e->getMessage())) {
                    return $this->statusError($e->getMessage());
                } else {
                    return $this->statusError(array($e->getMessage()));
                }
            }             
        } else if ($result->transaction) {        
            return $this->statusError(array($result->message));
        } else {            
            return $this->statusError(array($result->message));
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
        Mage::getModel('simibraintree/simibraintree')
                ->setData('transaction_id', $data['transaction_id'])
                ->setData('transaction_name', 'Braintree')                
                ->setData('transaction_email', $order->getData('customer_email'))
                ->setData('amount', $data['amount'])
                ->setData('currency_code', $data['currency_code'])
                ->setData('status', $data['status'])
                ->setData('order_id', $order->getId())
                // ->setData('transaction_dis', $data['last_four_digits'])
                ->save()
                ;                      
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