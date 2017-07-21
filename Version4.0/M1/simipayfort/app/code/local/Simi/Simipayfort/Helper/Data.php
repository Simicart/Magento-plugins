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


}
