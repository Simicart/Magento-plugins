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
        $info['conditions_serialized'] = unserialize($info['conditions_serialized']);
        $info['actions_serialized'] = unserialize($info['actions_serialized']);
        $info['actions'] = Mage::getModel('simigiftvoucher/simimapping')->getAction($info['giftvoucher_id']);
        $info['history'] = Mage::getModel('simigiftvoucher/history')
                            ->getCollection()
                            ->addFieldToFilter('giftvoucher_id', $giftcode->getId())->getData();
        return $this->getDetail($info);
    }

}