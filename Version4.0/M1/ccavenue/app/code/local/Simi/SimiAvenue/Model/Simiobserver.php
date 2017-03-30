<?php

/**
 */
class Simi_Simiavenue_Model_Simiobserver {
    /*
     * simiconnector 4.0 
     */

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('simiavenue', 3);
        return;
    }

    public function simiconnectorAfterPlaceOrder($observer) {
        $orderObject = $observer->getObject();
        $data = $orderObject->order_placed_info;
        if (isset($data['payment_method']) && $data['payment_method'] == "simiavenue") {
            $data['url_action'] = Mage::helper("simiavenue")->getFormFields($data['invoice_number']);
        }
        $orderObject->order_placed_info = $data;
    }

}
