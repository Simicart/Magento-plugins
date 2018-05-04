<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/4/17
 * Time: 3:55 PM
 */
class Simi_Simigiftvoucher_Model_Api_Simicustomercredits extends Simi_Simiconnector_Model_Api_Abstract {
    protected $_DEFAULT_ORDER = null;

    public function setBuilderQuery(){
        $data = $this->getData();
        if (Mage::getSingleton('customer/session')->isLoggedIn()){
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($data['resourceid'] == 'self'){
                $this->builderQuery = Mage::getModel('simigiftvoucher/credit')->load($customer->getId(),'customer_id');
            }
        }
        else {
            throw new Exception(Mage::helper('customer')->__('Please login First.'), 4);
        }
    }

    public function show(){
        $credit = $this->builderQuery;
        $data = $this->getData();
        $parameters = $data['params'];
        $fields = array();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $store = Mage::app()->getStore();
        $info = $credit->toArray($fields);
        $info['balance'] = $store->convertPrice($info['balance']);
        $info['currency'] = $store->getCurrentCurrencyCode();
        $info['currency_symbol'] = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $listcode = Mage::getModel('simigiftvoucher/simimapping')->getListCode();
        foreach ($listcode as $item){
            $voucher_id = $item['voucher_id'];
            $item['action'] =  Mage::getModel('simigiftvoucher/simimapping')->getAction($voucher_id);
            $item['giftvoucher_id'] = $item['voucher_id'];
            $info['listcode'][] = $item;
        }
        $history = Mage::getModel('simigiftvoucher/credithistory')
                        ->getCollection()
                        ->addFieldToFilter('customer_id',$customer->getId())
                        ->getData();
        foreach ($history as $item){
            if ($item['action'] == 'Redeem'){
                $item['action'] = Mage::helper('simigiftvoucher')->__('Customer Redemption');
            }
            elseif ($item['action'] == 'Api_re'){
                $item['action'] = Mage::helper('simigiftvoucher')->__('API User Redemption');
            }
            elseif ($item['action'] == 'Apiupdate'){
                $item['action'] = Mage::helper('simigiftvoucher')->__('API User Update');
            }
            elseif ($item['action'] == 'Adminupdate'){
                $item['action'] = Mage::helper('simigiftvoucher')->__('Admin Update');
            }
            elseif ($item['action'] == 'Spend'){
                $item['action'] = Mage::helper('simigiftvoucher')->__('Customer Spend');
            }
            elseif ($item['action'] == 'Refund'){
                $item['action'] = Mage::helper('simigiftvoucher')->__('Admin Refund');
            }
            $item['created_date'] = Mage::helper('core')->formatDate($item['created_date'],'medium');
            $item['currency_symbol'] = Mage::app()->getLocale()->currency($item['currency'])->getSymbol();
            $info['history'][] = $item;
        }
        $info['history'] = array_reverse($info['history']);
        return $this->getDetail($info);
    }

    /*
     *  PUT
     * */
    public function update(){
        $data = $this->getData();
        $params = (array) $data['contents'];

        // Use gift card change balance
        // param : "giftcode"
        if ($data['resourceid'] == 'addredeem'){
            $message = Mage::getModel('simigiftvoucher/simimapping')->AddRedeem($data);
            return array('message' => array('success' => $message));
        }
        elseif ($data['resourceid'] == 'addlist'){
            $message = Mage::getModel('simigiftvoucher/simimapping')->AddMyList($data);
            return array('message' => array('success' => $message));
        }
        elseif ($data['resourceid'] == 'sendemail'){
            $id = $params['voucher_id'];
            $giftCard = Mage::getModel('simigiftvoucher/giftvoucher')->load($id);

            if ($giftCard->getSetId() > 0 && $giftCard->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_PENDING) {
                $error = Mage::helper('simigiftvoucher')->__('Can not send email because it is Gift Code Set!');
                throw new Exception($error,4);
            }

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (!$customer ||
                ($giftCard->getCustomerId() != $customer->getId()
                    && $giftCard->getCustomerEmail() != $customer->getEmail()
                )
            ) {
                $error = Mage::helper('simigiftvoucher')->__('The Gift Card email has been failed to send.');
                throw new Exception($error,4);
            }
            $result = array();
            $giftCard->addData($params);
            $giftCard->setNotResave(true);
            $result = array();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            if ($giftCard->sendEmailToRecipient()) {
                $result['success'] = Mage::helper('simigiftvoucher')->__('The Gift Card email has been sent successfully.');
            } else {
                $error = Mage::helper('simigiftvoucher')->__('The Gift Card email cannot be sent to your friend!');
                throw new Exception($error,4);
            }
            $translate->setTranslateInline(true);
            return array('message' => $result);
        }
    }

    /*
     *  Delete
     * */
    public function destroy(){
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            throw new Exception(Mage::helper('customer')->__('Please login First.'),4);
        }

        $data = $this->getData();
        if ($data['nestedresource'] && $data['resourceid'] == 'self'){
            $params = (array) $data['contents'];
            $customerVoucherId = $data['nestedresource'];
            $voucher = Mage::getModel('simigiftvoucher/customervoucher')->load($customerVoucherId);
            if (!$voucher->getId()){
                throw new Exception(Mage::helper('simigiftvoucher')->__('Gift Code of customer not exist !'),4);
            }
            if ($voucher->getCustomerId() == Mage::getSingleton('customer/session')->getCustomer()->getId()) {
                try {
                    $voucher->delete();
                    $result =  Mage::helper('simigiftvoucher')->__('Gift Code was removed successfully !');
                } catch (Exception $e) {
                    $error = $e->getMessage();
                    throw new Exception($error,4);
                }
            }
            $detail = $this->show();
            $detail['simicustomercredit']['message'] = $result;
            return $detail;
        }
    }
}