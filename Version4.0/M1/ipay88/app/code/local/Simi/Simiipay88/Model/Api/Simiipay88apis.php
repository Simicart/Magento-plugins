<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Simiipay88_Model_Api_Simiipay88apis extends Simi_Simiconnector_Model_Api_Abstract {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            
        }
    }

    public function show() {
        $data = $this->getData();
        if ($data['resourceid'] == 'update_payment') {
            $info = Mage::getModel('simiipay88/simiipay88')->updatePayment((object) $data['params']);
            return $this->getDetail($info);
        } 
    }

}
