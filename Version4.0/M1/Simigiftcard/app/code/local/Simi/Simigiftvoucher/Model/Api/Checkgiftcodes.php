<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/6/17
 * Time: 8:54 AM
 */
class Simi_Simigiftvoucher_Model_Api_Checkgiftcodes extends Simi_Simiconnector_Model_Api_Abstract{

    public function setBuilderQuery(){}

    /*
     * Check gift card
     */
    public function store(){
        $data = $this->getData();
        $params = (array)$data['contents'];
        $code = $params['giftcode'];
        $giftcode = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
        if ($giftcode->getId()){
            $giftcode['conditions_serialized'] = unserialize($giftcode['conditions_serialized']);
            $giftcode['actions_serialized'] = unserialize($giftcode['actions_serialized']);
            return $giftcode;
        }
        else {
            throw new Exception(Mage::helper('simigiftvoucher')->__('Invalid gift code.'), 4);
        }

    }
}