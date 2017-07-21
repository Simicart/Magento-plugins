<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simipayfort
 * @copyright   Copyright (c) 2017 
 * @license     
 */

/**
 * Simipayfort Model
 * 
 * @category    
 * @package     Simipayfort
 * @author      Developer
 */
class Simi_Simipayfort_Model_Simipayfort extends Mage_Core_Model_Abstract {

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
        $this->_init('simipayfort/simipayfort');
    }

    public function statusPending() {
        return array(
            'status' => 'PENDING',
        );
    }

    public function updatePayment($dataComfrim) {
        $data = array();
        $data['invoice_number'] = $dataComfrim->invoice_number;
        //$data['transaction_id'] = $dataComfrim->transaction_id;
        $data['payment_status'] = Mage::getStoreConfig('payment/simipayfort/payment_success_status');
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
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING)
        ->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING)
        ->save();
        $order->save();

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

    //test function 
    public function getCart($data) {
        $informtaion = $this->statusSuccess();
        $data_return = array(
            'invoice_number' => "100001049",
            'payment_method' => "twout",
        );

        $event_name = 'simicart_after_place_order';
        $event_value = array(
            'object' => $this,
        );

        $data_change = $this->changeData($data_return, $event_name, $event_value);
        $informtaion['data'] = array($data_change);
        return $informtaion;
    }

    public function changeData($data_change, $event_name, $event_value) {
        $this->_data = $data_change;
        // dispatchEvent to change data
        $this->eventChangeData($event_name, $event_value);
        return $this->getCacheData();
    }

    public function setCacheData($data, $module_name = '') {
        if ($module_name == "simi_connector") {
            $this->_data = $data;
            return;
        }
        if ($module_name == '' || is_null(Mage::getModel('connector/plugin')->checkPlugin($module_name)))
            return;
        $this->_data = $data;
    }

    public function getCacheData() {
        return $this->_data;
    }

    protected $_data;

    //end test
}
