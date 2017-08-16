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
            $giftcode['expired_at'] = Mage::helper('core')->formatDate($giftcode['expired_at'],'medium');
            $giftcode['day_to_send'] = Mage::helper('core')->formatDate($giftcode['day_to_send'],'medium');
            $giftcode['day_store'] = Mage::helper('core')->formatDate($giftcode['day_store'],'medium');
            return $giftcode;
        }
        else {
            throw new Exception(Mage::helper('simigiftvoucher')->__('Invalid gift code.'), 4);
        }

    }
}