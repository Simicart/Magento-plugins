<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Twout
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Twout Helper
 * 
 * @category 	Magestore
 * @package 	Magestore_Twout
 * @author  	Magestore Developer
 */
class Simi_Twout_Helper_Data extends Mage_Core_Helper_Abstract {

    public $_orderId;

    public function formatData($key, $value, $check=0) {
		if($check == 1){
			return $key . "=" . $value;
		}
        return "&" . $key . "=" . $value;
    }

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    //get SID
    public function getSid() {
        $sid = Mage::getStoreConfig("payment/twout/send_id");
        return $sid;
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

    public function getFormFields($_order_id) {
        $this->_orderId = $_order_id;
        $order_id = $_order_id;
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $amount = round($order->getGrandTotal(), 2);
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
        $lineitemData = $this->getLineitemData();

        $tcoFields = "";
        $tcoFields .= $this->formatData("sid", $this->getSid(), 1);
        // $tcoFields .= '&lang=' . $this->getLanguage();
        $tcoFields .= $this->formatData("purchase_step", "payment-method");
        $tcoFields .= $this->formatData("merchant_order_id", $order_id);
        $tcoFields .= $this->formatData("email", $order->getData('customer_email'));
        $tcoFields .= $this->formatData("first_name", $b->getFirstname());
        $tcoFields .= $this->formatData("last_name", $b->getLastname());
        $tcoFields .= $this->formatData("phone", $b->getTelephone());
        $tcoFields .= $this->formatData("country", $b->getCountry());
        $tcoFields .= $this->formatData("street_address", $b->getStreet1());
        $tcoFields .= $this->formatData("street_address2", $b->getStreet2());
        $tcoFields .= $this->formatData("city", $b->getCity());
        if ($country == 'US' || $country == 'CA') {
            $tcoFields .= $this->formatData("state", $b->getRegion());
        } else {
            $tcoFields .= $this->formatData("state", "XX");
        }
        $tcoFields .= $this->formatData("zip", $b->getPostcode());

        if ($a) {
            $tcoFields .= $this->formatData("ship_name", $a->getFirstname() . ' ' . $a->getLastname());
            $tcoFields .= $this->formatData("ship_country", $a->getCountry());
            $tcoFields .= $this->formatData("ship_street_address", $a->getStreet1());
            $tcoFields .= $this->formatData("ship_street_address2", $a->getStreet2());
            $tcoFields .= $this->formatData("ship_city", $a->getCity());
            $tcoFields .= $this->formatData("ship_state", $a->getRegion());
            $tcoFields .= $this->formatData("ship_zip", $a->getPostcode());
            $tcoFields .= $this->formatData("sh_cost", $shipping);
            $tcoFields .= $this->formatData("sh_weight", $weight);
            $tcoFields .= $this->formatData("ship_method", $ship_method);
        }
        $tcoFields .= $this->formatData("2co_tax", $tax);
        $tcoFields .= $this->formatData("2co_cart_type", "magento");
        $tcoFields .= $this->formatData("currency_code", $currency_code);

        //Check Integration mode
        $lineitem_total = $this->checkTotal();		
        $result = "";
        if ($lineitem_total != $amount) {
            $tcoFields .= $this->formatData("id_type", '1');
            $tcoFields .= $this->formatData("total", $amount);
            $tcoFields .= $this->formatData("cart_order_id", $currency_code);
            $result = $tcoFields . $productData . $taxData;
        } else {            
            $tcoFields .= $this->formatData("mode", '2CO');
            $result = $tcoFields . $lineitemData;
        }
        return $result;
    }

}