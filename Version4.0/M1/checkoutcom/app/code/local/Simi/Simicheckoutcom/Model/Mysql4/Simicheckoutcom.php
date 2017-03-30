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
 * Simicheckoutcom Resource Model
 * 
 * @category    
 * @package     Simicheckoutcom
 * @author      Developer
 */
class Simi_Simicheckoutcom_Model_Mysql4_Simicheckoutcom extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simicheckoutcom/simicheckoutcom', 'simicheckoutcom_id');
	}
}