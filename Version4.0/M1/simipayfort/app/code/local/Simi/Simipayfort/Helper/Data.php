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
class Simi_Simipayfort_Helper_Data extends Mage_Core_Helper_Abstract {

    public $_orderId;

    public function formatData($key, $value, $check = 0) {
        if ($check == 1) {
            return $key . "=" . $value;
        }
        return "&" . $key . "=" . $value;
    }

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    //get Merchant ID
    public function getMerchantId() {
        $merchantId = Mage::getStoreConfig("payment/simipayfort/merchant_id");
        return $merchantId;
    }

    //get Merchant Password
    public function getMerchantPassword() {
        $merchantPassword = Mage::getStoreConfig("payment/simipayfort/merchant_password");
        return $merchantPassword;
    }

    //get Verify Key
    public function getVerifyKey() {
        $verifyKey = Mage::getStoreConfig("payment/simipayfort/verify_key");
        // $verifyKey = '32F2E735-4EAC-499E-8DC9-F4E4BC331A44';
        return $verifyKey;
    }

    //get Return URL
    public function getUrlCallBack() {
        // $urlCallBack = Mage::getStoreConfig('payment/simipayfort/url_back');
        $urlCallBack = Mage::getBaseUrl();
        return $urlCallBack;
    }

    public function getQuote() {
        $orderIncrementId = $this->_orderId;
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        return $order;
    }

    public function getProductData() {
        $products = "";
        $items = $this->getQuote()->getAllItems();
        if ($items) {
            $i = 0;
            foreach ($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                $products .= $this->formatData("c_name_" . $i, $item->getName());
                $products .= $this->formatData("c_description_" . $i, $item->getSku());
                $products .= $this->formatData("c_price_" . $i, number_format($item->getPrice(), 2, '.', ''));
                $products .= $this->formatData("c_prod_" . $i, $item->getSku() . ',' . $item->getQtyToInvoice());
                $i++;
            }
        }
        return $products;
    }

    //get lineitem data
    public function getLineitemData() {
        $lineitems = "";
        $items = $this->getQuote()->getAllItems();
        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $taxFull = $order->getFullTaxInfo();
        $ship_method = $order->getShipping_description();
        $coupon = $order->getCoupon_code();
        $lineitem_total = 0;
        $i = 0;
        //get products
        if ($items) {
            foreach ($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                $lineitems .= $this->formatData("li_" . $i . "_type", "product");
                $lineitems .= $this->formatData("li_" . $i . "_product_id", $item->getSku());
                $lineitems .= $this->formatData("li_" . $i . "_quantity", $item->getQtyOrdered() * 1);
                $lineitems .= $this->formatData("li_" . $i . "_name", $item->getName());
                $lineitems .= $this->formatData("li_" . $i . "_description", $item->getDescription());
                $lineitems .= $this->formatData("li_" . $i . "_price", number_format($item->getPrice(), 2, '.', ''));

                $lineitem_total += number_format($item->getPrice(), 2, '.', '');
                $i++;
            }
        }
        //get taxes
        if ($taxFull) {
            foreach ($taxFull as $rate) {
                $lineitems .= $this->formatData("li_" . $i . "_type", "tax");
                $lineitems .= $this->formatData("li_" . $i . "_name", $rate['rates']['0']['code']);
                $lineitems .= $this->formatData("li_" . $i . "_price", round($rate['amount'], 2));
                $lineitem_total += round($rate['amount'], 2);
                $i++;
            }
        }
        //get shipping
        if ($ship_method) {
            $lineitems .= $this->formatData("li_" . $i . "_type", "shipping");
            $lineitems .= $this->formatData("li_" . $i . "_name", $order->getShipping_description());
            $lineitems .= $this->formatData("li_" . $i . "_price", round($order->getShippingAmount(), 2));
            $lineitem_total += round($order->getShippingAmount(), 2);
            $i++;
        }
        //get coupons
        if ($coupon) {
            $lineitems .= $this->formatData("li_" . $i . "_type", "coupon");
            $lineitems .= $this->formatData("li_" . $i . "_name", $order->getCoupon_code());
            $lineitems .= $this->formatData("li_" . $i . "_price", trim(round($order->getBase_discount_amount(), 2), '-'));

            $i++;
        }
        return $lineitems;
    }

