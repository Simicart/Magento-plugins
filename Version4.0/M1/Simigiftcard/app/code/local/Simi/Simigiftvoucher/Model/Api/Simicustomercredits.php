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
            if (isset($data['resourceid']) && $data['resourceid'] == 'self'){
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
        $info = $credit->toArray($fields);
        $listcode = Mage::getModel('simigiftvoucher/simimapping')->getListCode();
        foreach ($listcode as $item){
            $voucher_id = $item->getVoucherId();
            $item['action'] =  Mage::getModel('simigiftvoucher/simimapping')->getAction($voucher_id);
            $item['giftvoucher_id'] = $info['voucher_id'];
            $info['listcode'][] = $item;
        }
        $info['history'] = Mage::getModel('simigiftvoucher/credithistory')
                        ->getCollection()
                        ->addFieldToFilter('customer_id',$customer->getId())
                        ->getData();
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
        $params = (array) $data['contents'];
        $customerVoucherId = $params['customer_voucher_id'];

        $result = array();
        $voucher = Mage::getModel('giftvoucher/customervoucher')->load($customerVoucherId);
        if (!$voucher->getId()){
            throw new Exception(Mage::helper('simigiftvoucher')->__('Gift Code of customer not exist !'),4);
        }
        if ($voucher->getCustomerId() == Mage::getSingleton('customer/session')->getCustomer()->getId()) {
            try {
                $voucher->delete();
                $result['success'] =  Mage::helper('simigiftvoucher')->__('Gift Code was successfully removed !');
            } catch (Exception $e) {
                $error = $e->getMessage();
                throw new Exception($error,4);
            }
        }
        return array('message' => $result);
    }
}