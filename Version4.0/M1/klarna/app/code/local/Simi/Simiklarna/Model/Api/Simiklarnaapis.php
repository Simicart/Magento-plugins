<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Simiklarna_Model_Api_Simiklarnaapis extends Simi_Simiconnector_Model_Api_Abstract {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            
        }
    }

    public function show() {
        $returnArray = array();
        $data = $this->getData();
        if ($data['resourceid'] == 'get_params') {
            $simiklarna = Mage::getModel('simiklarna/simiklarna');
            $returnArray = $simiklarna->getCart40();
        } else if ($data['resourceid'] == 'success') {
            $simiklarna = Mage::getModel('simiklarna/simiklarna');
            $returnArray = $simiklarna->confirmation40();
        } else if ($data['resourceid'] == 'push') {
            $simiklarna = Mage::getModel('simiklarna/simiklarna');
			$simiklarna->push40($data['params']['klarna_order']);
            $returnArray = array('message'=>Mage::helper('simiconnector')->__('Order Placed'));
        }
        return array('simiklarnaapi' => $returnArray);
    }

}
