<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 8/9/17
 * Time: 5:09 PM
 */
class Simi_Simigiftvoucher_Model_Api_Simitimezones extends Simi_Simiconnector_Model_Api_Abstract {
    public function setBuilderQuery(){}

    public function index(){
        $data = array();
        $data['timezones'] = Mage::getModel('core/locale')->getOptionTimezones();
        return $data;
    }
}