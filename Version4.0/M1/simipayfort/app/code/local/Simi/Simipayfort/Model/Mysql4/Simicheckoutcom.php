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
 * Simipayfort Resource Model
 * 
 * @category    
 * @package     Simipayfort
 * @author      Developer
 */
class Simi_Simipayfort_Model_Mysql4_Simipayfort extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simipayfort/simipayfort', 'simipayfort_id');
	}
}