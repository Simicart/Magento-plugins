<?php

class Simi_Simicustompayment_Model_Simiobserver {

    public function addPayment40($observer) {
        if (Mage::getSingleton('core/session')->getData('has_custom_payment')) {
            $object = $observer->getObject();
            $object->addPaymentMethod('payfort', 3);
            $object->addPaymentMethod('payfortcc', 3);
            $object->addPaymentMethod('payfortsadad', 3);
            $object->addPaymentMethod('payfortnaps', 3);
            $object->addPaymentMethod('payfortinstallments', 3);
        }
        return;
    }

    public function connectorConfigGetPluginsReturnCustompayment($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'customizepayments') {
            $observerObjectData['module'] = 'simicustompayment';
        }
        $observerObject->setData($observerObjectData);
    }

    public function simiconnectorAfterPlaceOrder($observer) {
        $orderObject = $observer->getObject();
        $data = $orderObject->order_placed_info;
        if (isset($data['payment_method']) && 
                (
                    $data['payment_method'] == "payfort" ||
                    $data['payment_method'] == "payfortcc" ||
                    $data['payment_method'] == "payfortsadad" ||
                    $data['payment_method'] == "payfortnaps" ||
                    $data['payment_method'] == "payfortinstallments"
                )
            ) {
            $data['url_action'] = $this->getOrderPlaceRedirectUrl($data['invoice_number']);
        }
        $orderObject->order_placed_info = $data;
    }

    public function getOrderPlaceRedirectUrl($order_id) {
        return Mage::getUrl('simicustompayment/api/placement', array('_secure' => true,
                'OrderID' => base64_encode($order_id),
                'LastRealOrderId' => base64_encode(Mage::getSingleton('checkout/session')->getLastRealOrderId()),
                'payfort_option' => base64_encode(Mage::getSingleton('checkout/session')->getData('payfort_option')),
        ));
    }

}
