<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simicheckoutcom
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simicheckoutcom Adminhtml Controller
 * 
 * @category    
 * @package     Simicheckoutcom
 * @author      Developer
 */
class Simi_Simicheckoutcom_ApiController extends Simi_Connector_Controller_Action
{
	public function update_paymentAction() {		
        $data = $this->getData();		
        $information = Mage::getModel('simicheckoutcom/simicheckoutcom')->updatePayment($data);
        $this->_printDataJson($information);
    }

    public function test2Action()
    {
    	$collection = Mage::getModel('simicheckoutcom/simicheckoutcom')->getCollection();
    	Zend_debug::dump($collection->getData());
    }	
	
}