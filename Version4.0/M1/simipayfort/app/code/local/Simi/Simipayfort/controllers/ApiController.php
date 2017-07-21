<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simipayfort
 * @copyright   Copyright (c) 2017 
 * @license     
 */

/**
 * Simipayfort Adminhtml Controller
 * 
 * @category    
 * @package     Simipayfort
 * @author      Developer
 */
class Simi_Simipayfort_ApiController extends Simi_Connector_Controller_Action {

    public function update_paymentAction() {
        $data = $this->getData();
        $information = Mage::getModel('simipayfort/simipayfort')->updatePayment($data);
        $this->_printDataJson($information);
    }

    public function test2Action() {
        $collection = Mage::getModel('simipayfort/simipayfort')->getCollection();
        Zend_debug::dump($collection->getData());
    }
    
}
