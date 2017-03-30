<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/2/16
 * Time: 4:20 PM
 */
class Simi_Simitracking_Model_Server extends Simi_Simiconnector_Model_Server {

    public function initialize(Simi_Simiconnector_Controller_Action $controller) {
        parent::initialize($controller);
        $data = $this->getData();
        if (isset($data['params']) && isset($data['params']['session_id'])) {
            Mage::helper('simitracking')->continueSessionWithSessionId($data['params']['session_id']);
        } else if (isset($data['contents_array']) && isset($data['contents_array']['session_id'])) {
            Mage::helper('simitracking')->continueSessionWithSessionId($data['contents_array']['session_id']);
        }
    }

}
