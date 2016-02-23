<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Paypalmobile
 * @copyright 	Copyright (c) 2012
 * @license 	
 */

 /**
 * Paypalmobile Resource Collection Model
 * 
 * @category 	
 * @package 	Paypalmobile
 * @author  	Developer
 */
class Simi_Paypalmobile_Model_Mysql4_Paypalmobile_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('paypalmobile/paypalmobile');
	}
}