    //check total
    public function checkTotal() {
        $items = $this->getQuote()->getAllItems();
        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $taxFull = $order->getFullTaxInfo();
        $ship_method = $order->getShipping_description();
        $coupon = $order->getCoupon_code();
        $lineitem_total = 0;
        $i = 1;
        //get products
        if ($items) {
            foreach ($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                $lineitem_total += number_format($item->getPrice(), 2, '.', '');
            }
        }
        //get taxes
        if ($taxFull) {
            foreach ($taxFull as $rate) {
                $lineitem_total += round($rate['amount'], 2);
            }
        }
        //get shipping
        if ($ship_method) {
            $lineitem_total += round($order->getShippingAmount(), 2);
        }
        //get coupons
        if ($coupon) {
            $lineitem_total -= trim(round($order->getBase_discount_amount(), 2), '-');
        }
        return $lineitem_total;
    }

    //get tax data
    public function getTaxData() {
        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $taxes = "";
        $taxFull = $order->getFullTaxInfo();
        if ($taxFull) {
            $i = 1;
            foreach ($taxFull as $rate) {
                $taxes .= $this->formatData("tax_id_" . $i, $rate['rates']['0']['code']);
                $taxes .= $this->formatData("tax_amount_" . $i, round($rate['amount'], 2));
                $i++;
            }
        }
        return $taxes;
    }

    public function getIframeUrl($_order_id) {
        $this->_orderId = $_order_id;
        $order_id = $_order_id;
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $amount = round($order->getGrandTotal(), 0);
        $a = $this->getQuote()->getShippingAddress();
        $b = $this->getQuote()->getBillingAddress();
        $country = $b->getCountry();
        $currency_code = $order->getOrderCurrencyCode();
        $shipping = round($order->getShippingAmount(), 2);
        $weight = round($order->getWeight(), 2);
        $ship_method = $order->getShipping_description();
        $tax = trim(round($order->getTaxAmount(), 2));
        $productData = $this->getProductData();
        $taxData = $this->getTaxData();
        $cart_order_id = $order_id;

        // Don't use for Checkout.com
        // $lineitemData = $this->getLineitemData(); 

        $requestData = array(
            'paymentmode' => '0',
            'amount' => $amount,
            'currencysymbol' => $currency_code,
            'merchantcode' => $this->getMerchantId(),
            'password' => $this->getMerchantPassword(),
            'action' => 1,
            'trackid' => $order_id,
            'returnurl' => $this->getUrlCallBack(),
            'bill_cardholder' => $b->getFirstname() . $b->getFirstname(),
            'bill_address' => $b->getStreet1(),
            'bill_postal' => $b->getPostcode(),
            'bill_city' => $b->getCity(),
            'bill_state' => $b->getRegion(),
            'bill_email' => $order->getData('customer_email'),
            'bill_country' => $b->getCountry(),
                // 'udf1' => $udf1,
                // 'udf2' => $udf2,
                // 'udf3' => $udf3,
                // 'udf4' => $udf4,
                // 'udf5' => $udf5,
                //          'recurring_flag' => $recurring_flag,
                //          'recurring_interval' => $recurring_interval,
                //          'recurring_intervaltype' => $recurring_intervaltype,
                //          'recurring_startdate' => $recurring_startdate,
                //          'recurring_transactiontype' => $recurring_transactiontype,
                //          'recurring_amount'=> $recurring_amount,
                //          'recurring_auto' => $recurring_auto,
                //          'recurring_number' => $recurring_number,
        );

        $TokenServiceURL = "https://api.checkout.com/tokenservice/createtoken.ashx";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $TokenServiceURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json; charset=utf-8"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $server_output = curl_exec($ch);
        curl_close($ch);

        //read JSon response
        $Payment_Token = "";
        $response = json_decode($server_output, TRUE);
        foreach ((array) $response as $key => $value) {
            if ($key == "PaymentToken") {
                $Payment_Token = $value;
            }
        }
        $hash = '';
        $hash = hash("sha512", $Payment_Token . strtoupper($this->getVerifyKey()));
        $payfortFields = "";
        $payfortFields .= $this->formatData("pt", $Payment_Token, 1);
        $payfortFields .= $this->formatData("sig", $hash);
        return $payfortFields;
    }

