<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Twout
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Twout Index Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Twout
 * @author  	Magestore Developer
 */
class Simi_Twout_ApiController extends Simi_Connector_Controller_Action
{
	public function update_paymentAction() {		
        $data = $this->getData();		
        $information = Mage::getModel('twout/twout')->updatePayment($data);
        $this->_printDataJson($information);
    }
	
	public function get_cartAction(){		
		$data = $this->getData();	
        $information = Mage::getModel('twout/twout')->getCart($data);
        $this->_printDataJson($information);
	}
		
	
}