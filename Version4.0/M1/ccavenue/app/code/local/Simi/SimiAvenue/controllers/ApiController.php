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
 * Simiavenue Index Controller
 * 
 * @category    
 * @package     Simiavenue
 * @author      Developer
 */
class Simi_SimiAvenue_ApiController extends Mage_Core_Controller_Front_Action {

    //error_reporting(0);

    public function indexAction() {
        
    }

    public function responseAction() {
        if ($this->getRequest()->isPost()) {
            // Retrieve POST Values
            $order_status = "";
            $workingKey = Mage::getStoreConfig('payment/simiavenue/working_key');
            $encResponse = $this->getRequest()->getPost('encResp');
            $rcvdString = Mage::helper('simiavenue/crypto')->decrypt($encResponse, $workingKey);  //Crypto Decryption used as per the specified working key.
            $decryptValues = explode('&', $rcvdString);
            $dataSize = sizeof($decryptValues);

            $Order_Id = '';
            $tracking_id = '';
            $order_status = '';
            $response_array = array();

            for ($i = 0; $i < $dataSize; $i++) {
                $information = explode('=', $decryptValues[$i]);
                if (count($information) == 2) {
                    $response_array[$information[0]] = $information[1];
                }
            }


            if (isset($response_array['order_id']))
                $order_id = $response_array['order_id'];
            if (isset($response_array['tracking_id']))
                $tracking_id = $response_array['tracking_id'];
            if (isset($response_array['order_status']))
                $order_status = $response_array['order_status'];
            if (isset($response_array['currency']))
                $currency = $response_array['currency'];
            if (isset($response_array['Amount']))
                $payment_mode = $response_array['Amount'];


            if ($order_status === "Success") {
                $order = Mage::getModel('sales/order');
                $order->loadByIncrementId($order_id);
                $f_passed_status = Mage::getStoreConfig('payment/simiavenue/payment_success_status');
                $order->setState($f_passed_status, true, 'CCAvenue has authorized the payment.');

                $order->sendNewOrderEmail();
                $order->setEmailSent(true);

                $order_history_comments = '';
                $order_history_keys = array('tracking_id', 'failure_message', 'payment_mode', 'card_name', 'status_code', 'status_message', 'bank_ref_no');
                foreach ($order_history_keys as $order_history_key) {

                    if ((isset($response_array[$order_history_key])) && trim($response_array[$order_history_key]) != '') {
                        if (trim($response_array[$order_history_key]) == 'null')
                            continue;
                        $order_history_comments .= $order_history_key . " : " . $response_array[$order_history_key];
                    }
                }
                // $order_history_comments_array= array();   
                // $order_history_comments_array[] = $order_history_comments;

                if ($order_history_comments != '')
                    $order->addStatusHistoryComment($order_history_comments, true);

                $order->save();

                Mage::getSingleton('checkout/session')->unsQuoteId();

                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure' => true));
            }
            else if ($order_status === "Aborted") {
                $this->reviewAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/review', array('_secure' => true));
            } else if ($order_status === "Failure") {
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure ' => true));
            } else {
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('simiavenue/api/index', array('_secure ' => true));
            }


            Mage::getModel('simiavenue/simiavenue')
                    ->setMerchantId($tracking_id)
                    ->setAmount($payment_mode)
                    ->setOrderId($order_id)
                    ->setMerchantParam($order_status)
                    ->setChecksum($encResponse)
                    ->setAuthdesc($order_status)
                    ->setCardCategory($order_status)
                    ->setBankName($order_status)
                    ->save();
        } else
            Mage_Core_Controller_Varien_Action::_redirect('simiavenue/api/index', array('_secure ' => true));
    }

    public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if ($order->getId()) {
                // Flag the order as 'cancelled' and save it
                $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'CCAvenue has declined the payment.')->save();
            }
        }
    }

    public function reviewAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if ($order->getId()) {
                // Flag the order as 'payment review' and save it
                $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, 'CCAvenue has sent AuthDesc as B.');
                $order->save();
            }
        }
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
