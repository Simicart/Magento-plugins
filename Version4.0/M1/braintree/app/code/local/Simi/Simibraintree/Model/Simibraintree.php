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
class Simi_Simibraintree_Model_Simibraintree extends Mage_Core_Model_Abstract {

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
                if ($this->_initInvoice($data->order_id, $transaction)) {
                    $informtaion = $this->statusSuccess();
                    $informtaion['message'] = array(Mage::helper('core')->__('Thank you for your purchase!'));
                    return $informtaion;
                } else {
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

    public function updateBraintreePayment40($data) {
        $result = Mage::helper('simibraintree')->createTransaction($data);
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
            if ($this->_initInvoice($data->order_id, $transaction)) {
                return Mage::helper('core')->__('Thank you for your purchase!');
            }
        Mage::log($result,null,'simibraintree.log');
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
            if ($this->_initInvoice($data->order_id, $transaction)) {
                return Mage::helper('core')->__('Thank you for your purchase!');
            }
        }
        else throw new Exception($result->message, 4);
        return Mage::helper('core')->__('Update Transaction Failed');
    }

    protected function _initInvoice($orderId, $data) {
        $items = array();
        $order = $this->_getOrder($orderId);
        if (!$order)
            return false;
        foreach ($order->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING)
        ->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING)
        ->save();
        $order->save();
        Mage::getModel('simibraintree/simibraintree')
                ->setData('transaction_id', $data['transaction_id'])
                ->setData('transaction_name', 'Braintree')
                ->setData('transaction_email', $order->getData('customer_email'))
                ->setData('amount', $data['amount'])
                ->setData('currency_code', $data['currency_code'])
                ->setData('status', $data['status'])
                ->setData('order_id', $order->getId())
                ->save()
        ;
        Mage::getSingleton('core/session')->setOrderIdForEmail($order->getId());
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

    public function getControllerName() {
        $request = Mage::app()->getFrontController()->getRequest();
        $name = $request->getRequestedRouteName() . '_' .
                $request->getRequestedControllerName() . '_' .
                $request->getRequestedActionName();
        return $name;
    }

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

}
