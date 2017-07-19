<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/4/17
 * Time: 3:55 PM
 */
class Simi_Simigiftvoucher_Model_Api_Simicustomercredit extends Simi_Simiconnector_Model_Api_Abstract {
    protected $_DEFAULT_ORDER = null;

    public function setBuilderQuery(){
        $data = $this->getData();
        if (Mage::getSingleton('customer/session')->isLoggedIn()){
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->builderQuery = Mage::getModel('simigiftvoucher/credit')->load($customer->getId(),'customer_id');
        }
        else {
            throw new Exception(Mage::helper('customer')->__('Please login First.'), 4);
        }
    }

    public function index(){
        $credit = $this->builderQuery;
        $data = $this->getData();
        $parameters = $data['params'];
        $fields = array();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $info = $credit->toArray($fields);
        $info['listcode'] = Mage::getModel('simigiftvoucher/simimapping')->getListCode();
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
            $detail = $this->index();
            $detail['message'] = $message;
            return $detail;
        }
        elseif ($data['resourceid'] == 'addlist'){
            $message = Mage::getModel('simigiftvoucher/simimapping')->AddMyList($data);
            $detail = $this->index();
            $detail['message'] = $message;
            return $detail;
        }
    }
}