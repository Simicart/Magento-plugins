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
 * Simicontact Helper
 * 
 * @category 	
 * @package 	Simicontact
 * @author  	Developer
 */
class Simi_Simicontact_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getContacts(){	
		return array(
			'email' => $this->getConfig("email"),
			'phone' => $this->getConfig("phone"),
			'website' => $this->getConfig("website"),			
		);		
	}
	
	public function getConfig($value){
		return Mage::getStoreConfig("simicontact/general".$value);
	}
}