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
 * Twout Resource Collection Model
 * 
 * @category 	
 * @package 	Twout
 * @author  	Developer
 */
class Simi_Twout_Model_Mysql4_Twout_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('twout/twout');
	}
}