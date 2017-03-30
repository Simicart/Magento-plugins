<?php

/**
 *
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simiavenue
 * @copyright   Copyright (c) 2012 
 * @license    
 */

/**
 * Simiavenue Helper
 * 
 * @category    
 * @package     Simiavenue
 * @author      Developer
 */
class Simi_SimiAvenue_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getRequestUrl($data) {
        $working_key = Mage::getStoreConfig('payment/simiavenue/working_key'); //Shared by CCAVENUES
        $access_code = Mage::getStoreConfig('payment/simiavenue/access_code'); //Shared by CCAVENUES
        $merchant_data = '';

        foreach ($data as $key => $value) {
            $merchant_data.=$key . '=' . $value . '&';
        }

        $encrypted_data = Mage::helper('simiavenue/crypto')->encrypt($merchant_data, $working_key); // Method for encrypting the data.
        // Zend_debug::dump($encrypted_data);die();
        $production_url = "";
        if (Mage::getStoreConfig('payment/simiavenue/enable_auth_query') == '1') {
            $production_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction&encRequest=' . $encrypted_data . '&access_code=' . $access_code;
        } else {
            $production_url = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction&encRequest=' . $encrypted_data . '&access_code=' . $access_code;
        }
        return $production_url;
    }

    public function getFormFields($order_id) {
        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId($order_id);

        $timestamp = strtotime(now());
        //Zend_debug::dump($timestamp);die();
        $country_code = Mage::getStoreConfig('general/country/default');
        $country = Mage::getModel('directory/country')->loadByCode($country_code);
        $ccavenue['tid'] = $timestamp;
        $ccavenue['order_id'] = $order_id;
        $ccavenue['merchant_id'] = Mage::getStoreConfig('payment/simiavenue/merchant_id');
        $ccavenue['amount'] = round($order->getBaseGrandTotal(), 2);
        $ccavenue['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
        $ccavenue['redirect_url'] = Mage::getBaseUrl() . 'simiavenue/api/response';
        $ccavenue['cancel_url'] = Mage::getBaseUrl() . 'simiavenue/api/response';
        $ccavenue['language'] = $country->getId();

        // Retrieve order details
        $billingAddress = $order->getBillingAddress();
        $billingData = $billingAddress->getData();
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress)
            $shippingData = $shippingAddress->getData();

        $billing_address = $billingAddress->getStreet();
        $ccavenue['billing_name'] = $billingData['firstname'] . ' ' . $billingData['lastname'];
        $ccavenue['billing_address'] = $billing_address[0];
        $ccavenue['billing_state'] = $billingAddress->getRegion();
        $ccavenue['billing_country'] = Mage::getModel('directory/country')->load($billingAddress->country_id)->getName();
        $ccavenue['billing_tel'] = $billingAddress->getTelephone();
        $ccavenue['billing_email'] = $order->getCustomerEmail();
        if ($shippingAddress) {
            $delivery_address = $shippingAddress->getStreet();
            $ccavenue['delivery_name'] = $shippingData['firstname'] . ' ' . $shippingData['lastname'];
            $ccavenue['delivery_address'] = $delivery_address[0];
            $ccavenue['delivery_state'] = $shippingAddress->getRegion();
            $ccavenue['delivery_country'] = Mage::getModel('directory/country')->load($shippingAddress->country_id)->getName();
            $ccavenue['delivery_tel'] = $shippingAddress->getTelephone();
            $ccavenue['delivery_city'] = $shippingAddress->getCity();
            $ccavenue['delivery_zip'] = $shippingAddress->getPostcode();
        } else {
            $ccavenue['delivery_name'] = '';
            $ccavenue['delivery_address'] = '';
            $ccavenue['delivery_state'] = '';
            $ccavenue['delivery_country'] = '';
            $ccavenue['delivery_tel'] = '';
            $ccavenue['delivery_city'] = '';
            $ccavenue['delivery_zip'] = '';
        }
        $ccavenue['merchant_param1'] = '';
        $ccavenue['billing_city'] = $billingAddress->getCity();
        $ccavenue['billing_zip'] = $billingAddress->getPostcode();
        //Zend_debug::dump($ccavenue);die();
        $url = $this->getRequestUrl($ccavenue);
        //Zend_debug::dump($url);
        return $url;
    }

    /* -------------------- DO NOT EDIT BELOW THIS LINE : CCAVENUE FUNCTIONS -------------------- */

    private function getchecksum($merchant_id, $amount, $order_id, $url, $working_key) {
        $str = "$merchant_id|$order_id|$amount|$url|$working_key";
        $adler = 1;
        $adler = $this->adler32($adler, $str);
        return $adler;
    }

    private function verifychecksum($merchant_id, $order_id, $amount, $auth_desc, $checksum, $working_key) {
        $str = "$merchant_id|$order_id|$amount|$auth_desc|$working_key";
        $adler = 1;
        $adler = $this->adler32($adler, $str);

        if ($adler == $checksum)
            return "true";
        else
            return "false";
    }

    private function adler32($adler, $str) {
        $BASE = 65521;

        $s1 = $adler & 0xffff;
        $s2 = ($adler >> 16) & 0xffff;
        for ($i = 0; $i < strlen($str); $i++) {
            $s1 = ($s1 + Ord($str[$i])) % $BASE;
            $s2 = ($s2 + $s1) % $BASE;
        }
        return $this->leftshift($s2, 16) + $s1;
    }

    private function leftshift($str, $num) {

        $str = DecBin($str);

        for ($i = 0; $i < (64 - strlen($str)); $i++)
            $str = "0" . $str;

        for ($i = 0; $i < $num; $i++) {
            $str = $str . "0";
            $str = substr($str, 1);
        }
        return $this->cdec($str);
    }

    private function cdec($num) {

        for ($n = 0; $n < strlen($num); $n++) {
            $temp = $num[$n];
            $dec = $dec + $temp * pow(2, strlen($num) - $n - 1);
        }

        return $dec;
    }

    /* -------------------- DO NOT EDIT ABOVE THIS LINE : CCAVENUE FUNCTIONS -------------------- */
}
