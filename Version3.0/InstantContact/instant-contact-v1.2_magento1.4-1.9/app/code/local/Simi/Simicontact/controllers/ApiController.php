<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simicontact
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simicontact Controller
 * 
 * @category 	
 * @package 	Simicontact
 * @author  	Developer
 */
class Simi_Simicontact_ApiController extends Simi_Connector_Controller_Action
{
	public function get_contactsAction() {
		$information = Mage::getModel('simicontact/contact')->getContacts();
		$this->_printDataJson($information);
    }
}