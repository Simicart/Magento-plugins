<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Twout
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

/**
 * Twout Controller
 * 
 * @category 	
 * @package 	Twout
 * @author  	Developer
 */
class Simi_Twout_ApiController extends Simi_Connector_Controller_Action {

    public function update_paymentAction() {
        $data = $this->getData();
        $information = Mage::getModel('twout/twout')->updatePayment($data);
        $this->_printDataJson($information);
    }

    public function get_cartAction() {
        $data = $this->getData();
        $information = Mage::getModel('twout/twout')->getCart($data);
        $this->_printDataJson($information);
    }

}
