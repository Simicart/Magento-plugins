<?php

class Simi_Simicustompayment_Model_Simiobserver {

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('bankpayment', 3);
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
        if (isset($data['payment_method']) && $data['payment_method'] == "bankpayment") {
            $data['url_action'] = $this->getOrderPlaceRedirectUrl($data['invoice_number']);
        }
        $orderObject->order_placed_info = $data;
    }

    public function getOrderPlaceRedirectUrl($order_id) {
        $value = Mage::app()->getRequest()->getParam('data');
        $data = json_decode($value);
        if (!$data) {
            $json = urldecode($value);
            $data = json_decode($json);
        }
        $email = 'none';
        $password = 'none';
        if ((is_object($data)) && isset($data->user_email) && isset($data->user_password)) {
            $email = $data->user_email;
            $password = $data->user_password;
        }

        return Mage::getUrl('simicustompayment/api/placement', array('_secure' => true,
                    'OrderID' => base64_encode($order_id),
                    'Amount' => base64_encode(Mage::getSingleton('checkout/session')->getAmount()),
                    'CurrencyCode' => base64_encode(Mage::getSingleton('checkout/session')->getCurrencycode()),
                    'TransactionType' => base64_encode(Mage::getSingleton('checkout/session')->getTransactiontype()),
                    'TransactionDateTime' => base64_encode(Mage::getSingleton('checkout/session')->getTransactiondatetime()),
                    'CallbackURL' => base64_encode(Mage::getSingleton('checkout/session')->getCallbackurl()),
                    'OrderDescription' => base64_encode(Mage::getSingleton('checkout/session')->getOrderdescription()),
                    'CustomerName' => base64_encode(Mage::getSingleton('checkout/session')->getCustomername()),
                    'Address1' => base64_encode(Mage::getSingleton('checkout/session')->getAddress1()),
                    'Address2' => base64_encode(Mage::getSingleton('checkout/session')->getAddress2()),
                    'Address3' => base64_encode(Mage::getSingleton('checkout/session')->getAddress3()),
                    'Address4' => base64_encode(Mage::getSingleton('checkout/session')->getAddress4()),
                    'City' => base64_encode(Mage::getSingleton('checkout/session')->getCity()),
                    'State' => base64_encode(Mage::getSingleton('checkout/session')->getState()),
                    'PostCode' => base64_encode(Mage::getSingleton('checkout/session')->getPostcode()),
                    'LastRealOrderId' => base64_encode(Mage::getSingleton('checkout/session')->getLastRealOrderId()),
                    //'Email' => base64_encode($email),
                    //'Password' => base64_encode($password),
                    'Payforttransactionid' => base64_encode(Mage::getSingleton('core/session')->getPayFortCwTransactionId()),
        ));
    }

}
