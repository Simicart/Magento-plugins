<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Twout
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

/**
 * Twout Model
 * 
 * @category 	
 * @package 	Twout
 * @author  	Developer
 */
class Simi_Simiipay88_Model_Simiobserver {
    /*
     * simiconnector 4.0 
     */

    public function addPayment40($observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('simiipay88', 2);
        return;
    }

    public function connectorConfigGetPluginsReturnCheckoutcom($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'simiipay88apis') {
            $observerObjectData['module'] = 'simiipay88';
        }
        $observerObject->setData($observerObjectData);
    }

    public function changePayment($observer) {
        $payment = $observer->getObject();
        $data = $payment->detail;
        if (isset($data['payment_method']) && $data['payment_method'] == "SIMIIPAY88") {
            $data['merchant_key'] = Mage::getStoreConfig("payment/simiipay88/merchant_key");
            $data['merchant_code'] = Mage::getStoreConfig("payment/simiipay88/merchant_code");
            $data['is_sandbox'] = Mage::getStoreConfig("payment/simiipay88/is_sandbox");
        }
        $payment->detail = $data;
    }

    public function simiconnectorAfterPlaceOrder($observer) {
        $orderObject = $observer->getObject();
        $data = $orderObject->order_placed_info;
        if (isset($data['payment_method']) && $data['payment_method'] == "simiipay88") {
            $order = Mage::getModel('sales/order')->loadByIncrementId($data['invoice_number']);
            $data['amount'] = round($order->getGrandTotal(), 2);
            $data['currency_code'] = $order->getOrderCurrencyCode();
            $b = $order->getBillingAddress();
            $data['name'] = $b->getFirstname() . " " . $b->getLastname();
            $data['contact'] = $b->getTelephone();
            $data['email'] = $order->getCustomerEmail();
            $data['product_des'] = $this->getProductData($order);
            $data['country_id'] = $b->getCountryId();
        }
        $orderObject->order_placed_info = $data;
    }

    public function getProductData($order) {
        $products = "";
        $items = $order->getAllItems();
        if ($items) {
            $i = 0;
            foreach ($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                $products .= $item->getSku();
                $products .= ",";
                $i++;
            }
        }
        return $products;
    }

}
