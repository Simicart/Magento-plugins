<?php

class Simi_PayPalExpress_Model_Observer {
    /*
     * simiconnector 4.0 
     */

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('paypal_express', 2);
        return;
    }

    public function preventOrderPlacing($observer) {
        $orderApiModel = $observer->getObject();
        if ((Mage::getSingleton('checkout/type_onepage')->getQuote()->getPayment()->getMethod()) && (Mage::getSingleton('checkout/type_onepage')->getQuote()->getPayment()->getMethodInstance()->getCode() == 'paypal_express')) {
            $paymentRedirect = Mage::getSingleton('checkout/type_onepage')->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($paymentRedirect && $paymentRedirect != '') {
                $orderApiModel->order_placed_info = array(
                    //'payment_redirect_url' => $paymentRedirect,
                    'payment_redirect' => 1,
                    'payment_method' => 'paypal_express'
                );
            }
            $orderApiModel->place_order = FALSE;
        }
    }

    public function connectorConfigGetPluginsReturnPaypalExpress($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();   
        if ($observerObjectData['resource'] == 'ppexpressapis') {
            $observerObjectData['module'] = 'paypalexpress';
        }   
        $observerObject->setData($observerObjectData);
    }

    /*
     * end 4.0 events hanlding
     */
}
