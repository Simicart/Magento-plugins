<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Simicustompayment_Model_Api_Customizepayments extends Simi_Simiconnector_Model_Api_Abstract {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            
        }
    }

    public function index() {
        $simiCustompaymentArray = array();
        $simiCustompaymentArray['all_ids'] = array('1');
        $simiCustompaymentArray['customizepayments'] = $this->getPaymentList();
        $simiCustompaymentArray['total'] = 1;
        $simiCustompaymentArray['page_size'] = 15;
        $simiCustompaymentArray['from'] = 0;
        return $simiCustompaymentArray;
    }

    public function getPaymentList() {
        Mage::getSingleton('core/session')->setData('has_custom_payment', true);
        return array(
            array(
                'paymentmethod' => 'payfort',
                'title_url_action' => 'url_action',
                'url_redirect' => Mage::getUrl(),
                'url_success' => 'checkout/onepage/success',
                'url_fail' => 'checkout/onepage/failure',
                'url_cancel' => 'checkout/onepage/cancel',
                'url_error' => 'checkout/onepage/failure ',
                'message_success' => 'Thank you for purchasing',
                'message_fail' => 'Sorry, payment failed',
                'message_cancel' => 'Your order has been canceled',
                'message_error' => 'Sorry, Your order has an error',
                'ischeckurl' => '0', //(bien check truoc khi chuyen sang webview. Co hoac khong) : "0" or "1"
                'url_check' => "checkout/onepage/failure"
            ),
            array(
                'paymentmethod' => 'payfortcc',
                'title_url_action' => 'url_action',
                'url_redirect' => Mage::getUrl(),
                'url_success' => 'checkout/onepage/success',
                'url_fail' => 'checkout/onepage/failure',
                'url_cancel' => 'checkout/onepage/cancel',
                'url_error' => 'checkout/onepage/failure ',
                'message_success' => 'Thank you for purchasing',
                'message_fail' => 'Sorry, payment failed',
                'message_cancel' => 'Your order has been canceled',
                'message_error' => 'Sorry, Your order has an error',
                'ischeckurl' => '0', //(bien check truoc khi chuyen sang webview. Co hoac khong) : "0" or "1"
                'url_check' => "checkout/onepage/failure"
            ),
            array(
                'paymentmethod' => 'payfortinstallments',
                'title_url_action' => 'url_action',
                'url_redirect' => Mage::getUrl(),
                'url_success' => 'checkout/onepage/success',
                'url_fail' => 'checkout/onepage/failure',
                'url_cancel' => 'checkout/onepage/cancel',
                'url_error' => 'checkout/onepage/failure ',
                'message_success' => 'Thank you for purchasing',
                'message_fail' => 'Sorry, payment failed',
                'message_cancel' => 'Your order has been canceled',
                'message_error' => 'Sorry, Your order has an error',
                'ischeckurl' => '0', //(bien check truoc khi chuyen sang webview. Co hoac khong) : "0" or "1"
                'url_check' => "checkout/onepage/failure"
            ),
            array(
                'paymentmethod' => 'payfortsadad',
                'title_url_action' => 'url_action',
                'url_redirect' => Mage::getUrl(),
                'url_success' => 'checkout/onepage/success',
                'url_fail' => 'checkout/onepage/failure',
                'url_cancel' => 'checkout/onepage/cancel',
                'url_error' => 'checkout/onepage/failure ',
                'message_success' => 'Thank you for purchasing',
                'message_fail' => 'Sorry, payment failed',
                'message_cancel' => 'Your order has been canceled',
                'message_error' => 'Sorry, Your order has an error',
                'ischeckurl' => '0', //(bien check truoc khi chuyen sang webview. Co hoac khong) : "0" or "1"
                'url_check' => "checkout/onepage/failure"
            ),
            array(
                'paymentmethod' => 'payfortnaps',
                'title_url_action' => 'url_action',
                'url_redirect' => Mage::getUrl(),
                'url_success' => 'checkout/onepage/success',
                'url_fail' => 'checkout/onepage/failure',
                'url_cancel' => 'checkout/onepage/cancel',
                'url_error' => 'checkout/onepage/failure ',
                'message_success' => 'Thank you for purchasing',
                'message_fail' => 'Sorry, payment failed',
                'message_cancel' => 'Your order has been canceled',
                'message_error' => 'Sorry, Your order has an error',
                'ischeckurl' => '0', //(bien check truoc khi chuyen sang webview. Co hoac khong) : "0" or "1"
                'url_check' => "checkout/onepage/failure"
            ),
        );
    }

}
