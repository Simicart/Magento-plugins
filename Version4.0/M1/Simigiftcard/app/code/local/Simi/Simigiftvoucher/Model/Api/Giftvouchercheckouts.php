<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/13/17
 * Time: 5:15 PM
 */
class Simi_Simigiftvoucher_Model_Api_Giftvouchercheckouts extends Simi_Simiconnector_Model_Api_Abstract{

    public function setBuilderQuery(){
        $data = $this->getData();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        } else
            throw new Exception(Mage::helper('customer')->__('Please login First.'), 4);
    }

    /**
     *  PUT : update Checkout page and Cart page
     * */
    public function update(){
        $data = $this->getData();
        /* Use balance credit to cart page
         * params : "usecredit"
         *          "credit_amount"
         */
        if ($data['resourceid'] == 'usecredit') {
            $quoteitemsAPI = Mage::getModel('simiconnector/api_quoteitems');
            $data['resource'] = 'quoteitems';
            $quoteitemsAPI->setData($data);
            $quoteitemsAPI->setBuilderQuery();
            $quoteitemsAPI->setPluralKey('quoteitems');
            $message = Mage::getModel('simigiftvoucher/simimapping')->UseCredit($data);
            $detail = $quoteitemsAPI->show();
            $detail['gift_card']['message'] = $message;
            return $detail;
        }
        /*
         *  Add gift code from card page
         *  params : "giftvoucher"
         *           "giftcode"
         *           "existed_giftcode"
         * */
        elseif ($data['resourceid'] == 'usecode'){
            $message = Mage::getModel('simigiftvoucher/simimapping')->UseGiftCode($data);
            $quoteitemsAPI = Mage::getModel('simiconnector/api_quoteitems');
            $data['resource'] = 'quoteitems';
            $quoteitemsAPI->setData($data);
            $quoteitemsAPI->setBuilderQuery();
            $quoteitemsAPI->setPluralKey('quoteitems');
            $detail = $quoteitemsAPI->show();
            $detail['gift_card']['message'] = $message;
            return $detail;
        }
        /*
         *  Update amount giftcode from cart page
         * params : {
         *  "giftcoe"
         *  "amount"
         * }
         * */
        elseif ($data['resourceid'] == 'updatecode'){
            $message = Mage::getModel('simigiftvoucher/simimapping')->updateAmountGiftcode($data);
            $quoteitemsAPI = Mage::getModel('simiconnector/api_quoteitems');
            $data['resource'] = 'quoteitems';
            $quoteitemsAPI->setData($data);
            $quoteitemsAPI->setBuilderQuery();
            $quoteitemsAPI->setPluralKey('quoteitems');
            $detail = $quoteitemsAPI->show();
            $detail['gift_card']['message'] = $message;
            return $detail;
        }
        /*
         *  Remove giftcode from Cart Page
         *  params : "giftcode"
         * */
        elseif($data['resourceid'] == 'remove'){
            $message = Mage::getModel('simigiftvoucher/simimapping')->removeGiftCode($data);
            $quoteitemsAPI = Mage::getModel('simiconnector/api_quoteitems');
            $data['resource'] = 'quoteitems';
            $quoteitemsAPI->setData($data);
            $quoteitemsAPI->setBuilderQuery();
            $quoteitemsAPI->setPluralKey('quoteitems');
            $detail = $quoteitemsAPI->show();
            $detail['gift_card']['message'] = $message;
            return $detail;
        }
        /*
         * Checkout Page
         * params : "giftvoucher"
         * */
        elseif ($data['resourceid'] == 'changeusegiftcode'){
            $onepageAPI = Mage::getModel('simiconnector/api_orders');
            $data['resource'] = 'orders';
            $data['resourceid'] = 'onepage';
            $onepageAPI->setData($data);
            $onepageAPI->setBuilderQuery();
            $onepageAPI->setPluralKey('orders');
            $message = Mage::getModel('simigiftvoucher/simimapping')->ChangeUseCode($data);
            $detail = $onepageAPI->show();
            $detail['order']['gift_card']['message'] = $message;
            return $detail;
        }
        /*
         *  Checkout Page
         *  params : "existed_giftcode"
         *           "giftcode"
         * */
        elseif ($data['resourceid'] == 'addcodecheckout'){
            $message = Mage::getModel('simigiftvoucher/simimapping')->UseGiftCode($data);
            $onepageAPI = Mage::getModel('simiconnector/api_orders');
            $data['resource'] = 'orders';
            $data['resourceid'] = 'onepage';
            $onepageAPI->setData($data);
            $onepageAPI->setBuilderQuery();
            $onepageAPI->setPluralKey('orders');
            $detail = $onepageAPI->show();
            $detail['order']['gift_card']['message'] = $message;
            return $detail;
        }
        /*
         *  Checkout Page
         *  params : "giftcode"
         *           "amount"
         * */
        elseif ($data['resourceid'] == 'updategiftcode'){
            $onepageAPI = Mage::getModel('simiconnector/api_orders');
            $data['resource'] = 'orders';
            $data['resourceid'] = 'onepage';
            $onepageAPI->setData($data);
            $onepageAPI->setBuilderQuery();
            $onepageAPI->setPluralKey('orders');
            $message = Mage::getModel('simigiftvoucher/simimapping')->updateAmountGiftcode($data);
            $detail = $onepageAPI->show();
            $detail['order']['gift_card']['message'] = $message;
            return $detail;
        }
        /*
         *  Checkout Page
         *  params : "giftcode"
         * */
        elseif ($data['resourceid'] == 'removegiftcode'){
            $onepageAPI = Mage::getModel('simiconnector/api_orders');
            $data['resource'] = 'orders';
            $data['resourceid'] = 'onepage';
            $onepageAPI->setData($data);
            $onepageAPI->setBuilderQuery();
            $onepageAPI->setPluralKey('orders');
            $message = Mage::getModel('simigiftvoucher/simimapping')->removeCodeCheckout($data);
            $detail = $onepageAPI->show();
            $detail['order']['gift_card']['message'] = $message;
            return $detail;
        }
        /*
         *  Checkout Page
         *  params : "usecredit"
         * */
        elseif ($data['resourceid'] == 'usecreditcheckout'){
            $onepageAPI = Mage::getModel('simiconnector/api_orders');
            $data['resource'] = 'orders';
            $data['resourceid'] = 'onepage';
            $onepageAPI->setData($data);
            $onepageAPI->setBuilderQuery();
            $onepageAPI->setPluralKey('orders');
            $message = Mage::getModel('simigiftvoucher/simimapping')->UseCredit($data);
            $detail = $onepageAPI->show();
            $detail['order']['gift_card']['message'] = $message;
            return $detail;
        }
        /*
         *  Update credit amount to checkout
         *  params : "credit_amount"
         * */
        elseif ($data['resourceid'] == 'updatecredit'){
            $onepageAPI = Mage::getModel('simiconnector/api_orders');
            $data['resource'] = 'orders';
            $data['resourceid'] = 'onepage';
            $onepageAPI->setData($data);
            $onepageAPI->setBuilderQuery();
            $onepageAPI->setPluralKey('orders');
            $message = Mage::getModel('simigiftvoucher/simimapping')->creditamountAction($data);
            $detail = $onepageAPI->show();
            $detail['order']['gift_card']['message'] = $message;
            return $detail;
        }
    }
}