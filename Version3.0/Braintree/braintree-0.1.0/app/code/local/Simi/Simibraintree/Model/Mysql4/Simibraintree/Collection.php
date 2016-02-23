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
 * Simibraintree Resource Collection Model
 * 
 * @category    
 * @package     Simibraintree
 * @author      Developer
 */
class Simi_Simibraintree_Model_Mysql4_Simibraintree_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('simibraintree/simibraintree');
	}
}