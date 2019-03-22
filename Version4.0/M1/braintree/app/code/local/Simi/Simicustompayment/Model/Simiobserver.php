<?php

class Simi_Simicustompayment_Model_Simiobserver {

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('simibraintree', 3);
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
        if (isset($data['payment_method']) && $data['payment_method'] == "simibraintree") {
            $data['url_action'] = $this->getOrderPlaceRedirectUrl($data['invoice_number']);
        }
        $orderObject->order_placed_info = $data;
    }

    public function getOrderPlaceRedirectUrl($order_id) {
        return Mage::getUrl('simicustompayment/api/placement', array('_secure' => true,
                    'OrderID' => base64_encode($order_id),
                    'LastRealOrderId' => base64_encode(Mage::getSingleton('checkout/session')->getLastRealOrderId()),
        ));
    }

}
