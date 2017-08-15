<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/4/17
 * Time: 10:31 AM
 */
class Simi_Simigiftvoucher_Model_Api_Simigiftcodes extends Simi_Simiconnector_Model_Api_Abstract {

    protected $_DEFAULT_ORDER = 'giftvoucher_id';

    public function setBuilderQuery(){
        $data = $this->getData();
        if (Mage::getSingleton('customer/session')->isLoggedIn()){
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (isset($data['resourceid']) ){
                $this->builderQuery = Mage::getModel('simigiftvoucher/simimapping')->loadGiftcode($data['resourceid'],$customer->getId());
            } else {
                $this->builderQuery = Mage::getModel('simigiftvoucher/giftvoucher')->getCollection()->addFieldToFilter('customer_id',$customer->getId());
            }
        }
        else {
            throw new Exception(Mage::helper('customer')->__('Please login First.'), 4);
        }
    }

    public function index()
    {
        $collection = $this->builderQuery;
        $this->filter();
        $data = $this->getData();
        $parameters = $data['params'];
        $page = 1;
        if (isset($parameters[self::PAGE]) && $parameters[self::PAGE]) {
            $page = $parameters[self::PAGE];
        }

        $limit = self::DEFAULT_LIMIT;
        if (isset($parameters[self::LIMIT]) && $parameters[self::LIMIT]) {
            $limit = $parameters[self::LIMIT];
        }

        $offset = $limit * ($page - 1);
        if (isset($parameters[self::OFFSET]) && $parameters[self::OFFSET]) {
            $offset = $parameters[self::OFFSET];
        }
        $collection->setPageSize($offset + $limit);

        $all_ids = array();
        $info = array();
        $total = $collection->getSize();

        if ($offset > $total)
            throw new Exception($this->_helper->__('Invalid method.'), 4);

        $fields = array();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }

        $check_limit = 0;
        $check_offset = 0;

        foreach ($collection as $giftcode) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;

            $info_detail = $giftcode->toArray($fields);
            $all_ids[] = $giftcode->getId();
            $info_detail['conditions_serialized'] = unserialize($giftcode->getConditionsSerialized());
            $info_detail['actions_serialized'] = unserialize($info['actions_serialized']);
            /*$info_detail['history'] = Mage::getModel('simigiftvoucher/history')
                ->getCollection()
                ->addFieldToFilter('giftvoucher_id', $giftcode->getId())->getData();*/

            $info[] = $info_detail;
        }
        return $this->getList($info, $all_ids, $total, $limit, $offset);
    }

    public function show(){
        $giftcode = $this->builderQuery;
        $data = $this->getData();
        $parameters = $data['params'];
        $fields = array();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $info = $giftcode->toArray($fields);
        $customer_voucher = Mage::getModel('simigiftvoucher/customervoucher')->load($info['giftvoucher_id'])->getData();
        $info['added_date'] = Mage::helper('core')->formatDate($customer_voucher['added_date'],'medium');
        $info['expired_at'] = Mage::helper('core')->formatDate($info['expired_at'],'medium');
        if (($giftcode->getRecipientName() && $giftcode->getRecipientEmail()
            && $giftcode->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId())){
            $info['comment'] = Mage::help('simigiftvoucher')->__('This is your gift to give for %s (%s)',$giftcode->getRecipientName(), $giftcode->getRecipientEmail());
        }
        $info['currency_symbol'] = Mage::app()->getLocale()->currency($info['currency'])->getSymbol();
        $info['conditions_serialized'] = unserialize($info['conditions_serialized']);
        $info['actions_serialized'] = unserialize($info['actions_serialized']);
        $info['actions'] = Mage::getModel('simigiftvoucher/simimapping')->getAction($info['giftvoucher_id']);
        $history = Mage::getModel('simigiftvoucher/history')
                            ->getCollection()
                            ->addFieldToFilter('giftvoucher_id', $giftcode->getId())->getData();
        foreach ($history as $item){
            $item['created_at'] = Mage::helper('core')->formatDate($info['created_at'],'medium');
            $item['currency_symbol'] = Mage::app()->getLocale()->currency($item['currency'])->getSymbol();
            $info['history'][] = $item;
        }

        return $this->getDetail($info);
    }

}