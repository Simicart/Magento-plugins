<?php
/**
 *
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simibraintree
 * @copyright   Copyright (c) 2012 
 * @license    
 */

/**
 * Simibraintree Model
 * 
 * @category    
 * @package     Simibraintree
 * @author      Developer
 */
class Simi_Simibraintree_Model_Mysql4_Simibraintree extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simibraintree/simibraintree', 'braintree_id');
	}
}