    /*
     * 4.0 functions
     */

    public function getPaymentToken($orderId) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $paramArray = array();
        $paramArray['autoCapTime'] = '24';
        $paramArray['autoCapture'] = 'Y';
        $paramArray['chargeMode'] = 1;
        $paramArray['email'] = $order->getData('customer_email');
        $paramArray['customerName'] = $order->getData('customer_firstname') . ' ' . $order->getData('customer_middlename') . ' ' . $order->getData('customer_lastname');
        $paramArray['description'] = Mage::helper('simiconnector')->__('Checkout.com Order');
        $paramArray['value'] = floatval($order->getData('grand_total')) * 100;
        $paramArray['currency'] = $order->getData('order_currency_code');
        $paramArray['trackId'] = $orderId;
        $paramArray['transactionIndicator'] = '1';
        $paramArray['customerIp'] = $_SERVER['REMOTE_ADDR'];
        $paramArray['cardToken'] = '';

        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress)
            $shippingAddress = $order->getBillingAddress();

        $paramArray['shippingDetails'] = array(
            'addressLine1' => $shippingAddress->getData('street'),
            'addressLine2' => $shippingAddress->getData('city'),
            'postcode' => $shippingAddress->getData('postcode'),
            'country' => $shippingAddress->getData('country_id'),
            'city' => $shippingAddress->getData('city'),
            'state' => $shippingAddress->getData('region'),
            'phone' => array('countryCode' => '',
                'number' => $shippingAddress->getData('telephone')),
        );

        $productInfo = array();
        $itemCollection = $order->getAllVisibleItems();
        foreach ($itemCollection as $item) {
            $productInfo[] = array(
                'description' => $item->getData('name'),
                'image' => NULL,
                'name' => $item->getData('name'),
                'price' => $item->getData('price_incl_tax'),
                'quantity' => (int) $item->getData('qty_ordered'),
                'shippingCost' => 0,
                'sku' => $item->getData('sku'),
                'trackingUrl' => '',
            );
        }
        //$paramArray['products'] = $productInfo;
        if (Mage::getStoreConfig("payment/simipayfort/is_sandbox"))
            $url = 'https://sandbox.checkout.com/api2/v2/tokens/payment';
        else 
            $url = 'https://api2.checkout.com/v2/tokens/payment';
        $currentUrl = Mage::getUrl('*/*/paymentrestv22').'?order_id='.$orderId;
        echo ' 
            <script>
            var http = new XMLHttpRequest();
            var url = "'.$url.'";
            var params = \''.  json_encode($paramArray).'\';
            http.open("POST", url, true);
            //Send the proper header information along with the request
            http.setRequestHeader("Content-type", "application/json;charset=UTF-8");
            http.setRequestHeader("Authorization", "'.Mage::getStoreConfig("payment/simipayfort/private_key").'");

            http.onreadystatechange = function() {//Call a function when the state changes.
                if(http.readyState == 4 && http.status == 200) {
                    var returned = JSON.parse(http.responseText);
                    window.location = "'.$currentUrl.'"+"&payment_token="+returned.id;
                }
            }
            http.send(params);
            </script>
        ';
    }

}
