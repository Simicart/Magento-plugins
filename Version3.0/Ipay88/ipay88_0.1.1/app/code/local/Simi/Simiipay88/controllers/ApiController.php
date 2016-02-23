<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simiipay88
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simiipay88 Controller
 * 
 * @category 	
 * @package 	Simiipay88
 * @author  	Developer
 */
class Simi_Simiipay88_ApiController extends Simi_Connector_Controller_Action
{
	public function update_paymentAction() {		
        $data = $this->getData();		
        $information = Mage::getModel('simiipay88/simiipay88')->updatePayment($data);
        $this->_printDataJson($information);
    }	
}