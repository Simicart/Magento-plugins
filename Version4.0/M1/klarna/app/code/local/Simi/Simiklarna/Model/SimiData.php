<?php

class Simi_Simiklarna_Model_Simidata extends Mage_Core_Model_Abstract {

    protected $_data;

    public function _getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    public function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function cleanSession() {
        $session = $this->_getOnepage()->getCheckout();
        $lastOrderId = $session->getLastOrderId();
        $this->_oldQuote = $session->getData('old_quote');
        $session->clear();
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
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

    public function saveOrder($data) {
        $address_cache = Mage::getSingleton('core/session')->getSimiAddress();
        $shipping_method = Mage::getSingleton('core/session')->getSimiShippingMethod();
        $payment_method = array('method' => strtolower('simiklarna'));
        $information = '';
        try {
            $this->_getOnepage()->saveCheckoutMethod($address_cache['checkout_method']);

            Mage::helper('simiklarna')->converDataAddress($data['billing_address'], $address_cache['billing_address']);
            Mage::helper('simiklarna')->converDataAddress($data['shipping_address'], $address_cache['shipping_address']);

            $this->_getOnepage()->saveBilling($address_cache['billing_address'], $address_cache['billing_address']['customer_address_id']);
            $this->_getOnepage()->saveShipping($address_cache['shipping_address'], $address_cache['shipping_address']['customer_address_id']);
            $this->_getCheckoutSession()->getQuote()->getShippingAddress()->collectShippingRates()->save();
            // save shipping method
            $this->_getOnepage()->saveShippingMethod($shipping_method);
            $this->_getOnepage()->getQuote()->collectTotals()->save();


            $this->_getOnepage()->savePayment($payment_method);

            if ($payment_method) {
                $dataPayment = $payment_method;
                if (version_compare(Mage::getVersion(), '1.8.0.0', '>=') === true) {
                    $dataPayment['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                }
            }
            $this->_getOnepage()->getQuote()->getPayment()->importData($dataPayment);
            $this->_getOnepage()->saveOrder();
        } catch (Exception $e) {
            $informtaion = '';
            if (is_array($e->getMessage())) {
                $information = $this->statusError($e->getMessage());
            } else {
                $information = $this->statusError(array($e->getMessage()));
            }
            $this->_getOnepage()->getCheckout()->setUpdateSection(null);
//            $this->cleanSession();
            return $information;
        }
        $this->_getOnepage()->getQuote()->save();

        $this->saveInfor($this->_getCheckoutSession()->getLastRealOrderId(), $data);

        $informtaion = $this->statusSuccess();

        $data_return = array(
            'invoice_number' => $this->_getCheckoutSession()->getLastRealOrderId(),
            'payment_method' => $this->_getOnepage()->getQuote()->getPayment()->getMethodInstance()->getCode(),
        );
        $informtaion['data'] = array($data_return);

        $message_success = Mage::helper('checkout')->__("Thank you for your purchase!");
        $informtaion['message'] = array($message_success);

        Mage::getSingleton('core/session')->unsSimiAddress();
        Mage::getSingleton('core/session')->unsSimiShippingMethod();
        $this->cleanSession();
        return $informtaion;
    }

    public function saveInfor($order_id, $data) {
        $model = Mage::getModel('simiklarna/simiklarna');
        $model->setData('reference', $data['reference']);
        $model->setData('reservation', $data['reservation']);
        $model->setData('order_id', $order_id);
        try {
            $model->save();
        } catch (Exception $e) {
            
        }
    }

}
