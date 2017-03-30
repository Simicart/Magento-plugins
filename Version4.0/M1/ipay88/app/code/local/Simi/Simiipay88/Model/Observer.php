<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simiipay88
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

/**
 * Simiipay88 Model
 * 
 * @category 	
 * @package 	Simiipay88
 * @author  	Developer
 */
class Simi_Simiipay88_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_IPay88_Model_Observer
     */
    public function addPayment($observer) {
        $object = $observer->getObject();
        $object->addMethod('simiipay88', 3);
        return;
    }

    public function paymentMethodIsActive($observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        //$store = $quote ? $quote->getStoreId() : null; 
        if ($result->isAvailable && ($method->getCode() == 'simiipay88')) {
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' 
                    && Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress' 
                    && Mage::app()->getRequest()->getControllerModule() != 'Simi_Simiconnector') {
                $result->isAvailable = false;
            }
        }
    }

    public function changePayment($observer) {

        $object = $observer->getObject();
        $data = $object->getCacheData();
        $check = false;
        $i = -1;
        foreach ($data as $item) {
            // Zend_debug::dump($item);
            $i ++;
            if (isset($item['payment_method']) && $item['payment_method'] == "SIMIIPAY88") {
                $check = true;
                break;
            }
        }
        // die();
        if ($check) {
            $data[$i]['merchant_key'] = Mage::getStoreConfig("payment/simiipay88/merchant_key");
            $data[$i]['merchant_code'] = Mage::getStoreConfig("payment/simiipay88/merchant_code");
            $data[$i]['is_sandbox'] = Mage::getStoreConfig("payment/simiipay88/is_sandbox");
        }
        $object->setCacheData($data, "simi_connector");
    }

    public function afterPlaceOrder($observer) {
        $object = $observer->getObject();
        $data = $object->getCacheData();
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
        $object->setCacheData($data, "simi_connector");
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
                //   $products .= $this->formatData("c_name_" . $i, $item->getName());
                $products .= $item->getSku();
                $products .= ",";
                //  $products .= $this->formatData("c_price_" . $i, number_format($item->getPrice(), 2, '.', ''));
                // $products .= $this->formatData("c_prod_" . $i, $item->getSku() . ',' . $item->getQtyToInvoice());
                $i++;
            }
        }
        return $products;
    }

}
