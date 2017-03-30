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
 * Simiipay88 Resource Model
 * 
 * @category 	
 * @package 	Simiipay88
 * @author  	Developer
 */
class Simi_Simiipay88_Model_Mysql4_Simiipay88 extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simiipay88/simiipay88', 'simiipay88_id');
	}
}